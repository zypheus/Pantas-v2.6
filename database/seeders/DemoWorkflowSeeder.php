<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdminActivity;
use App\Models\AttendanceEmployee;
use App\Models\AttendanceFeedback;
use App\Models\AttendanceLog;
use App\Models\AttendancePendingEmployee;
use App\Models\AttendancePendingStudent;
use App\Models\AttendanceStudent;
use App\Models\Book;
use App\Models\BookLog;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\LibraryAttendanceFeedback;
use App\Models\LibraryAttendanceLog;
use App\Models\LibraryRole;
use App\Models\PendingEmployee;
use App\Models\PendingStudent;
use App\Models\Program;
use App\Models\ReservationLog;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Student;
use App\Models\StudentEditRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class DemoWorkflowSeeder extends Seeder
{
    private const TIMEZONE = 'Asia/Manila';

    public function run(): void
    {
        $this->ensureBaseDataExists();

        DB::transaction(function (): void {
            $staff = $this->seedStaffAccounts();
            $libraryPatrons = $this->seedLibraryPatrons();
            $attendancePatrons = $this->seedAttendancePatrons();

            $this->clearDemoWorkflowRows($libraryPatrons, $attendancePatrons);
            $this->seedLibraryPendingPatrons();
            $this->seedAttendancePendingPatrons();
            $this->seedCirculation($libraryPatrons);
            $this->seedLibraryVisits($libraryPatrons);
            $this->seedAttendanceLogs($attendancePatrons);
            $this->seedFeedback($libraryPatrons, $attendancePatrons);
            $this->seedRoomReservations($libraryPatrons, $staff);
            $this->seedProfileRequest($libraryPatrons, $staff);
            $this->seedAdminActivities($staff);
        });

        $this->command?->info('Demo workflow data seeded for dashboards, sidebars, reports, pending approvals, circulation, rooms, feedback, and audit activity.');
    }

    private function ensureBaseDataExists(): void
    {
        $this->call(RoleSeeder::class);

        if (! User::query()->where('email', 'super_admin@pantas.test')->exists()) {
            $this->call(SuperAdminSeeder::class);
        }

        if (Program::query()->count() === 0) {
            $this->call(ProspectusSeeder::class);
        }

        if (Employee::query()->count() === 0) {
            $this->call(EmployeeSampleSeeder::class);
        }

        if (Student::query()->count() === 0) {
            $this->call(StudentSampleSeeder::class);
        }

        if (Book::query()->count() === 0) {
            $this->call(BookSampleSeeder::class);
        }

        if (Room::query()->count() === 0) {
            $this->call(RoomSampleSeeder::class);
        }

        $this->call(FineSettingSeeder::class);
    }

    /** @return array<string, User> */
    private function seedStaffAccounts(): array
    {
        $accounts = [
            'library_admin' => ['email' => 'library.admin@pantas.test', 'fname' => 'Lara', 'lname' => 'Library Admin'],
            'library_staff' => ['email' => 'library.staff@pantas.test', 'fname' => 'Leo', 'lname' => 'Library Staff'],
            'attendance_admin' => ['email' => 'attendance.admin@pantas.test', 'fname' => 'Amara', 'lname' => 'Attendance Admin'],
            'attendance_staff' => ['email' => 'attendance.staff@pantas.test', 'fname' => 'Alden', 'lname' => 'Attendance Staff'],
            'inactive_library_staff' => ['email' => 'inactive.staff@pantas.test', 'fname' => 'Inactive', 'lname' => 'Library Staff'],
        ];

        $users = [];

        foreach ($accounts as $key => $data) {
            $role = $key === 'inactive_library_staff' ? 'library_staff' : $key;
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'fname' => $data['fname'],
                    'lname' => $data['lname'],
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'student_id' => null,
                    'is_active' => $key !== 'inactive_library_staff',
                ],
            );

            $user->syncRoles([$role]);
            $users[$key] = $user;
        }

        $users['super_admin'] = User::query()->where('email', 'super_admin@pantas.test')->firstOrFail();

        return $users;
    }

    /** @return array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} */
    private function seedLibraryPatrons(): array
    {
        $students = Student::query()
            ->whereIn('qrcode', ['S-00000001', 'S-00000002', 'S-00000003', 'S-00000004'])
            ->orderBy('qrcode')
            ->get();

        $employees = Employee::query()
            ->whereIn('qrcode', ['E-00000001', 'E-00000002'])
            ->orderBy('qrcode')
            ->get();

        return ['students' => $students, 'employees' => $employees];
    }

    /** @return array{students: \Illuminate\Support\Collection<int, AttendanceStudent>, employees: \Illuminate\Support\Collection<int, AttendanceEmployee>} */
    private function seedAttendancePatrons(): array
    {
        $students = collect([
            [
                'student_id' => 'ATT-2024-001',
                'lastname' => 'Villanueva',
                'firstname' => 'Bianca',
                'middle_initial' => 'R',
                'birth_date' => '2004-02-10',
                'blood_type' => 'O+',
                'qrcode' => 'A-STU-0001',
                'course' => 'BSCS',
                'year' => '1st Year',
                'mobile_number' => '09175550101',
                'address' => 'Koronadal City',
                'emergency_person' => 'Ramon Villanueva',
                'emergency_relationship' => 'Father',
                'emergency_number' => '09185550101',
                'emergency_address' => 'Koronadal City',
                'normalized_name' => 'bianca villanueva',
            ],
            [
                'student_id' => 'ATT-2024-002',
                'lastname' => 'Navarro',
                'firstname' => 'Paolo',
                'middle_initial' => 'S',
                'birth_date' => '2003-08-21',
                'blood_type' => 'A+',
                'qrcode' => 'A-STU-0002',
                'course' => 'BSIT',
                'year' => '2nd Year',
                'mobile_number' => '09175550102',
                'address' => 'Tupi',
                'emergency_person' => 'Nora Navarro',
                'emergency_relationship' => 'Mother',
                'emergency_number' => '09185550102',
                'emergency_address' => 'Tupi',
                'normalized_name' => 'paolo navarro',
            ],
            [
                'student_id' => 'ATT-2024-003',
                'lastname' => 'Lopez',
                'firstname' => 'Clarisse',
                'middle_initial' => 'M',
                'birth_date' => '2005-01-14',
                'blood_type' => 'B+',
                'qrcode' => 'A-STU-0003',
                'course' => 'BEED',
                'year' => '1st Year',
                'mobile_number' => '09175550103',
                'address' => 'Surallah',
                'emergency_person' => 'Lito Lopez',
                'emergency_relationship' => 'Guardian',
                'emergency_number' => '09185550103',
                'emergency_address' => 'Surallah',
                'normalized_name' => 'clarisse lopez',
            ],
        ])->map(fn (array $row): AttendanceStudent => AttendanceStudent::query()->updateOrCreate(
            ['student_id' => $row['student_id']],
            $row,
        ))->values();

        $employees = collect([
            [
                'employee_id' => 'ATT-FAC-001',
                'employee_number' => 'ATT-FAC-001',
                'firstname' => 'Rafael',
                'lastname' => 'Soriano',
                'middle_initial' => 'G',
                'department' => 'BSCS',
                'position' => 'Instructor',
                'birth_date' => '1988-06-12',
                'sex' => 'Male',
                'civil_status' => 'Married',
                'blood_type' => 'O+',
                'qrcode' => 'A-EMP-0001',
                'emergency_contact_name' => 'Mila Soriano',
                'emergency_contact_relationship' => 'Spouse',
                'emergency_contact_number' => '09185550201',
                'address' => 'General Santos City',
                'normalized_name' => 'rafael soriano',
            ],
            [
                'employee_id' => 'ATT-STAFF-002',
                'employee_number' => 'ATT-STAFF-002',
                'firstname' => 'Mylene',
                'lastname' => 'Cruz',
                'middle_initial' => 'A',
                'department' => 'Administration',
                'position' => 'Office Staff',
                'birth_date' => '1992-09-17',
                'sex' => 'Female',
                'civil_status' => 'Single',
                'blood_type' => 'AB+',
                'qrcode' => 'A-EMP-0002',
                'emergency_contact_name' => 'Rosa Cruz',
                'emergency_contact_relationship' => 'Mother',
                'emergency_contact_number' => '09185550202',
                'address' => 'Koronadal City',
                'normalized_name' => 'mylene cruz',
            ],
        ])->map(fn (array $row): AttendanceEmployee => AttendanceEmployee::query()->updateOrCreate(
            ['employee_id' => $row['employee_id']],
            $row,
        ))->values();

        return ['students' => $students, 'employees' => $employees];
    }

    /** @param array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} $libraryPatrons
     * @param  array{students: \Illuminate\Support\Collection<int, AttendanceStudent>, employees: \Illuminate\Support\Collection<int, AttendanceEmployee>}  $attendancePatrons
     */
    private function clearDemoWorkflowRows(array $libraryPatrons, array $attendancePatrons): void
    {
        $bookIds = Book::query()
            ->whereIn('accession_no', ['GG-2024-0001', 'GG-2024-0002', 'GG-2024-0003', 'GG-2024-0004'])
            ->pluck('id');

        BookLog::query()->whereIn('book_id', $bookIds)->delete();
        LibraryAttendanceLog::query()
            ->whereIn('student_id', $libraryPatrons['students']->pluck('id'))
            ->orWhereIn('employee_id', $libraryPatrons['employees']->pluck('id'))
            ->delete();
        LibraryAttendanceFeedback::query()
            ->whereIn('student_id', $libraryPatrons['students']->pluck('id'))
            ->orWhereIn('employee_id', $libraryPatrons['employees']->pluck('id'))
            ->delete();
        AttendanceLog::query()
            ->whereIn('student_id', $attendancePatrons['students']->pluck('id'))
            ->orWhereIn('employee_id', $attendancePatrons['employees']->pluck('id'))
            ->delete();
        AttendanceFeedback::query()
            ->whereIn('student_id', $attendancePatrons['students']->pluck('id'))
            ->orWhereIn('employee_id', $attendancePatrons['employees']->pluck('id'))
            ->delete();

        $demoReservations = RoomReservation::query()
            ->where('notes', 'like', 'Demo workflow:%')
            ->pluck('id');
        ReservationLog::query()
            ->whereIn('reservation_id', $demoReservations)
            ->orWhereIn('room_reservation_id', $demoReservations)
            ->delete();
        RoomReservation::query()->whereIn('id', $demoReservations)->delete();

        Feedback::query()->whereIn('email', ['demo.library.feedback@pantas.test'])->delete();
        StudentEditRequest::query()->whereIn('student_id', $libraryPatrons['students']->pluck('id'))->delete();
        AdminActivity::query()->where('type', 'like', 'demo.%')->delete();
    }

    private function seedLibraryPendingPatrons(): void
    {
        PendingStudent::query()->updateOrCreate(
            ['qrcode' => 'LIB-PEND-STU-001'],
            [
                'id_number' => 'LIB-PEND-001',
                'lastname' => 'Mercado',
                'firstname' => 'Althea',
                'middle_initial' => 'P',
                'birthday' => '2004-04-12',
                'course' => 'BSN',
                'year' => '1st Year',
                'mobile_number' => '09176660101',
                'address' => 'Banga',
                'emergency_person' => 'Rico Mercado',
                'emergency_relationship' => 'Father',
                'emergency_number' => '09186660101',
                'emergency_address' => 'Banga',
            ],
        );

        $role = LibraryRole::query()->firstOrCreate(['description' => 'Faculty/Staff']);
        PendingEmployee::query()->updateOrCreate(
            ['qrcode' => 'LIB-PEND-EMP-001'],
            [
                'employee_id' => 'LIB-PEND-FAC-001',
                'employee_number' => 'LIB-PEND-FAC-001',
                'firstname' => 'Nadine',
                'lastname' => 'Sanchez',
                'middle_initial' => 'T',
                'department' => 'BSBA',
                'position' => 'Instructor',
                'designation' => 'Instructor I',
                'program' => 'BSBA',
                'birth_date' => '1991-10-10',
                'mobile_number' => '09176660201',
                'role_id' => $role->id,
                'address' => 'Polomolok',
                'emergency_contact_name' => 'Marco Sanchez',
                'emergency_contact_relationship' => 'Brother',
                'emergency_contact_number' => '09186660201',
                'emergency_address' => 'Polomolok',
            ],
        );
    }

    private function seedAttendancePendingPatrons(): void
    {
        AttendancePendingStudent::query()->updateOrCreate(
            ['student_id' => 'ATT-PEND-001'],
            [
                'lastname' => 'Francisco',
                'firstname' => 'Janelle',
                'middle_initial' => 'V',
                'birth_date' => '2004-12-01',
                'blood_type' => 'O+',
                'qrcode' => 'A-PEND-STU-001',
                'course' => 'BSBA',
                'year' => '2nd Year',
                'mobile_number' => '09175550301',
                'address' => 'Tacurong City',
                'emergency_person' => 'Joel Francisco',
                'emergency_relationship' => 'Father',
                'emergency_number' => '09185550301',
                'emergency_address' => 'Tacurong City',
            ],
        );

        AttendancePendingEmployee::query()->updateOrCreate(
            ['employee_id' => 'ATT-PEND-EMP-001'],
            [
                'employee_number' => 'ATT-PEND-EMP-001',
                'firstname' => 'Dennis',
                'lastname' => 'Aguilar',
                'middle_initial' => 'C',
                'department' => 'Registrar',
                'position' => 'Records Assistant',
                'birth_date' => '1990-03-19',
                'sex' => 'Male',
                'civil_status' => 'Married',
                'blood_type' => 'B+',
                'qrcode' => 'A-PEND-EMP-001',
                'emergency_contact_name' => 'Diana Aguilar',
                'emergency_contact_relationship' => 'Spouse',
                'emergency_contact_number' => '09185550302',
                'address' => 'Koronadal City',
            ],
        );
    }

    /** @param array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} $patrons */
    private function seedCirculation(array $patrons): void
    {
        $now = CarbonImmutable::now(self::TIMEZONE);
        $students = $patrons['students']->values();
        $books = Book::query()
            ->whereIn('accession_no', ['GG-2024-0001', 'GG-2024-0002', 'GG-2024-0003', 'GG-2024-0004'])
            ->orderBy('accession_no')
            ->get();

        if ($students->count() < 3 || $books->count() < 4) {
            return;
        }

        $rows = [
            [
                'book' => $books[0],
                'student' => $students[0],
                'status' => 'Checked Out',
                'timestamp' => $now->subHours(3),
                'due_date' => $now->addDays(7)->toDateString(),
                'returned_date' => null,
                'fine_incurred' => 0,
                'fine_balance' => 0,
            ],
            [
                'book' => $books[1],
                'student' => $students[1],
                'status' => 'Checked In',
                'timestamp' => $now->subDays(4),
                'due_date' => $now->subDay()->toDateString(),
                'returned_date' => $now->subHours(2),
                'fine_incurred' => 0,
                'fine_balance' => 0,
            ],
            [
                'book' => $books[2],
                'student' => $students[2],
                'status' => 'Checked Out',
                'timestamp' => $now->subDays(12),
                'due_date' => $now->subDays(4)->toDateString(),
                'returned_date' => null,
                'fine_incurred' => 60,
                'fine_balance' => 60,
            ],
            [
                'book' => $books[3],
                'student' => $students[0],
                'status' => 'Checked Out',
                'timestamp' => $now->subDay(),
                'due_date' => $now->toDateString(),
                'returned_date' => null,
                'fine_incurred' => 0,
                'fine_balance' => 0,
            ],
        ];

        foreach ($rows as $row) {
            /** @var Book $book */
            $book = $row['book'];
            /** @var Student $student */
            $student = $row['student'];

            BookLog::query()->create([
                'book_id' => $book->id,
                'student_id' => $student->id,
                'patron_name' => "{$student->lastname}, {$student->firstname}",
                'status' => $row['status'],
                'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
                'renew_count' => $row['status'] === 'Checked Out' ? 0 : 1,
                'timestamp' => $row['timestamp'],
                'due_date' => $row['due_date'],
                'returned_date' => $row['returned_date'],
                'fine_incurred' => $row['fine_incurred'],
                'fine_original' => $row['fine_incurred'],
                'fine_balance' => $row['fine_balance'],
                'fine_paid_total' => 0,
                'fine_waived_total' => 0,
            ]);

            $book->update(['availability' => $row['status'] === 'Checked Out' ? 'Borrowed' : 'Available']);
        }
    }

    /** @param array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} $patrons */
    private function seedLibraryVisits(array $patrons): void
    {
        $now = CarbonImmutable::now(self::TIMEZONE);
        $students = $patrons['students']->values();
        $employees = $patrons['employees']->values();

        foreach (range(6, 0) as $dayOffset) {
            foreach ($students as $index => $student) {
                $scanIn = $now->subDays($dayOffset)->setTime(8 + $index, 10);
                LibraryAttendanceLog::query()->create([
                    'student_id' => $student->id,
                    'status' => 'IN',
                    'section' => 'Reading Area',
                    'scanned_at' => $scanIn,
                ]);

                if (! ($dayOffset === 0 && $index === 0)) {
                    LibraryAttendanceLog::query()->create([
                        'student_id' => $student->id,
                        'status' => 'OUT',
                        'section' => 'Reading Area',
                        'scanned_at' => $scanIn->addHours(2),
                    ]);
                }
            }
        }

        foreach ($employees as $index => $employee) {
            LibraryAttendanceLog::query()->create([
                'employee_id' => $employee->id,
                'status' => $index === 0 ? 'IN' : 'OUT',
                'section' => 'Faculty Reading Area',
                'scanned_at' => $now->setTime(10 + $index, 30),
            ]);
        }
    }

    /** @param array{students: \Illuminate\Support\Collection<int, AttendanceStudent>, employees: \Illuminate\Support\Collection<int, AttendanceEmployee>} $patrons */
    private function seedAttendanceLogs(array $patrons): void
    {
        $now = CarbonImmutable::now(self::TIMEZONE);

        foreach (range(6, 0) as $dayOffset) {
            foreach ($patrons['students']->values() as $index => $student) {
                $scanIn = $now->subDays($dayOffset)->setTime(7 + $index, 45);
                AttendanceLog::query()->create([
                    'student_id' => $student->id,
                    'status' => 'IN',
                    'section' => $student->course,
                    'scanned_at' => $scanIn,
                ]);

                if (! ($dayOffset === 0 && $index === 0)) {
                    AttendanceLog::query()->create([
                        'student_id' => $student->id,
                        'status' => 'OUT',
                        'section' => $student->course,
                        'scanned_at' => $scanIn->addHours(8),
                    ]);
                }
            }
        }

        foreach ($patrons['employees']->values() as $index => $employee) {
            AttendanceLog::query()->create([
                'employee_id' => $employee->id,
                'status' => $index === 0 ? 'IN' : 'OUT',
                'section' => $employee->department,
                'scanned_at' => $now->setTime(8 + $index, 5),
            ]);
        }
    }

    /** @param array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} $libraryPatrons
     * @param  array{students: \Illuminate\Support\Collection<int, AttendanceStudent>, employees: \Illuminate\Support\Collection<int, AttendanceEmployee>}  $attendancePatrons
     */
    private function seedFeedback(array $libraryPatrons, array $attendancePatrons): void
    {
        Feedback::query()->create([
            'name' => 'Demo Library Patron',
            'email' => 'demo.library.feedback@pantas.test',
            'comments' => 'The updated Library dashboard and OPAC workflow are ready for manual testing.',
        ]);

        foreach ($libraryPatrons['students']->take(2) as $student) {
            LibraryAttendanceFeedback::query()->create([
                'student_id' => $student->id,
                'rating' => 'satisfied',
                'declined' => false,
            ]);
        }

        foreach ($attendancePatrons['students']->take(2) as $student) {
            AttendanceFeedback::query()->create([
                'student_id' => $student->id,
                'rating' => 'satisfied',
                'declined' => false,
            ]);
        }
    }

    /** @param array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} $patrons
     * @param  array<string, User>  $staff
     */
    private function seedRoomReservations(array $patrons, array $staff): void
    {
        $room = Room::query()->where('name', 'Discussion Room A')->first();
        $student = $patrons['students']->first();

        if (! $room || ! $student) {
            return;
        }

        $reservations = [
            ['status' => 'pending', 'date' => today()->addDay(), 'start_time' => '09:00', 'end_time' => '10:00'],
            ['status' => 'approved', 'date' => today()->addDays(2), 'start_time' => '13:00', 'end_time' => '14:30'],
            ['status' => 'rejected', 'date' => today()->addDays(3), 'start_time' => '15:00', 'end_time' => '16:00'],
        ];

        foreach ($reservations as $reservation) {
            $model = RoomReservation::query()->create([
                'room_id' => $room->id,
                'user_id' => null,
                'student_id' => $student->id,
                'status' => $reservation['status'],
                'date' => $reservation['date'],
                'start_time' => $reservation['start_time'],
                'end_time' => $reservation['end_time'],
                'patron_email' => 'demo.room@pantas.test',
                'number_of_students' => 4,
                'notes' => 'Demo workflow: '.$reservation['status'].' room reservation',
                'approved_by' => $reservation['status'] !== 'pending' ? $staff['library_admin']->id : null,
                'approved_at' => $reservation['status'] !== 'pending' ? now() : null,
            ]);

            ReservationLog::query()->create([
                'reservation_id' => $model->id,
                'user_id' => $staff['library_admin']->id,
                'action' => $reservation['status'],
                'meta' => ['demo' => true, 'status' => $reservation['status']],
            ]);
        }
    }

    /** @param array{students: \Illuminate\Support\Collection<int, Student>, employees: \Illuminate\Support\Collection<int, Employee>} $patrons
     * @param  array<string, User>  $staff
     */
    private function seedProfileRequest(array $patrons, array $staff): void
    {
        $student = $patrons['students']->first();

        if (! $student) {
            return;
        }

        StudentEditRequest::query()->create([
            'student_id' => $student->id,
            'lastname' => $student->lastname,
            'firstname' => $student->firstname,
            'middle_initial' => $student->middle_initial,
            'birthday' => $student->birthday,
            'year' => $student->year,
            'mobile_number' => '09179990000',
            'address' => 'Demo updated address',
            'emergency_person' => $student->emergency_person,
            'emergency_relationship' => $student->emergency_relationship,
            'emergency_number' => $student->emergency_number,
            'emergency_address' => $student->emergency_address,
            'status' => 'pending',
            'admin_note' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    /** @param array<string, User> $staff */
    private function seedAdminActivities(array $staff): void
    {
        $activities = [
            ['module' => 'super-admin', 'type' => 'demo.staff.created', 'title' => 'Demo staff accounts refreshed', 'body' => 'Seeder created staff accounts for every module role.', 'icon' => 'bi-person-plus'],
            ['module' => 'library', 'type' => 'demo.catalog.updated', 'title' => 'Demo catalog seeded', 'body' => 'Sample books and circulation records are available for manual testing.', 'icon' => 'bi-book'],
            ['module' => 'library', 'type' => 'demo.room.approved', 'title' => 'Demo room reservation activity', 'body' => 'Pending, approved, and rejected room reservations were created.', 'icon' => 'bi-calendar-check'],
            ['module' => 'library', 'type' => 'demo.fine.created', 'title' => 'Demo outstanding fine available', 'body' => 'An overdue circulation record with an outstanding fine was seeded.', 'icon' => 'bi-exclamation-circle'],
            ['module' => 'attendance', 'type' => 'demo.patron.pending', 'title' => 'Demo Attendance pending registrations', 'body' => 'Pending Attendance student and employee records are ready for approval testing.', 'icon' => 'bi-person-plus'],
            ['module' => 'attendance', 'type' => 'demo.scans.created', 'title' => 'Demo Attendance scans seeded', 'body' => 'Seven days of school Attendance logs were seeded for dashboard charts.', 'icon' => 'bi-upc-scan'],
        ];

        foreach ($activities as $activity) {
            AdminActivity::query()->create([
                'user_id' => $staff['super_admin']->id,
                'module' => $activity['module'],
                'type' => $activity['type'],
                'title' => $activity['title'],
                'body' => $activity['body'],
                'action_url' => null,
                'icon' => $activity['icon'],
            ]);
        }
    }
}
