@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tailwind-build.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/prospectus/index.css') }}">
@endsection

@section('content')

    <div id="prospectus-page" class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Prospectus Manager</h1>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="p-3 mb-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Add Program --}}
        <div class="bg-white rounded shadow p-4 mb-6">
            <h2 class="font-semibold mb-3">Add Program/Strand</h2>
            <form method="POST" action="{{ route('prospectus.storeProgram') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="program_code" placeholder="Program Code" class="border px-3 py-2" required>
                <input type="text" name="program_name" placeholder="Program Name" class="border px-3 py-2 md:col-span-2"
                    required>
                <div class="flex gap-2">
                    <input type="number" name="total_years" placeholder="Years" min="1" max="6"
                        class="border px-3 py-2 w-20" required>
                    <button type="submit" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">Add</button>
                </div>
            </form>
        </div>

        {{-- Programs List --}}
        @foreach($programs as $program)
        <div class="bg-white rounded shadow mb-6">
            <div class="flex justify-between items-center px-4 py-3 bg-gray-800 text-white rounded-t">
                <span id="program-name-{{ $program->id }}" class="font-semibold">
                    {{ $program->program_code }} — {{ $program->program_name }}
                </span>
                <div class="flex gap-2">
                    <button type="button"
                        onclick="openProgramEditModal({{ $program->id }}, '{{ $program->program_code }}', '{{ $program->program_name }}')"
                        class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">
                        Edit Program
                    </button>
                    <button type="button"
                        onclick="openProgramDeleteModal({{ $program->id }}, '{{ $program->program_code }}')"
                        class="bg-red-600 text-white px-2 py-1 rounded text-sm">
                        Delete
                    </button>
                    <button type="button" data-prospectus-panel="#program-{{ $program->id }}"
                        class="bg-gray-600 px-2 py-1 rounded text-sm">
                        Toggle
                    </button>
                </div>
            </div>


            <div id="program-{{ $program->id }}" class="p-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($program->years as $year)
                    <div class="bg-gray-50 rounded shadow">
                        <div class="flex justify-between items-center px-3 py-2 border-b">
                            <span class="font-semibold">Year {{ $year->year_level }}</span>
                            <button type="button" data-prospectus-panel="#year-{{ $year->id }}"
                                class="text-sm text-gray-600">
                                Toggle
                            </button>
                        </div>
                        {{-- Courses --}}
                        <div id="year-{{ $year->id }}" class="p-3 hidden">
                            <ul class="space-y-2 mb-3 max-h-52 overflow-y-auto">
                                @forelse($year->courses as $course)
                                <li id="course-{{ $course->id }}"
                                    class="flex justify-between items-center border-b pb-1">
                                    <span><strong>{{ $course->course_code }}</strong> — {{ $course->course_name
                                        }}</span>
                                    <div class="flex gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs"
                                            onclick="openEditModal({{ $course->id }}, '{{ $course->course_code }}', '{{ $course->course_name }}')">
                                            Edit
                                        </button>
                                        <!-- Delete Button (triggers modal) -->
                                        <button type="button" class="bg-red-600 text-white px-2 py-1 rounded text-xs"
                                            onclick="openDeleteModal({{ $course->id }}, '{{ $course->course_code }}')">
                                            Delete
                                        </button>

                                    </div>
                                </li>
                                @empty
                                <li class="text-gray-500">No courses yet.</li>
                                @endforelse
                            </ul>

                            {{-- Add Course --}}
                            <form method="POST" action="{{ route('prospectus.storeCourse', $year->id) }}"
                                class="add-course-form grid grid-cols-1 md:grid-cols-3 gap-2"
                                data-year="{{ $year->id }}">
                                @csrf
                                <input type="text" name="course_code" placeholder="Course Code" class="border px-2 py-1"
                                    required>
                                <input type="text" name="course_name" placeholder="Course Name"
                                    class="border px-2 py-1 md:col-span-1" required>
                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">
                                    <span class="btn-text">Add</span>
                                    <span
                                        class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                </button>
                            </form>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Confirm Delete</h2>
            <p id="deleteMessage" class="mb-4 text-gray-700"></p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Cancel</button>
                    <button type="submit" id="deleteBtn"
                        class="px-4 py-2 bg-red-600 rounded text-white flex items-center gap-2">
                        <span class="btn-text">Delete</span>
                        <span
                            class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Edit Course</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-sm font-medium">Course Code</label>
                    <input type="text" id="editCourseCode" name="course_code" class="border px-3 py-2 w-full" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Course Name</label>
                    <input type="text" id="editCourseName" name="course_name" class="border px-3 py-2 w-full" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Cancel</button>
                    <button type="submit" id="editBtn"
                        class="px-4 py-2 bg-yellow-600 rounded text-white flex items-center gap-2">
                        <span class="btn-text">Update</span>
                        <span
                            class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div id="editProgramModal"
        class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Edit Program</h2>
            <form id="editProgramForm" method="POST">
                @csrf
                @method('PUT')

                <input type="text" name="program_code" id="editProgramCode" class="w-full border rounded px-3 py-2 mb-3"
                    placeholder="Program Code" required>

                <input type="text" name="program_name" id="editProgramName" class="w-full border rounded px-3 py-2 mb-3"
                    placeholder="Program Name" required>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeProgramEditModal()"
                        class="px-3 py-1 border rounded">Cancel</button>
                    <button id="editProgramBtn" type="submit"
                        class="bg-blue-600 text-white px-3 py-1 rounded flex items-center">
                        <span class="btn-text">Save</span>
                        <span
                            class="spinner hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Program Modal -->
    <div id="deleteProgramModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Delete Program</h2>
            <p class="mb-4">Are you sure you want to delete <span id="deleteProgramCode"></span>? This action cannot be undone.</p>
            <form id="deleteProgramForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeProgramDeleteModal()" class="px-3 py-1 border rounded">Cancel</button>
                    <button id="deleteProgramBtn" type="submit" class="bg-red-600 text-white px-3 py-1 rounded flex items-center">
                        <span class="btn-text">Delete</span>
                        <span class="spinner hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed bottom-5 right-5 space-y-2 z-50"></div>
    <!-- Scripts -->
    <script src="{{ asset('js/prospectus.js') }}"></script>
    
    
@endsection