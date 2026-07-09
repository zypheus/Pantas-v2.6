<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'library_programs';

    protected $fillable = ['program_code', 'program_name', 'total_years'];

    public function years()
    {
        return $this->hasMany(ProgramYear::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($program) {
            // Delete all related years and their courses
            foreach ($program->years as $year) {
                $year->courses()->delete();
                $year->delete();
            }
        });
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'library_book_program');
    }
}
