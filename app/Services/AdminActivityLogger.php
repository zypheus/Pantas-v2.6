<?php

namespace App\Services;

use App\Models\AdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminActivityLogger
{
    public function log(
        string $module,
        string $type,
        string $title,
        ?string $body = null,
        ?Model $subject = null,
        ?string $actionUrl = null,
        ?string $icon = null,
    ): ?AdminActivity {
        if (! Schema::hasTable('admin_activities')) {
            return null;
        }

        return AdminActivity::query()->create([
            'user_id' => Auth::id(),
            'module' => $module,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'icon' => $icon,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
        ]);
    }
}
