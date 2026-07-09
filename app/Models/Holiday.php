<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'library_holidays';

    protected $fillable = ['holiday_date', 'name'];
}
