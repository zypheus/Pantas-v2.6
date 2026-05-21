<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceFeedback extends Model
{
    protected $table = 'attendance_feedback'; // because your table is NOT plural

    protected $fillable = [
        'student_id',
        'rating',
        'declined'
    ];

    // 🔥 ADD THIS
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}