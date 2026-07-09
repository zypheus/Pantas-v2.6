<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceProgramCourse extends Model
{
    protected $guarded = [];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AttendanceProgram::class, 'program_id');
    }

    public function programYear(): BelongsTo
    {
        return $this->belongsTo(AttendanceProgramYear::class, 'program_year_id');
    }
}
