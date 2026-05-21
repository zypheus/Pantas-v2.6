<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;

class NormalizeStudentNames extends Command
{
    protected $signature = 'students:normalize';
    protected $description = 'Generate normalized_name for all students';

    public function handle()
    {
        $this->info('Normalizing student names...');

        Student::chunk(500, function ($students) {
            foreach ($students as $student) {

                $full = strtoupper($student->firstname . ' ' . $student->lastname);

                // Remove non letters
                $full = preg_replace('/[^A-Z\s]/', '', $full);

                // Remove single-letter words (middle initials)
                $full = preg_replace('/\b[A-Z]\b/', '', $full);

                // Remove spaces
                $full = preg_replace('/\s+/', '', $full);

                $student->normalized_name = $full;
                $student->save();
            }
        });

        $this->info('Done!');
    }
}