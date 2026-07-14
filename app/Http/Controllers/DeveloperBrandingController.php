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
            'opacBannerUrl' => $this->branding->assetUrl('opac_banner_path'),
            'opacLogoUrl' => $this->branding->assetUrl('opac_logo_path'),
            'opacDefaultBookCoverUrl' => $this->branding->assetUrl('opac_default_book_cover_path'),
            'logoUrl' => $this->branding->assetUrl('sidebar_logo_path'),
            'originalBannerUrl' => asset($this->branding->defaults()['banner_path']),
            'originalOpacBannerUrl' => asset($this->branding->defaults()['opac_banner_path']),
            'originalOpacLogoUrl' => asset($this->branding->defaults()['opac_logo_path']),
            'originalOpacDefaultBookCoverUrl' => asset($this->branding->defaults()['opac_default_book_cover_path']),
            'originalLogoUrl' => asset($this->branding->defaults()['sidebar_logo_path']),
        ]);
    }

    public function update(UpdateBrandingRequest $request): RedirectResponse
    {
        $this->branding->update(
            $request->safe()->except(['banner', 'opac_banner', 'opac_logo', 'opac_default_book_cover', 'sidebar_logo']),
            $request->user(),
            $request->file('banner'),
            $request->file('opac_banner'),
            $request->file('opac_logo'),
            $request->file('opac_default_book_cover'),
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

    public function versions(): View
    {
        return view('developer.branding.versions', [
            'versions' => $this->branding->getVersions(),
        ]);
    }

    public function restoreVersion(Request $request, int $version): RedirectResponse
    {
        $this->branding->restoreFromVersion($version, $request->user());

        return redirect()->route('developer.branding.versions')->with('success', 'Branding restored from version #'.$version.'.');
    }
}