<style id="pantas-branding-overrides">
    :root:not([data-theme]),
    [data-theme="pantas-default"] {
        --brand-primary: {{ $activeBranding['primary_color'] }};
        --brand-accent: {{ $activeBranding['accent_color'] }};
        --brand-button-bg: {{ $activeBranding['button_color'] }};
        --brand-nav-link: {{ $activeBranding['primary_color'] }};
        --brand-nav-link-active: {{ $activeBranding['sidebar_text_color'] }};
        --shell-primary: {{ $activeBranding['primary_color'] }};
        --shell-action: {{ $activeBranding['sidebar_active_color'] }};
        --shell-chart-palette: {{ $activeBranding['primary_color'] }}, {{ $activeBranding['secondary_color'] }}, {{ $activeBranding['accent_color'] }}, #6D28D9, #B91C1C, #047857;
        --branding-sidebar-background: {{ $activeBranding['sidebar_background_color'] }};
        --branding-sidebar-text: {{ $activeBranding['sidebar_text_color'] }};
        --branding-sidebar-active: {{ $activeBranding['sidebar_active_color'] }};
        --branding-button: {{ $activeBranding['button_color'] }};
    }

    :root:not([data-theme]) #sidebar,
    [data-theme="pantas-default"] #sidebar {
        background: var(--branding-sidebar-background);
        color: var(--branding-sidebar-text);
    }

    :root:not([data-theme]) #sidebar .sidebar-link.active,
    [data-theme="pantas-default"] #sidebar .sidebar-link.active {
        background: var(--branding-sidebar-active);
        color: #FFFFFF;
    }

    :root:not([data-theme]) .btn-primary,
    [data-theme="pantas-default"] .btn-primary {
        border-color: var(--branding-button);
        background-color: var(--branding-button);
    }
</style>
