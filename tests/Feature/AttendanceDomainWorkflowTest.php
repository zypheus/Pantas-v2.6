<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\AttendanceStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceDomainWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_attendance_scan_uses_attendance_student_records(): void
    {
        $student = AttendanceStudent::query()->create([
            'student_id' => '25-00003',
            'firstname' => 'Dorothy',
            'lastname' => 'Vaughan',
            'qrcode' => 'ATT-QR-0003',
        ]);

        $response = $this->postJson('/attendance', [
            'qrcode' => 'ATT-QR-0003',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('type', 'student')
            ->assertJsonPath('student_id', $student->id)
            ->assertJsonPath('status', 'IN');

        $this->assertDatabaseHas('attendance_logs', [
            'student_id' => $student->id,
            'status' => 'IN',
        ]);

        $this->assertTrue(AttendanceLog::query()->firstOrFail()->student->is($student));
    }
}
