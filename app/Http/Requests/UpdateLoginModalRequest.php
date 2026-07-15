<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\WcagContrast;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateLoginModalRequest extends FormRequest
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
            'login_modal_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:min_width=64,min_height=64,max_width=1000,max_height=1000'],
            'login_modal_welcome_label' => ['nullable', 'string', 'max:80'],
            'login_modal_portal_name' => ['nullable', 'string', 'max:100'],
            'login_modal_description' => ['nullable', 'string', 'max:255'],
            'login_modal_sign_in_heading' => ['nullable', 'string', 'max:120'],
            'login_modal_email_placeholder' => ['nullable', 'string', 'max:120'],
            'login_modal_password_placeholder' => ['nullable', 'string', 'max:120'],
            'login_modal_left_background_color' => $color,
            'login_modal_welcome_portal_color' => $color,
            'login_modal_description_color' => $color,
            'login_modal_background_color' => $color,
            'login_modal_form_background_color' => $color,
            'login_modal_form_border_color' => $color,
            'login_modal_text_color' => $color,
            'login_modal_button_color' => $color,
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $data = $this->safe();

            // Login modal text against form background
            if ($data->has('login_modal_text_color') && $data->has('login_modal_background_color')) {
                $rule = new WcagContrast('login_modal_text_color', 'login_modal_background_color', 'Modal text', 'Form background');
                $rule->validate('login_modal_text_color', $data->input('login_modal_text_color'), function (string $message) use ($validator): void {
                    $validator->errors()->add('login_modal_text_color', $message);
                });
            }

            // Login form text against the inner form-card background
            if ($data->has('login_modal_text_color') && $data->has('login_modal_form_background_color')) {
                $rule = new WcagContrast('login_modal_text_color', 'login_modal_form_background_color', 'Modal text', 'Login form background');
                $rule->validate('login_modal_text_color', $data->input('login_modal_text_color'), function (string $message) use ($validator): void {
                    $validator->errors()->add('login_modal_text_color', $message);
                });
            }

            foreach ([
                'login_modal_welcome_portal_color' => 'Welcome and portal text',
                'login_modal_description_color' => 'Login description',
            ] as $field => $label) {
                if ($data->has($field) && $data->has('login_modal_left_background_color')) {
                    $rule = new WcagContrast($field, 'login_modal_left_background_color', $label, 'Left panel background');
                    $rule->validate($field, $data->input($field), function (string $message) use ($validator, $field): void {
                        $validator->errors()->add($field, $message);
                    });
                }
            }

            // Button text (#FFFFFF assumed) against button background
            if ($data->has('login_modal_button_color')) {
                $rule = new WcagContrast(
                    foregroundField: 'login_modal_button_color',
                    backgroundField: 'login_modal_button_color',
                    foregroundLabel: 'Sign-in button text',
                    backgroundLabel: 'Sign-in button',
                );
                // We check white (#FFFFFF) against the button color
                $ratio = \App\Services\ContrastValidator::ratio('#FFFFFF', $data->input('login_modal_button_color'));
                if ($ratio < 3.0) {
                    $validator->errors()->add('login_modal_button_color', 'The Sign-in button must have at least 3:1 contrast ratio against white button text (current ratio: '.number_format($ratio, 2).':1).');
                }
            }

            // Left panel text (white assumed) against left panel background
            if ($data->has('login_modal_left_background_color')) {
                $ratio = \App\Services\ContrastValidator::ratio('#FFFFFF', $data->input('login_modal_left_background_color'));
                if ($ratio < 4.5) {
                    $validator->errors()->add('login_modal_left_background_color', 'The Left panel background must have at least 4.5:1 contrast ratio against white text (current ratio: '.number_format($ratio, 2).':1).');
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        foreach (['login_modal_left_background_color', 'login_modal_welcome_portal_color', 'login_modal_description_color', 'login_modal_background_color', 'login_modal_form_background_color', 'login_modal_form_border_color', 'login_modal_text_color', 'login_modal_button_color'] as $field) {
            if ($this->filled($field)) {
                $this->merge([$field => strtoupper(trim((string) $this->input($field)))]);
            }
        }

        foreach (['login_modal_welcome_label', 'login_modal_portal_name', 'login_modal_description', 'login_modal_sign_in_heading', 'login_modal_email_placeholder', 'login_modal_password_placeholder'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }
    }
}
