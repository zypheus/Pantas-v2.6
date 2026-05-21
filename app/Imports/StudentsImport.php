<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentsImport implements ToModel
{
    public function model(array $row)
    {
        return new Student([
            'id_number' => $row[0] ?? null,
            'lastname' => $row[1] ?? '',
            'firstname' => $row[2] ?? '',
            'middle_initial' => $row[3] ?? null,
            'birthday' => $row[4] ?? null,
            'qrcode' => $row[5] ?? null,
            'course' => $row[6] ?? null,
            'year' => $row[7] ?? null,
            'mobile_number' => $row[8] ?? null,
            'address' => $row[9] ?? null,
        ]);
    }
}
