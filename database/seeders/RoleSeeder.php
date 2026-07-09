<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Auth\ModuleAccessService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ModuleAccessService::STAFF_ROLES as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
