<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookLog;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestCirculationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $s1 = Student::updateOrCreate(
                ['id_number' => 'TST-0001'],
                [
                    'lastname' => 'Tester',
                    'firstname' => 'Juan',
                    'middle_initial' => null,
                    'birthday' => null,
                    'mobile_number' => '09171234567',
                    'qrcode' => 'S-TST0001',
                    'course' => 'BSIT',
                    'year' => '1st Year',
                ]
            );

            $s2 = Student::updateOrCreate(
                ['id_number' => 'TST-0002'],
                [
                    'lastname' => 'Tester',
                    'firstname' => 'Maria',
                    'middle_initial' => null,
                    'birthday' => null,
                    'mobile_number' => '09179876543',
                    'qrcode' => 'S-TST0002',
                    'course' => 'BSBA',
                    'year' => '2nd Year',
                ]
            );

            $b1 = Book::updateOrCreate(
                ['barcode' => 'TST-BK-0001'],
                [
                    'rfid' => 'TST-RFID-0001',
                    'title_statement' => 'Test Book 1 (Overdue example)',
                    'main_author' => 'Author One',
                    'publisher' => 'Test Publisher',
                    'pub_year' => '2024',
                    'availability' => 'Available',
                ]
            );

            $b2 = Book::updateOrCreate(
                ['barcode' => 'TST-BK-0002'],
                [
                    'rfid' => 'TST-RFID-0002',
                    'title_statement' => 'Test Book 2 (Renew example)',
                    'main_author' => 'Author Two',
                    'publisher' => 'Test Publisher',
                    'pub_year' => '2023',
                    'availability' => 'Available',
                ]
            );

            $b3 = Book::updateOrCreate(
                ['barcode' => 'TST-BK-0003'],
                [
                    'rfid' => 'TST-RFID-0003',
                    'title_statement' => 'Test Book 3 (Room use example)',
                    'main_author' => 'Author Three',
                    'publisher' => 'Test Publisher',
                    'pub_year' => '2022',
                    'availability' => 'Available',
                ]
            );

            // 1) Checked out & overdue (for SMS overdue targeting)
            BookLog::updateOrCreate(
                [
                    'book_id' => $b1->id,
                    'student_id' => $s1->id,
                    'status' => 'Checked Out',
                ],
                [
                    'patron_name' => "{$s1->lastname}, {$s1->firstname}",
                    'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
                    'renew_count' => 0,
                    'timestamp' => Carbon::now('Asia/Manila')->subDays(12),
                    'due_date' => Carbon::now('Asia/Manila')->subDays(3)->toDateString(),
                    'returned_date' => null,
                    'fine_incurred' => 0,
                ]
            );
            $b1->update(['availability' => 'Borrowed']);

            // 2) Checked out with renewals already used
            BookLog::updateOrCreate(
                [
                    'book_id' => $b2->id,
                    'student_id' => $s1->id,
                    'status' => 'Checked Out',
                ],
                [
                    'patron_name' => "{$s1->lastname}, {$s1->firstname}",
                    'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
                    'renew_count' => 2,
                    'last_renewed_at' => Carbon::now('Asia/Manila')->subDays(1),
                    'timestamp' => Carbon::now('Asia/Manila')->subDays(5),
                    'due_date' => Carbon::now('Asia/Manila')->addDays(5)->toDateString(),
                    'returned_date' => null,
                    'fine_incurred' => 0,
                ]
            );
            $b2->update(['availability' => 'Borrowed']);

            // 3) Room use (no due date) out with another student
            BookLog::updateOrCreate(
                [
                    'book_id' => $b3->id,
                    'student_id' => $s2->id,
                    'status' => 'Checked Out',
                ],
                [
                    'patron_name' => "{$s2->lastname}, {$s2->firstname}",
                    'circulation_type' => BookLog::CIRCULATION_ROOM_USE,
                    'renew_count' => 0,
                    'timestamp' => Carbon::now('Asia/Manila')->subHours(2),
                    'due_date' => null,
                    'returned_date' => null,
                    'fine_incurred' => 0,
                ]
            );
            $b3->update(['availability' => 'Borrowed']);

            // 4) Cooldown example: student 2 returned book 2 recently (blocks re-borrow for 7 days)
            BookLog::updateOrCreate(
                [
                    'book_id' => $b2->id,
                    'student_id' => $s2->id,
                    'status' => 'Checked In',
                    'returned_date' => Carbon::now('Asia/Manila')->subDays(2),
                ],
                [
                    'patron_name' => "{$s2->lastname}, {$s2->firstname}",
                    'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
                    'renew_count' => 1,
                    'timestamp' => Carbon::now('Asia/Manila')->subDays(9),
                    'due_date' => Carbon::now('Asia/Manila')->subDays(4)->toDateString(),
                    'fine_incurred' => 0,
                ]
            );
        });
    }
}

