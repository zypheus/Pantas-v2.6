<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AdminActivity;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_activity_logger_records_module_type_actor_and_subject(): void
    {
        $admin = $this->staffUser('super_admin');
        $target = $this->staffUser('library_staff', ['email' => 'library.staff@example.test']);

        $this->actingAs($admin);
        app(AdminActivityLogger::class)->log(
            module: 'super-admin',
            type: 'staff.updated',
            title: 'Staff account updated',
            body: $target->email,
            subject: $target,
            actionUrl: '/view-users',
            icon: 'person-gear'
        );

        $this->assertDatabaseHas('admin_activities', [
            'user_id' => $admin->id,
            'module' => 'super-admin',
            'type' => 'staff.updated',
            'title' => 'Staff account updated',
            'body' => 'library.staff@example.test',
            'subject_type' => User::class,
            'subject_id' => $target->id,
            'action_url' => '/view-users',
            'icon' => 'person-gear',
        ]);
        $this->assertSame('staff.updated', AdminActivity::query()->firstOrFail()->type);
    }

    public function test_super_admin_staff_creation_writes_audit_activity(): void
    {
        $admin = $this->staffUser('super_admin');

        $response = $this->actingAs($admin)->post('/users', [
            'lname' => 'Admin',
            'fname' => 'Library',
            'email' => 'new.library.admin@example.test',
            'password' => 'password',
            'role' => 'library_admin',
        ]);

        $response->assertRedirect(route('users.create', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'new.library.admin@example.test',
            'role' => 'library_admin',
        ]);
        $this->assertDatabaseHas('admin_activities', [
            'user_id' => $admin->id,
            'module' => 'super-admin',
            'type' => 'staff.created',
            'body' => 'new.library.admin@example.test',
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function staffUser(string $role, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'is_active' => true,
        ], $attributes));

        $user->assignRole($role);

        return $user;
    }
}
