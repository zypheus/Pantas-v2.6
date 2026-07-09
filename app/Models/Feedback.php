<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'library_feedback';

    protected $fillable = ['name', 'email', 'comments'];
}
