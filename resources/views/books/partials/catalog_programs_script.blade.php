<script>
    const programs = @json($programs);
    const container = document.getElementById('program-container');
    const addBtn = document.getElementById('add-program-btn');

    if (container && addBtn) {
        function refreshOptions() {
            const selectedValues = Array.from(document.querySelectorAll('.program-select'))
                .map(sel => sel.value)
                .filter(v => v);

            document.querySelectorAll('.program-select').forEach(select => {
                const currentVal = select.value;
                Array.from(select.options).forEach(opt => {
                    if (opt.value && selectedValues.includes(opt.value) && opt.value !== currentVal) {
                        opt.hidden = true;
                    } else {
                        opt.hidden = false;
                    }
                });
            });
        }

        addBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.classList.add('program-row', 'd-flex', 'gap-2', 'align-items-start', 'mt-2');

            const select = document.createElement('select');
            select.name = 'program_ids[]';
            select.classList.add('form-control', 'program-select', 'flex-grow-1');

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '— Select program —';
            select.appendChild(defaultOption);

            programs.forEach(program => {
                const option = document.createElement('option');
                option.value = program.id;
                option.textContent = program.program_name;
                select.appendChild(option);
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = 'Remove';
            removeBtn.classList.add('btn', 'btn-sm', 'btn-outline-danger', 'remove-program', 'flex-shrink-0');

            row.appendChild(select);
            row.appendChild(removeBtn);
            container.appendChild(row);

            refreshOptions();
            if (typeof window.refreshBookCourseOptions === 'function') {
                window.refreshBookCourseOptions();
            }
        });

        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-program')) {
                e.target.closest('.program-row').remove();
                refreshOptions();
                if (typeof window.refreshBookCourseOptions === 'function') {
                    window.refreshBookCourseOptions();
                }
            }
        });

        container.addEventListener('change', (e) => {
            if (e.target.classList.contains('program-select')) {
                refreshOptions();
            }
        });

        refreshOptions();
        if (typeof window.refreshBookCourseOptions === 'function') {
            window.refreshBookCourseOptions();
        }
    }
</script>
