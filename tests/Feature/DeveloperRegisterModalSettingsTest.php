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

final class DeveloperRegisterModalSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget(BrandingService::CACHE_KEY);
        $this->seed(RoleSeeder::class);
    }

    public function test_developer_can_open_separate_register_modal_settings_page_and_navigation(): void
    {
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)
            ->get('/developer/register-modal')
            ->assertOk()
            ->assertSee('Register Modal Settings')
            ->assertSee('Live preview')
            ->assertSee('Restore Register Modal Defaults')
            ->assertSee('name="register_modal_heading"', false)
            ->assertSee('name="register_modal_attendance_panel_color"', false)
            ->assertSee('name="register_modal_attendance_text_color"', false)
            ->assertSee('name="register_modal_attendance_accent_color"', false)
            ->assertSee('name="register_modal_library_text_color"', false)
            ->assertSee('name="register_modal_library_accent_color"', false)
            ->assertSee('name="register_modal_library_submit_color"', false);

        $this->actingAs($developer)
            ->get('/developer/dashboard')
            ->assertOk()
            ->assertSee('Open Register Modal Settings');
    }

    public function test_non_developers_cannot_access_register_modal_settings_actions(): void
    {
        foreach (['super_admin', 'library_admin', 'attendance_admin'] as $role) {
            $user = $this->staffUser($role);

            $this->actingAs($user)->get('/developer/register-modal')->assertForbidden();
            $this->actingAs($user)->put('/developer/register-modal', $this->validSettings())->assertForbidden();
            $this->actingAs($user)->post('/developer/register-modal/restore')->assertForbidden();
        }
    }

    public function test_developer_can_save_normalized_text_colors_and_logos(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/register-modal', $this->validSettings([
            'register_modal_attendance_logo' => UploadedFile::fake()->image('attendance.png', 300, 300),
            'register_modal_library_logo' => UploadedFile::fake()->image('library.png', 300, 300),
            'register_modal_heading' => '  Create Account  ',
            'register_modal_attendance_portal_name' => '  Campus Attendance  ',
            'register_modal_library_portal_name' => '  Campus Library  ',
            'register_modal_attendance_panel_color' => '#1a3a8a',
            'register_modal_attendance_text_color' => '#FFFFFF',
            'register_modal_library_submit_color' => '#1a3a8a',
        ]))->assertRedirect(route('developer.register-modal.edit', absolute: false));

        $settings = BrandingSetting::query()->firstOrFail();
        $this->assertSame('Create Account', $settings->register_modal_heading);
        $this->assertSame('Campus Attendance', $settings->register_modal_attendance_portal_name);
        $this->assertSame('Campus Library', $settings->register_modal_library_portal_name);
        $this->assertSame('#1A3A8A', $settings->register_modal_attendance_panel_color);
        $this->assertSame('#1A3A8A', $settings->register_modal_library_submit_color);
        $this->assertStringStartsWith('branding/register-modal/', (string) $settings->register_modal_attendance_logo_path);
        $this->assertStringStartsWith('branding/register-modal/', (string) $settings->register_modal_library_logo_path);
        Storage::disk('public')->assertExists($settings->register_modal_attendance_logo_path);
        Storage::disk('public')->assertExists($settings->register_modal_library_logo_path);

        $activity = AdminActivity::query()->where('type', 'branding_update')->firstOrFail();
        $this->assertSame('/developer/register-modal', $activity->action_url);
    }

    public function test_unsafe_colors_oversized_text_and_invalid_logos_are_rejected(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/register-modal', $this->validSettings([
            'register_modal_attendance_logo' => UploadedFile::fake()->image('tiny.png', 32, 32),
            'register_modal_library_logo' => UploadedFile::fake()->image('tiny.png', 32, 32),
            'register_modal_heading' => str_repeat('x', 81),
            'register_modal_attendance_panel_color' => 'url(javascript:alert(1))',
            'register_modal_attendance_text_color' => 'red',
            'register_modal_attendance_accent_color' => '#1234',
            'register_modal_attendance_active_role_color' => 'var(--danger)',
            'register_modal_attendance_submit_color' => 'maroon',
            'register_modal_library_panel_color' => 'transparent',
            'register_modal_library_text_color' => '#GGGBBB',
            'register_modal_library_accent_color' => 'rebeccapurple',
            'register_modal_library_active_role_color' => '#12',
            'register_modal_library_submit_color' => 'hsl(0, 100%, 50%)',
        ]))->assertSessionHasErrors([
            'register_modal_attendance_logo',
            'register_modal_library_logo',
            'register_modal_heading',
            'register_modal_attendance_panel_color',
            'register_modal_attendance_text_color',
            'register_modal_attendance_accent_color',
            'register_modal_attendance_active_role_color',
            'register_modal_attendance_submit_color',
            'register_modal_library_panel_color',
            'register_modal_library_text_color',
            'register_modal_library_accent_color',
            'register_modal_library_active_role_color',
            'register_modal_library_submit_color',
        ]);

        $this->assertDatabaseCount('branding_settings', 0);
    }

    public function test_oversized_and_undecodable_logo_files_are_rejected(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/register-modal', $this->validSettings([
            'register_modal_attendance_logo' => UploadedFile::fake()->create('oversized.png', 2049, 'image/png'),
        ]))->assertSessionHasErrors('register_modal_attendance_logo');

        $this->actingAs($developer)->put('/developer/register-modal', $this->validSettings([
            'register_modal_attendance_logo' => UploadedFile::fake()->createWithContent('broken.png', 'not a decodable image'),
        ]))->assertSessionHasErrors('register_modal_attendance_logo');

        $this->actingAs($developer)->put('/developer/register-modal', $this->validSettings([
            'register_modal_library_logo' => UploadedFile::fake()->create('oversized.png', 2049, 'image/png'),
        ]))->assertSessionHasErrors('register_modal_library_logo');

        $this->actingAs($developer)->put('/developer/register-modal', $this->validSettings([
            'register_modal_library_logo' => UploadedFile::fake()->createWithContent('broken.png', 'not a decodable image'),
        ]))->assertSessionHasErrors('register_modal_library_logo');

        $this->assertDatabaseCount('branding_settings', 0);
    }

    public function test_developer_can_restore_one_field_or_only_the_register_modal_group(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('branding/register-modal/custom-att.png', 'custom');
        Storage::disk('public')->put('branding/register-modal/custom-lib.png', 'custom');
        $developer = $this->staffUser('developer');
        BrandingSetting::query()->create([
            'register_modal_attendance_logo_path' => 'branding/register-modal/custom-att.png',
            'register_modal_library_logo_path' => 'branding/register-modal/custom-lib.png',
            'register_modal_heading' => 'Custom Register',
            'register_modal_attendance_portal_name' => 'Custom Attendance',
            'register_modal_attendance_panel_color' => '#010203',
            'primary_color' => '#ABCDEF',
            'updated_by' => $developer->id,
        ]);

        $this->actingAs($developer)->post('/developer/register-modal/restore', [
            'field' => 'register_modal_heading',
        ])->assertRedirect(route('developer.register-modal.edit', absolute: false));

        $this->assertNull(BrandingSetting::query()->value('register_modal_heading'));
        $this->assertSame('#010203', BrandingSetting::query()->value('register_modal_attendance_panel_color'));

        $this->actingAs($developer)->post('/developer/register-modal/restore')
            ->assertRedirect(route('developer.register-modal.edit', absolute: false));

        $settings = BrandingSetting::query()->firstOrFail();
        foreach (BrandingService::REGISTER_MODAL_FIELDS as $field) {
            $this->assertNull($settings->{$field});
        }
        $this->assertSame('#ABCDEF', $settings->primary_color);
        Storage::disk('public')->assertMissing('branding/register-modal/custom-att.png');
        Storage::disk('public')->assertMissing('branding/register-modal/custom-lib.png');
        $this->assertDatabaseHas('admin_activities', ['type' => 'branding_restore_group']);
    }

    /** @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validSettings(array $overrides = []): array
    {
        return array_merge([
            'register_modal_heading' => 'Register',
            'register_modal_login_label' => 'Login',
            'register_modal_attendance_tab' => 'Attendance',
            'register_modal_library_tab' => 'Library',
            'register_modal_attendance_welcome_label' => 'Register for',
            'register_modal_attendance_portal_name' => 'PANTAS Attendance',
            'register_modal_attendance_description' => 'Create your attendance record.',
            'register_modal_attendance_heading' => 'Attendance Registration',
            'register_modal_attendance_student_label' => 'Student',
            'register_modal_attendance_employee_label' => 'Employee',
            'register_modal_attendance_student_submit' => 'Submit Student Registration',
            'register_modal_attendance_employee_submit' => 'Submit Employee Registration',
            'register_modal_library_welcome_label' => 'Register for',
            'register_modal_library_portal_name' => 'PANTAS Library',
            'register_modal_library_description' => 'Apply for library access.',
            'register_modal_library_heading' => 'Library Registration',
            'register_modal_library_student_label' => 'Student',
            'register_modal_library_employee_label' => 'Faculty & Staff',
            'register_modal_library_student_submit' => 'Submit Student Registration',
            'register_modal_library_employee_submit' => 'Submit Faculty & Staff Registration',
            'register_modal_attendance_panel_color' => '#d97706',
            'register_modal_attendance_text_color' => '#FFFFFF',
            'register_modal_attendance_accent_color' => '#B45309',
            'register_modal_attendance_active_role_color' => '#d97706',
            'register_modal_attendance_submit_color' => '#d97706',
            'register_modal_library_panel_color' => '#123C8C',
            'register_modal_library_text_color' => '#FFFFFF',
            'register_modal_library_accent_color' => '#123C8C',
            'register_modal_library_active_role_color' => '#175dbd',
            'register_modal_library_submit_color' => '#123C8C',
        ], $overrides);
    }

    private function staffUser(string $role): User
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);
        $user->assignRole($role);

        return $user;
    }
}