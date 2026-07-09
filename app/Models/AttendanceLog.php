<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = ['student_id', 'employee_id', 'status', 'section', 'scanned_at'];

    protected $casts = [
        'scanned_at' => 'datetime',
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
