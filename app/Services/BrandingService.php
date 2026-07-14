<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BrandingSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

final class BrandingService
{
    public const CACHE_KEY = 'branding.active';

    /** @var list<string> */
    public const ASSET_FIELDS = [
        'banner_path',
        'opac_banner_path',
        'opac_logo_path',
        'opac_default_book_cover_path',
        'sidebar_logo_path',
        'login_modal_logo_path',
    ];

    /** @var list<string> */
    public const COLOR_FIELDS = [
        'primary_color',
        'secondary_color',
        'accent_color',
        'sidebar_background_color',
        'sidebar_text_color',
        'sidebar_brand_text_color',
        'sidebar_active_color',
        'sidebar_hover_background_color',
        'sidebar_hover_text_color',
        'button_color',
        'sidebar_footer_background_color',
        'table_header_color',
        'table_header_text_color',
        'table_border_color',
        'table_hover_color',
        'login_modal_left_background_color',
        'login_modal_background_color',
        'login_modal_text_color',
        'login_modal_button_color',
    ];

    /** @var list<string> */
    public const TEXT_FIELDS = [
        'sidebar_brand_name',
        'sidebar_brand_subtitle',
        'login_modal_welcome_label',
        'login_modal_portal_name',
        'login_modal_description',
        'login_modal_sign_in_heading',
        'login_modal_email_placeholder',
        'login_modal_password_placeholder',
    ];

    /** @var list<string> */
    public const LOGIN_MODAL_FIELDS = [
        'login_modal_logo_path',
        'login_modal_welcome_label',
        'login_modal_portal_name',
        'login_modal_description',
        'login_modal_sign_in_heading',
        'login_modal_email_placeholder',
        'login_modal_password_placeholder',
        'login_modal_left_background_color',
        'login_modal_background_color',
        'login_modal_text_color',
        'login_modal_button_color',
    ];

    public function __construct(
        private readonly AdminActivityLogger $activityLogger,
    ) {}

    /** @return array<string, mixed> */
    public function active(): array
    {
        $cached = Cache::rememberForever(self::CACHE_KEY, function (): array {
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

            foreach (self::ASSET_FIELDS as $field) {
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

        // A forever-cached payload may predate newly introduced branding fields.
        // Re-merge current defaults on every read so deployments remain upgrade-safe
        // even before the next branding update clears the cache.
        $active = $this->defaults();
        foreach (array_keys($active) as $field) {
            if (filled($cached[$field] ?? null)) {
                $active[$field] = $cached[$field];
            }
        }

        return $active + [
            'is_customized' => (bool) ($cached['is_customized'] ?? false),
            'updated_at' => $cached['updated_at'] ?? null,
            'updated_by' => $cached['updated_by'] ?? null,
        ];
    }

    /** @return array<string, mixed> */
    public function defaults(): array
    {
        return config('branding.defaults', []);
    }

    public function assetUrl(string $field): string
    {
        $path = (string) ($this->active()[$field] ?? $this->defaults()[$field] ?? '');

        return str_starts_with($path, 'branding/')
            ? '/branding-assets/'.substr($path, strlen('branding/'))
            : '/'.ltrim($path, '/');
    }

    /** @param array<string, mixed> $values */
    public function update(array $values, User $user, ?UploadedFile $banner = null, ?UploadedFile $opacBanner = null, ?UploadedFile $opacLogo = null, ?UploadedFile $opacDefaultBookCover = null, ?UploadedFile $logo = null, ?UploadedFile $loginModalLogo = null): BrandingSetting
    {
        $settings = $this->settings() ?? new BrandingSetting;
        $old = collect(array_keys($this->defaults()))
            ->mapWithKeys(fn (string $field): array => [$field => $settings->{$field}])
            ->all();
        $oldBanner = $settings->banner_path;
        $oldOpacBanner = $settings->opac_banner_path;
        $oldOpacLogo = $settings->opac_logo_path;
        $oldOpacDefaultBookCover = $settings->opac_default_book_cover_path;
        $oldLogo = $settings->sidebar_logo_path;
        $oldLoginModalLogo = $settings->login_modal_logo_path;
        $newBanner = $banner?->store('branding/banners', 'public');
        $newOpacBanner = $opacBanner?->store('branding/banners', 'public');
        $newOpacLogo = $opacLogo?->store('branding/opac', 'public');
        $newOpacDefaultBookCover = $opacDefaultBookCover?->store('branding/opac', 'public');
        $newLogo = $logo?->store('branding/logos', 'public');
        $newLoginModalLogo = $loginModalLogo?->store('branding/login-modal', 'public');

        try {
            DB::transaction(function () use ($settings, $values, $user, $newBanner, $newOpacBanner, $newOpacLogo, $newOpacDefaultBookCover, $newLogo, $newLoginModalLogo): void {
                foreach (self::COLOR_FIELDS as $field) {
                    if (array_key_exists($field, $values)) {
                        $settings->{$field} = $values[$field] ? strtoupper((string) $values[$field]) : null;
                    }
                }

                foreach (self::TEXT_FIELDS as $field) {
                    if (array_key_exists($field, $values)) {
                        $settings->{$field} = filled($values[$field]) ? trim((string) $values[$field]) : null;
                    }
                }

                if ($newBanner) {
                    $settings->banner_path = $newBanner;
                }

                if ($newOpacBanner) {
                    $settings->opac_banner_path = $newOpacBanner;
                }

                if ($newOpacLogo) {
                    $settings->opac_logo_path = $newOpacLogo;
                }

                if ($newOpacDefaultBookCover) {
                    $settings->opac_default_book_cover_path = $newOpacDefaultBookCover;
                }

                if ($newLogo) {
                    $settings->sidebar_logo_path = $newLogo;
                }

                if ($newLoginModalLogo) {
                    $settings->login_modal_logo_path = $newLoginModalLogo;
                }

                $settings->updated_by = $user->getKey();
                $settings->save();
            });
        } catch (\Throwable $exception) {
            $this->deleteCustomFile($newBanner);
            $this->deleteCustomFile($newOpacBanner);
            $this->deleteCustomFile($newOpacLogo);
            $this->deleteCustomFile($newOpacDefaultBookCover);
            $this->deleteCustomFile($newLogo);
            $this->deleteCustomFile($newLoginModalLogo);

            throw $exception;
        }

        $this->clearCache();
        $this->deleteReplacedFile($oldBanner, $settings->banner_path);
        $this->deleteReplacedFile($oldOpacBanner, $settings->opac_banner_path);
        $this->deleteReplacedFile($oldOpacLogo, $settings->opac_logo_path);
        $this->deleteReplacedFile($oldOpacDefaultBookCover, $settings->opac_default_book_cover_path);
        $this->deleteReplacedFile($oldLogo, $settings->sidebar_logo_path);
        $this->deleteReplacedFile($oldLoginModalLogo, $settings->login_modal_logo_path);

        $this->logUpdateActivity($old, $settings, $user);

        return $settings->fresh('updater');
    }

    /** @param array<string, mixed> $old */
    private function logUpdateActivity(array $old, BrandingSetting $settings, User $user): void
    {
        $changed = [];
        $fields = array_keys(config('branding.defaults', []));

        foreach ($fields as $field) {
            $newVal = $settings->{$field};
            if ((string) $old[$field] !== (string) $newVal) {
                $changed[] = $field;
            }
        }

        if ($changed === []) {
            return;
        }

        $title = 'Branding settings updated';
        $body = 'Changed: '.implode(', ', $changed).'.';
        $actionRoute = collect($changed)->every(fn (string $field): bool => in_array($field, self::LOGIN_MODAL_FIELDS, true))
            ? 'developer.login-modal.edit'
            : 'developer.branding.edit';

        $this->activityLogger->log(
            module: 'branding',
            type: 'branding_update',
            title: $title,
            body: $body,
            subject: $settings,
            actionUrl: route($actionRoute, absolute: false),
            icon: 'palette',
        );
    }

    private function deleteCustomFile(string|false|null $path): void
    {
        if (is_string($path) && str_starts_with($path, 'branding/')) {
            Storage::disk('public')->delete($path);
        }
    }

    public function restore(?string $field, User $user): BrandingSetting
    {
        $settings = $this->settings() ?? new BrandingSetting;
        $fields = array_keys($this->defaults());
        $oldPaths = [];

        if ($field !== null && in_array($field, $fields, true)) {
            $oldVal = $settings->{$field};
            $oldPath = $settings->{$field};
            $settings->{$field} = null;
            $settings->updated_by = $user->getKey();
            $settings->save();
            $this->clearCache();
            $this->deleteReplacedFile($oldPath, null);

            $this->activityLogger->log(
                module: 'branding',
                type: 'branding_restore',
                title: 'Branding value restored: '.$field,
                body: 'Restored '.$field.' to its original Pantas default.',
                subject: $settings,
                actionUrl: route('developer.branding.edit', absolute: false),
                icon: 'restore',
            );

            return $settings->fresh('updater');
        }

        $oldPaths = collect(self::ASSET_FIELDS)
            ->map(fn (string $assetField): ?string => $settings->{$assetField})
            ->all();
        foreach ($fields as $defaultField) {
            $settings->{$defaultField} = null;
        }
        $settings->updated_by = $user->getKey();
        $settings->save();
        $this->clearCache();

        foreach ($oldPaths as $path) {
            $this->deleteReplacedFile($path, null);
        }

        $this->activityLogger->log(
            module: 'branding',
            type: 'branding_restore_all',
            title: 'All branding restored to Pantas defaults',
            body: 'Full restoration of banner, sidebar logo, and all colors.',
            subject: $settings,
            actionUrl: route('developer.branding.edit', absolute: false),
            icon: 'restore',
        );

        return $settings->fresh('updater');
    }

    public function restoreLoginModal(?string $field, User $user): BrandingSetting
    {
        if ($field !== null) {
            if (! in_array($field, self::LOGIN_MODAL_FIELDS, true)) {
                throw new \InvalidArgumentException('The requested field is not a login modal setting.');
            }

            return $this->restoreField(
                $field,
                $user,
                route('developer.login-modal.edit', absolute: false),
            );
        }

        $settings = $this->settings() ?? new BrandingSetting;
        $oldLogo = $settings->login_modal_logo_path;

        DB::transaction(function () use ($settings, $user): void {
            foreach (self::LOGIN_MODAL_FIELDS as $loginModalField) {
                $settings->{$loginModalField} = null;
            }

            $settings->updated_by = $user->getKey();
            $settings->save();
        });

        $this->clearCache();
        $this->deleteReplacedFile($oldLogo, null);

        $this->activityLogger->log(
            module: 'branding',
            type: 'branding_restore_group',
            title: 'Login modal restored to Pantas defaults',
            body: 'Restored all login modal assets, text, and colors to their original Pantas defaults.',
            subject: $settings,
            actionUrl: route('developer.login-modal.edit', absolute: false),
            icon: 'restore',
        );

        return $settings->fresh('updater');
    }

    private function restoreField(string $field, User $user, string $actionUrl): BrandingSetting
    {
        $settings = $this->settings() ?? new BrandingSetting;
        $oldPath = $settings->{$field};
        $settings->{$field} = null;
        $settings->updated_by = $user->getKey();
        $settings->save();
        $this->clearCache();
        $this->deleteReplacedFile($oldPath, null);

        $this->activityLogger->log(
            module: 'branding',
            type: 'branding_restore',
            title: 'Branding value restored: '.$field,
            body: 'Restored '.$field.' to its original Pantas default.',
            subject: $settings,
            actionUrl: $actionUrl,
            icon: 'restore',
        );

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
