@if (session()->has('contrast_warnings'))
    @php $warnings = session('contrast_warnings'); @endphp
    @if (count($warnings) > 0)
        <div id="contrast-warnings-data"
             data-warnings='{{ json_encode($warnings) }}'
             style="display:none;"></div>
    @endif
@endif

<script>
/**
 * WCAG Contrast Calculator for in-browser luminance and ratio.
 */
function srgbLuminance(hex) {
    hex = hex.replace('#', '');
    var r = parseInt(hex.substr(0, 2), 16) / 255;
    var g = parseInt(hex.substr(2, 2), 16) / 255;
    var b = parseInt(hex.substr(4, 2), 16) / 255;
    function lin(c) { return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4); }
    return 0.2126 * lin(r) + 0.7152 * lin(g) + 0.0722 * lin(b);
}
function contrastRatio(hex1, hex2) {
    var l1 = srgbLuminance(hex1), l2 = srgbLuminance(hex2);
    var lighter = Math.max(l1, l2), darker = Math.min(l1, l2);
    return (lighter + 0.05) / (darker + 0.05);
}

/**
 * Show SweetAlert2 warning for accessibility contrast issues.
 */
function showContrastWarning(warnings, index) {
    if (index >= warnings.length) return;
    var w = warnings[index];
    var threshold = w.threshold;
    var textType = w.largeText ? 'large text (≥3:1 required)' : 'normal text (≥4.5:1 required)';

    var html = '<div style="font-size:14px;line-height:1.6;text-align:left">';

    // Color swatches
    html += '<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">';
    html += '<div style="flex:1;text-align:center"><div style="width:30px;height:30px;border-radius:6px;background:' + w.fgColor + ';border:1px solid #ccc;margin:0 auto 4px"></div><strong style="font-size:13px">' + w.fgColor + '</strong><br><small style="color:#666">' + w.fgLabel + '</small></div>';
    html += '<div style="font-size:20px;color:#999">vs</div>';
    html += '<div style="flex:1;text-align:center"><div style="width:30px;height:30px;border-radius:6px;background:' + w.bgColor + ';border:1px solid #ccc;margin:0 auto 4px"></div><strong style="font-size:13px">' + w.bgColor + '</strong><br><small style="color:#666">' + w.bgLabel + '</small></div>';
    html += '</div>';

    // Ratio bar
    var percent = Math.min(w.ratio / threshold * 100, 100);
    var barColor = percent >= 100 ? '#22c55e' : percent >= 75 ? '#eab308' : '#ef4444';
    html += '<div style="margin-bottom:10px">';
    html += '<div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">';
    html += '<span><strong>Contrast ratio:</strong> ' + w.ratio + ':1</span>';
    html += '<span><strong>Required:</strong> ' + threshold + ':1</span>';
    html += '</div>';
    html += '<div style="background:#e5e7eb;border-radius:4px;height:8px;overflow:hidden">';
    html += '<div style="height:100%;width:' + percent + '%;background:' + barColor + ';border-radius:4px;transition:width .3s"></div>';
    html += '</div>';
    html += '</div>';

    // Explanation
    html += '<p style="margin:0;padding:10px;background:#fef2f2;border-radius:6px;border:1px solid #fecaca;color:#991b1b;font-size:13px">';
    html += '<strong>' + w.fgLabel + '</strong> (<code>' + w.fgColor + '</code>) on <strong>' + w.bgLabel + '</strong> (<code>' + w.bgColor + '</code>) ';
    html += 'has only <strong>' + w.ratio + ':1</strong> contrast ratio, which is below the WCAG minimum of <strong>' + threshold + ':1</strong> for ' + textType + '. ';
    html += 'This may be difficult for users with low vision to read.';
    html += '</p>';

    html += '</div>';

    // Continue to next warning if any
    function next() {
        showContrastWarning(warnings, index + 1);
    }

    Swal.fire({
        title: '<span style="font-size:20px">⚠️ Accessibility Warning</span>',
        html: html,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'I understand, save anyway',
        cancelButtonText: 'Let me fix it',
        reverseButtons: true
    }).then(function (result) {
        if (result.isConfirmed) {
            next();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            var input = document.getElementById(w.field + '_text') || document.getElementById(w.field);
            if (input) {
                input.focus();
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
}

/**
 * Initialize in-browser contrast checking for color input changes.
 */
function initContrastChecker(rules) {
    rules.forEach(function (rule) {
        function check() {
            var fgInput, bgInput;

            if (rule.fgOverride) {
                fgInput = null;
            } else {
                fgInput = document.getElementById(rule.fg + '_text') || document.getElementById(rule.fg);
            }

            bgInput = document.getElementById(rule.bg + '_text') || document.getElementById(rule.bg);

            if (!bgInput) return;

            var fgVal = rule.fgOverride || (fgInput ? fgInput.value.toUpperCase() : null);
            var bgVal = bgInput.value.toUpperCase();

            if (!fgVal || !bgVal) return;
            if (!/^#[0-9A-F]{6}$/.test(fgVal) || !/^#[0-9A-F]{6}$/.test(bgVal)) return;

            var ratio = contrastRatio(fgVal, bgVal);
            var threshold = rule.largeText ? 3.0 : 4.5;

            // Find or create the status badge
            var container = bgInput.closest('.mb-3') || bgInput.closest('.input-group');
            if (!container) container = bgInput.parentElement;
            var badge = container.querySelector('.contrast-badge');

            if (ratio >= threshold) {
                if (badge) {
                    badge.className = 'contrast-badge badge bg-success ms-1';
                    badge.textContent = '✓ Good';
                }
            } else {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'contrast-badge badge bg-warning text-dark ms-1';
                    badge.style.cursor = 'pointer';
                    badge.title = 'Click for details';
                    container.appendChild(badge);

                    badge.addEventListener('click', function () {
                        showContrastWarning([{
                            field: rule.fg || rule.bg,
                            fgLabel: rule.fgLabel,
                            bgLabel: rule.bgLabel,
                            fgColor: fgVal,
                            bgColor: bgVal,
                            ratio: Math.round(ratio * 100) / 100,
                            threshold: threshold,
                            largeText: rule.largeText
                        }], 0);
                    });
                }
                badge.className = 'contrast-badge badge bg-warning text-dark ms-1';
                badge.textContent = '⚠ ' + Math.round(ratio * 100) / 100 + ':1';
                badge.title = rule.fgLabel + ' vs ' + rule.bgLabel + ' — click for details';
            }
        }

        var fgInput = rule.fg ? (document.getElementById(rule.fg + '_text') || document.getElementById(rule.fg)) : null;
        var bgInput = document.getElementById(rule.bg + '_text') || document.getElementById(rule.bg);

        if (fgInput) {
            fgInput.addEventListener('input', check);
            fgInput.addEventListener('change', check);
        }
        if (bgInput) {
            bgInput.addEventListener('input', check);
            bgInput.addEventListener('change', check);
        }

        setTimeout(check, 100);
    });
}

/**
 * Show server-side flashed warnings as stacked SweetAlert popups.
 */
function showFlashedWarnings() {
    var el = document.getElementById('contrast-warnings-data');
    if (!el) return;
    var warnings = JSON.parse(el.getAttribute('data-warnings') || '[]');
    if (warnings.length > 0) {
        setTimeout(function () { showContrastWarning(warnings, 0); }, 500);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    showFlashedWarnings();
});
</script>