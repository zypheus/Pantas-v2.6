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
        'sidebar_logo_path' => 'images/pantasLogo-box.png',
        'primary_color' => '#1E3A8A',
        'secondary_color' => '#0F766E',
        'accent_color' => '#B45309',
        'sidebar_background_color' => '#FFFFFF',
        'sidebar_text_color' => '#0F172A',
        'sidebar_active_color' => '#2563EB',
        'button_color' => '#1E3A8A',
    ],
];
