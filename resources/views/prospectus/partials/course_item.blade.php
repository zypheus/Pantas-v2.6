<li id="course-{{ $course->id }}" class="flex justify-between items-center border-b pb-1">
    <span>
        <strong>{{ $course->course_code }}</strong> — {{ $course->course_name }}
    </span>
    <div class="flex gap-2">
        <!-- Edit -->
        <button type="button"   {{-- ✅ ensures no reload --}}
                class="bg-yellow-500 text-white px-2 py-1 rounded text-xs"
                onclick="openEditModal({{ $course->id }}, '{{ $course->course_code }}', '{{ $course->course_name }}')">
            Edit
        </button>

        <!-- Delete -->
        <button type="button"   {{-- ✅ ensures no reload --}}
                class="bg-red-600 text-white px-2 py-1 rounded text-xs"
                onclick="openDeleteModal({{ $course->id }}, '{{ $course->course_code }}')">
            Delete
        </button>
    </div>
</li>
