<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Auth\ModuleAccessService;
use App\Services\BrandingService;
use App\Services\DashboardMetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly ModuleAccessService $moduleAccess,
        private readonly DashboardMetricsService $metrics,
    ) {}

    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();
        $activeModule = $request->session()->get('active_module');

        if ($user && is_string($activeModule) && $this->moduleAccess->canAccessModule($user, $activeModule)) {
            return redirect()->route($this->moduleAccess->dashboardRouteForModule($user, $activeModule));
        }

        $request->session()->forget('active_module');

        try {
            $defaultModule = $this->moduleAccess->defaultModule($user);
            $request->session()->put('active_module', $defaultModule);

            return redirect()->route($this->moduleAccess->dashboardRouteForModule($user, $defaultModule));
        } catch (\InvalidArgumentException) {
            abort(403, 'No dashboard available for your role.');
        }
    }

    public function superAdmin(): View
    {
        return view('dashboards.super-admin', [
            'title' => 'Super Admin Dashboard',
            'summary' => 'System-wide access for staff accounts, Library administration, and Attendance administration.',
        ] + $this->metrics->superAdmin());
    }

    public function developer(BrandingService $branding): View
    {
        return view('dashboards.developer', [
            'title' => 'Developer Dashboard',
            'summary' => 'Isolated workspace for Pantas branding configuration.',
            'branding' => $branding->active(),
            'bannerUrl' => $branding->assetUrl('banner_path'),
            'logoUrl' => $branding->assetUrl('sidebar_logo_path'),
        ]);
    }

    public function libraryAdmin(): View
    {
        return view('dashboards.library-admin', [
            'title' => 'Library Admin Dashboard',
            'summary' => 'Library administration workspace for catalog, circulation, patrons, rooms, reports, and settings.',
        ] + $this->metrics->libraryAdmin());
    }

    public function libraryStaff(): View
    {
        return view('dashboards.library-staff', [
            'title' => 'Library Staff Dashboard',
            'summary' => 'Library staff workspace for day-to-day catalog and patron workflows.',
        ] + $this->metrics->libraryStaff());
    }

    public function attendanceAdmin(): View
    {
        return view('dashboards.attendance-admin', [
            'title' => 'Attendance Admin Dashboard',
            'summary' => 'Attendance administration workspace for school attendance patrons, logs, reports, and settings.',
        ] + $this->metrics->attendanceAdmin());
    }

    public function attendanceStaff(): View
    {
        return view('dashboards.attendance-staff', [
            'title' => 'Attendance Staff Dashboard',
            'summary' => 'Attendance staff workspace for operational scanning and attendance support.',
        ] + $this->metrics->attendanceStaff());
    }
}
