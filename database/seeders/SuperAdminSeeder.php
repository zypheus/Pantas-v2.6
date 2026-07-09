<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'super_admin@pantas.test'],
            [
                'fname' => 'PANTAS',
                'lname' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'student_id' => null,
                'is_active' => true,
            ],
        );

        $user->syncRoles(['super_admin']);
    }
}
