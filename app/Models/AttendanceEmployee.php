<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceEmployee extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'employee_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(AttendanceFeedback::class, 'employee_id');
    }
}
