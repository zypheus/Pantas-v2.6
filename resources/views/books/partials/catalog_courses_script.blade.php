{{-- Expects: course select id="book_course", program selects name="program_ids[]" inside #program-container --}}
<script>
(function () {
    var coursesUrl = @json(route('books.coursesForPrograms'));

    function programIds() {
        return Array.prototype.slice.call(document.querySelectorAll('select[name="program_ids[]"]'))
            .map(function (s) { return s.value; })
            .filter(Boolean);
    }

    window.refreshBookCourseOptions = function () {
        var sel = document.getElementById('book_course');
        if (!sel) return;

        var prev = sel.value;
        var ids = programIds();

        sel.innerHTML = '<option value="">' + '-- Select course --' + '</option>';

        if (ids.length === 0) {
            sel.disabled = true;
            return;
        }

        sel.disabled = true;
        var params = new URLSearchParams();
        ids.forEach(function (id) { params.append('program_ids[]', id); });

        fetch(coursesUrl + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(function (r) { return r.json(); })
            .then(function (names) {
                names.forEach(function (name) {
                    var o = document.createElement('option');
                    o.value = name;
                    o.textContent = name;
                    sel.appendChild(o);
                });
                if (prev) {
                    var has = Array.prototype.some.call(sel.options, function (opt) { return opt.value === prev; });
                    if (!has) {
                        var cur = document.createElement('option');
                        cur.value = prev;
                        cur.textContent = prev + ' (current)';
                        sel.appendChild(cur);
                    }
                    sel.value = prev;
                }
                sel.disabled = false;
            })
            .catch(function () {
                sel.disabled = false;
            });
    };

    document.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('program-container');
        if (container) {
            container.addEventListener('change', function (e) {
                if (e.target && e.target.matches && e.target.matches('select[name="program_ids[]"]')) {
                    window.refreshBookCourseOptions();
                }
            });
        }
        window.refreshBookCourseOptions();
    });
})();
</script>
