<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryHoliday extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];
}
