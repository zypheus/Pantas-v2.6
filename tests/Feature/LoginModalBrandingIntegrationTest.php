<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BrandingSetting;
use App\Models\User;
use App\Services\BrandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class LoginModalBrandingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Cache::forget(BrandingService::CACHE_KEY);
    }

    public function test_landing_login_panel_renders_active_developer_settings(): void
    {
        Storage::disk('public')->put('branding/login-modal/custom.png', 'custom-logo');
        $developer = User::factory()->create();
        BrandingSetting::query()->create([
            'login_modal_logo_path' => 'branding/login-modal/custom.png',
            'login_modal_welcome_label' => 'Welcome back',
            'login_modal_portal_name' => 'Campus Access',
            'login_modal_description' => 'Use your approved account.',
            'login_modal_sign_in_heading' => 'Staff access',
            'login_modal_email_placeholder' => 'staff@example.edu',
            'login_modal_password_placeholder' => 'Secure password',
            'login_modal_left_background_color' => '#102030',
            'login_modal_background_color' => '#F8FAFC',
            'login_modal_text_color' => '#203040',
            'login_modal_button_color' => '#405060',
            'updated_by' => $developer->id,
        ]);
        Cache::forget(BrandingService::CACHE_KEY);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-login-welcome="Welcome back"', false)
            ->assertSee('data-login-portal-name="Campus Access"', false)
            ->assertSee('data-login-description="Use your approved account."', false)
            ->assertSee('--lm-login-left:#102030', false)
            ->assertSee('--lm-login-bg:#F8FAFC', false)
            ->assertSee('--lm-login-text:#203040', false)
            ->assertSee('--lm-login-button:#405060', false)
            ->assertSee('/branding-assets/login-modal/custom.png', false)
            ->assertSee('Staff access')
            ->assertSee('placeholder="staff@example.edu"', false)
            ->assertSee('placeholder="Secure password"', false);

        $this->get('/branding-assets/login-modal/custom.png')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_login_customization_keeps_registration_content_and_endpoints_unchanged(): void
    {
        BrandingSetting::query()->create([
            'login_modal_portal_name' => 'Custom Login Only',
            'login_modal_left_background_color' => '#102030',
        ]);
        Cache::forget(BrandingService::CACHE_KEY);

        $this->get('/')
            ->assertOk()
            ->assertSee('Attendance Registration')
            ->assertSee('Library Registration')
            ->assertSee(route('attendance.pending.store', absolute: false))
            ->assertSee(route('attendance.pendingEmployee.store', absolute: false))
            ->assertSee(route('library.pending.store', absolute: false))
            ->assertSee(route('library.pendingEmployee.store', absolute: false))
            ->assertSee('data-default-registration-src="'.asset('img/pantas-10.png').'"', false);
    }

    public function test_direct_login_uses_the_same_branded_modal_and_opens_it_automatically(): void
    {
        BrandingSetting::query()->create([
            'login_modal_portal_name' => 'Direct Campus Login',
            'login_modal_sign_in_heading' => 'Continue securely',
            'login_modal_email_placeholder' => 'account@example.edu',
            'login_modal_button_color' => '#405060',
        ]);
        Cache::forget(BrandingService::CACHE_KEY);

        $this->get('/login')
            ->assertOk()
            ->assertSee('data-open-on-load="true"', false)
            ->assertSee('data-initial-view="login"', false)
            ->assertSee('data-close-url="'.route('landing').'"', false)
            ->assertSee('Direct Campus Login')
            ->assertSee('Continue securely')
            ->assertSee('placeholder="account@example.edu"', false)
            ->assertSee('--lm-login-button:#405060', false)
            ->assertDontSee('Welcome! Let');
    }

    public function test_direct_login_error_retains_email_and_remember_values(): void
    {
        $user = User::factory()->create();

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'incorrect-password',
            'remember' => '1',
        ])->assertRedirect('/login');

        $this->get('/login')
            ->assertOk()
            ->assertSee('data-open-on-load="true"', false)
            ->assertSee('data-initial-view="login"', false)
            ->assertSee('value="'.$user->email.'"', false)
            ->assertSee('name="remember" value="1" checked', false)
            ->assertSee('Invalid credentials.');
    }

    public function test_direct_login_displays_password_status_and_expired_session_errors(): void
    {
        $this->withSession(['status' => 'Your password has been reset.'])
            ->get('/login')
            ->assertOk()
            ->assertSee('Your password has been reset.');

        $this->withSession([
            'status' => null,
            'error' => 'Your session has expired. Please try logging in again.',
        ])->get('/login')
            ->assertOk()
            ->assertSee('Your session has expired. Please try logging in again.');
    }
}
