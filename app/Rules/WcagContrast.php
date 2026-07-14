<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\ContrastValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class WcagContrast implements ValidationRule
{
    /**
     * @param string      $foregroundField The name of the foreground color field
     * @param string      $backgroundField The name of the background color field
     * @param string|null $foregroundLabel Optional human-readable label for the foreground
     * @param string|null $backgroundLabel Optional human-readable label for the background
     * @param bool        $largeText       Whether this is large text (≥18pt or ≥14pt bold)
     */
    public function __construct(
        private readonly string $foregroundField,
        private readonly string $backgroundField,
        private readonly ?string $foregroundLabel = null,
        private readonly ?string $backgroundLabel = null,
        private readonly bool $largeText = false,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // We validate when both fields are present in the request
        $data = request()->all();
        $foreground = $data[$this->foregroundField] ?? null;
        $background = $data[$this->backgroundField] ?? null;

        if ($foreground === null || $background === null) {
            return; // Skip if either field is not being updated
        }

        $foreground = strtoupper((string) $foreground);
        $background = strtoupper((string) $background);

        if (! preg_match('/^#[0-9A-F]{6}$/', $foreground) || ! preg_match('/^#[0-9A-F]{6}$/', $background)) {
            return; // Skip if colors are not valid hex (other rules will catch this)
        }

        $ratio = ContrastValidator::ratio($foreground, $background);
        $threshold = $this->largeText ? 3.0 : 4.5;

        if ($ratio < $threshold) {
            $fgLabel = $this->foregroundLabel ?? $this->foregroundField;
            $bgLabel = $this->backgroundLabel ?? $this->backgroundField;

            $fail("The {$fgLabel} must have at least {$threshold}:1 contrast ratio against the {$bgLabel} (current ratio: ".number_format($ratio, 2).":1).");
        }
    }
}