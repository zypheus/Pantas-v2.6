<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BrandingService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class BrandingAssetController extends Controller
{
    public function __invoke(string $type, string $filename, BrandingService $branding): BinaryFileResponse
    {
        if ($type === 'opac') {
            $requestedPath = "branding/opac/{$filename}";
            $matches = in_array($requestedPath, [
                $branding->active()['opac_logo_path'] ?? null,
                $branding->active()['opac_default_book_cover_path'] ?? null,
            ], true);
            abort_unless($matches, 404);
        } elseif ($type === 'banners') {
            $requestedPath = "branding/banners/{$filename}";
            $matches = in_array($requestedPath, [
                $branding->active()['banner_path'] ?? null,
                $branding->active()['opac_banner_path'] ?? null,
            ], true);
            abort_unless($matches, 404);
        } elseif ($type === 'logos') {
            $requestedPath = "branding/logos/{$filename}";
            abort_unless(($branding->active()['sidebar_logo_path'] ?? null) === $requestedPath, 404);
        } else {
            abort(404);
        }
        abort_unless(Storage::disk('public')->exists($requestedPath), 404);

        return response()->file(Storage::disk('public')->path($requestedPath), [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
