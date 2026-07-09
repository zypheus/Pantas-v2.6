<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Auth\ModuleAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class ModuleSelectionController extends Controller
{
    public function __construct(private readonly ModuleAccessService $moduleAccess) {}

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module' => ['required', 'string', Rule::in([
                ModuleAccessService::SUPER_ADMIN,
                ModuleAccessService::ATTENDANCE,
                ModuleAccessService::LIBRARY,
            ])],
        ]);

        $module = $validated['module'];
        $user = $request->user();

        abort_unless($user && $this->moduleAccess->canAccessModule($user, $module), 403);

        $request->session()->put('active_module', $module);

        return redirect()->route($this->moduleAccess->dashboardRouteForModule($user, $module));
    }
}
