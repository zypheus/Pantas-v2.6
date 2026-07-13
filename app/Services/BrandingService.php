<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BrandingSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

final class BrandingService
{
    public const CACHE_KEY = 'branding.active';

    /** @var list<string> */
    public const COLOR_FIELDS = [
        'primary_color',
        'secondary_color',
        'accent_color',
        'sidebar_background_color',
        'sidebar_text_color',
        'sidebar_active_color',
        'button_color',
    ];

    /** @return array<string, mixed> */
    public function active(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $defaults = config('branding.defaults', []);
            $settings = $this->settings();

            if (! $settings) {
                return $defaults + ['is_customized' => false, 'updated_at' => null, 'updated_by' => null];
            }

            $active = $defaults;
            foreach (array_keys($defaults) as $field) {
                if (filled($settings->{$field})) {
                    $active[$field] = $settings->{$field};
                }
            }

            foreach (['banner_path', 'sidebar_logo_path'] as $field) {
                if ($active[$field] !== $defaults[$field] && ! Storage::disk('public')->exists($active[$field])) {
                    $active[$field] = $defaults[$field];
                }
            }

            return $active + [
                'is_customized' => collect(array_keys($defaults))->contains(fn (string $field): bool => filled($settings->{$field})),
                'updated_at' => $settings->updated_at,
                'updated_by' => $settings->updater?->name,
            ];
        });
    }

    /** @return array<string, mixed> */
    public function defaults(): array
    {
        return config('branding.defaults', []);
    }

    public function assetUrl(string $field): string
    {
        $path = (string) ($this->active()[$field] ?? $this->defaults()[$field] ?? '');

        return str_starts_with($path, 'branding/') ? Storage::disk('public')->url($path) : asset($path);
    }

    /** @param array<string, mixed> $values */
    public function update(array $values, User $user, ?UploadedFile $banner = null, ?UploadedFile $logo = null): BrandingSetting
    {
        $settings = $this->settings() ?? new BrandingSetting;
        $oldBanner = $settings->banner_path;
        $oldLogo = $settings->sidebar_logo_path;

        foreach (self::COLOR_FIELDS as $field) {
            if (array_key_exists($field, $values)) {
                $settings->{$field} = $values[$field] ? strtoupper((string) $values[$field]) : null;
            }
        }

        if ($banner) {
            $settings->banner_path = $banner->store('branding/banners', 'public');
        }

        if ($logo) {
            $settings->sidebar_logo_path = $logo->store('branding/logos', 'public');
        }

        $settings->updated_by = $user->getKey();
        $settings->save();
        $this->clearCache();

        $this->deleteReplacedFile($oldBanner, $settings->banner_path);
        $this->deleteReplacedFile($oldLogo, $settings->sidebar_logo_path);

        return $settings->fresh('updater');
    }

    public function restore(?string $field, User $user): BrandingSetting
    {
        $settings = $this->settings() ?? new BrandingSetting;
        $fields = array_keys($this->defaults());

        if ($field !== null && in_array($field, $fields, true)) {
            $oldPath = $settings->{$field};
            $settings->{$field} = null;
            $settings->updated_by = $user->getKey();
            $settings->save();
            $this->clearCache();
            $this->deleteReplacedFile($oldPath, null);

            return $settings->fresh('updater');
        }

        $oldPaths = [$settings->banner_path, $settings->sidebar_logo_path];
        foreach ($fields as $defaultField) {
            $settings->{$defaultField} = null;
        }
        $settings->updated_by = $user->getKey();
        $settings->save();
        $this->clearCache();

        foreach ($oldPaths as $path) {
            $this->deleteReplacedFile($path, null);
        }

        return $settings->fresh('updater');
    }

    public function settings(): ?BrandingSetting
    {
        if (! Schema::hasTable('branding_settings')) {
            return null;
        }

        return BrandingSetting::query()->with('updater')->first();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function deleteReplacedFile(?string $oldPath, ?string $newPath): void
    {
        if ($oldPath && $oldPath !== $newPath && str_starts_with($oldPath, 'branding/')) {
            Storage::disk('public')->delete($oldPath);
        }
    }
}
