<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Auth\ModuleAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAttendanceAccess
{
    public function __construct(private readonly ModuleAccessService $moduleAccess) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user() && $this->moduleAccess->hasAttendanceAccess($request->user()), 403);
        $request->session()->put('active_module', ModuleAccessService::ATTENDANCE);

        return $next($request);
    }
}
