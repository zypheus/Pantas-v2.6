<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AttendanceStudent;
use App\Models\LibraryAttendanceSetting;
use App\Models\LibraryStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryAttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_library_scanner_records_independent_library_visit_logs(): void
    {
        $student = LibraryStudent::query()->create([
            'id_number' => 'LIB-001',
            'qrcode' => 'LIB-QR-001',
            'firstname' => 'Ada',
            'lastname' => 'Lovelace',
        ]);

        $firstScan = $this->postJson('/library/attendance/scanner', [
            'qrcode' => 'LIB-QR-001',
            'section' => 'Reading Area',
        ]);

        $firstScan->assertOk()
            ->assertJsonPath('type', 'student')
            ->assertJsonPath('patron_id', $student->id)
            ->assertJsonPath('status', 'IN');

        $this->assertDatabaseHas('library_attendance_logs', [
            'student_id' => $student->id,
            'employee_id' => null,
            'status' => 'IN',
            'section' => 'Reading Area',
        ]);
        $this->assertDatabaseCount('attendance_logs', 0);

        $secondScan = $this->postJson('/library/attendance/scanner', [
            'qrcode' => 'LIB-QR-001',
        ]);

        $secondScan->assertOk()->assertJsonPath('status', 'OUT');
        $this->assertDatabaseHas('library_attendance_logs', [
            'student_id' => $student->id,
            'status' => 'OUT',
        ]);
    }

    public function test_library_scanner_does_not_accept_attendance_only_patrons(): void
    {
        AttendanceStudent::query()->create([
            'student_id' => 'ATT-001',
            'qrcode' => 'ATT-QR-001',
            'firstname' => 'Grace',
            'lastname' => 'Hopper',
        ]);

        $response = $this->postJson('/library/attendance/scanner', [
            'qrcode' => 'ATT-QR-001',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('message', 'Library patron not recognized.');
        $this->assertDatabaseCount('library_attendance_logs', 0);
    }

    public function test_library_feedback_setting_controls_feedback_submission(): void
    {
        $student = LibraryStudent::query()->create([
            'id_number' => 'LIB-002',
            'qrcode' => 'LIB-QR-002',
            'firstname' => 'Katherine',
            'lastname' => 'Johnson',
        ]);

        $this->postJson('/library/attendance/feedback', [
            'student_id' => $student->id,
            'rating' => 'excellent',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('library_attendance_feedbacks', [
            'student_id' => $student->id,
            'rating' => 'excellent',
            'declined' => false,
        ]);

        LibraryAttendanceSetting::query()->create([
            'key' => 'logout_feedback_enabled',
            'value' => '0',
        ]);

        $this->postJson('/library/attendance/feedback', [
            'student_id' => $student->id,
            'rating' => 'good',
        ])->assertForbidden();
    }
}
