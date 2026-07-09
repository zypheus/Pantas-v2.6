<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\AttendanceStudent;
use App\Models\LibraryAttendanceLog;
use App\Models\LibraryBook;
use App\Models\LibraryBookLog;
use App\Models\LibraryRoom;
use App\Models\LibraryRoomReservation;
use App\Models\LibraryStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_log_belongs_to_attendance_student(): void
    {
        $student = AttendanceStudent::query()->create([
            'student_id' => '25-00001',
            'firstname' => 'Ada',
            'lastname' => 'Lovelace',
        ]);

        $log = AttendanceLog::query()->create([
            'student_id' => $student->id,
            'status' => 'IN',
            'scanned_at' => now(),
        ]);

        $this->assertTrue($log->student->is($student));
        $this->assertTrue($student->logs->first()->is($log));
    }

    public function test_library_circulation_uses_library_models(): void
    {
        $student = LibraryStudent::query()->create([
            'id_number' => 'LIB-0001',
            'firstname' => 'Grace',
            'lastname' => 'Hopper',
        ]);

        $book = LibraryBook::query()->create([
            'title_statement' => 'Domain Boundaries',
            'availability' => 'Checked Out',
        ]);

        $log = LibraryBookLog::query()->create([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'status' => 'Checked Out',
            'circulation_type' => 'checkout',
        ]);

        $this->assertTrue($log->book->is($book));
        $this->assertTrue($log->student->is($student));
        $this->assertTrue($student->bookLogs->first()->is($log));
    }

    public function test_library_room_and_visit_logs_use_library_patrons(): void
    {
        $student = LibraryStudent::query()->create([
            'id_number' => 'LIB-0002',
            'firstname' => 'Katherine',
            'lastname' => 'Johnson',
        ]);

        $room = LibraryRoom::query()->create([
            'name' => 'Discussion Room 1',
            'capacity' => 6,
        ]);

        $reservation = LibraryRoomReservation::query()->create([
            'room_id' => $room->id,
            'student_id' => $student->id,
            'status' => 'pending',
            'date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $visit = LibraryAttendanceLog::query()->create([
            'student_id' => $student->id,
            'status' => 'IN',
            'scanned_at' => now(),
        ]);

        $this->assertTrue($reservation->room->is($room));
        $this->assertTrue($reservation->student->is($student));
        $this->assertTrue($visit->student->is($student));
    }
}
