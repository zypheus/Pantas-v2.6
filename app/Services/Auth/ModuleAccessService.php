<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use InvalidArgumentException;

final class ModuleAccessService
{
    public const ATTENDANCE = 'attendance';

    public const LIBRARY = 'library';

    public const SUPER_ADMIN = 'super-admin';

    /** @var list<string> */
    public const STAFF_ROLES = [
        'super_admin',
        'library_admin',
        'library_staff',
        'attendance_admin',
        'attendance_staff',
    ];

    public function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function hasLibraryAccess(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $user->hasAnyRole(['library_admin', 'library_staff'])
            || in_array($user->getRawOriginal('role'), ['admin', 'staff'], true);
    }

    public function hasLibraryAdminAccess(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $user->hasRole('library_admin')
            || $user->getRawOriginal('role') === 'admin';
    }

    public function hasAttendanceAccess(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $user->hasAnyRole(['attendance_admin', 'attendance_staff']);
    }

    public function hasAttendanceAdminAccess(User $user): bool
    {
        return $this->isSuperAdmin($user) || $user->hasRole('attendance_admin');
    }

    /** @return list<string> */
    public function availableModules(User $user): array
    {
        $modules = [];

        if ($this->hasAttendanceAccess($user)) {
            $modules[] = self::ATTENDANCE;
        }

        if ($this->hasLibraryAccess($user)) {
            $modules[] = self::LIBRARY;
        }

        if ($this->isSuperAdmin($user)) {
            array_unshift($modules, self::SUPER_ADMIN);
        }

        return array_values(array_unique($modules));
    }

    public function canAccessModule(User $user, string $module): bool
    {
        return match ($module) {
            self::SUPER_ADMIN => $this->isSuperAdmin($user),
            self::ATTENDANCE => $this->hasAttendanceAccess($user),
            self::LIBRARY => $this->hasLibraryAccess($user),
            default => false,
        };
    }

    public function defaultModule(User $user): string
    {
        if ($this->isSuperAdmin($user)) {
            return self::SUPER_ADMIN;
        }

        if ($this->hasAttendanceAccess($user)) {
            return self::ATTENDANCE;
        }

        if ($this->hasLibraryAccess($user)) {
            return self::LIBRARY;
        }

        throw new InvalidArgumentException('The user has no assigned staff module.');
    }

    public function dashboardRouteForModule(User $user, string $module): string
    {
        if (! $this->canAccessModule($user, $module)) {
            throw new InvalidArgumentException('The user cannot access the selected module.');
        }

        return match ($module) {
            self::SUPER_ADMIN => 'dashboard.super-admin',
            self::ATTENDANCE => $this->hasAttendanceAdminAccess($user)
                ? 'dashboard.attendance-admin'
                : 'dashboard.attendance-staff',
            self::LIBRARY => $this->hasLibraryAdminAccess($user)
                ? 'dashboard.library-admin'
                : 'dashboard.library-staff',
            default => throw new InvalidArgumentException('Unknown staff module.'),
        };
    }

    public function defaultDashboardRoute(User $user): string
    {
        return $this->dashboardRouteForModule($user, $this->defaultModule($user));
    }
}
