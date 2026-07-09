<?php

namespace Database\Seeders;

use App\Models\Ebook;
use App\Models\Program;
use App\Models\ProgramCourse;
use App\Models\ProgramYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProspectusSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearProspectus();
        $this->seedAll();

        $programCount = Program::count();
        $courseCount = ProgramCourse::count();
        $this->command?->info("Prospectus seeded: {$programCount} programs, {$courseCount} courses.");
    }

    private function clearProspectus(): void
    {
        DB::table('library_book_program')->delete();
        Ebook::query()->update(['program_id' => null, 'course_id' => null]);
        ProgramCourse::query()->delete();
        ProgramYear::query()->delete();
        Program::query()->delete();

        $this->command?->info('Cleared programs, years, courses, and book–program links.');
    }

    private function seedAll(): void
    {
        $definitions = $this->programDefinitions();

        foreach ($definitions as $def) {
            $this->seedProgram(
                $def['program_code'],
                $def['program_name'],
                $def['total_years'],
                $def['courses_by_year']
            );
        }
    }

    /**
     * @param  array<int, list<array{0: string, 1: string}>>  $coursesByYear
     */
    private function seedProgram(string $code, string $name, int $totalYears, array $coursesByYear): void
    {
        $program = Program::create([
            'program_code' => $code,
            'program_name' => $name,
            'total_years' => $totalYears,
        ]);

        for ($year = 1; $year <= $totalYears; $year++) {
            $programYear = ProgramYear::create([
                'program_id' => $program->id,
                'year_level' => $year,
            ]);

            foreach ($coursesByYear[$year] ?? [] as [$courseCode, $courseName]) {
                ProgramCourse::create([
                    'program_year_id' => $programYear->id,
                    'course_code' => $courseCode,
                    'course_name' => $courseName,
                ]);
            }
        }
    }

    /**
     * @return list<array{program_code: string, program_name: string, total_years: int, courses_by_year: array<int, list<array{0: string, 1: string}>>}>
     */
    private function programDefinitions(): array
    {
        $ge1 = [
            ['GE101', 'Understanding the Self'],
            ['GE102', 'Readings in Philippine History'],
            ['GE103', 'Mathematics in the Modern World'],
            ['GE104', 'Purposive Communication'],
            ['PE101', 'Physical Education 1'],
            ['NSTP101', 'National Service Training Program 1'],
        ];

        $ge2 = [
            ['GE201', 'Ethics'],
            ['GE202', 'Art Appreciation'],
            ['GE203', 'Science, Technology and Society'],
            ['GE204', 'The Contemporary World'],
            ['PE201', 'Physical Education 2'],
            ['NSTP201', 'National Service Training Program 2'],
        ];

        return [
            [
                'program_code' => 'BSCS',
                'program_name' => 'Bachelor of Science in Computer Science',
                'total_years' => 4,
                'courses_by_year' => [
                    1 => array_merge($ge1, [
                        ['CS101', 'Introduction to Computing'],
                        ['CS102', 'Computer Programming 1'],
                        ['MATH101', 'College Algebra'],
                    ]),
                    2 => array_merge($ge2, [
                        ['CS201', 'Computer Programming 2'],
                        ['CS202', 'Data Structures'],
                        ['CS203', 'Discrete Mathematics'],
                        ['CS204', 'Computer Organization'],
                    ]),
                    3 => [
                        ['CS301', 'Algorithms and Complexity'],
                        ['CS302', 'Database Systems'],
                        ['CS303', 'Operating Systems'],
                        ['CS304', 'Software Engineering 1'],
                        ['CS305', 'Web Systems and Technologies'],
                        ['ITEL301', 'Integrative Programming'],
                    ],
                    4 => [
                        ['CS401', 'Software Engineering 2'],
                        ['CS402', 'Computer Networks'],
                        ['CS403', 'Information Assurance and Security'],
                        ['CS404', 'Capstone Project 1'],
                        ['CS405', 'Capstone Project 2'],
                        ['CS406', 'Practicum / OJT'],
                    ],
                ],
            ],
            [
                'program_code' => 'BSIT',
                'program_name' => 'Bachelor of Science in Information Technology',
                'total_years' => 4,
                'courses_by_year' => [
                    1 => array_merge($ge1, [
                        ['IT101', 'Introduction to Computing'],
                        ['IT102', 'Programming Logic and Design'],
                        ['IT103', 'Computer Hardware Fundamentals'],
                    ]),
                    2 => array_merge($ge2, [
                        ['IT201', 'Object-Oriented Programming'],
                        ['IT202', 'Data Structures'],
                        ['IT203', 'Networking Fundamentals'],
                        ['IT204', 'Systems Analysis and Design'],
                    ]),
                    3 => [
                        ['IT301', 'Database Management Systems'],
                        ['IT302', 'Web Development'],
                        ['IT303', 'Mobile Application Development'],
                        ['IT304', 'IT Infrastructure and Services'],
                        ['IT305', 'Human-Computer Interaction'],
                    ],
                    4 => [
                        ['IT401', 'Information Assurance'],
                        ['IT402', 'Systems Integration'],
                        ['IT403', 'IT Project Management'],
                        ['IT404', 'Capstone Project 1'],
                        ['IT405', 'Capstone Project 2'],
                        ['IT406', 'Practicum / OJT'],
                    ],
                ],
            ],
            [
                'program_code' => 'BEED',
                'program_name' => 'Bachelor of Elementary Education',
                'total_years' => 4,
                'courses_by_year' => [
                    1 => array_merge($ge1, [
                        ['ED101', 'Foundations of Education'],
                        ['ED102', 'Child and Adolescent Development'],
                        ['ED103', 'Teaching Arts in the Elementary Grades 1'],
                    ]),
                    2 => array_merge($ge2, [
                        ['ED201', 'Principles of Teaching'],
                        ['ED202', 'Assessment of Learning 1'],
                        ['ED203', 'Teaching Science in the Elementary Grades'],
                        ['ED204', 'Teaching Math in the Elementary Grades'],
                    ]),
                    3 => [
                        ['ED301', 'Curriculum Development'],
                        ['ED302', 'Assessment of Learning 2'],
                        ['ED303', 'Teaching Filipino in the Elementary Grades'],
                        ['ED304', 'Teaching English in the Elementary Grades'],
                        ['ED305', 'Educational Technology'],
                    ],
                    4 => [
                        ['ED401', 'Practice Teaching 1'],
                        ['ED402', 'Practice Teaching 2'],
                        ['ED403', 'Research in Education'],
                        ['ED404', 'Professional Education Seminar'],
                    ],
                ],
            ],
            [
                'program_code' => 'BSED',
                'program_name' => 'Bachelor of Secondary Education',
                'total_years' => 4,
                'courses_by_year' => [
                    1 => array_merge($ge1, [
                        ['SED101', 'Foundations of Education'],
                        ['SED102', 'Assessment in Learning 1'],
                        ['SED103', 'Facilitating Learner-Centered Teaching'],
                    ]),
                    2 => array_merge($ge2, [
                        ['SED201', 'Principles of Teaching'],
                        ['SED202', 'Curriculum Planning'],
                        ['SED203', 'Assessment in Learning 2'],
                        ['SED204', 'Educational Technology'],
                    ]),
                    3 => [
                        ['SED301', 'Teaching Methods in Major Subject'],
                        ['SED302', 'Content and Pedagogy 1'],
                        ['SED303', 'Content and Pedagogy 2'],
                        ['SED304', 'Research in Education'],
                    ],
                    4 => [
                        ['SED401', 'Practice Teaching in Secondary 1'],
                        ['SED402', 'Practice Teaching in Secondary 2'],
                        ['SED403', 'Professional Education Capstone'],
                    ],
                ],
            ],
            [
                'program_code' => 'BSBA',
                'program_name' => 'Bachelor of Science in Business Administration',
                'total_years' => 4,
                'courses_by_year' => [
                    1 => array_merge($ge1, [
                        ['BA101', 'Introduction to Business'],
                        ['BA102', 'Business Mathematics'],
                        ['BA103', 'Fundamentals of Accounting 1'],
                    ]),
                    2 => array_merge($ge2, [
                        ['BA201', 'Fundamentals of Accounting 2'],
                        ['BA202', 'Business Communication'],
                        ['BA203', 'Microeconomics'],
                        ['BA204', 'Macroeconomics'],
                    ]),
                    3 => [
                        ['BA301', 'Financial Management'],
                        ['BA302', 'Marketing Management'],
                        ['BA303', 'Operations Management'],
                        ['BA304', 'Business Law'],
                        ['BA305', 'Organizational Behavior'],
                    ],
                    4 => [
                        ['BA401', 'Strategic Management'],
                        ['BA402', 'Business Research'],
                        ['BA403', 'Entrepreneurship'],
                        ['BA404', 'Practicum / Business Immersion'],
                    ],
                ],
            ],
            [
                'program_code' => 'BSN',
                'program_name' => 'Bachelor of Science in Nursing',
                'total_years' => 4,
                'courses_by_year' => [
                    1 => array_merge($ge1, [
                        ['N101', 'Anatomy and Physiology'],
                        ['N102', 'Biochemistry'],
                        ['N103', 'Fundamentals of Nursing'],
                    ]),
                    2 => array_merge($ge2, [
                        ['N201', 'Health Assessment'],
                        ['N202', 'Nursing Pharmacology'],
                        ['N203', 'Medical-Surgical Nursing 1'],
                        ['N204', 'Community Health Nursing'],
                    ]),
                    3 => [
                        ['N301', 'Medical-Surgical Nursing 2'],
                        ['N302', 'Maternal and Child Nursing'],
                        ['N303', 'Mental Health Nursing'],
                        ['N304', 'Nursing Research'],
                    ],
                    4 => [
                        ['N401', 'Leadership and Management in Nursing'],
                        ['N402', 'Related Learning Experience 1'],
                        ['N403', 'Related Learning Experience 2'],
                        ['N404', 'Nursing Board Review Preparation'],
                    ],
                ],
            ],
        ];
    }
}
