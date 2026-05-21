<?php

namespace App\Http\Controllers;

use App\Models\CatalogFramework;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Services\GoogleBooksService;
use App\Services\OpenLibraryService;
use App\Models\Book;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OpenLibraryCopyCatalogController extends Controller
{
    protected OpenLibraryService $openLibrary;

    protected GoogleBooksService $googleBooks;

    public function __construct(OpenLibraryService $openLibrary, GoogleBooksService $googleBooks)
    {
        $this->openLibrary = $openLibrary;
        $this->googleBooks = $googleBooks;
    }

    protected function booksFramework(): ?CatalogFramework
    {
        return CatalogFramework::where('name', 'Books')
            ->with(['fields' => function ($q) {
                $q->where('visible', true)->orderBy('sort_order')->with('marcField');
            }])
            ->first();
    }

    /**
     * Map Open Library flat fields into marc[][][] for the framework-driven editor.
     *
     * @param  array<string, mixed>  $record
     * @return array<string, array<string, array<int, string>>>
     */
    protected function marcValuesFromOpenLibraryRecord(array $record, $frameworkFields): array
    {
        $out = [];

        foreach ($frameworkFields as $ff) {
            $mf = $ff->marcField;
            if (! $mf || ! $ff->book_column) {
                continue;
            }

            $col = $ff->book_column;
            if (! array_key_exists($col, $record)) {
                continue;
            }

            $val = $record[$col];
            if ($val === null || $val === '') {
                continue;
            }

            if (is_array($val)) {
                $val = implode('; ', array_map(static fn ($v) => (string) $v, $val));
            } else {
                $val = (string) $val;
            }

            $tag = $mf->tag;
            $subKey = $mf->subfield ?? '_';

            if ($mf->repeatable && str_contains($val, ';')) {
                $parts = array_values(array_filter(array_map('trim', explode(';', $val))));
                foreach ($parts as $i => $part) {
                    $out[$tag][$subKey][$i] = $part;
                }

                continue;
            }

            $out[$tag][$subKey][0] = $val;
        }

        return $out;
    }

    public function searchForm(Request $request)
    {
        $prefillIsbn = (string) $request->query('isbn', '');

        return view('catalog.copy.openlibrary-search', compact('prefillIsbn'));
    }

    public function search(Request $request)
    {
        // POST from ISBN form → PRG so the review screen is always GET (refresh / validation errors work).
        if ($request->isMethod('post')) {
            $request->validate([
                'isbn' => 'required|string|max:32',
            ]);

            return redirect()->route('catalog.copy.openlibrary.search', [
                'isbn' => $request->input('isbn'),
            ]);
        }

        if (! $request->filled('isbn')) {
            return redirect()->route('catalog.copy.openlibrary.form');
        }

        $request->validate([
            'isbn' => 'required|string|max:32',
        ]);

        $record = $this->openLibrary->lookupByIsbn($request->isbn);
        $catalogSource = 'openlibrary';

        if (! $record) {
            $record = $this->googleBooks->lookupByIsbn($request->isbn);
            $catalogSource = 'googlebooks';
        }

        if (! $record) {
            return redirect()
                ->route('catalog.copy.openlibrary.form', ['isbn' => $request->input('isbn')])
                ->with('error', 'No record found for that ISBN in Open Library or Google Books. Try another ISBN or catalog manually.');
        }

        $programs = Program::orderBy('program_name')->get();
        $framework = $this->booksFramework();
        $frameworkFields = $framework?->fields ?? collect();
        $marcValues = $this->marcValuesFromOpenLibraryRecord($record, $frameworkFields);
        $isbnQuery = $request->input('isbn');

        return view('catalog.copy.openlibrary-review', compact(
            'record',
            'programs',
            'frameworkFields',
            'marcValues',
            'isbnQuery',
            'catalogSource'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'control_no'          => 'nullable|string',
            'date_time_stamp'     => 'nullable|string',
            'fixed_length_data'   => 'nullable|string',
            'isbn'                => 'required|string',
            'price'               => 'nullable|string',
            'cataloging_source_a' => 'nullable|string',
            'cataloging_source_b' => 'nullable|string',
            'cataloging_source_e' => 'nullable|string',
            'main_author'         => 'nullable|string',
            'title_statement'     => 'required|string',
            'title_author'        => 'nullable|string',
            'edition'             => 'nullable|string',
            'pub_place'           => 'nullable|string',
            'publisher'           => 'nullable|string',
            'pub_year'            => 'nullable|string',
            'pages'               => 'nullable|string',
            'illustrations'       => 'nullable|string',
            'size'                => 'nullable|string',
            'volume'              => 'nullable|string',
            'content_type'        => 'nullable|string',
            'media_type'          => 'nullable|string',
            'carrier_type'        => 'nullable|string',
            'series_title'        => 'nullable|string',
            'general_note'        => 'nullable|string',
            'bibliography_note'   => 'nullable|string',
            'source_vendor'       => 'nullable|string',
            'source_date'         => 'nullable|date',
            'subject_topic'       => 'nullable|string',
            'subject_form'        => 'nullable|string',
            'genre'               => 'nullable|string',
            'library_name'        => 'nullable|string',
            'section'             => 'nullable|string',
            'call_number'         => 'nullable|string',
            'accession_no'        => 'nullable|string',
            'barcode'             => 'nullable|string',
            'rfid'                => 'nullable|string',
            'year'                => 'nullable|string',
            'course'              => 'nullable|string',
            'cover_image'         => 'nullable|string', // Open Library URL
        ]);
    
        $coverPath = null;
    
        // Download cover image from Open Library if provided
        if ($request->cover_image) {
            try {
                $imageContents = Http::get($request->cover_image)->body();
    
                $extension = pathinfo(parse_url($request->cover_image, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = $request->isbn ? $request->isbn . '.' . $extension : uniqid() . '.' . $extension;
    
                Storage::disk('public')->put('books/' . $filename, $imageContents);
    
                $coverPath = 'books/' . $filename;
            } catch (\Exception $e) {
                \Log::error("Cover download failed: " . $e->getMessage());
            }
        }
    
        // Gather all data from request
        $data = $request->only([
            'control_no',
            'date_time_stamp',
            'fixed_length_data',
            'isbn',
            'price',
            'cataloging_source_a',
            'cataloging_source_b',
            'cataloging_source_e',
            'main_author',
            'title_statement',
            'title_author',
            'edition',
            'pub_place',
            'publisher',
            'pub_year',
            'pages',
            'illustrations',
            'size',
            'volume',
            'content_type',
            'media_type',
            'carrier_type',
            'series_title',
            'general_note',
            'bibliography_note',
            'source_vendor',
            'source_date',
            'subject_topic',
            'subject_form',
            'genre',
            'library_name',
            'section',
            'call_number',
            'accession_no',
            'barcode',
            'rfid',
            'year',
            'course',
        ]);
    
        // Add downloaded cover path
        $data['cover_image'] = $coverPath;
    
        // Save to DB
        \App\Models\Book::create($data);
    
        return redirect()
        ->back()
        ->with('catalog_status', 'success')
        ->with('catalog_message', 'Book successfully cataloged!');
        }


}
