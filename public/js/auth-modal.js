document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('lmOverlay');
    if (!overlay) return;

    const views = document.getElementById('lmViews');
    const left = document.getElementById('lmLeft');
    const leftTop = document.getElementById('lmLeftTop');
    const badge = document.getElementById('lmBadge');
    const badgeImage = document.getElementById('lmBadgeImage');
    const brandName = document.getElementById('lmBrandName');
    const blurb = document.getElementById('lmBlurb');
    const service = document.getElementById('lmService');
    const serviceTrack = document.getElementById('lmSvcTrack');
    const serviceButtons = {
        attendance: document.getElementById('lmSv0'),
        library: document.getElementById('lmSv1'),
    };

    const copy = {
        login: {
            top: overlay.dataset.loginWelcome || 'Welcome to',
            cls: 'lib',
            name: overlay.dataset.loginPortalName || 'PANTAS Portal',
            text: overlay.dataset.loginDescription || 'Sign in to access the Library and Attendance systems.',
            att: false,
            login: true,
        },
        attendance: {
            top: overlay.dataset.registerAttWelcome || 'Register for',
            cls: 'att',
            name: overlay.dataset.registerAttName || 'PANTAS Attendance',
            text: overlay.dataset.registerAttDescription || 'Create your attendance record. Students and employees can log school attendance once approved.',
            att: true,
            login: false,
        },
        library: {
            top: overlay.dataset.registerLibWelcome || 'Register for',
            cls: 'lib',
            name: overlay.dataset.registerLibName || 'PANTAS Library',
            text: overlay.dataset.registerLibDescription || 'Apply for library access. A librarian reviews each request before your library ID is issued.',
            att: false,
            login: false,
        },
    };

    let page = 'login';
    let activeService = overlay.dataset.initialService || 'attendance';

    function paint(mode) {
        const config = copy[mode] || copy.login;
        leftTop.textContent = config.top;
        badge.className = `lm-badge ${config.cls}`;
        brandName.textContent = config.name;
        blurb.textContent = config.text;
        left.classList.toggle('att-mode', config.att);
        left.classList.toggle('login-mode', config.login);
        if (badgeImage) {
            if (config.login) {
                badgeImage.src = badgeImage.dataset.loginSrc;
            } else if (activeService === 'attendance' && badgeImage.dataset.registrationAttSrc) {
                badgeImage.src = badgeImage.dataset.registrationAttSrc;
            } else if (activeService === 'library' && badgeImage.dataset.registrationLibSrc) {
                badgeImage.src = badgeImage.dataset.registrationLibSrc;
            } else {
                badgeImage.src = badgeImage.dataset.defaultRegistrationSrc || badgeImage.dataset.loginSrc;
            }
        }
    }

    function initCanvas(canvas) {
        if (!canvas || canvas.dataset.ready === '1') return;
        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0) return;
        const ratio = window.devicePixelRatio || 1;
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        const context = canvas.getContext('2d');
        context.scale(ratio, ratio);
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.lineWidth = 2;
        context.strokeStyle = '#1f2937';
        canvas.dataset.ready = '1';
        canvas.dataset.hasDrawing = 'false';
    }

    function initAllCanvases() {
        overlay.querySelectorAll('.lm-sig-pad').forEach(initCanvas);
    }

    function go(target) {
        page = target === 'register' ? 'register' : 'login';
        views.style.transform = page === 'register' ? 'translateX(-50%)' : 'translateX(0)';
        paint(page === 'register' ? activeService : 'login');
        setTimeout(initAllCanvases, 60);
    }

    function setService(target) {
        activeService = target === 'library' ? 'library' : 'attendance';
        serviceTrack.style.transform = activeService === 'library' ? 'translateX(-50%)' : 'translateX(0)';
        service.classList.toggle('lib-mode', activeService === 'library');
        serviceButtons.attendance.classList.toggle('sv-active', activeService === 'attendance');
        serviceButtons.library.classList.toggle('sv-active', activeService === 'library');

        // Update tab labels from data attributes
        if (serviceButtons.attendance) {
            serviceButtons.attendance.textContent = overlay.dataset.registerAttendanceTab || 'Attendance';
        }
        if (serviceButtons.library) {
            serviceButtons.library.textContent = overlay.dataset.registerLibraryTab || 'Library';
        }

        paint(activeService);
        setRole(`${activeService}-${overlay.dataset.initialType || 'student'}`);
        setTimeout(initAllCanvases, 60);
    }

    function setRole(role) {
        if (!role.startsWith(`${activeService}-`)) {
            role = `${activeService}-student`;
        }

        overlay.querySelectorAll('[data-lm-role]').forEach((button) => {
            const active = button.dataset.lmRole === role;
            const sameService = button.dataset.lmRole.startsWith(`${activeService}-`);
            button.classList.toggle('on-att', sameService && active && activeService === 'attendance');
            button.classList.toggle('on-lib', sameService && active && activeService === 'library');
        });

        overlay.querySelectorAll('[data-lm-role-panel]').forEach((panel) => {
            panel.classList.toggle('is-active', panel.dataset.lmRolePanel === role);
        });

        setTimeout(initAllCanvases, 60);
    }

    function openLoginModal() {
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('auth-modal-open');
        setTimeout(initAllCanvases, 60);
    }

    function closeLoginModal() {
        if (overlay.dataset.closeUrl) {
            window.location.assign(overlay.dataset.closeUrl);
            return;
        }

        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('auth-modal-open');
    }

    window.openLoginModal = openLoginModal;
    window.closeLoginModal = closeLoginModal;
    window.lmGo = (indexOrName) => go(indexOrName === 1 || indexOrName === 'register' ? 'register' : 'login');
    window.lmSetService = (indexOrName) => setService(indexOrName === 1 || indexOrName === 'library' ? 'library' : 'attendance');

    document.querySelectorAll('[data-auth-open]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            go(button.dataset.authOpen === 'register' ? 'register' : 'login');
            openLoginModal();
        });
    });

    overlay.querySelectorAll('[data-lm-close]').forEach((button) => {
        button.addEventListener('click', closeLoginModal);
    });

    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) closeLoginModal();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && overlay.classList.contains('open')) {
            closeLoginModal();
        }
    });

    overlay.querySelectorAll('[data-lm-go]').forEach((button) => {
        button.addEventListener('click', () => go(button.dataset.lmGo));
    });

    overlay.querySelectorAll('[data-lm-service]').forEach((button) => {
        button.addEventListener('click', () => setService(button.dataset.lmService));
    });

    overlay.querySelectorAll('[data-lm-role]').forEach((button) => {
        button.addEventListener('click', () => setRole(button.dataset.lmRole));
    });

    overlay.querySelectorAll('.lm-file input[type="file"]').forEach((input) => {
        input.addEventListener('change', () => {
            const label = input.closest('.lm-file')?.querySelector('span');
            if (!label) return;
            label.textContent = input.files.length ? input.files[0].name : 'No file chosen';
        });
    });

    overlay.querySelectorAll('[data-lm-clear-sig]').forEach((button) => {
        button.addEventListener('click', () => {
            const [canvasId, hiddenId] = button.dataset.lmClearSig.split(':');
            const canvas = document.getElementById(canvasId);
            const hidden = document.getElementById(hiddenId);
            if (!canvas || !hidden) return;
            initCanvas(canvas);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            canvas.dataset.hasDrawing = 'false';
            hidden.value = '';
        });
    });

    overlay.querySelectorAll('.lm-sig-pad').forEach((canvas) => {
        const hiddenId = canvas.id
            .replace('lmCanvas', 'lmSig')
            .replace('Faculty', 'Faculty');
        const hidden = document.getElementById(hiddenId);
        const context = canvas.getContext('2d');
        let drawing = false;

        function position(event) {
            const rect = canvas.getBoundingClientRect();
            const source = event.touches ? event.touches[0] : event;
            return {
                x: source.clientX - rect.left,
                y: source.clientY - rect.top,
            };
        }

        function start(event) {
            initCanvas(canvas);
            drawing = true;
            const point = position(event);
            context.beginPath();
            context.moveTo(point.x, point.y);
            event.preventDefault();
        }

        function move(event) {
            if (!drawing) return;
            const point = position(event);
            context.lineTo(point.x, point.y);
            context.stroke();
            canvas.dataset.hasDrawing = 'true';
            event.preventDefault();
        }

        function end() {
            if (!drawing) return;
            drawing = false;
            if (hidden && canvas.dataset.hasDrawing === 'true') {
                hidden.value = canvas.toDataURL('image/png');
            }
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        canvas.addEventListener('touchend', end);
    });

    overlay.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelectorAll('.lm-sig-pad').forEach((canvas) => {
                const hidden = document.getElementById(canvas.id.replace('lmCanvas', 'lmSig'));
                if (hidden && canvas.dataset.hasDrawing === 'true') {
                    hidden.value = canvas.toDataURL('image/png');
                }
            });
        });
    });

    let startX = null;
    let startY = null;
    document.getElementById('lmSvcWindow').addEventListener('touchstart', (event) => {
        startX = event.touches[0].clientX;
        startY = event.touches[0].clientY;
    }, { passive: true });

    document.getElementById('lmSvcWindow').addEventListener('touchend', (event) => {
        if (startX === null || startY === null) return;
        const dx = event.changedTouches[0].clientX - startX;
        const dy = event.changedTouches[0].clientY - startY;
        startX = null;
        startY = null;
        if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy)) return;
        setService(dx < 0 ? 'library' : 'attendance');
    }, { passive: true });

    window.addEventListener('resize', () => {
        overlay.querySelectorAll('.lm-sig-pad').forEach((canvas) => {
            canvas.dataset.ready = '';
        });
        initAllCanvases();
    });

    // Update register mode heading and back label from data attributes
    const regHead = overlay.querySelector('[data-lm-view-panel="register"] .lm-reg-head h2');
    const regBack = overlay.querySelector('[data-lm-view-panel="register"] .lm-back');
    if (regHead && overlay.dataset.registerHeading) {
        regHead.textContent = overlay.dataset.registerHeading;
    }
    if (regBack && overlay.dataset.registerLoginLabel) {
        regBack.textContent = overlay.dataset.registerLoginLabel;
    }

    setService(activeService);
    setRole(`${activeService}-${overlay.dataset.initialType || 'student'}`);
    go(overlay.dataset.initialView === 'register' ? 'register' : 'login');

    if (overlay.dataset.openOnLoad === 'true') {
        openLoginModal();
    }
});