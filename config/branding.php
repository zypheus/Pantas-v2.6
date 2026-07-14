<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Branding stylesheet (per school / per subdomain)
    |--------------------------------------------------------------------------
    |
    | Point this to a file under /public (served via asset()).
    | Example: BRANDING_CSS=branding/usm.css
    |
    */
    'css_path' => env('BRANDING_CSS', 'branding/branding.css'),

    'defaults' => [
        'banner_path' => 'images/Bannernew.jpg',
        'opac_banner_path' => 'images/Bannernew.jpg',
        'opac_logo_path' => 'images/d.png',
        'opac_default_book_cover_path' => 'images/defaultBook.png',
        'sidebar_logo_path' => 'images/pantasLogo-box.png',
        'login_modal_logo_path' => 'img/pantas-10.png',
        'sidebar_brand_name' => 'Pantas',
        'sidebar_brand_subtitle' => 'Admin Portal',
        'login_modal_welcome_label' => 'Welcome to',
        'login_modal_portal_name' => 'PANTAS Portal',
        'login_modal_description' => 'Sign in to access the Library and Attendance systems.',
        'login_modal_sign_in_heading' => 'Sign in to your account',
        'login_modal_email_placeholder' => 'staff@pantas.edu.ph',
        'login_modal_password_placeholder' => 'Enter password',
        'sidebar_brand_text_color' => '#0F172A',
        'primary_color' => '#1E3A8A',
        'secondary_color' => '#0F766E',
        'accent_color' => '#B45309',
        'sidebar_background_color' => '#FFFFFF',
        'sidebar_text_color' => '#0F172A',
        'sidebar_active_color' => '#2563EB',
        'sidebar_hover_background_color' => '#F1F5F9',
        'sidebar_hover_text_color' => '#0F172A',
        'button_color' => '#1E3A8A',
        'sidebar_footer_background_color' => '#FFFFFF',
        'table_header_color' => '#F1F5F9',
        'table_header_text_color' => '#475569',
        'table_border_color' => '#E2E8F0',
        'table_hover_color' => '#EFF6FF',
        'login_modal_left_background_color' => '#123C8C',
        'login_modal_background_color' => '#FFFFFF',
        'login_modal_text_color' => '#172033',
        'login_modal_button_color' => '#123C8C',
    ],
];
