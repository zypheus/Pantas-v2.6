<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBrandingRequest;
use App\Models\AdminActivity;
use App\Services\BrandingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DeveloperBrandingController extends Controller
{
    public function __construct(private readonly BrandingService $branding) {}

    public function activity(): View
    {
        $activities = AdminActivity::query()
            ->where('module', 'branding')
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('developer.branding.activity', [
            'activities' => $activities,
        ]);
    }

    public function edit(): View
    {
        return view('developer.branding.edit', [
            'branding' => $this->branding->active(),
            'defaults' => $this->branding->defaults(),
            'bannerUrl' => $this->branding->assetUrl('banner_path'),
            'logoUrl' => $this->branding->assetUrl('sidebar_logo_path'),
            'originalBannerUrl' => asset($this->branding->defaults()['banner_path']),
            'originalLogoUrl' => asset($this->branding->defaults()['sidebar_logo_path']),
        ]);
    }

    public function update(UpdateBrandingRequest $request): RedirectResponse
    {
        $this->branding->update(
            $request->safe()->except(['banner', 'sidebar_logo']),
            $request->user(),
            $request->file('banner'),
            $request->file('sidebar_logo'),
        );

        return redirect()->route('developer.branding.edit')->with('success', 'Branding settings saved.');
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field' => ['nullable', 'string', 'in:'.implode(',', array_keys($this->branding->defaults()))],
        ]);

        $this->branding->restore($validated['field'] ?? null, $request->user());

        return redirect()->route('developer.branding.edit')->with(
            'success',
            isset($validated['field']) ? 'Branding value restored to its Pantas default.' : 'Original Pantas branding restored.',
        );
    }
}
