<?php

declare(strict_types=1);

namespace App\Http\Requests;

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
            'banner' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'sidebar_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'primary_color' => $color,
            'secondary_color' => $color,
            'accent_color' => $color,
            'sidebar_background_color' => $color,
            'sidebar_text_color' => $color,
            'sidebar_active_color' => $color,
            'button_color' => $color,
        ];
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
