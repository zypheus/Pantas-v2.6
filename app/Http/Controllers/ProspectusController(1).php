<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prospectus;

class ProspectusController extends Controller
{
    public function create() {
        return view('prospectus.create');
    }

    public function store(Request $request) {
        // Validation: no more subject, accept multiple years
        $validated = $request->validate([
            'course' => 'required|string|max:255',
            'years' => 'required|array|min:1',
            'years.*' => 'required|string|max:255',
        ]);

        // Save course for each selected year
        foreach ($validated['years'] as $year) {
            Prospectus::create([
                'course' => $validated['course'],
                'year' => $year,
            ]);
        }

        return redirect()->route('prospectus.index')->with('success', 'Course added for selected years!');
    }

    public function getYears(Request $request) {
        $course = $request->course;
        $years = Prospectus::where('course', $course)
                    ->select('year')->distinct()->orderBy('year')->pluck('year');
        return response()->json($years);
    }

    public function getPrograms(Request $request) {
        $course = $request->course;
        $year = $request->year;
        $programs = Prospectus::where('course', $course)
                        ->where('year', $year)
                        ->select('subject')
                        ->distinct()
                        ->orderBy('subject')
                        ->pluck('subject');
        return response()->json($programs);
    }

    public function index(Request $request) {
        $courses = Prospectus::select('course')->distinct()->orderBy('course')->pluck('course');
        $selectedCourse = $request->input('course');

        $subjectsByYear = [];

        if ($selectedCourse) {
            $subjects = Prospectus::where('course', $selectedCourse)
                ->orderBy('year')
                ->get()
                ->groupBy('year');

            $subjectsByYear = $subjects;
        }

        return view('prospectus.index', compact('courses', 'selectedCourse', 'subjectsByYear'));
    }

    public function edit($id) {
        $entry = Prospectus::findOrFail($id);
        return view('prospectus.edit', compact('entry'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'course' => 'required|string',
            'year' => 'required|string',
        ]);

        Prospectus::where('id', $id)->update($data);
        return redirect()->route('prospectus.index', ['course' => $data['course']])->with('success', 'Entry updated!');
    }

    public function destroy($id) {
        $entry = Prospectus::findOrFail($id);
        $course = $entry->course;
        $entry->delete();
        return redirect()->route('prospectus.index', ['course' => $course])->with('success', 'Entry deleted!');
    }
    
    public function createSubject(Request $request)
{
    $course = $request->course;
    $year = $request->year;

    // Ensure course and year were passed
    if (!$course || !$year) {
        return redirect()->route('prospectus.index')->withErrors('Course and year are required.');
    }

    return view('prospectus.add_subject', compact('course', 'year'));
}

public function storeSubject(Request $request)
{
    $validated = $request->validate([
        'course' => 'required|string|max:255',
        'year' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
    ]);

    Prospectus::create($validated);

    return redirect()->route('prospectus.index', ['course' => $validated['course']])
                     ->with('success', 'Subject added successfully!');
}

}
_code,
            'program_name' => $program->program_name,
        ]);
    }
    
    public function destroyProgram(Program $program)
    {
        $program->delete();
    
        return response()->json([
            'success' => true,
            'id' => $program->id
        ]);
    }
}
