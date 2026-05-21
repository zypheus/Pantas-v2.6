document.addEventListener('DOMContentLoaded', () => {
    const loginBtn = document.querySelector('.login-btn');
    const title = document.querySelector('h1');

    // Simple interaction: Alert on click
    loginBtn.addEventListener('click', () => {
        alert('Redirecting to Login Page...');
    });

    // Dynamic Header transparency on scroll
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.style.boxShadow = "0 2px 10px rgba(0,0,0,0.1)";
        } else {
            header.style.boxShadow = "none";
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // ... (Previous Button Click Code) ...

    // Intersection Observer for Scroll Animations
    const observerOptions = {
        threshold: 0.2
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Target the new about section
    const aboutSection = document.querySelector('.about-section');
    observer.observe(aboutSection);
});


document.addEventListener('DOMContentLoaded', () => {
    const formBox = document.querySelector('.form-box');
    const contactInfo = document.querySelector('.contact-info-section');

    // Fade in info
    contactInfo.style.opacity = "0";
    contactInfo.style.transform = "translateY(-20px)";
    
    // Fade in form
    formBox.style.opacity = "0";
    formBox.style.transform = "translateY(20px)";

    setTimeout(() => {
        contactInfo.style.transition = "all 0.8s ease-out";
        contactInfo.style.opacity = "1";
        contactInfo.style.transform = "translateY(0)";
    }, 200);

    setTimeout(() => {
        formBox.style.transition = "all 1s ease-out";
        formBox.style.opacity = "1";
        formBox.style.transform = "translateY(0)";
    }, 500);
});
