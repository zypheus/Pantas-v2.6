<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingEmployee extends Model
{
    protected $table = 'library_pending_employees';

    protected $fillable = [
        'firstname',
        'lastname',
        'middle_initial',
        'department',
        'position',
        'designation',
        'program',
        'year_start_work',
        'employee_id',
        'birth_date',
        'mobile_number',
        'employee_number',
        'sex',
        'civil_status',
        'blood_type',
        'tin_id_number',
        'philhealth_number',
        'sss_number',
        'hdmf_number',
        'qrcode',
        'role_id',
        'formal_picture',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        'emergency_address',
        'address',
        'employee_signature',
    ];

    // Optional: relationship to Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
