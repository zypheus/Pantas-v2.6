<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceProgram extends Model
{
    protected $guarded = [];

    public function years(): HasMany
    {
        return $this->hasMany(AttendanceProgramYear::class, 'program_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(AttendanceProgramCourse::class, 'program_id');
    }
}
