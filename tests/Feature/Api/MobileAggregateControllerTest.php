<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\BookLog;
use App\Models\ReservationStudent;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileAggregateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_student_can_fetch_mobile_home_aggregate(): void
    {
        $student = $this->student();
        $book = $this->book();

        BookLog::query()->create([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'patron_name' => 'Test Student',
            'status' => 'Checked Out',
            'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
            'renew_count' => 0,
            'timestamp' => Carbon::now('Asia/Manila'),
            'due_date' => Carbon::now('Asia/Manila')->addDays(2)->toDateString(),
            'fine_incurred' => 0,
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/mobile/home')
            ->assertOk()
            ->assertHeader('ETag')
            ->assertJsonPath('data.loan_stats.active_count', 1)
            ->assertJsonCount(1, 'data.new_arrivals')
            ->assertJsonCount(1, 'data.active_loans');
    }

    public function test_student_can_fetch_borrow_overview(): void
    {
        $student = $this->student();
        $book = $this->book();

        BookLog::query()->create([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'patron_name' => 'Test Student',
            'status' => 'Checked Out',
            'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
            'renew_count' => 0,
            'timestamp' => Carbon::now('Asia/Manila'),
            'due_date' => Carbon::now('Asia/Manila')->addDays(7)->toDateString(),
            'fine_incurred' => 0,
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/mobile/borrow-overview')
            ->assertOk()
            ->assertJsonPath('data.limits.current_active_loans', 1)
            ->assertJsonCount(1, 'data.active_loans')
            ->assertJsonCount(1, 'data.history');
    }

    public function test_room_dashboard_defaults_to_first_room(): void
    {
        $student = $this->student();
        $room = Room::query()->create([
            'name' => 'Discussion Room',
            'description' => 'Small group room',
            'capacity' => 6,
        ]);
        $reservation = RoomReservation::query()->create([
            'room_id' => $room->id,
            'student_id' => $student->id,
            'status' => 'pending',
            'date' => '2026-06-19',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patron_email' => 'S-100',
            'number_of_students' => 1,
        ]);
        ReservationStudent::query()->create([
            'reservation_id' => $reservation->id,
            'name' => 'Test Student',
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/mobile/rooms/dashboard?date=2026-06-19')
            ->assertOk()
            ->assertJsonPath('data.availability.room_id', $room->id)
            ->assertJsonCount(1, 'data.rooms')
            ->assertJsonCount(1, 'data.reservations')
            ->assertJsonCount(1, 'data.availability.booked_slots');
    }

    public function test_staff_user_is_rejected_from_student_aggregates(): void
    {
        $user = User::query()->create([
            'fname' => 'Staff',
            'lname' => 'User',
            'email' => 'staff@example.test',
            'password' => 'password',
            'role' => 'staff',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/mobile/home')->assertForbidden();
    }

    private function student(): Student
    {
        return Student::query()->create([
            'id_number' => 'S-100',
            'lastname' => 'Student',
            'firstname' => 'Test',
            'qrcode' => 'S-100',
        ]);
    }

    private function book(): Book
    {
        return Book::query()->create([
            'title_statement' => 'Aggregate Testing',
            'main_author' => 'Area 51',
            'pub_year' => '2026',
            'availability' => 'Available',
            'accession_no' => 'ACC-100',
            'call_number' => 'QA 100',
            'content_type' => 'Book',
            'library_name' => 'Main',
            'section' => 'General',
        ]);
    }
}
