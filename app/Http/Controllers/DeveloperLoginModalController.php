<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLoginModalRequest;
use App\Services\BrandingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DeveloperLoginModalController extends Controller
{
    public function __construct(private readonly BrandingService $branding) {}

    public function edit(): View
    {
        $branding = $this->branding->active();
        $defaults = $this->branding->defaults();

        return view('developer.login-modal.edit', [
            'branding' => $branding,
            'defaults' => $defaults,
            'logoUrl' => $this->branding->assetUrl('login_modal_logo_path'),
            'originalLogoUrl' => asset($defaults['login_modal_logo_path']),
            'isCustomized' => collect(BrandingService::LOGIN_MODAL_FIELDS)
                ->contains(fn (string $field): bool => $branding[$field] !== $defaults[$field]),
        ]);
    }

    public function update(UpdateLoginModalRequest $request): RedirectResponse
    {
        $this->branding->update(
            $request->safe()->except(['login_modal_logo']),
            $request->user(),
            loginModalLogo: $request->file('login_modal_logo'),
        );

        return redirect()->route('developer.login-modal.edit')->with('success', 'Login modal settings saved.');
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field' => ['nullable', 'string', 'in:'.implode(',', BrandingService::LOGIN_MODAL_FIELDS)],
        ]);

        $this->branding->restoreLoginModal($validated['field'] ?? null, $request->user());

        return redirect()->route('developer.login-modal.edit')->with(
            'success',
            isset($validated['field'])
                ? 'Login modal value restored to its Pantas default.'
                : 'Login modal restored to its Pantas defaults.',
        );
    }
}
