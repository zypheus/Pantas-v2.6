<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class LinkMobileStudentUser extends Command
{
    protected $signature = 'mobile:link-student-user
        {email : User email address for the mobile login account}
        {--student-id= : Internal students.id value}
        {--id-number= : Student id_number value}
        {--create : Create the user when the email does not exist}
        {--password= : Password to use when creating the user}
        {--force-role : Change an existing non-staff, non-admin user role to student}';

    protected $description = 'Link a mobile student user account to an existing student record.';

    public function handle(): int
    {
        $email = strtolower((string) $this->argument('email'));
        $student = $this->findStudent();

        if (! $student) {
            $this->error('Student not found. Use --student-id or --id-number with an existing student.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user && ! $this->option('create')) {
            $this->error('User not found. Pass --create and --password to create a mobile student account.');

            return self::FAILURE;
        }

        if (! $user) {
            $password = (string) $this->option('password');

            if ($password === '') {
                $this->error('The --password option is required when using --create.');

                return self::FAILURE;
            }

            $user = User::query()->create([
                'fname' => $student->firstname,
                'lname' => $student->lastname,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'student',
                'student_id' => $student->id,
            ]);

            $this->info("Created student mobile user #{$user->id} and linked it to student #{$student->id}.");

            return self::SUCCESS;
        }

        if (in_array($user->role, ['admin', 'staff'], true)) {
            $this->error('Refusing to link admin/staff users to mobile student records.');

            return self::FAILURE;
        }

        if ($user->role !== 'student') {
            if (! $this->option('force-role')) {
                $this->error('Existing user is not a student. Pass --force-role if this account should become a student mobile account.');

                return self::FAILURE;
            }

            $user->role = 'student';
        }

        $user->student_id = $student->id;
        $user->save();

        $this->info("Linked user #{$user->id} ({$user->email}) to student #{$student->id} ({$student->id_number}).");

        return self::SUCCESS;
    }

    private function findStudent(): ?Student
    {
        $studentId = $this->option('student-id');
        $idNumber = $this->option('id-number');

        if ($studentId && $idNumber) {
            $this->warn('Both --student-id and --id-number were provided. Using --student-id.');
        }

        if ($studentId) {
            return Student::query()->find($studentId);
        }

        if ($idNumber) {
            return Student::query()->where('id_number', $idNumber)->first();
        }

        return null;
    }
}
