<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    private const EMAIL = 'developer.admin@pantas.text';

    public function up(): void
    {
        DB::transaction(function (): void {
            $roleId = DB::table('roles')
                ->where('name', 'developer')
                ->where('guard_name', 'web')
                ->value('id');

            if (! $roleId) {
                $roleId = DB::table('roles')->insertGetId([
                    'name' => 'developer',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $userId = DB::table('users')->where('email', self::EMAIL)->value('id');

            if (! $userId) {
                $userId = DB::table('users')->insertGetId([
                    'fname' => 'Developer',
                    'lname' => 'Admin',
                    'email' => self::EMAIL,
                    'password' => Hash::make('password'),
                    'role' => 'developer',
                    'is_active' => true,
                    'theme_preference' => 'pantas-default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('users')->where('id', $userId)->update([
                    'role' => 'developer',
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            }

            DB::table('model_has_roles')->updateOrInsert([
                'role_id' => $roleId,
                'model_type' => User::class,
                'model_id' => $userId,
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $userId = DB::table('users')->where('email', self::EMAIL)->value('id');

            if ($userId) {
                DB::table('model_has_roles')
                    ->where('model_type', User::class)
                    ->where('model_id', $userId)
                    ->delete();

                DB::table('users')->where('id', $userId)->delete();
            }
        });
    }
};
