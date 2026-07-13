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
        --branding-sidebar-hover-background: {{ $activeBranding['sidebar_hover_background_color'] }};
        --branding-sidebar-hover-text: {{ $activeBranding['sidebar_hover_text_color'] }};
        --branding-button: {{ $activeBranding['button_color'] }};
        --branding-sidebar-footer-background: {{ $activeBranding['sidebar_footer_background_color'] }};
        --branding-table-header: {{ $activeBranding['table_header_color'] }};
        --branding-table-header-text: {{ $activeBranding['table_header_text_color'] }};
        --branding-table-border: {{ $activeBranding['table_border_color'] }};
        --branding-table-hover: {{ $activeBranding['table_hover_color'] }};
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

    :root:not([data-theme]) #sidebar .sidebar-link:not(.active):hover,
    :root:not([data-theme]) #sidebar .sidebar-direct-link:not(.active):hover,
    [data-theme="pantas-default"] #sidebar .sidebar-link:not(.active):hover,
    [data-theme="pantas-default"] #sidebar .sidebar-direct-link:not(.active):hover {
        background: var(--branding-sidebar-hover-background);
        color: var(--branding-sidebar-hover-text);
        transform: translateX(2px);
    }

    :root:not([data-theme]) .btn-primary,
    [data-theme="pantas-default"] .btn-primary {
        border-color: var(--branding-button);
        background-color: var(--branding-button);
    }

    :root:not([data-theme]) #sidebar .sidebar-footer-actions,
    [data-theme="pantas-default"] #sidebar .sidebar-footer-actions {
        background: var(--branding-sidebar-footer-background);
    }

    :root:not([data-theme]) .sidebar-page-body .table,
    [data-theme="pantas-default"] .sidebar-page-body .table {
        --bs-table-border-color: var(--branding-table-border);
        --bs-table-hover-bg: var(--branding-table-hover);
        border-color: var(--branding-table-border);
    }

    :root:not([data-theme]) .sidebar-page-body .table thead th,
    [data-theme="pantas-default"] .sidebar-page-body .table thead th {
        border-color: var(--branding-table-border);
        background: var(--branding-table-header);
        color: var(--branding-table-header-text);
    }

    :root:not([data-theme]) .sidebar-page-body .table tbody tr,
    [data-theme="pantas-default"] .sidebar-page-body .table tbody tr {
        border-color: var(--branding-table-border);
    }

    :root:not([data-theme]) .sidebar-page-body .table tbody tr:hover,
    [data-theme="pantas-default"] .sidebar-page-body .table tbody tr:hover {
        background: var(--branding-table-hover);
    }
</style>
