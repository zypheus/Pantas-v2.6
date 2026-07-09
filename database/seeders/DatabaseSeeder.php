<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MarcFrameworkSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MarcFrameworkSeeder::class,
            ProspectusSeeder::class,
            EmployeeSampleSeeder::class,
            StudentSampleSeeder::class,
            BookSampleSeeder::class,
            RoomSampleSeeder::class,
            FineSettingSeeder::class,
        ]);

        $adminPassword = Hash::make('password', [
            'rounds' => 12,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@test.local'],
            [
                'fname' => 'PANTAS',
                'lname' => 'Admin',
                'password' => $adminPassword,
                'role' => 'admin',
                'student_id' => null,
            ]
        );

        $mobileStudent = Student::query()
            ->where('id_number', '24-10003')
            ->first();

        if ($mobileStudent) {
            User::updateOrCreate(
                ['email' => 'mobile.student@test.local'],
                [
                    'fname' => $mobileStudent->firstname,
                    'lname' => $mobileStudent->lastname,
                    'password' => Hash::make('password', [
                        'rounds' => 12,
                    ]),
                    'role' => 'student',
                    'student_id' => $mobileStudent->id,
                ]
            );
        }

        $this->command?->info('Database seeded: MARC framework, programs, students, books, rooms, admin user, and mobile student user.');
    }
}
