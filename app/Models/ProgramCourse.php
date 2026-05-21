<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramCourse extends Model
{
    protected $fillable = [
        'program_year_id', 'course_code', 'course_name'
    ];

    public function year()
    {
        return $this->belongsTo(ProgramYear::class, 'program_year_id');
    }

    public function program()
    {
        return $this->hasOneThrough(
            Program::class,
            ProgramYear::class,
            'id',           // ProgramYear PK
            'id',           // Program PK
            'program_year_id', // FK on ProgramCourse
            'program_id'    // FK on ProgramYear
        );
    }

    public function ebooks()
    {
        return $this->hasMany(Ebook::class, 'course_id'); // make sure ebooks has course_id
    }
}
