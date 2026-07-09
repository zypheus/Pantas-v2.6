<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceFeedback extends Model
{
    protected $table = 'attendance_feedback';

    protected $fillable = [
        'student_id',
        'employee_id',
        'rating',
        'declined',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(AttendanceStudent::class, 'student_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(AttendanceEmployee::class, 'employee_id');
    }
}
