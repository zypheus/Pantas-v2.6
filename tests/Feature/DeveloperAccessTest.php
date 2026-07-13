<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class DeveloperAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_role_seeder_creates_developer_role(): void
    {
        $this->assertTrue(Role::query()->where('name', 'developer')->where('guard_name', 'web')->exists());
    }

    public function test_developer_defaults_to_isolated_dashboard(): void
    {
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)
            ->get('/dashboard')
            ->assertRedirect(route('dashboard.developer', absolute: false));

        $this->actingAs($developer)
            ->get('/developer/dashboard')
            ->assertOk()
            ->assertSee('Developer Dashboard')
            ->assertSee('Branding Settings')
            ->assertDontSee('Super Admin Dashboard')
            ->assertDontSee('Library Dashboard')
            ->assertDontSee('Attendance Dashboard')
            ->assertDontSee('Module');
    }

    public function test_developer_login_redirects_to_developer_dashboard(): void
    {
        $developer = $this->staffUser('developer', ['password' => bcrypt('password')]);

        $this->post('/login', [
            'email' => $developer->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard.developer', absolute: false));
    }

    public function test_super_admin_and_other_staff_cannot_access_developer_dashboard(): void
    {
        foreach (['super_admin', 'library_admin', 'library_staff', 'attendance_admin', 'attendance_staff'] as $role) {
            $this->actingAs($this->staffUser($role))
                ->get('/developer/dashboard')
                ->assertForbidden();
        }
    }

    public function test_developer_cannot_access_other_staff_dashboards_or_account_management(): void
    {
        $developer = $this->staffUser('developer');

        foreach ([
            '/dashboard/super-admin',
            '/dashboard/library-admin',
            '/dashboard/library-staff',
            '/dashboard/attendance-admin',
            '/dashboard/attendance-staff',
            '/view-users',
            '/create-user',
        ] as $uri) {
            $this->actingAs($developer)->get($uri)->assertForbidden();
        }
    }

    public function test_super_admin_cannot_create_assign_edit_or_delete_developer_accounts(): void
    {
        $superAdmin = $this->staffUser('super_admin');
        $developer = $this->staffUser('developer');

        $this->actingAs($superAdmin)
            ->get('/view-users')
            ->assertOk()
            ->assertDontSee($developer->email);

        $this->actingAs($superAdmin)
            ->post('/users', [
                'fname' => 'New',
                'lname' => 'Developer',
                'email' => 'new-developer@example.test',
                'password' => 'password',
                'role' => 'developer',
            ])
            ->assertSessionHasErrors('role');

        $this->actingAs($superAdmin)->get("/edit-user/{$developer->id}")->assertForbidden();
        $this->actingAs($superAdmin)->put("/update-user/{$developer->id}", [
            'fname' => $developer->fname,
            'lname' => $developer->lname,
            'email' => $developer->email,
            'role' => 'library_staff',
        ])->assertForbidden();
        $this->actingAs($superAdmin)->delete("/delete-user/{$developer->id}")->assertForbidden();
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
