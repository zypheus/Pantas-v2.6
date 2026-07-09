<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SeparatedDomainSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_schema_contains_separate_library_and_attendance_domains(): void
    {
        foreach ([
            'library_students',
            'library_employees',
            'library_books',
            'library_book_logs',
            'library_room_reservations',
            'library_attendance_logs',
            'attendance_students',
            'attendance_employees',
            'attendance_logs',
            'attendance_feedback',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected [{$table}] table to exist.");
        }
    }

    public function test_school_attendance_logs_reference_only_attendance_patrons(): void
    {
        $this->assertTrue(Schema::hasColumns('attendance_logs', [
            'student_id',
            'employee_id',
            'section',
            'scanned_at',
        ]));

        $foreignTables = $this->foreignTablesFor('attendance_logs');

        $this->assertContains('attendance_students', $foreignTables);
        $this->assertContains('attendance_employees', $foreignTables);
        $this->assertNotContains('library_students', $foreignTables);
        $this->assertNotContains('library_employees', $foreignTables);
    }

    public function test_library_attendance_logs_reference_only_library_patrons(): void
    {
        $this->assertTrue(Schema::hasColumns('library_attendance_logs', [
            'student_id',
            'employee_id',
            'section',
            'scanned_at',
        ]));

        $foreignTables = $this->foreignTablesFor('library_attendance_logs');

        $this->assertContains('library_students', $foreignTables);
        $this->assertContains('library_employees', $foreignTables);
        $this->assertNotContains('attendance_students', $foreignTables);
        $this->assertNotContains('attendance_employees', $foreignTables);
    }

    /** @return list<string> */
    private function foreignTablesFor(string $table): array
    {
        return collect(DB::select("PRAGMA foreign_key_list({$table})"))
            ->pluck('table')
            ->values()
            ->all();
    }
}
