@php
    $formId = $formId ?? 'catalogBookForm';
@endphp
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById(@json($formId));
        if (!form) return;

        const mainTabIds = ['tab-bibliographic', 'tab-programs', 'tab-cover'];
        const prevBtn = document.getElementById('catalogTabPrev');
        const nextBtn = document.getElementById('catalogTabNext');

        function getActiveMainIndex() {
            return mainTabIds.findIndex(id => {
                const pane = document.getElementById(id);
                return pane && pane.classList.contains('active');
            });
        }

        function showMainTab(index) {
            const id = mainTabIds[index];
            if (!id) return;
            const btn = document.querySelector(`[data-bs-target="#${id}"]`);
            if (btn && typeof bootstrap !== 'undefined') {
                bootstrap.Tab.getOrCreateInstance(btn).show();
            }
            updateNavButtons(index);
        }

        function updateNavButtons(index) {
            if (prevBtn) prevBtn.disabled = index <= 0;
            if (nextBtn) nextBtn.disabled = index >= mainTabIds.length - 1;
        }

        prevBtn?.addEventListener('click', () => {
            const i = getActiveMainIndex();
            if (i > 0) showMainTab(i - 1);
        });

        nextBtn?.addEventListener('click', () => {
            const i = getActiveMainIndex();
            if (i < mainTabIds.length - 1) showMainTab(i + 1);
        });

        document.querySelectorAll('#catalogMainTabs [data-bs-toggle="tab"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', () => {
                updateNavButtons(getActiveMainIndex());
            });
        });

        updateNavButtons(0);

        form.addEventListener('keydown', e => {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const inputs = [...form.querySelectorAll('input:not([type="file"]), select, textarea')];
                const index = inputs.indexOf(document.activeElement);
                if (inputs[index + 1]) inputs[index + 1].focus();
            }
        });

        form.addEventListener('submit', (e) => {
            if (form.checkValidity()) return;
            e.preventDefault();
            const invalid = form.querySelector(':invalid');
            if (!invalid) return;

            const marcPane = invalid.closest('[id^="marc-tab-"]');
            const mainPane = invalid.closest('#catalogMainTabContent > .tab-pane');

            if (marcPane) {
                showMainTab(0);
                const marcBtn = document.querySelector(`[data-bs-target="#${marcPane.id}"]`);
                if (marcBtn && typeof bootstrap !== 'undefined') {
                    bootstrap.Tab.getOrCreateInstance(marcBtn).show();
                }
            } else if (mainPane?.id && mainTabIds.includes(mainPane.id)) {
                showMainTab(mainTabIds.indexOf(mainPane.id));
            }

            invalid.focus();
            form.reportValidity();
        });
    });
</script>
