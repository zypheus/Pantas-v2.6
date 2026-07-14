<?php

declare(strict_types=1);

namespace App\Services;

final class ContrastValidator
{
    /**
     * Calculate the WCAG relative luminance of a hex color.
     */
    public static function relativeLuminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $linearize = function (float $channel): float {
            return $channel <= 0.04045
                ? $channel / 12.92
                : (($channel + 0.055) / 1.055) ** 2.4;
        };

        return 0.2126 * $linearize($r) + 0.7152 * $linearize($g) + 0.0722 * $linearize($b);
    }

    /**
     * Calculate the contrast ratio between two hex colors (WCAG 2.1).
     */
    public static function ratio(string $hex1, string $hex2): float
    {
        $l1 = self::relativeLuminance($hex1);
        $l2 = self::relativeLuminance($hex2);

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Check if the contrast ratio passes WCAG AA.
     *
     * @param string $hex1     Foreground color
     * @param string $hex2     Background color
     * @param bool   $largeText If true, uses 3:1 threshold; otherwise 4.5:1
     */
    public static function passesAA(string $hex1, string $hex2, bool $largeText = false): bool
    {
        return self::ratio($hex1, $hex2) >= ($largeText ? 3.0 : 4.5);
    }

    /**
     * Check if the contrast ratio passes WCAG AAA.
     *
     * @param string $hex1     Foreground color
     * @param string $hex2     Background color
     * @param bool   $largeText If true, uses 4.5:1 threshold; otherwise 7:1
     */
    public static function passesAAA(string $hex1, string $hex2, bool $largeText = false): bool
    {
        return self::ratio($hex1, $hex2) >= ($largeText ? 4.5 : 7.0);
    }
}