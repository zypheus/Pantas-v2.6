<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\ContrastRules;
use App\Services\ContrastValidator;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateBrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('developer') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $color = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];

        return [
            'banner' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120', 'dimensions:min_width=800,min_height=200,max_width=4000,max_height=2000'],
            'opac_banner' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120', 'dimensions:min_width=800,min_height=200,max_width=4000,max_height=2000'],
            'opac_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:min_width=64,min_height=64,max_width=1000,max_height=1000'],
            'opac_default_book_cover' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=200,min_height=300,max_width=2000,max_height=3000'],
            'sidebar_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:min_width=64,min_height=64,max_width=1000,max_height=1000'],
            'sidebar_brand_name' => ['nullable', 'string', 'max:60'],
            'sidebar_brand_subtitle' => ['nullable', 'string', 'max:100'],
            'sidebar_brand_text_color' => $color,
            'primary_color' => $color,
            'secondary_color' => $color,
            'accent_color' => $color,
            'sidebar_background_color' => $color,
            'sidebar_text_color' => $color,
            'sidebar_active_color' => $color,
            'sidebar_hover_background_color' => $color,
            'sidebar_hover_text_color' => $color,
            'button_color' => $color,
            'sidebar_footer_background_color' => $color,
            'table_header_color' => $color,
            'table_header_text_color' => $color,
            'table_border_color' => $color,
            'table_hover_color' => $color,
        ];
    }

    /**
     * Run contrast checks after validation passes — block saving if WCAG AA fails.
     */
    public function passedValidation(): void
    {
        $warnings = [];
        $data = $this->safe();

        foreach (ContrastRules::for('branding') as $rule) {
            $foreground = $rule['fgOverride'] ?? $data->input($rule['fg']);
            $background = $data->input($rule['bg']);

            if ($foreground === null || $background === null) {
                continue;
            }

            $foreground = strtoupper((string) $foreground);
            $background = strtoupper((string) $background);

            if (! preg_match('/^#[0-9A-F]{6}$/', $foreground) || ! preg_match('/^#[0-9A-F]{6}$/', $background)) {
                continue;
            }

            $ratio = ContrastValidator::ratio($foreground, $background);
            $threshold = $rule['largeText'] ? 3.0 : 4.5;

            if ($ratio < $threshold) {
                $warnings[] = [
                    'field' => $rule['fg'] ?? $rule['bg'],
                    'fgLabel' => $rule['fgLabel'],
                    'bgLabel' => $rule['bgLabel'],
                    'fgColor' => $foreground,
                    'bgColor' => $background,
                    'ratio' => round($ratio, 2),
                    'threshold' => $threshold,
                    'largeText' => $rule['largeText'],
                ];
            }
        }

        if ($warnings !== []) {
            session()->flash('contrast_warnings', $warnings);

            $errorMessages = [];
            foreach ($warnings as $w) {
                $textType = $w['largeText'] ? 'large text (≥3:1)' : 'normal text (≥4.5:1)';
                $errorMessages[$w['field']] = "{$w['fgLabel']} ({$w['fgColor']}) on {$w['bgLabel']} ({$w['bgColor']}) has only {$w['ratio']}:1 contrast ratio — minimum {$w['threshold']}:1 required for {$textType}.";
            }

            throw \Illuminate\Validation\ValidationException::withMessages($errorMessages);
        }
    }

    protected function prepareForValidation(): void
    {
        foreach (array_keys(config('branding.defaults', [])) as $field) {
            if (str_ends_with($field, '_color') && $this->filled($field)) {
                $this->merge([$field => strtoupper((string) $this->input($field))]);
            }
        }
    }
}