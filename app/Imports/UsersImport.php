<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'lname'    => $row['lname'],
            'fname'    => $row['fname'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']), // plain password from Excel
            'role'     => $row['role'],
        ]);
    }
}
