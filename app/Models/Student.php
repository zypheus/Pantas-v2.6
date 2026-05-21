<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_number',
        'lastname',
        'firstname',
        'middle_initial',
        'birthday',
        'qrcode',
        'course',
        'year',
        'profile_picture',
        'student_signature',
        'mobile_number',
        'address',
        'emergency_person',
        'emergency_relationship',
        'emergency_number',
        'emergency_address',
    ];

    public function editRequests()
    {
        return $this->hasMany(StudentEditRequest::class);
    }

    public function bookLogs()
    {
        return $this->hasMany(BookLog::class, 'student_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'student_id');
    }
    
   
}
