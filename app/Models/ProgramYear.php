<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramYear extends Model
{
    protected $fillable = ['program_id', 'year_level'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(ProgramCourse::class);
    }
}
