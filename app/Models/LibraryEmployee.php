<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryEmployee extends Model
{
    protected $guarded = [];

    public function role(): BelongsTo
    {
        return $this->belongsTo(LibraryRole::class, 'role_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookLogs(): HasMany
    {
        return $this->hasMany(LibraryBookLog::class, 'employee_id');
    }

    public function roomReservations(): HasMany
    {
        return $this->hasMany(LibraryRoomReservation::class, 'employee_id');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(LibraryAttendanceLog::class, 'employee_id');
    }
}
