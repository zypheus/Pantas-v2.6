<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'employee_id',
        'formal_picture',
        'department',
        'firstname',
        'lastname',
        'position',
        'employee_number',
        'birth_date',
        'sex',
        'tin_id_number',
        'philhealth_number',
        'civil_status',
        'blood_type',
        'sss_number',
        'hdmf_number',
        'qrcode',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'address',
        'emergency_contact_number',
        'employee_signature',
    ];

    // Optional relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
