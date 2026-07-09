<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AdminActivity;
use Illuminate\View\View;

final class AdminActivityController extends Controller
{
    public function index(): View
    {
        return view('admin.activities.index', [
            'activities' => AdminActivity::query()
                ->with('user')
                ->latest()
                ->paginate(20),
        ]);
    }
}
