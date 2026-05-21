<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Ebook;
use App\Models\Program;
use App\Models\ProgramCourse;
use App\Models\BookMarcField;
use App\Models\CatalogFramework;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class BookController extends Controller
{
    /**
     * Max number of active loans (check-out or room use) one patron may have at once.
     * Enforced in {@see \App\Http\Controllers\BookLogController} and {@see \App\Http\Controllers\CheckoutController}.
     */
    public const MAX_CONCURRENT_BOOK_LOANS_PER_STUDENT = 3;

    /** Max number of renewals allowed per loan. */
    public const MAX_RENEWALS_PER_LOAN = 3;

    /** Cooldown (in days) before the same patron can borrow the same book again after return. */
    public const REBORROW_COOLDOWN_DAYS = 7;

   protected function applyBookSearch($query, ?string $search)
   {
       $search = is_string($search) ? trim($search) : '';
       if ($search === '') {
           return $query;
       }

       // Multi-keyword search: all tokens must match somewhere.
       $tokens = preg_split('/\s+/', $search) ?: [];
       $tokens = array_values(array_filter(array_map('trim', $tokens)));

       foreach ($tokens as $token) {
           $like = "%{$token}%";
           $query->where(function ($q) use ($like, $token) {
               $q->where('title_statement', 'like', $like)
                   ->orWhere('main_author', 'like', $like)
                   ->orWhere('title_author', 'like', $like)
                   ->orWhere('control_no', 'like', $like)
                   ->orWhere('isbn', 'like', $like)
                   ->orWhere('publisher', 'like', $like)
                   ->orWhere('pub_place', 'like', $like)
                   ->orWhere('pub_year', 'like', $like)
                   ->orWhere('edition', 'like', $like)
                   ->orWhere('call_number', 'like', $like)
                   ->orWhere('accession_no', 'like', $like)
                   ->orWhere('barcode', 'like', $like)
                   ->orWhere('rfid', 'like', $like)
                   ->orWhere('availability', 'like', $like)
                   ->orWhere('content_type', 'like', $like)
                   ->orWhere('media_type', 'like', $like)
                   ->orWhere('carrier_type', 'like', $like)
                   ->orWhere('library_name', 'like', $like)
                   ->orWhere('section', 'like', $like)
                   ->orWhere('course', 'like', $like)
                   ->orWhere('year', 'like', $like)
                   ->orWhere('series_title', 'like', $like)
                   ->orWhere('subject_topic', 'like', $like)
                   ->orWhere('subject_form', 'like', $like)
                   ->orWhere('genre', 'like', $like)
                   ->orWhere('general_note', 'like', $like)
                   ->orWhere('bibliography_note', 'like', $like)
                   ->orWhere('source_vendor', 'like', $like)
                   ->orWhere('source_date', 'like', $like);

               // Allow searching by program name/code via pivot
               $q->orWhereHas('programs', function ($p) use ($token) {
                   $p->where('programs.program_name', 'like', "%{$token}%")
                       ->orWhere('programs.program_code', 'like', "%{$token}%");
               });
           });
       }

       return $query;
   }
   protected function booksFramework()
   {
       return CatalogFramework::where('name', 'Books')
           ->with(['fields' => function ($q) {
               $q->where('visible', true)->orderBy('sort_order')->with('marcField');
           }])
           ->first();
   }

   /** Strip empty selects so `exists:programs,id` does not run on "". */
   protected function normalizeProgramIdsOnRequest(Request $request): void
   {
       $raw = $request->input('program_ids', []);
       if (! is_array($raw)) {
           $raw = [];
       }
       $ids = array_values(array_unique(array_filter(array_map(static function ($v) {
           $i = (int) $v;

           return $i > 0 ? $i : null;
       }, $raw))));
       $request->merge(['program_ids' => $ids]);
   }

   protected function marcValuesForBook(Book $book, $frameworkFields = null): array
   {
       $out = [];
       foreach ($book->marcFields as $mf) {
           $subKey = $mf->subfield ?? '_';
           $out[$mf->tag][$subKey][$mf->occurrence] = $mf->value;
       }

       if (! $frameworkFields) {
           return $out;
       }

       foreach ($frameworkFields as $ff) {
           $mf = $ff->marcField;
           if (! $mf || ! $ff->book_column) {
               continue;
           }

           $tag = $mf->tag;
           $subKey = $mf->subfield ?? '_';
           $existing = $out[$tag][$subKey] ?? [];
           $hasValue = is_array($existing) && count(array_filter($existing, static function ($v) {
               return $v !== null && $v !== '';
           })) > 0;
           if ($hasValue) {
               continue;
           }

           $val = $book->{$ff->book_column} ?? null;
           if ($val === null || $val === '') {
               continue;
           }

           $val = (string) $val;
           if ($mf->repeatable && str_contains($val, ';')) {
               $parts = array_values(array_filter(array_map('trim', explode(';', $val))));
               foreach ($parts as $i => $part) {
                   $out[$tag][$subKey][$i] = $part;
               }
           } else {
               $out[$tag][$subKey][0] = $val;
           }
       }

       return $out;
   }

   protected function extractMarcPayload(Request $request): array
   {
       $marc = $request->input('marc', []);
       return is_array($marc) ? $marc : [];
   }

   protected function normalizeMarcValues(array $marc, string $tag, ?string $subfield): array
   {
       $subKey = $subfield ?? '_';
       $vals = $marc[$tag][$subKey] ?? [];
       if (! is_array($vals)) {
           $vals = [$vals];
       }
       $vals = array_values(array_filter(array_map(static function ($v) {
           $v = is_string($v) ? trim($v) : $v;
           return $v === '' ? null : $v;
       }, $vals)));
       return $vals;
   }

   protected function saveMarcFieldsForBook(Book $book, $framework, array $marc): void
   {
       if (! $framework) {
           return;
       }

       foreach ($framework->fields as $ff) {
           $mf = $ff->marcField;
           if (! $mf) continue;

           $values = $this->normalizeMarcValues($marc, $mf->tag, $mf->subfield);

           if ($ff->required && count($values) === 0) {
               $subKey = $mf->subfield ?? '_';
               throw ValidationException::withMessages([
                   "marc.{$mf->tag}.{$subKey}" => ["{$mf->tag}".($mf->subfield ? " ‡{$mf->subfield}" : '')." is required."],
               ]);
           }

           BookMarcField::where('book_id', $book->id)
               ->where('tag', $mf->tag)
               ->where(function ($q) use ($mf) {
                   if ($mf->subfield === null) {
                       $q->whereNull('subfield');
                   } else {
                       $q->where('subfield', $mf->subfield);
                   }
               })
               ->delete();

           foreach ($values as $i => $val) {
               BookMarcField::create([
                   'book_id' => $book->id,
                   'tag' => $mf->tag,
                   'subfield' => $mf->subfield,
                   'occurrence' => $i,
                   'value' => $val,
               ]);
           }

           if ($ff->book_column) {
               $book->{$ff->book_column} = $values[0] ?? null;
           }
       }

       $book->save();
   }
   public function index(Request $request)
    {
        // --- Get programs for filter dropdown ---
        $programs = Program::orderBy('program_name')->get();

        $hasActiveQuery = $request->filled('search')
            || $request->filled('program')
            || ($request->filled('year_filter') && $request->filled('year1'))
            || ($request->has('status') && in_array($request->status, ['Available', 'Borrowed'], true));

        if (! $hasActiveQuery) {
            $books = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
            $courses = collect();
            $years = collect();

            return view('books.index', compact('books', 'programs', 'courses', 'years', 'hasActiveQuery'));
        }
    
        // --- Base filtered query ---
        $filteredQuery = Book::query()->whereNull('archived_at');
    
        // Status filter
        if ($request->has('status') && in_array($request->status, ['Available', 'Borrowed'])) {
            $filteredQuery->where('availability', $request->status);
        }
    
        // Program filter
        if ($request->filled('program')) {
            $filteredQuery->whereHas('programs', function ($q) use ($request) {
                $q->where('programs.id', $request->program);
            });
        }
    
        // Year filter
        if ($request->filled('year_filter') && $request->filled('year1')) {
            $year1 = (int) $request->year1;
            $year2 = (int) $request->year2;
    
            switch ($request->year_filter) {
                case 'exact':
                    $filteredQuery->where('pub_year', $year1);
                    break;
                case 'before':
                    $filteredQuery->where('pub_year', '<=', $year1);
                    break;
                case 'after':
                    $filteredQuery->where('pub_year', '>=', $year1);
                    break;
                case 'between':
                    if ($request->filled('year2')) {
                        $filteredQuery->whereBetween('pub_year', [$year1, $year2]);
                    }
                    break;
            }
        }
    
        // Search (multi-field, multi-keyword)
        $this->applyBookSearch($filteredQuery, $request->input('search'));
    
        // --- Dynamic dropdowns for course/year ---
        $courses = Book::whereNull('archived_at')
            ->when($request->program, fn($q) => $q->whereHas('programs', fn($p) => $p->where('program_name', $request->program)))
            ->select('course')->distinct()->orderBy('course')->pluck('course');
    
        $years = Book::whereNull('archived_at')
            ->when($request->program, fn($q) => $q->whereHas('programs', fn($p) => $p->where('program_name', $request->program)))
            ->when($request->course, fn($q) => $q->where('course', $request->course))
            ->select('year')->distinct()->orderBy('year')->pluck('year');
    
        // --- Aggregate on filtered query ---
        $books = DB::table(DB::raw("({$filteredQuery->toSql()}) as sub"))
            ->mergeBindings($filteredQuery->getQuery())
            ->select(
                'main_author',
                'title_statement',
                'pub_year',
                'content_type', // add this
                DB::raw('COUNT(*) as copies'),
                DB::raw('MIN(id) as sample_id')
            )
            ->groupBy('main_author', 'title_statement', 'pub_year', 'content_type') // add content_type here
            ->orderBy('title_statement')
            ->paginate(10)
            ->withQueryString();

    
        return view('books.index', compact('books', 'programs', 'courses', 'years', 'hasActiveQuery'));
    }
    
    public function viewCopies(Request $request)
    {
        // Validate that nullable params exist
        if (!$request->filled('title') || !$request->filled('author') || !$request->filled('year')) {
            abort(404, 'Missing book group information.');
        }
    
        $title = $request->title;
        $author = $request->author;
        $year = $request->year;
    
        // Get all copies matching the same group
        $copies = Book::whereNull('archived_at')
            ->where('title_statement', $title)
            ->where('main_author', $author)
            ->where('pub_year', $year)
            ->orderBy('accession_no', 'asc')
            ->paginate(10)
            ->withQueryString(); // Keep URL parameters when switching pages
    
        return view('books.copies', compact('copies', 'title', 'author', 'year'));
    }

    /**
     * Public OPAC JSON for grouped title: holdings list, description fields, optional MARC lines.
     */
    public function opacBookDetails(Book $book)
    {
        if ($book->archived_at !== null) {
            abort(404);
        }

        $copies = Book::query()
            ->whereNull('archived_at')
            ->where('title_statement', $book->title_statement)
            ->where('main_author', $book->main_author)
            ->where('pub_year', $book->pub_year)
            ->orderBy('accession_no')
            ->get([
                'id',
                'accession_no',
                'call_number',
                'volume',
                'barcode',
                'rfid',
                'availability',
                'course',
                'section',
                'library_name',
                'content_type',
                'title_statement',
                'main_author',
                'pub_year',
                'barcode',
                'rfid',
            ]);

        $physicalParts = array_values(array_filter([
            $book->pages ? trim((string) $book->pages).' p.' : null,
            $book->illustrations ? trim((string) $book->illustrations) : null,
            $book->size ? trim((string) $book->size).' cm' : null,
        ]));
        $physicalDesc = $physicalParts !== [] ? implode(' ', $physicalParts) : null;

        $published = trim(implode(' ', array_filter([
            $book->pub_place,
            $book->publisher,
            $book->pub_year !== null && $book->pub_year !== '' ? (string) $book->pub_year : null,
        ])));

        $copyIds = $copies->pluck('id')->all();

        $fullBooks = $copyIds === []
            ? collect()
            : Book::query()
                ->whereNull('archived_at')
                ->whereIn('id', $copyIds)
                ->orderBy('accession_no')
                ->get();

        $rep = $fullBooks->firstWhere('id', $book->id) ?? $fullBooks->first() ?? $book;
        $rep->loadMissing('programs');
        $fullBooks->loadMissing('programs');

        $marcViewRows = $this->opacMarcViewRowsForGroupedTitle($rep, $fullBooks);

        return response()->json([
            'group' => [
                'title' => $book->title_statement,
                'author' => $book->main_author,
                'year' => $book->pub_year,
            ],
            'description' => [
                'main_author' => $book->main_author,
                'title' => $book->title_statement,
                'format' => $book->content_type,
                'edition' => $book->edition,
                'published' => $published !== '' ? $published : null,
                'isbn' => $book->isbn,
                'general_note' => $book->general_note,
                'physical_description' => $physicalDesc,
                'bibliography' => $book->bibliography_note,
                'subject_topic' => $book->subject_topic,
                'subject_form' => $book->subject_form,
                'genre' => $book->genre,
                'series' => $book->series_title,
            ],
            'copies' => $copies->map(function (Book $c) {
                $statusLabel = match ($c->availability) {
                    'Available' => 'On-Shelf',
                    'Borrowed' => 'Checked out',
                    default => $c->availability ?? '—',
                };

                return [
                    'id' => $c->id,
                    'accession_no' => $c->accession_no,
                    'call_number' => $c->call_number,
                    'volume' => $c->volume,
                    'copy_no' => null,
                    'collection' => $c->course,
                    'shelving_location' => trim(implode(' — ', array_filter([$c->library_name, $c->section]))),
                    'circulation_type' => 'Regular circulation',
                    'circulation_status' => $statusLabel,
                    'availability' => $c->availability,
                    'barcode' => $c->barcode,
                    'rfid' => $c->rfid,
                ];
            })->values(),
            'marc_view_rows' => $marcViewRows,
        ]);
    }

    /**
     * MARC-style rows aligned with {@see \App\Http\Controllers\BookController::show} / books.show — only fields
     * that are non-empty on the representative copy and identical on every copy in the group.
     *
     * @param  \Illuminate\Support\Collection<int, Book>  $fullBooks
     * @return list<array{label: string, value: string}>
     */
    protected function opacMarcViewRowsForGroupedTitle(Book $rep, $fullBooks): array
    {
        if ($fullBooks->isEmpty()) {
            return [];
        }

        $rows = [];

        $scalarDefs = [
            ['attr' => 'control_no', 'label' => '001 (Control No.):'],
            ['attr' => 'date_time_stamp', 'label' => '005 (Date & Time Stamp):'],
            ['attr' => 'fixed_length_data', 'label' => '008 (Fixed-Length Data):'],
            ['attr' => 'isbn', 'label' => '020 ‡a (ISBN):'],
            ['attr' => 'price', 'label' => '020 ‡c (Price):'],
            ['attr' => 'cataloging_source_a', 'label' => '040 ‡a (Cataloging Source):'],
            ['attr' => 'cataloging_source_b', 'label' => '040 ‡b (Language):'],
            ['attr' => 'cataloging_source_e', 'label' => '040 ‡e (Description Conventions)'],
            ['attr' => 'main_author', 'label' => '100 ‡a (Main Author)'],
            ['attr' => 'title_statement', 'label' => '245 ‡a (Title)'],
            ['attr' => 'title_author', 'label' => '245 ‡c (Title Responsibility)'],
            ['attr' => 'edition', 'label' => '250 ‡a (Edition)'],
            ['attr' => 'pub_place', 'label' => '264 ‡a (Publication Place)'],
            ['attr' => 'publisher', 'label' => '264 ‡b (Publisher)'],
            ['attr' => 'pub_year', 'label' => '264 ‡c (Publication Year)'],
            ['attr' => 'pages', 'label' => '300 ‡a (Pages)'],
            ['attr' => 'illustrations', 'label' => '300 ‡b (Illustrations)'],
            ['attr' => 'size', 'label' => '300 ‡c (Size)'],
            ['attr' => 'volume', 'label' => '300 ‡f (Type of unit)'],
            ['attr' => 'content_type', 'label' => '336 ‡a (Content Type)'],
            ['attr' => 'media_type', 'label' => '337 ‡a (Media Type)'],
            ['attr' => 'carrier_type', 'label' => '338 ‡a (Carrier Type)'],
            ['attr' => 'series_title', 'label' => '490 ‡a (Series Title)'],
            ['attr' => 'general_note', 'label' => '500 ‡a (General Note)'],
            ['attr' => 'bibliography_note', 'label' => '504 ‡a (Bibliography Note)'],
            ['attr' => 'source_vendor', 'label' => '541 ‡a (Immediate source of acquisition)'],
            ['attr' => 'source_date', 'label' => '541 ‡d (Date of acquisition)'],
            ['attr' => 'subject_topic', 'label' => '650 ‡a (Subject)'],
            ['attr' => 'subject_form', 'label' => '650 ‡v (Form)'],
            ['attr' => 'genre', 'label' => '655 ‡a (Genre)'],
            ['attr' => 'library_name', 'label' => '852 ‡b (Library Name)'],
            ['attr' => 'section', 'label' => '852 ‡c (Sublocation / shelving)'],
            ['attr' => 'call_number', 'label' => '852 ‡h (Call Number)'],
            ['attr' => 'accession_no', 'label' => '949 (Accession No.)'],
            ['attr' => 'barcode', 'label' => '876 ‡p (Barcode)'],
            ['attr' => 'rfid', 'label' => '999 ‡r (RFID, local)'],
            ['attr' => 'year', 'label' => '996 ‡e (Year)'],
            ['attr' => 'course', 'label' => '650 ‡a (Course)'],
        ];

        foreach ($scalarDefs as $def) {
            $attr = $def['attr'];
            if (! $this->opacBooksShareSameAttribute($fullBooks, $attr)) {
                continue;
            }
            $raw = $rep->getAttribute($attr);
            if ($raw instanceof \DateTimeInterface) {
                $raw = $raw->format('Y-m-d H:i:s');
            }
            if (! filled($raw)) {
                continue;
            }
            $rows[] = ['label' => $def['label'], 'value' => (string) $raw];
        }

        if ($this->opacProgramsShareSame($fullBooks) && $rep->programs->isNotEmpty()) {
            $value = $rep->programs->sortBy('program_name')->pluck('program_name')->filter()->implode(', ');
            if (filled($value)) {
                $rows[] = ['label' => '996 ‡f (Program)', 'value' => $value];
            }
        }

        if ($this->opacBooksShareSameAttribute($fullBooks, 'availability') && filled($rep->availability)) {
            $rows[] = ['label' => 'Status:', 'value' => (string) $rep->availability];
        }

        return $rows;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Book>  $books
     */
    protected function opacBooksShareSameAttribute($books, string $attribute): bool
    {
        if ($books->isEmpty()) {
            return false;
        }

        $normalize = static function ($value): string {
            if ($value === null) {
                return "\0null";
            }
            if ($value === '') {
                return "\0empty";
            }
            if ($value instanceof \DateTimeInterface) {
                return $value->format('c');
            }
            if (is_bool($value)) {
                return $value ? '1' : '0';
            }

            return (string) $value;
        };

        $firstVal = $normalize($books->first()->getAttribute($attribute));

        foreach ($books as $b) {
            if ($normalize($b->getAttribute($attribute)) !== $firstVal) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Book>  $books
     */
    protected function opacProgramsShareSame($books): bool
    {
        $books->loadMissing('programs');
        $signatures = $books->map(function (Book $b) {
            return $b->programs->pluck('id')->sort()->values()->implode(',');
        })->unique();

        return $signatures->count() === 1;
    }

    public function viewCopiesStaff(Request $request)
    {
        if (!$request->filled('title') || !$request->filled('author') || !$request->filled('year')) {
            abort(404, 'Missing book group information.');
        }

        $title = $request->title;
        $author = $request->author;
        $year = $request->year;

        $copies = Book::whereNull('archived_at')
            ->where('title_statement', $title)
            ->where('main_author', $author)
            ->where('pub_year', $year)
            ->orderBy('accession_no', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('books.copies_staff', compact('copies', 'title', 'author', 'year'));
    }

    public function landingPage(Request $request)
    {
        $viewMode = (string) $request->input('view', 'books'); // 'books' | 'ebooks'
        $viewMode = in_array($viewMode, ['books', 'ebooks'], true) ? $viewMode : 'books';

        $searchActive =
            $viewMode === 'ebooks' ||
            ($request->filled('search') && trim((string) $request->input('search')) !== '');

        // ----------------------
        // 1) Carousel (recent rows, always unfiltered) + grouped stats (same as OPAC)
        // ----------------------
        // 1) Carousel: one card per title (title + author + year), newest groups first.
        $carouselGroup = Book::query()
            ->whereNull('archived_at')
            ->select(
                'title_statement',
                'main_author',
                'pub_year',
                DB::raw('COUNT(*) AS copies'),
                DB::raw('MIN(id) AS sample_id'),
                DB::raw("MAX(CASE WHEN availability = 'Available' THEN 1 ELSE 0 END) AS is_available")
            )
            ->groupBy('title_statement', 'main_author', 'pub_year')
            ->orderByDesc(DB::raw('MAX(created_at)'))
            ->limit(12);
        
        $carouselGroupRows = DB::table(DB::raw("({$carouselGroup->toSql()}) as grouped"))
            ->mergeBindings($carouselGroup->getQuery())
            ->select('grouped.sample_id', 'grouped.copies', 'grouped.is_available')
            ->get();
        
        $carouselSampleIds = $carouselGroupRows->pluck('sample_id')->all();
        $carouselBooksById = $carouselSampleIds === []
            ? collect()
            : Book::query()->whereIn('id', $carouselSampleIds)->get()->keyBy('id');
        
        $carouselBooks = collect($carouselSampleIds)
            ->map(fn ($id) => $carouselBooksById->get($id))
            ->filter()
            ->values();
        
        $carouselMeta = [];
        foreach ($carouselGroupRows as $row) {
            $carouselMeta[(int) $row->sample_id] = [
                'copies' => (int) $row->copies,
                'is_available' => (int) $row->is_available === 1,
            ];
        }

        $carouselStatLookup = $this->carouselGroupStatsLookup($carouselBooks);
        $carouselMeta = [];
        foreach ($carouselBooks as $cb) {
            $key = $cb->title_statement."\0".$cb->main_author."\0".$cb->pub_year;
            $carouselMeta[$cb->id] = $carouselStatLookup[$key] ?? [
                'copies' => 1,
                'is_available' => $cb->availability === 'Available',
            ];
        }

        $ebooks = null;

        if ($viewMode === 'ebooks') {
            $q = Ebook::query();
            if ($request->filled('search')) {
                $term = trim((string) $request->input('search'));
                $q->where(function ($w) use ($term) {
                    $like = '%'.$term.'%';
                    $w->where('title', 'like', $like)
                        ->orWhere('author', 'like', $like)
                        ->orWhere('publisher', 'like', $like)
                        ->orWhere('source', 'like', $like)
                        ->orWhere('publication_year', 'like', $like);
                });
            }

            $ebooks = $q->orderBy('title')->paginate(20)->withQueryString();

            // Keep `$books` as empty paginator to avoid blade errors on counts.
            $perPage = 20;
            $currentPage = max(1, (int) $request->input('page', 1));
            $books = new LengthAwarePaginator([], 0, $perPage, $currentPage, [
                'path' => $request->url(),
                'pageName' => 'page',
            ]);
            $books->withQueryString();
        } elseif (! $searchActive) {
            $perPage = 20;
            $currentPage = max(1, (int) $request->input('page', 1));
            $books = new LengthAwarePaginator([], 0, $perPage, $currentPage, [
                'path' => $request->url(),
                'pageName' => 'page',
            ]);
            $books->withQueryString();
        } else {
            // ----------------------
            // 2) Base Eloquent query (filters + search) — only after a non-empty search
            // ----------------------
            $query = Book::query()->whereNull('archived_at');

            if ($request->filled('course') && $request->course !== 'all') {
                $query->where('course', $request->course);
            }

            if ($request->filled('subject_topic') && $request->subject_topic !== 'All') {
                $query->where('subject_topic', $request->subject_topic);
            }

            if ($request->filled('genre') && $request->genre !== 'All') {
                $query->where('genre', $request->genre);
            }

            if ($request->filled('content_type') && $request->content_type !== 'All') {
                $query->where('content_type', $request->content_type);
            }

            if ($request->filled('section') && $request->section !== 'All') {
                $query->where('section', $request->section);
            }

            $this->applyBookSearch($query, $request->input('search'));

            // ----------------------
            // 3) Grouped subquery (count copies, get a sample id, detect availability)
            // ----------------------
            $grouped = $query->getQuery()->clone()
                ->select(
                    'title_statement',
                    'main_author',
                    'pub_year',
                    DB::raw('COUNT(*) AS copies'),
                    DB::raw('MIN(id) AS sample_id'),
                    DB::raw("MAX(CASE WHEN availability = 'Available' THEN 1 ELSE 0 END) AS is_available")
                )
                ->groupBy('title_statement', 'main_author', 'pub_year');

            // ----------------------
            // 4) Join grouped subquery back to books to grab sample fields
            // ----------------------
            $books = DB::table(DB::raw("({$grouped->toSql()}) as grouped"))
                ->mergeBindings($grouped)
                ->join('books', 'books.id', '=', 'grouped.sample_id')
                ->select(
                    'grouped.title_statement',
                    'grouped.main_author',
                    'grouped.pub_year',
                    'grouped.copies',
                    'grouped.sample_id as id',
                    'grouped.is_available',
                    'books.call_number',
                    'books.general_note',
                    'books.cover_image',
                    'books.rfid',
                    'books.barcode',
                    'books.content_type',
                    'books.fixed_length_data',
                    'books.library_name',
                    'books.course'
                )
                ->orderBy('grouped.title_statement')
                ->paginate(20)
                ->withQueryString();
        }
    
        // ----------------------
        // 5) Distinct dropdown sources (always from full table)
        // ----------------------
        $subjectTopics = Book::select('subject_topic')
            ->distinct()
            ->whereNull('archived_at')
            ->whereNotNull('subject_topic')
            ->orderBy('subject_topic')
            ->pluck('subject_topic');
    
        $genres = Book::select('genre')
            ->distinct()
            ->whereNull('archived_at')
            ->whereNotNull('genre')
            ->orderBy('genre')
            ->pluck('genre');
        
        $content_type = Book::select('content_type')
            ->distinct()
            ->whereNull('archived_at')
            ->whereNotNull('content_type')
            ->orderBy('content_type')
            ->pluck('content_type');
            
        $sections = Book::select('section')
            ->distinct()
            ->whereNull('archived_at')
            ->whereNotNull('section')
            ->orderBy('section')
            ->pluck('section');
    
        $courses = Book::select('course')
            ->distinct()
            ->whereNull('archived_at')
            ->whereNotNull('course')
            ->orderBy('course')
            ->pluck('course');
    
        // ----------------------
        // 6) Return view
        // ----------------------
        return view('books.landing', compact(
            'books',
            'ebooks',
            'carouselBooks',
            'carouselMeta',
            'subjectTopics',
            'genres',
            'sections',
            'courses',
            'content_type',
            'searchActive',
            'viewMode'
        ));
    }

    /**
     * Copy count + "any available" per title/author/year for carousel cards (matches OPAC grouping).
     *
     * @param  \Illuminate\Support\Collection<int, Book>  $carouselBooks
     * @return array<string, array{copies: int, is_available: bool}>
     */
    protected function carouselGroupStatsLookup($carouselBooks): array
    {
        if ($carouselBooks->isEmpty()) {
            return [];
        }

        $tuples = $carouselBooks->map(fn (Book $b) => [
            'title_statement' => $b->title_statement,
            'main_author' => $b->main_author,
            'pub_year' => $b->pub_year,
        ])->unique(fn (array $t) => $t['title_statement']."\0".$t['main_author']."\0".$t['pub_year'])
            ->values();

        $query = Book::query()
            ->select('title_statement', 'main_author', 'pub_year')
            ->selectRaw('COUNT(*) as copies')
            ->selectRaw("MAX(CASE WHEN availability = 'Available' THEN 1 ELSE 0 END) as is_available");

        $query->where(function ($outer) use ($tuples) {
            foreach ($tuples as $t) {
                $outer->orWhere(function ($w) use ($t) {
                    $w->where('title_statement', $t['title_statement'])
                        ->where('main_author', $t['main_author'])
                        ->where('pub_year', $t['pub_year']);
                });
            }
        });

        $rows = $query->groupBy('title_statement', 'main_author', 'pub_year')->get();

        $lookup = [];
        foreach ($rows as $row) {
            $k = $row->title_statement."\0".$row->main_author."\0".$row->pub_year;
            $lookup[$k] = [
                'copies' => (int) $row->copies,
                'is_available' => (int) $row->is_available === 1,
            ];
        }

        return $lookup;
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('book.index')->with('success', 'Book deleted successfully!');
    }

    public function archivedIndex(Request $request)
    {
        $books = Book::query()
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->paginate(20)
            ->withQueryString();

        return view('books.archived', compact('books'));
    }

    public function trashIndex(Request $request)
    {
        $books = Book::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(20)
            ->withQueryString();

        return view('books.trash', compact('books'));
    }

    public function archive(Book $book)
    {
        if ($book->archived_at === null) {
            $book->archived_at = Carbon::now();
            $book->save();
        }

        return back()->with('success', 'Book archived.');
    }

    public function unarchive(Book $book)
    {
        if ($book->archived_at !== null) {
            $book->archived_at = null;
            $book->save();
        }

        return back()->with('success', 'Book restored from archive.');
    }

    public function restoreTrashed(int $id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();
        return back()->with('success', 'Book restored.');
    }

    public function forceDeleteTrashed(int $id)
    {
        $book = Book::onlyTrashed()->with(['programs', 'marcFields', 'logs'])->findOrFail($id);

        DB::transaction(function () use ($book) {
            $book->programs()->detach();
            $book->marcFields()->delete();
            $book->logs()->delete();
            $book->forceDelete();
        });

        return back()->with('success', 'Book permanently deleted.');
    }

    public function create()
    {
        $programs = Program::orderBy('program_name')->get();

        $framework = $this->booksFramework();
        $frameworkFields = $framework?->fields ?? collect();

        return view('books.create', compact('programs', 'frameworkFields'));
    }

    /**
     * Prospectus courses (program_courses.course_name) for one or more programs — cataloging AJAX.
     */
    public function coursesForPrograms(Request $request)
    {
        $ids = $request->input('program_ids', []);
        if (! is_array($ids)) {
            $ids = array_filter([$ids]);
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if ($ids === []) {
            return response()->json([]);
        }

        $names = ProgramCourse::query()
            ->whereHas('year', static function ($q) use ($ids) {
                $q->whereIn('program_id', $ids);
            })
            ->orderBy('course_name')
            ->pluck('course_name')
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique(fn ($n) => mb_strtolower($n))
            ->values();

        return response()->json($names);
    }

    public function store(Request $request)
    {
        $this->normalizeProgramIdsOnRequest($request);

        $request->validate([
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'integer|exists:programs,id',
            'year' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'external_cover_url' => 'nullable|string|max:2048',
            'catalog_source' => 'nullable|string|in:openlibrary,googlebooks',
        ]);

        $framework = $this->booksFramework();
        $marc = $this->extractMarcPayload($request);

        try {
            $book = DB::transaction(function () use ($request, $framework, $marc) {
                // Handle cover image
                $coverPath = null;
                if ($request->hasFile('cover_image')) {
                    $coverPath = $request->file('cover_image')->store('covers', 'public');
                    \File::copy(
                        storage_path('app/public/' . $coverPath),
                        public_path('storage/' . $coverPath)
                    );
                } elseif ($request->filled('external_cover_url')) {
                    $url = trim((string) $request->input('external_cover_url'));
                    if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                        try {
                            $resp = Http::timeout(25)->get($url);
                            if ($resp->successful() && strlen($resp->body()) > 0) {
                                $ext = strtolower((string) pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                                    $ext = 'jpg';
                                }
                                $coverPath = 'covers/ol_'.Str::random(12).'.'.$ext;
                                Storage::disk('public')->put($coverPath, $resp->body());
                                \File::copy(
                                    storage_path('app/public/'.$coverPath),
                                    public_path('storage/'.$coverPath)
                                );
                            }
                        } catch (\Throwable $e) {
                            \Log::warning('external_cover_url download failed: '.$e->getMessage());
                        }
                    }
                }

                $book = Book::create([
                    'availability' => 'Available',
                    'year' => $request->year,
                    'course' => $request->course,
                    'cover_image' => $coverPath,
                ]);

                $this->saveMarcFieldsForBook($book, $framework, $marc);

                if ($book->barcode && Book::withTrashed()->where('barcode', $book->barcode)->where('id', '!=', $book->id)->exists()) {
                    throw ValidationException::withMessages(['marc.876.p' => ['Barcode must be unique.']]);
                }
                if ($book->rfid && Book::withTrashed()->where('rfid', $book->rfid)->where('id', '!=', $book->id)->exists()) {
                    throw ValidationException::withMessages(['marc.999.r' => ['RFID must be unique.']]);
                }

                if (! empty($request->program_ids)) {
                    $book->programs()->attach($request->program_ids);
                }

                return $book;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Book store failed: '.$e->getMessage(), ['exception' => $e]);

            return back()
                ->withInput()
                ->with('error', 'Could not save the book: '.$e->getMessage());
        }

        if (in_array($request->input('catalog_source'), ['openlibrary', 'googlebooks'], true)) {
            $returnIsbn = $book->isbn ?: $request->input('openlibrary_return_isbn');
            if ($returnIsbn) {
                return redirect()
                    ->route('catalog.copy.openlibrary.search', ['isbn' => $returnIsbn])
                    ->with('success', 'Book saved successfully.');
            }
        }

        return redirect()->route('book.index')->with('success', 'Book added successfully!');
    }


    public function show($id)
    {
        $book = Book::with('programs')->findOrFail($id);

        return view('books.show', compact('book'));
    }

    public function edit($id)
    {
        $book = Book::with(['programs', 'marcFields'])->findOrFail($id);
        $programs = Program::orderBy('program_name')->get();

        $framework = $this->booksFramework();
        $frameworkFields = $framework?->fields ?? collect();
        $marcValues = $this->marcValuesForBook($book, $frameworkFields);

        return view('books.edit', compact('book', 'programs', 'frameworkFields', 'marcValues'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $this->normalizeProgramIdsOnRequest($request);

        $request->validate([
            'year' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            // ❌ remove single program validation (we use many-to-many now)
            // 'program' => 'nullable|string|max:255',
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'integer|exists:programs,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['year', 'course']);

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');

            \File::copy(
                storage_path('app/public/' . $coverPath),
                public_path('storage/' . $coverPath)
            );

            $data['cover_image'] = $coverPath;
        }

        $book->update($data);

        $framework = $this->booksFramework();
        $marc = $this->extractMarcPayload($request);
        $this->saveMarcFieldsForBook($book, $framework, $marc);

        if ($book->barcode && Book::withTrashed()->where('barcode', $book->barcode)->where('id', '!=', $book->id)->exists()) {
            throw ValidationException::withMessages(['marc.876.p' => ['Barcode must be unique.']]);
        }
        if ($book->rfid && Book::withTrashed()->where('rfid', $book->rfid)->where('id', '!=', $book->id)->exists()) {
            throw ValidationException::withMessages(['marc.999.r' => ['RFID must be unique.']]);
        }

        if (!empty($request->program_ids)) {
            // Replace existing programs with the new ones
            $book->programs()->sync($request->program_ids);
        } else {
            // No program selected → detach all
            $book->programs()->detach();
        }

        return redirect()->route('book.index')->with('success', 'Book updated successfully!');
    }


    public function getYears(Request $request)
    {
        $program = $request->program;
        $years = Book::where('program', $program)
            ->select('year')->distinct()->orderBy('year')->pluck('year');
        return response()->json($years);
    }

    public function getCourses(Request $request)
    {
        $program = $request->program;
        $year = $request->year;
        $courses = Book::where('program', $program)
            ->where('year', $year)
            ->select('course')->distinct()->orderBy('course')->pluck('course');
        return response()->json($courses);
    }

    public function downloadBookReport()
    {
        // Count total books per title
        $booksByTitle = Book::select('title_statement')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('title_statement')
            ->orderBy('title_statement')
            ->get();

        $totalBooks = $booksByTitle->sum('total');

        // Get all subjects grouped by course
        $books = DB::table('books')
            ->select('course', 'title_statement')
            ->groupBy('course', 'title_statement')
            ->orderBy('course')
            ->orderBy('title_statement')
            ->get();

        $groupedBooks = $books->groupBy('course');

        // Pass both variables to the view
        $pdf = Pdf::loadView('pdf.book_report', compact('booksByTitle', 'totalBooks', 'groupedBooks'));

        return $pdf->download('book_report.pdf');
    }
}