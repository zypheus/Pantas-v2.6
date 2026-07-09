<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryAttendanceFeedback extends Model
{
    protected $table = 'library_attendance_feedbacks';

    protected $guarded = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(LibraryStudent::class, 'student_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(LibraryEmployee::class, 'employee_id');
    }
}
