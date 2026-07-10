/**
 * sidebar.js — Pantas Dashboard Sidebar
 * Handles: collapsible group toggles, localStorage state, mobile overlay,
 * desktop collapse, and command palette.
 */
(function () {
    'use strict';

    const GROUPS_STORAGE_KEY = 'pantas_sidebar_open_groups';
    const SCROLL_STORAGE_KEY = 'pantas_sidebar_scroll_top';
    const COLLAPSED_STORAGE_KEY = 'pantas_sidebar_collapsed';

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    function getSavedGroups() {
        try {
            return JSON.parse(localStorage.getItem(GROUPS_STORAGE_KEY) || '[]');
        } catch (_) {
            return [];
        }
    }

    function saveGroups(openIds) {
        try {
            localStorage.setItem(GROUPS_STORAGE_KEY, JSON.stringify(openIds));
        } catch (_) { /* storage may be unavailable */ }
    }

    function getScrollTarget() {
        return document.querySelector('#sidebar .sidebar-nav') || document.getElementById('sidebar');
    }

    function getSavedScrollTop() {
        try {
            const scrollTop = Number(localStorage.getItem(SCROLL_STORAGE_KEY) || 0);
            return Number.isFinite(scrollTop) ? scrollTop : 0;
        } catch (_) {
            return 0;
        }
    }

    function saveScrollTop(scrollTop) {
        try {
            localStorage.setItem(SCROLL_STORAGE_KEY, String(scrollTop));
        } catch (_) { /* storage may be unavailable */ }
    }

    function isDesktop() {
        return window.matchMedia('(min-width: 768px)').matches;
    }

    function openGroup(label, items) {
        label.classList.add('open');
        items.classList.add('open');
        label.setAttribute('aria-expanded', 'true');
    }

    function closeGroup(label, items) {
        label.classList.remove('open');
        items.classList.remove('open');
        label.setAttribute('aria-expanded', 'false');
    }

    // -------------------------------------------------------------------------
    // Collapsible sidebar groups
    // -------------------------------------------------------------------------

    function initGroups() {
        const savedOpen = getSavedGroups();
        const groupLabels = document.querySelectorAll('.sidebar-group-label');

        groupLabels.forEach(function (label) {
            const groupId = label.dataset.group;
            const items = document.getElementById('sidebar-group-' + groupId);
            if (!items) return;

            // Restore from localStorage or open if group contains active link
            const hasActive = items.querySelector('.sidebar-link.active') !== null;
            const wasSaved = savedOpen.includes(groupId);

            if (hasActive || wasSaved) {
                openGroup(label, items);
            } else {
                closeGroup(label, items);
            }

            label.addEventListener('click', function () {
                const isOpen = label.classList.contains('open');
                if (isOpen) {
                    closeGroup(label, items);
                } else {
                    openGroup(label, items);
                }
                // Persist state
                const nowOpen = Array.from(document.querySelectorAll('.sidebar-group-label.open'))
                    .map(function (l) { return l.dataset.group; })
                    .filter(Boolean);
                saveGroups(nowOpen);
            });
        });
    }

    // -------------------------------------------------------------------------
    // Sidebar scroll restoration
    // -------------------------------------------------------------------------

    function initScrollRestoration() {
        const scroller = getScrollTarget();
        if (!scroller) {
            document.body.classList.remove('sidebar-hydrating');
            return;
        }

        requestAnimationFrame(function () {
            scroller.scrollTop = getSavedScrollTop();
            document.body.classList.remove('sidebar-hydrating');
        });

        scroller.addEventListener('scroll', function () {
            saveScrollTop(scroller.scrollTop);
        }, { passive: true });

        window.addEventListener('beforeunload', function () {
            saveScrollTop(scroller.scrollTop);
        });
    }

    // -------------------------------------------------------------------------
    // Mobile sidebar toggle
    // -------------------------------------------------------------------------

    function initMobileToggle() {
        const sidebar   = document.getElementById('sidebar');
        const toggle    = document.getElementById('sidebarToggle');
        const overlay   = document.getElementById('sidebarOverlay');

        if (!sidebar || !toggle || !overlay) return;

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            overlay.removeAttribute('aria-hidden');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            overlay.setAttribute('aria-hidden', 'true');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        toggle.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
    }

    // -------------------------------------------------------------------------
    // Desktop collapse
    // -------------------------------------------------------------------------

    function initDesktopCollapse() {
        const toggle = document.getElementById('desktopSidebarToggle');
        if (!toggle) return;

        function readCollapsed() {
            try {
                return localStorage.getItem(COLLAPSED_STORAGE_KEY) === 'true';
            } catch (_) {
                return false;
            }
        }

        function writeCollapsed(value) {
            try {
                localStorage.setItem(COLLAPSED_STORAGE_KEY, value ? 'true' : 'false');
            } catch (_) { /* storage may be unavailable */ }
        }

        function applyCollapsed(value) {
            document.body.classList.toggle('sidebar-collapsed', value && isDesktop());
            toggle.setAttribute('aria-expanded', value ? 'false' : 'true');
            toggle.setAttribute('aria-label', value ? 'Expand sidebar' : 'Collapse sidebar');
        }

        applyCollapsed(readCollapsed());

        toggle.addEventListener('click', function () {
            const next = !document.body.classList.contains('sidebar-collapsed');
            applyCollapsed(next);
            writeCollapsed(next);
        });

        window.addEventListener('resize', function () {
            applyCollapsed(readCollapsed());
        });
    }

    // -------------------------------------------------------------------------
    // Command palette
    // -------------------------------------------------------------------------

    function initCommandPalette() {
        const palette = document.getElementById('commandPalette');
        const input = document.getElementById('commandSearchInput');
        const empty = document.getElementById('commandEmpty');
        const openers = document.querySelectorAll('[data-command-open]');
        const items = Array.from(document.querySelectorAll('[data-command-item]'));

        if (!palette || !input) return;

        function filterItems() {
            const query = input.value.trim().toLowerCase();
            let visibleCount = 0;

            items.forEach(function (item) {
                const haystack = item.dataset.search || item.textContent.toLowerCase();
                const visible = !query || haystack.includes(query);
                item.hidden = !visible;
                if (visible) visibleCount += 1;
            });

            if (empty) {
                empty.classList.toggle('show', visibleCount === 0);
            }
        }

        function openPalette() {
            palette.classList.add('open');
            palette.removeAttribute('aria-hidden');
            document.body.style.overflow = 'hidden';
            input.value = '';
            filterItems();
            requestAnimationFrame(function () {
                input.focus();
            });
        }

        function closePalette() {
            palette.classList.remove('open');
            palette.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        openers.forEach(function (opener) {
            opener.addEventListener('click', openPalette);
        });

        input.addEventListener('input', filterItems);

        palette.addEventListener('click', function (event) {
            if (event.target === palette) {
                closePalette();
            }
        });

        document.addEventListener('keydown', function (event) {
            const isCommandShortcut = (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k';

            if (isCommandShortcut) {
                event.preventDefault();
                openPalette();
                return;
            }

            if (event.key === 'Escape' && palette.classList.contains('open')) {
                closePalette();
            }
        });
    }

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    document.addEventListener('DOMContentLoaded', function () {
        initGroups();
        initScrollRestoration();
        initMobileToggle();
        initDesktopCollapse();
        initCommandPalette();
    });

}());
