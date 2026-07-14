@extends('layouts.sidebar')

@section('title', $title)

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            <div class="dashboard-section-header">
                <div>
                    <p class="dashboard-section-kicker">Developer workspace</p>
                    <h1>{{ $title }}</h1>
                    <p>{{ $summary }}</p>
                </div>
                <span class="sidebar-role-badge">Developer only</span>
            </div>

            <div class="dashboard-content-grid">
                <article class="dashboard-panel" id="branding-settings">
                    <div class="dashboard-panel-header">
                        <div>
                            <span class="dashboard-panel-kicker">Current identity</span>
                            <h2>Branding Settings</h2>
                        </div>
                        <span class="dashboard-panel-meta">{{ $branding['is_customized'] ? 'Customized' : 'Original' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 my-3">
                        <img src="{{ $logoUrl }}" alt="Current sidebar logo" style="width:72px;height:72px;object-fit:contain">
                        <div>
                            <strong>{{ $branding['updated_by'] ?? 'Pantas defaults' }}</strong>
                            <p class="mb-0 text-muted">{{ $branding['updated_at']?->format('M j, Y g:i A') ?? 'No custom update yet' }}</p>
                        </div>
                    </div>
                    <img src="{{ $bannerUrl }}" alt="Current banner" class="img-fluid rounded border mb-3" style="max-height:180px;width:100%;object-fit:cover">
                    <a href="{{ route('developer.branding.edit') }}" class="btn btn-primary">Open Branding Settings</a>
                </article>

                <article class="dashboard-panel" id="login-modal-settings">
                    <div class="dashboard-panel-header">
                        <div>
                            <span class="dashboard-panel-kicker">Public authentication</span>
                            <h2>Login Modal Settings</h2>
                        </div>
                        <span class="dashboard-panel-meta">Separate settings</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 my-3">
                        <img src="{{ $brandingLoginModalLogoUrl }}" alt="Current login modal logo" style="width:72px;height:72px;object-fit:contain">
                        <div>
                            <strong>{{ $branding['login_modal_portal_name'] ?? config('branding.defaults.login_modal_portal_name', 'PANTAS Portal') }}</strong>
                            <p class="mb-0 text-muted">{{ $branding['login_modal_sign_in_heading'] ?? config('branding.defaults.login_modal_sign_in_heading', 'Sign in to your account') }}</p>
                        </div>
                    </div>
                    <p class="text-muted">Manage the public login logo, wording, placeholders, and colors without changing registration workflows.</p>
                    <a href="{{ route('developer.login-modal.edit') }}" class="btn btn-primary">Open Login Modal Settings</a>
                </article>

                <article class="dashboard-panel" id="register-modal-settings">
                    <div class="dashboard-panel-header">
                        <div>
                            <span class="dashboard-panel-kicker">Public registration</span>
                            <h2>Register Modal Settings</h2>
                        </div>
                        <span class="dashboard-panel-meta">Separate settings</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 my-3">
                        <img src="{{ $brandingAttendanceRegisterLogoUrl }}" alt="Current Attendance register logo" style="width:48px;height:48px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0">
                        <img src="{{ $brandingLibraryRegisterLogoUrl }}" alt="Current Library register logo" style="width:48px;height:48px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0">
                        <div>
                            <strong>{{ $branding['register_modal_heading'] ?? config('branding.defaults.register_modal_heading', 'Register') }}</strong>
                            <p class="mb-0 text-muted">Attendance & Library registration branding</p>
                        </div>
                    </div>
                    <p class="text-muted">Customize Attendance and Library registration presentation independently from login mode branding.</p>
                    <a href="{{ route('developer.register-modal.edit') }}" class="btn btn-primary">Open Register Modal Settings</a>
                </article>
            </div>
        </section>
    </div>
@endsection
