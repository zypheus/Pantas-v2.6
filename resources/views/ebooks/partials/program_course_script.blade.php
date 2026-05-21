@php
    $programSelectId = $programSelectId ?? 'program';
    $courseSelectId = $courseSelectId ?? 'course';
    $selectedProgramId = $selectedProgramId ?? '';
    $selectedCourseId = $selectedCourseId ?? '';
    $coursesUrl = $coursesUrl ?? url('/ebooks/get-courses');
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    const programSelect = document.getElementById(@json($programSelectId));
    const courseSelect = document.getElementById(@json($courseSelectId));
    const selectedCourseId = @json((string) $selectedCourseId);

    if (!programSelect || !courseSelect) return;

    function loadCourses(programId, preserveCourseId) {
        const pid = programId || 'all';
        fetch(@json(rtrim($coursesUrl, '/')) + '/' + encodeURIComponent(pid))
            .then(r => r.json())
            .then(courses => {
                courseSelect.innerHTML = '<option value="">— All subjects —</option>';
                courses.forEach(function (course) {
                    const opt = document.createElement('option');
                    opt.value = course.id;
                    opt.textContent = course.name;
                    if (preserveCourseId && String(course.id) === String(preserveCourseId)) {
                        opt.selected = true;
                    }
                    courseSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Error loading courses:', err));
    }

    programSelect.addEventListener('change', function () {
        loadCourses(this.value, null);
    });

    loadCourses(programSelect.value, selectedCourseId);
});
</script>
