document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('header');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-links');
    const navLinks = document.querySelectorAll('.nav-links a');
    const mobileNavQuery = window.matchMedia('(max-width: 1440px)');

    const closeMobileNav = () => {
        if (!header || !navToggle) return;
        header.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
    };

    const scrollToSection = (target) => {
        if (!target || !header) return;
        const headerOffset = header.getBoundingClientRect().height + 12;
        const targetTop = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;

        window.scrollTo({
            top: Math.max(targetTop, 0),
            behavior: 'smooth'
        });
    };

    if (header && navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            const isOpen = header.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        navLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                const href = link.getAttribute('href');
                const target = href && href.startsWith('#') ? document.querySelector(href) : null;

                if (target) {
                    event.preventDefault();
                    closeMobileNav();
                    scrollToSection(target);
                    history.pushState(null, '', href);
                    return;
                }

                closeMobileNav();
            });
            link.addEventListener('mouseenter', () => link.classList.add('nav-hover'));
            link.addEventListener('mouseleave', () => link.classList.remove('nav-hover'));
        });

        mobileNavQuery.addEventListener('change', (event) => {
            if (!event.matches) {
                closeMobileNav();
            }
        });
    }

    const loginButton = document.querySelector('.login-button');
    if (loginButton) {
        loginButton.addEventListener('mouseenter', () => loginButton.classList.add('is-hovering'));
        loginButton.addEventListener('mouseleave', () => loginButton.classList.remove('is-hovering'));
    }

    window.addEventListener('scroll', () => {
        if (!header) return;
        header.style.boxShadow = window.scrollY > 50
            ? '0 2px 10px rgba(0,0,0,0.1)'
            : '0 2px 10px rgba(0,0,0,0.05)';
    });

    const aboutSection = document.querySelector('.about-section');
    if (aboutSection && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.2 });

        observer.observe(aboutSection);
    }

    const imageCards = document.querySelectorAll('.image-card');
    imageCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-in');
        }, index * 50);
    });

    const formBox = document.querySelector('.form-box');
    const contactInfo = document.querySelector('.contact-info-section');

    if (formBox && contactInfo) {
        contactInfo.style.opacity = '0';
        contactInfo.style.transform = 'translateY(-20px)';
        formBox.style.opacity = '0';
        formBox.style.transform = 'translateY(20px)';

        setTimeout(() => {
            contactInfo.style.transition = 'all 0.8s ease-out';
            contactInfo.style.opacity = '1';
            contactInfo.style.transform = 'translateY(0)';
        }, 200);

        setTimeout(() => {
            formBox.style.transition = 'all 1s ease-out';
            formBox.style.opacity = '1';
            formBox.style.transform = 'translateY(0)';
        }, 500);
    }

    document.querySelectorAll('footer a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (!target) return;

            event.preventDefault();
            scrollToSection(target);
            history.pushState(null, '', link.getAttribute('href'));
        });
    });
});
