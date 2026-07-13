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
        $field = match ($type) {
            'banners' => 'banner_path',
            'logos' => 'sidebar_logo_path',
            default => abort(404),
        };

        $requestedPath = "branding/{$type}/{$filename}";
        abort_unless(($branding->active()[$field] ?? null) === $requestedPath, 404);
        abort_unless(Storage::disk('public')->exists($requestedPath), 404);

        return response()->file(Storage::disk('public')->path($requestedPath), [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
