<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRegisterModalRequest;
use App\Services\BrandingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DeveloperRegisterModalController extends Controller
{
    public function __construct(private readonly BrandingService $branding) {}

    public function edit(): View
    {
        $branding = $this->branding->active();
        $defaults = $this->branding->defaults();

        return view('developer.register-modal.edit', [
            'branding' => $branding,
            'defaults' => $defaults,
            'attendanceLogoUrl' => $this->branding->assetUrl('register_modal_attendance_logo_path'),
            'libraryLogoUrl' => $this->branding->assetUrl('register_modal_library_logo_path'),
            'originalAttendanceLogoUrl' => asset($defaults['register_modal_attendance_logo_path']),
            'originalLibraryLogoUrl' => asset($defaults['register_modal_library_logo_path']),
            'isCustomized' => collect(BrandingService::REGISTER_MODAL_FIELDS)
                ->contains(fn (string $field): bool => $branding[$field] !== $defaults[$field]),
        ]);
    }

    public function update(UpdateRegisterModalRequest $request): RedirectResponse
    {
        $this->branding->update(
            $request->safe()->except(['register_modal_attendance_logo', 'register_modal_library_logo']),
            $request->user(),
            attendanceRegisterLogo: $request->file('register_modal_attendance_logo'),
            libraryRegisterLogo: $request->file('register_modal_library_logo'),
        );

        return redirect()->route('developer.register-modal.edit')->with('success', 'Register modal settings saved.');
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field' => ['nullable', 'string', 'in:'.implode(',', BrandingService::REGISTER_MODAL_FIELDS)],
        ]);

        $this->branding->restoreRegisterModal($validated['field'] ?? null, $request->user());

        return redirect()->route('developer.register-modal.edit')->with(
            'success',
            isset($validated['field'])
                ? 'Register modal value restored to its Pantas default.'
                : 'Register modal restored to its Pantas defaults.',
        );
    }
}