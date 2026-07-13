<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BrandingSetting;
use App\Models\User;
use App\Services\BrandingService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class DeveloperBrandingSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget(BrandingService::CACHE_KEY);
        $this->seed(RoleSeeder::class);
    }

    public function test_developer_can_view_branding_settings_with_original_values(): void
    {
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)
            ->get('/developer/branding')
            ->assertOk()
            ->assertSee('Original Pantas')
            ->assertSee('Banner')
            ->assertSee('Sidebar logo')
            ->assertSee('Restore to Default');
    }

    public function test_non_developer_roles_cannot_access_any_branding_action(): void
    {
        $superAdmin = $this->staffUser('super_admin');

        $this->actingAs($superAdmin)->get('/developer/branding')->assertForbidden();
        $this->actingAs($superAdmin)->put('/developer/branding', ['primary_color' => '#112233'])->assertForbidden();
        $this->actingAs($superAdmin)->post('/developer/branding/restore')->assertForbidden();
    }

    public function test_developer_can_save_normalized_colors(): void
    {
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/branding', $this->colors([
            'primary_color' => '#aabbcc',
        ]))->assertRedirect(route('developer.branding.edit', absolute: false));

        $this->assertDatabaseHas('branding_settings', [
            'primary_color' => '#AABBCC',
            'updated_by' => $developer->id,
        ]);
        $this->assertSame('#AABBCC', app(BrandingService::class)->active()['primary_color']);
    }

    public function test_developer_can_upload_banner_and_logo(): void
    {
        Storage::fake('public');
        $developer = $this->staffUser('developer');

        $this->actingAs($developer)->put('/developer/branding', $this->colors([
            'banner' => UploadedFile::fake()->image('banner.jpg', 1200, 400),
            'sidebar_logo' => UploadedFile::fake()->image('logo.png', 300, 300),
        ]))->assertRedirect(route('developer.branding.edit', absolute: false));

        $settings = BrandingSetting::query()->firstOrFail();
        Storage::disk('public')->assertExists($settings->banner_path);
        Storage::disk('public')->assertExists($settings->sidebar_logo_path);
    }

    public function test_developer_can_restore_one_value_or_all_values(): void
    {
        $developer = $this->staffUser('developer');
        BrandingSetting::query()->create($this->colors([
            'updated_by' => $developer->id,
        ]));

        $this->actingAs($developer)->post('/developer/branding/restore', [
            'field' => 'primary_color',
        ])->assertRedirect(route('developer.branding.edit', absolute: false));

        $this->assertNull(BrandingSetting::query()->value('primary_color'));
        $this->assertNotNull(BrandingSetting::query()->value('accent_color'));

        $this->actingAs($developer)->post('/developer/branding/restore')
            ->assertRedirect(route('developer.branding.edit', absolute: false));

        $settings = BrandingSetting::query()->firstOrFail();
        foreach (array_keys(config('branding.defaults')) as $field) {
            $this->assertNull($settings->{$field});
        }
        $this->assertFalse(app(BrandingService::class)->active()['is_customized']);
    }

    /** @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function colors(array $overrides = []): array
    {
        return array_merge([
            'primary_color' => '#1E3A8A',
            'secondary_color' => '#0F766E',
            'accent_color' => '#B45309',
            'sidebar_background_color' => '#FFFFFF',
            'sidebar_text_color' => '#0F172A',
            'sidebar_active_color' => '#2563EB',
            'button_color' => '#1E3A8A',
        ], $overrides);
    }

    private function staffUser(string $role): User
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);
        $user->assignRole($role);

        return $user;
    }
}
