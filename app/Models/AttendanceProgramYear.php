<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceProgramYear extends Model
{
    protected $guarded = [];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AttendanceProgram::class, 'program_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(AttendanceProgramCourse::class, 'program_year_id');
    }
}
