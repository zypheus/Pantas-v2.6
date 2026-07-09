<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LibraryRole;
use Illuminate\Database\Seeder;

class EmployeeSampleSeeder extends Seeder
{
    public function run(): void
    {
        $employeeRole = LibraryRole::query()->firstOrCreate([
            'description' => 'Faculty/Staff',
        ]);

        $samples = [
            [
                'employee_id' => 'FAC-2024-001',
                'firstname' => 'Maria',
                'lastname' => 'Reyes',
                'middle_initial' => 'L',
                'designation' => 'Instructor I',
                'program' => 'BSCS',
                'year_start_work' => '2019',
                'birth_date' => '1990-05-12',
                'mobile_number' => '09171234001',
                'address' => 'Koronadal City, South Cotabato',
                'emergency_contact_name' => 'Juan Reyes',
                'emergency_contact_relationship' => 'Spouse',
                'emergency_contact_number' => '09181234001',
                'qrcode' => 'E-00000001',
            ],
            [
                'employee_id' => 'FAC-2024-002',
                'firstname' => 'Pedro',
                'lastname' => 'Garcia',
                'middle_initial' => 'S',
                'designation' => 'College Librarian',
                'program' => 'BEED',
                'year_start_work' => '2015',
                'birth_date' => '1985-11-03',
                'mobile_number' => '09171234002',
                'address' => 'General Santos City',
                'emergency_contact_name' => 'Ana Garcia',
                'emergency_contact_relationship' => 'Sister',
                'emergency_contact_number' => '09181234002',
                'qrcode' => 'E-00000002',
            ],
            [
                'employee_id' => 'STAFF-2024-003',
                'firstname' => 'Liza',
                'lastname' => 'Mendoza',
                'designation' => 'Library Staff',
                'program' => 'BSBA',
                'year_start_work' => '2022',
                'birth_date' => '1998-02-20',
                'mobile_number' => '09171234003',
                'address' => 'Tupi, South Cotabato',
                'emergency_contact_name' => 'Rosa Mendoza',
                'emergency_contact_relationship' => 'Mother',
                'emergency_contact_number' => '09181234003',
                'qrcode' => 'E-00000003',
            ],
        ];

        foreach ($samples as $row) {
            $programCode = $row['program'];
            $row['role_id'] = $employeeRole->id;
            $row['department'] = $programCode;
            $row['position'] = $row['designation'];

            Employee::updateOrCreate(
                ['employee_id' => $row['employee_id']],
                $row
            );
        }

        $this->command?->info('Sample faculty & staff seeded (3 records).');
    }
}
