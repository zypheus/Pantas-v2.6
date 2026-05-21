<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcField extends Model
{
    protected $fillable = [
        'tag',
        'subfield',
        'label',
        'repeatable',
        'input_type',
        'options',
    ];

    protected $casts = [
        'repeatable' => 'boolean',
        'options' => 'array',
    ];
}

