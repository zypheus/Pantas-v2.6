<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserThemePreferenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles for module access
        $this->seed(RoleSeeder::class);

        // Ensure the themes config is loaded
        $this->app->make('config')->set('themes.themes', [
            'pantas-default' => ['label' => 'Pantas Default', 'type' => 'light'],
            'nord-dark' => ['label' => 'Nord Dark', 'type' => 'dark'],
            'catppuccin-light' => ['label' => 'Catppuccin Light', 'type' => 'light'],
        ]);
    }

    private function staffUser(string $role, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'is_active' => true,
        ], $overrides));

        $user->assignRole($role);

        return $user;
    }

    public function test_authenticated_user_can_save_an_allowed_theme(): void
    {
        $user = $this->staffUser('super_admin', [
            'theme_preference' => 'pantas-default',
        ]);

        $response = $this->actingAs($user)->postJson(route('user.preferences.theme'), [
            'theme' => 'nord-dark',
        ]);

        $response->assertOk();
        $response->assertJson([
            'theme' => 'nord-dark',
            'label' => 'Nord Dark',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme_preference' => 'nord-dark',
        ]);
    }

    public function test_guest_cannot_save_a_theme(): void
    {
        $response = $this->postJson(route('user.preferences.theme'), [
            'theme' => 'nord-dark',
        ]);

        $response->assertUnauthorized();
    }

    public function test_invalid_theme_key_is_rejected(): void
    {
        $user = $this->staffUser('super_admin', [
            'theme_preference' => 'pantas-default',
        ]);

        $response = $this->actingAs($user)->postJson(route('user.preferences.theme'), [
            'theme' => 'nonexistent-theme',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('theme');

        // Ensure the saved preference was not changed
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme_preference' => 'pantas-default',
        ]);
    }

    public function test_saved_theme_appears_in_authenticated_layout(): void
    {
        $user = $this->staffUser('super_admin', [
            'theme_preference' => 'catppuccin-light',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_module' => 'super-admin'])
            ->get(route('dashboard.super-admin'));

        $response->assertOk();

        // The HTML element should have the data-theme attribute set
        $response->assertSee('data-theme="catppuccin-light"', false);
        $response->assertSee('data-saved-theme="catppuccin-light"', false);
    }

    public function test_default_theme_is_pantas_default_when_not_set(): void
    {
        // Create user without specifying theme_preference — factory default applies
        $user = $this->staffUser('super_admin');

        $response = $this->actingAs($user)
            ->withSession(['active_module' => 'super-admin'])
            ->get(route('dashboard.super-admin'));

        $response->assertOk();
        $response->assertSee('data-theme="pantas-default"', false);
    }
}
