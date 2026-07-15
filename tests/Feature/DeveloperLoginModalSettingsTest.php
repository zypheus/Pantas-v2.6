<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AdminActivity;
use App\Models\BrandingSetting;
use App\Models\User;
use App\Services\BrandingService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class DeveloperLoginModalSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget(BrandingService::CACHE_KEY);
        $this->seed(RoleSeeder::class);
    }

    public function test_developer_can_open_separate_login_modal_settings_page_and_navigation(): void
    {
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)
            ->get('/developer/login-modal')
            ->assertOk()
            ->assertSee('Login Modal Settings')
            ->assertSee('Live preview')
            ->assertSee('Restore Login Modal Defaults')
            ->assertSee('name="login_modal_portal_name"', false)
            ->assertSee('name="login_modal_left_background_color"', false)
            ->assertSee('name="login_modal_background_color"', false)
            ->assertSee('name="login_modal_text_color"', false)
            ->assertSee('name="login_modal_button_color"', false);

        $this->actingAs($developer)
            ->get('/developer/dashboard')
            ->assertOk()
            ->assertSee('Open Login Modal Settings');
    }

    public function test_non_developers_cannot_access_login_modal_settings_actions(): void
    {
        foreach (['super_admin', 'library_admin', 'attendance_admin'] as $role) {
            $user = $this->staffUser($role);

            $this->actingAs($user)->get('/developer/login-modal')->assertForbidden();
            $this->actingAs($user)->put('/developer/login-modal', $this->validSettings())->assertForbidden();
            $this->actingAs($user)->post('/developer/login-modal/restore')->assertForbidden();
        }
    }

    public function test_developer_can_save_normalized_text_colors_and_logo(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/login-modal', $this->validSettings([
            'login_modal_logo' => UploadedFile::fake()->image('portal.png', 300, 300),
            'login_modal_portal_name' => '  Campus Portal  ',
            'login_modal_button_color' => '#1a3a8a',
        ]))->assertRedirect(route('developer.login-modal.edit', absolute: false));

        $settings = BrandingSetting::query()->firstOrFail();
        $this->assertSame('Campus Portal', $settings->login_modal_portal_name);
        $this->assertSame('#1A3A8A', $settings->login_modal_button_color);
        $this->assertStringStartsWith('branding/login-modal/', (string) $settings->login_modal_logo_path);
        Storage::disk('public')->assertExists($settings->login_modal_logo_path);

        $activity = AdminActivity::query()->where('type', 'branding_update')->firstOrFail();
        $this->assertSame('/developer/login-modal', $activity->action_url);
    }

    public function test_unsafe_colors_oversized_text_and_invalid_logo_are_rejected(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/login-modal', $this->validSettings([
            'login_modal_logo' => UploadedFile::fake()->image('tiny.png', 32, 32),
            'login_modal_portal_name' => str_repeat('x', 101),
            'login_modal_left_background_color' => 'url(javascript:alert(1))',
            'login_modal_background_color' => 'red',
            'login_modal_text_color' => '#1234',
            'login_modal_button_color' => 'var(--danger)',
        ]))->assertSessionHasErrors([
            'login_modal_logo',
            'login_modal_portal_name',
            'login_modal_left_background_color',
            'login_modal_background_color',
            'login_modal_text_color',
            'login_modal_button_color',
        ]);

        $this->assertDatabaseCount('branding_settings', 0);
    }

    public function test_oversized_and_undecodable_logo_files_are_rejected(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/login-modal', $this->validSettings([
            'login_modal_logo' => UploadedFile::fake()->create('oversized.png', 2049, 'image/png'),
        ]))->assertSessionHasErrors('login_modal_logo');

        $this->actingAs($developer)->put('/developer/login-modal', $this->validSettings([
            'login_modal_logo' => UploadedFile::fake()->createWithContent('broken.png', 'not a decodable image'),
        ]))->assertSessionHasErrors('login_modal_logo');

        $this->assertDatabaseCount('branding_settings', 0);
    }

    public function test_developer_can_restore_one_field_or_only_the_login_modal_group(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('branding/login-modal/custom.png', 'custom');
        $developer = $this->staffUser('developer');
        BrandingSetting::query()->create([
            'login_modal_logo_path' => 'branding/login-modal/custom.png',
            'login_modal_portal_name' => 'Custom Portal',
            'login_modal_button_color' => '#010203',
            'primary_color' => '#ABCDEF',
            'updated_by' => $developer->id,
        ]);

        $this->actingAs($developer)->post('/developer/login-modal/restore', [
            'field' => 'login_modal_portal_name',
        ])->assertRedirect(route('developer.login-modal.edit', absolute: false));

        $this->assertNull(BrandingSetting::query()->value('login_modal_portal_name'));
        $this->assertSame('#010203', BrandingSetting::query()->value('login_modal_button_color'));

        $this->actingAs($developer)->post('/developer/login-modal/restore')
            ->assertRedirect(route('developer.login-modal.edit', absolute: false));

        $settings = BrandingSetting::query()->firstOrFail();
        foreach (BrandingService::LOGIN_MODAL_FIELDS as $field) {
            $this->assertNull($settings->{$field});
        }
        $this->assertSame('#ABCDEF', $settings->primary_color);
        Storage::disk('public')->assertMissing('branding/login-modal/custom.png');
        $this->assertDatabaseHas('admin_activities', ['type' => 'branding_restore_group']);
    }

    /** @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validSettings(array $overrides = []): array
    {
        return array_merge([
            'login_modal_welcome_label' => 'Welcome to',
            'login_modal_portal_name' => 'PANTAS Portal',
            'login_modal_description' => 'Sign in to access the Library and Attendance systems.',
            'login_modal_sign_in_heading' => 'Sign in to your account',
            'login_modal_email_placeholder' => 'staff@pantas.edu.ph',
            'login_modal_password_placeholder' => 'Enter password',
            'login_modal_left_background_color' => '#123C8C',
            'login_modal_background_color' => '#FFFFFF',
            'login_modal_text_color' => '#172033',
            'login_modal_button_color' => '#123C8C',
        ], $overrides);
    }

    private function staffUser(string $role): User
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);
        $user->assignRole($role);

        return $user;
    }
}