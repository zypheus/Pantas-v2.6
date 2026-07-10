@php
    $themes = config('themes.themes', []);
    $currentTheme = auth()->user()->theme_preference ?? 'pantas-default';
@endphp

<style>
/* Theme Picker modal styling — uses CSS vars so it adapts to both light and dark themes */
#themePickerModal .modal-content {
    border-radius: 1rem;
    border: 1px solid var(--shell-border);
    background: var(--shell-surface);
    color: var(--shell-text);
    box-shadow: 0 24px 60px var(--shell-modal-overlay, rgba(15, 23, 42, 0.28));
}

#themePickerModal .modal-header {
    border-bottom: 1px solid var(--shell-border);
    padding: 1.25rem 1.5rem;
}

#themePickerModal .modal-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--shell-text);
}

#themePickerModal .modal-title i {
    color: var(--shell-primary);
}

#themePickerModal .btn-close {
    filter: none;
    opacity: 0.6;
}

#themePickerModal .btn-close:hover {
    opacity: 1;
}

#themePickerModal .modal-body {
    padding: 1.25rem 1.5rem;
}

#themePickerModal .modal-footer {
    border-top: 1px solid var(--shell-border);
    padding: 1rem 1.5rem;
    gap: 0.65rem;
}

/* Theme option button inside the grid */
.theme-option {
    border-color: var(--shell-border) !important;
    background: var(--shell-surface);
    color: var(--shell-text);
    transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
}

.theme-option:hover {
    border-color: var(--shell-action) !important;
    box-shadow: 0 0 0 2px color-mix(in srgb, var(--shell-action) 20%, transparent);
    transform: translateY(-2px);
}

.theme-option.theme-option-active,
.theme-option[data-active="true"] {
    border-color: var(--shell-primary) !important;
    box-shadow: 0 0 0 2px color-mix(in srgb, var(--shell-primary) 25%, transparent);
}

.theme-option .theme-type-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.65rem;
    font-weight: 600;
    border-radius: 999px;
    padding: 0.12rem 0.45rem;
    background: var(--shell-surface-muted);
    color: var(--shell-subtle);
    border: 1px solid var(--shell-border);
}

.theme-option .theme-dot {
    display: inline-block;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    background: var(--shell-primary);
}

.theme-option .theme-label {
    font-size: 0.8125rem;
    font-weight: 600;
    line-height: 1.25rem;
    color: var(--shell-text);
}

.theme-option .theme-key {
    font-size: 0.7rem;
    color: var(--shell-subtle);
    margin-top: 0.15rem;
}

/* Modal backdrop */
.modal-backdrop.show {
    background: var(--shell-modal-overlay, rgba(15, 23, 42, 0.5));
    opacity: 1;
}

/* Footer buttons */
.theme-footer-btn {
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.55rem 1rem;
    border: 1px solid var(--shell-border);
    background: var(--shell-surface);
    color: var(--shell-muted);
    transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
}

.theme-footer-btn:hover {
    border-color: var(--shell-primary);
    background: var(--shell-primary-soft);
    color: var(--shell-primary);
}

.theme-footer-btn.primary {
    border-color: var(--shell-primary);
    background: var(--shell-primary);
    color: #ffffff;
}

.theme-footer-btn.primary:hover {
    background: var(--shell-primary-dark);
    border-color: var(--shell-primary-dark);
}

.theme-footer-btn.muted {
    background: var(--shell-surface-muted);
    color: var(--shell-muted);
    border-color: var(--shell-border);
}
</style>

{{-- Theme Picker Modal --}}
<div class="modal fade" id="themePickerModal" tabindex="-1" aria-labelledby="themePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="themePickerModalLabel">
                    <i class="bi bi-palette me-2"></i>Choose Theme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="themeGrid">
                    @foreach ($themes as $key => $theme)
                        @php
                            $isDark = ($theme['type'] ?? 'light') === 'dark';
                            $isActive = $key === $currentTheme;
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button"
                                class="theme-option btn w-100 text-start p-3 rounded-3 border {{ $isActive ? 'theme-option-active' : '' }}"
                                data-theme-key="{{ $key }}"
                                data-theme-type="{{ $theme['type'] ?? 'light' }}"
                                data-active="{{ $isActive ? 'true' : 'false' }}"
                            >
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="theme-dot" style="background: {{ $isDark ? 'var(--shell-primary)' : 'var(--shell-primary)' }};"></span>
                                    <span class="theme-type-badge">{{ $theme['type'] ?? 'light' }}</span>
                                </div>
                                <div class="theme-label">{{ $theme['label'] }}</div>
                                <div class="theme-key">{{ $key }}</div>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn theme-footer-btn muted" id="resetThemeBtn">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset to default
                </button>
                <button type="button" class="btn theme-footer-btn" id="cancelThemeBtn" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn theme-footer-btn primary" id="saveThemeBtn">
                    <i class="bi bi-check-lg me-1"></i>Save theme
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeModal = document.getElementById('themePickerModal');
    if (!themeModal) return;

    let savedTheme = document.documentElement.dataset.savedTheme || 'pantas-default';
    let previewedTheme = savedTheme;

    // --- Preview on selection ---
    const themeOptions = themeModal.querySelectorAll('.theme-option');
    themeOptions.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const key = this.dataset.themeKey;
            previewedTheme = key;
            document.documentElement.dataset.theme = key;

            // Update active visual state via CSS classes
            themeOptions.forEach(function (opt) {
                opt.classList.remove('theme-option-active');
                opt.dataset.active = 'false';
            });
            this.classList.add('theme-option-active');
            this.dataset.active = 'true';

            // Dispatch event for charts
            document.dispatchEvent(new CustomEvent('theme-preview', { detail: { theme: key } }));
        });
    });

    // --- Save ---
    document.getElementById('saveThemeBtn').addEventListener('click', function () {
        const saveBtn = this;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';

        fetch('{{ route("user.preferences.theme") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ theme: previewedTheme }),
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Save failed');
            return res.json();
        })
        .then(function (data) {
            document.documentElement.dataset.savedTheme = data.theme;
            savedTheme = data.theme;
            // Close modal
            var modal = bootstrap.Modal.getInstance(themeModal);
            if (modal) modal.hide();
            // Show success feedback
            Swal.fire({
                icon: 'success',
                title: 'Theme saved',
                text: data.label + ' is now your active theme.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        })
        .catch(function () {
            // Restore saved theme on failure
            document.documentElement.dataset.theme = savedTheme;
            previewedTheme = savedTheme;
            Swal.fire({
                icon: 'error',
                title: 'Could not save theme',
                text: 'Please try again.',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        })
        .finally(function () {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save theme';
        });
    });

    // --- Cancel ---
    document.getElementById('cancelThemeBtn').addEventListener('click', function () {
        document.documentElement.dataset.theme = savedTheme;
        previewedTheme = savedTheme;
        // Dispatch event for charts
        document.dispatchEvent(new CustomEvent('theme-preview', { detail: { theme: savedTheme } }));
    });

    // --- Reset to default ---
    document.getElementById('resetThemeBtn').addEventListener('click', function () {
        previewedTheme = 'pantas-default';
        document.documentElement.dataset.theme = 'pantas-default';
        // Update active visual via CSS classes
        themeOptions.forEach(function (opt) {
            opt.classList.remove('theme-option-active');
            opt.dataset.active = 'false';
            if (opt.dataset.themeKey === 'pantas-default') {
                opt.classList.add('theme-option-active');
                opt.dataset.active = 'true';
            }
        });
        // Dispatch event for charts
        document.dispatchEvent(new CustomEvent('theme-preview', { detail: { theme: 'pantas-default' } }));
    });

    // --- Restore saved theme when modal is closed without saving ---
    themeModal.addEventListener('hidden.bs.modal', function () {
        document.documentElement.dataset.theme = savedTheme;
        previewedTheme = savedTheme;
        document.dispatchEvent(new CustomEvent('theme-preview', { detail: { theme: savedTheme } }));
    });
});
</script>
@endpush
