<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\BookController;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookLog;
use App\Models\FineSetting;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AggregateController extends Controller
{
    public function home(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if ($student instanceof JsonResponse) {
            return $student;
        }

        $activeLoans = $this->activeLoans($student);
        $newArrivals = $this->newArrivals();

        return $this->etagResponse($request, [
            'message' => 'Mobile home data retrieved.',
            'data' => [
                'new_arrivals' => $newArrivals,
                'active_loans' => $activeLoans,
                'loan_stats' => $this->loanStats($activeLoans),
            ],
        ]);
    }

    public function borrowOverview(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if ($student instanceof JsonResponse) {
            return $student;
        }

        $history = BookLog::query()
            ->with('book')
            ->where('student_id', $student->id)
            ->latest('timestamp')
            ->paginate(10)
            ->withQueryString();

        return $this->etagResponse($request, [
            'message' => 'Borrow overview retrieved.',
            'data' => [
                'active_loans' => $this->activeLoans($student),
                'history' => $history->getCollection()->map(fn (BookLog $log) => $this->formatLoan($log))->values(),
                'history_meta' => $this->paginationMeta($history),
                'limits' => $this->borrowLimits($student),
            ],
        ]);
    }

    public function roomsDashboard(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if ($student instanceof JsonResponse) {
            return $student;
        }

        $validated = $request->validate([
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'date' => ['nullable', 'date'],
        ]);

        $rooms = Room::query()
            ->orderBy('name')
            ->get();
        $selectedRoom = $rooms->firstWhere('id', (int) ($validated['room_id'] ?? 0)) ?? $rooms->first();
        $date = Carbon::parse($validated['date'] ?? Carbon::now('Asia/Manila'))->toDateString();

        $reservations = RoomReservation::query()
            ->with(['room', 'students'])
            ->where('student_id', $student->id)
            ->latest('date')
            ->latest('start_time')
            ->get()
            ->map(fn (RoomReservation $reservation) => $this->formatReservation($reservation))
            ->values();

        $bookedSlots = collect();

        if ($selectedRoom instanceof Room) {
            $bookedSlots = RoomReservation::query()
                ->where('room_id', $selectedRoom->id)
                ->whereDate('date', $date)
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('start_time')
                ->get()
                ->map(fn (RoomReservation $reservation) => [
                    'reservation_id' => $reservation->id,
                    'start_time' => $this->formatTime((string) $reservation->start_time),
                    'end_time' => $this->formatTime((string) $reservation->end_time),
                    'status' => $reservation->status,
                ])
                ->values();
        }

        return $this->etagResponse($request, [
            'message' => 'Room dashboard retrieved.',
            'data' => [
                'current_user' => [
                    'user' => $this->formatUser($student),
                    'student' => $this->formatStudent($student),
                ],
                'rooms' => $rooms->map(fn (Room $room) => [
                    'id' => $room->id,
                    'name' => $room->name,
                    'description' => $room->description,
                    'capacity' => $room->capacity,
                ])->values(),
                'reservations' => $reservations,
                'availability' => [
                    'room_id' => $selectedRoom?->id,
                    'date' => $date,
                    'booked_slots' => $bookedSlots,
                ],
            ],
        ]);
    }

    private function resolveStudent(Request $request): Student|JsonResponse
    {
        $tokenable = $request->user();

        if ($tokenable instanceof Student) {
            return $tokenable;
        }

        if ($tokenable instanceof User) {
            if (in_array($tokenable->role, ['admin', 'staff'], true)) {
                return response()->json([
                    'message' => 'This account is not allowed to use the mobile app.',
                    'data' => null,
                ], 403);
            }

            $tokenable->loadMissing('student');

            if ($tokenable->student) {
                return $tokenable->student;
            }
        }

        return response()->json([
            'message' => 'No student profile is linked to this account.',
            'data' => null,
        ], 409);
    }

    private function newArrivals(): array
    {
        $grouped = Book::query()
            ->whereNull('archived_at')
            ->select(
                'title_statement',
                'main_author',
                'pub_year',
                DB::raw('COUNT(*) AS copies'),
                DB::raw('MIN(id) AS sample_id'),
                DB::raw("MAX(CASE WHEN availability = 'Available' THEN 1 ELSE 0 END) AS is_available"),
                DB::raw('MAX(created_at) AS newest_copy_at')
            )
            ->groupBy('title_statement', 'main_author', 'pub_year');

        return DB::query()
            ->fromSub($grouped, 'grouped')
            ->join('books', 'books.id', '=', 'grouped.sample_id')
            ->select(
                'grouped.title_statement',
                'grouped.main_author',
                'grouped.pub_year',
                'grouped.copies',
                'grouped.sample_id as id',
                'grouped.is_available',
                'books.call_number',
                'books.cover_image',
                'books.content_type',
                'books.library_name',
                'books.course',
                'books.section'
            )
            ->orderByDesc('grouped.newest_copy_at')
            ->limit(10)
            ->get()
            ->map(fn (object $book) => $this->formatBookSearchRow($book))
            ->values()
            ->all();
    }

    private function activeLoans(Student $student): array
    {
        return $this->activeLoanQuery($student)
            ->with('book')
            ->orderBy('due_date')
            ->get()
            ->map(fn (BookLog $log) => $this->formatLoan($log))
            ->values()
            ->all();
    }

    private function activeLoanQuery(Student $student)
    {
        $latestIds = DB::table('book_logs')
            ->selectRaw('MAX(id) as id')
            ->groupBy('book_id');

        return BookLog::query()
            ->whereIn('id', $latestIds)
            ->where('student_id', $student->id)
            ->where('status', 'Checked Out');
    }

    private function loanStats(array $activeLoans): array
    {
        $today = Carbon::now('Asia/Manila')->startOfDay();

        return [
            'active_count' => count($activeLoans),
            'due_soon_count' => collect($activeLoans)->filter(function (array $loan) use ($today): bool {
                if ($loan['is_overdue'] || empty($loan['due_at'])) {
                    return false;
                }

                $dueDate = Carbon::parse($loan['due_at'], 'Asia/Manila')->startOfDay();

                return $dueDate->gte($today) && $today->diffInDays($dueDate) <= 3;
            })->count(),
            'overdue_count' => collect($activeLoans)->where('is_overdue', true)->count(),
        ];
    }

    private function borrowLimits(Student $student): array
    {
        $maxLoans = BookController::MAX_CONCURRENT_BOOK_LOANS_PER_STUDENT;
        $currentLoans = BookLog::countActiveLoansForStudent((int) $student->id);
        $fineSetting = FineSetting::currentOrDefault();

        return [
            'max_active_loans' => $maxLoans,
            'current_active_loans' => $currentLoans,
            'remaining_loans' => max(0, $maxLoans - $currentLoans),
            'has_overdue' => $this->hasOverdueLoans($student),
            'can_borrow' => $currentLoans < $maxLoans && ! $this->hasOverdueLoans($student),
            'reborrow_cooldown_days' => BookController::REBORROW_COOLDOWN_DAYS,
            'fine_settings_configured' => FineSetting::current() !== null,
            'loan_duration_days' => $fineSetting->loan_duration_days,
            'grace_period_days' => $fineSetting->grace_period_days,
        ];
    }

    private function hasOverdueLoans(Student $student): bool
    {
        return $this->activeLoanQuery($student)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::now('Asia/Manila')->toDateString())
            ->exists();
    }

    private function formatBookSearchRow(object $book): array
    {
        return [
            'id' => (int) $book->id,
            'type' => 'book',
            'title' => $book->title_statement,
            'author' => $book->main_author,
            'publication_year' => $book->pub_year,
            'cover_url' => filled($book->cover_image) ? asset('storage/'.$book->cover_image) : asset('images/defaultBook.png'),
            'availability' => (int) $book->is_available === 1 ? 'Available' : 'Unavailable',
            'copies' => (int) $book->copies,
            'call_number' => $book->call_number,
            'content_type' => $book->content_type,
            'library_name' => $book->library_name,
            'course' => $book->course,
            'section' => $book->section,
        ];
    }

    private function formatLoan(BookLog $log): array
    {
        $book = $log->book;

        return [
            'id' => $log->id,
            'book_id' => $log->book_id,
            'title' => $book?->title_statement,
            'author' => $book?->main_author,
            'call_number' => $book?->call_number,
            'accession_no' => $book?->accession_no,
            'barcode' => $book?->barcode,
            'borrowed_at' => $log->timestamp?->timezone('Asia/Manila')->toDateTimeString(),
            'due_at' => $log->due_date?->format('Y-m-d'),
            'returned_at' => $log->returned_date?->timezone('Asia/Manila')->toDateTimeString(),
            'status' => $log->status,
            'circulation_type' => $log->circulation_type,
            'is_overdue' => (bool) $log->is_overdue,
            'days_overdue' => (int) $log->days_overdue,
            'fine' => (float) $log->total_fine,
            'renew_count' => (int) $log->renew_count,
        ];
    }

    private function formatReservation(RoomReservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'room' => [
                'id' => $reservation->room?->id,
                'name' => $reservation->room?->name,
                'capacity' => $reservation->room?->capacity,
            ],
            'date' => Carbon::parse($reservation->date)->toDateString(),
            'start_time' => $this->formatTime((string) $reservation->start_time),
            'end_time' => $this->formatTime((string) $reservation->end_time),
            'status' => $reservation->status,
            'patron_email' => $reservation->patron_email,
            'number_of_students' => $reservation->number_of_students,
            'student_names' => $reservation->students->pluck('name')->values(),
            'notes' => $reservation->notes,
            'approved_at' => $reservation->approved_at ? Carbon::parse($reservation->approved_at)->toDateTimeString() : null,
            'created_at' => $reservation->created_at?->toDateTimeString(),
        ];
    }

    private function formatUser(Student $student): array
    {
        return [
            'id' => $student->id,
            'name' => trim((string) $student->firstname.' '.(string) $student->lastname),
            'fname' => $student->firstname,
            'lname' => $student->lastname,
            'email' => null,
            'role' => 'student',
        ];
    }

    private function formatStudent(Student $student): array
    {
        return [
            'id' => $student->id,
            'id_number' => $student->id_number,
            'lastname' => $student->lastname,
            'firstname' => $student->firstname,
            'middle_initial' => $student->middle_initial,
            'course' => $student->course,
            'year' => $student->year,
            'mobile_number' => $student->mobile_number,
            'address' => $student->address,
        ];
    }

    private function formatTime(string $time): string
    {
        return Carbon::parse($time, 'Asia/Manila')->format('H:i:s');
    }

    private function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    private function etagResponse(Request $request, array $payload): JsonResponse
    {
        $etag = '"'.sha1(json_encode($payload, JSON_THROW_ON_ERROR)).'"';

        if ($request->headers->get('If-None-Match') === $etag) {
            return response()->json(null, 304)->header('ETag', $etag);
        }

        return response()->json($payload)->header('ETag', $etag);
    }
}
