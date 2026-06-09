/**
 * sidebar.js — Pantas Dashboard Sidebar
 * Handles: collapsible group toggles, localStorage state, mobile overlay.
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'pantas_sidebar_open_groups';

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    function getSavedGroups() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch (_) {
            return [];
        }
    }

    function saveGroups(openIds) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(openIds));
        } catch (_) { /* storage may be unavailable */ }
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
    // Boot
    // -------------------------------------------------------------------------

    document.addEventListener('DOMContentLoaded', function () {
        initGroups();
        initMobileToggle();
    });

}());
