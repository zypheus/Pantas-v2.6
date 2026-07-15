<?php

declare(strict_types=1);

namespace App\Services;

final class ContrastRules
{
    /**
     * @return array<string, list<array{fg: string, bg: string, fgLabel: string, bgLabel: string, largeText: bool}>>
     */
    public static function all(): array
    {
        return [
            'branding' => [
                ['fg' => 'sidebar_brand_text_color', 'bg' => 'sidebar_background_color', 'fgLabel' => 'Sidebar brand text', 'bgLabel' => 'Sidebar background', 'largeText' => false],
                ['fg' => 'sidebar_text_color', 'bg' => 'sidebar_background_color', 'fgLabel' => 'Sidebar text', 'bgLabel' => 'Sidebar background', 'largeText' => false],
                ['fg' => 'sidebar_hover_text_color', 'bg' => 'sidebar_hover_background_color', 'fgLabel' => 'Sidebar hover text', 'bgLabel' => 'Sidebar hover background', 'largeText' => false],
                ['fg' => 'table_header_text_color', 'bg' => 'table_header_color', 'fgLabel' => 'Table header text', 'bgLabel' => 'Table header background', 'largeText' => false],
            ],
            'login-modal' => [
                ['fg' => 'login_modal_text_color', 'bg' => 'login_modal_background_color', 'fgLabel' => 'Modal text', 'bgLabel' => 'Form background', 'largeText' => false],
                ['fg' => 'login_modal_text_color', 'bg' => 'login_modal_form_background_color', 'fgLabel' => 'Modal text', 'bgLabel' => 'Login form background', 'largeText' => false],
                ['fg' => 'login_modal_welcome_portal_color', 'bg' => 'login_modal_left_background_color', 'fgLabel' => 'Welcome and portal text', 'bgLabel' => 'Left panel background', 'largeText' => true],
                ['fg' => 'login_modal_description_color', 'bg' => 'login_modal_left_background_color', 'fgLabel' => 'Login description', 'bgLabel' => 'Left panel background', 'largeText' => false],
                ['fg' => null, 'bg' => 'login_modal_button_color', 'fgLabel' => 'Sign-in button text', 'bgLabel' => 'Sign-in button', 'largeText' => true, 'fgOverride' => '#FFFFFF'],
                ['fg' => null, 'bg' => 'login_modal_left_background_color', 'fgLabel' => 'Left panel text', 'bgLabel' => 'Left panel background', 'largeText' => false, 'fgOverride' => '#FFFFFF'],
            ],
            'register-modal' => [
                ['fg' => 'register_modal_attendance_text_color', 'bg' => 'register_modal_attendance_panel_color', 'fgLabel' => 'Attendance text', 'bgLabel' => 'Attendance panel', 'largeText' => false],
                ['fg' => 'register_modal_library_text_color', 'bg' => 'register_modal_library_panel_color', 'fgLabel' => 'Library text', 'bgLabel' => 'Library panel', 'largeText' => false],
                ['fg' => null, 'bg' => 'register_modal_attendance_submit_color', 'fgLabel' => 'Attendance submit button text', 'bgLabel' => 'Attendance submit button', 'largeText' => true, 'fgOverride' => '#FFFFFF'],
                ['fg' => null, 'bg' => 'register_modal_library_submit_color', 'fgLabel' => 'Library submit button text', 'bgLabel' => 'Library submit button', 'largeText' => true, 'fgOverride' => '#FFFFFF'],
            ],
        ];
    }

    /**
     * Get all rules for a given page type.
     *
     * @return list<array{fg: string|null, bg: string, fgLabel: string, bgLabel: string, largeText: bool, fgOverride?: string}>
     */
    public static function for(string $page): array
    {
        return self::all()[$page] ?? [];
    }
}
