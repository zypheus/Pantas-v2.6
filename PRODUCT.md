# PANTAS — Product Definition

## What It Is
PANTAS is a library and attendance management system for the University of Southern Mindanao (USM). It provides an integrated admin/staff shell for managing library catalog operations, circulation, patron records, room reservations, attendance tracking, and system administration.

## Who Uses It
- **Super Admins** — System-wide access to staff accounts, library administration, and attendance administration.
- **Library Admins** — Full library management: catalog, MARC frameworks, circulation, fines, patrons, rooms, reports, and settings.
- **Library Staff** — Day-to-day library operations: catalog lookup, OPAC, kiosk, room scheduling, and library attendance scanning.
- **Attendance Admins** — School attendance management: patron records, logs, reports, feedback settings, and pending registrations.
- **Attendance Staff** — Operational scanning and daily attendance support.

## Purpose
To provide a modern, unified operational dashboard for USM library and attendance staff, replacing legacy workflows with a cohesive, role-based interface that surfaces the most relevant metrics, actions, and data for each user type.

## Design Personality
- **Modern** — Clean, minimal, enterprise-grade UI with thoughtful spacing, typography, and interaction patterns.
- **Bold** — Confident use of colour, contrast, and visual hierarchy. The PANTAS brand (navy + gold) anchors the experience.
- **Precise** — Every element has purpose. Data is surfaced with clarity. Actions are predictable and safe.

## Accessibility Baseline
- WCAG AA compliance target.
- Sufficient colour contrast ratios.
- Keyboard-navigable interfaces.
- Screen-reader-friendly semantic markup.
- Support for `prefers-reduced-motion`.

## Theme System
- Authenticated admin/staff shell supports account-level theme customization.
- Themes are CSS custom property overrides applied via `[data-theme="..."]` on the `<html>` element.
- Theme picker provides instant preview without saving; explicit save commits to the database.
- Theme preference persists across sessions and is restored on login.
- Public pages (OPAC, login, registration, kiosk, PDFs, emails) are unaffected by theme selection.
- The original PANTAS palette remains the default and safest fallback.
