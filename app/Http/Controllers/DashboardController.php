<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Auth\ModuleAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly ModuleAccessService $moduleAccess) {}

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
        return view('dashboards.module', [
            'title' => 'Super Admin Dashboard',
            'module' => ModuleAccessService::SUPER_ADMIN,
            'summary' => 'System-wide access for staff accounts, Library administration, and Attendance administration.',
        ]);
    }

    public function libraryAdmin(): View
    {
        return view('dashboards.module', [
            'title' => 'Library Admin Dashboard',
            'module' => ModuleAccessService::LIBRARY,
            'summary' => 'Library administration workspace for catalog, circulation, patrons, rooms, reports, and settings.',
        ]);
    }

    public function libraryStaff(): View
    {
        return view('dashboards.module', [
            'title' => 'Library Staff Dashboard',
            'module' => ModuleAccessService::LIBRARY,
            'summary' => 'Library staff workspace for day-to-day catalog and patron workflows.',
        ]);
    }

    public function attendanceAdmin(): View
    {
        return view('dashboards.module', [
            'title' => 'Attendance Admin Dashboard',
            'module' => ModuleAccessService::ATTENDANCE,
            'summary' => 'Attendance administration workspace for school attendance patrons, logs, reports, and settings.',
        ]);
    }

    public function attendanceStaff(): View
    {
        return view('dashboards.module', [
            'title' => 'Attendance Staff Dashboard',
            'module' => ModuleAccessService::ATTENDANCE,
            'summary' => 'Attendance staff workspace for operational scanning and attendance support.',
        ]);
    }
}
