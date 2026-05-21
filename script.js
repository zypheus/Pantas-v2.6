// Wait for the entire HTML document to be loaded
document.addEventListener('DOMContentLoaded', function() {
    // --- Existing Login Button and Nav Link Animation Code ---
    
    const loginButton = document.querySelector('.login-button');
    if (loginButton) {
        loginButton.addEventListener('mouseenter', function() {
            loginButton.classList.add('is-hovering');
        });
        loginButton.addEventListener('mouseleave', function() {
            loginButton.classList.remove('is-hovering');
        });
    }

    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            link.classList.add('nav-hover');
        });
        link.addEventListener('mouseleave', function() {
            link.classList.remove('nav-hover');
        });
    });

    // --- NEW: Staggered Image Load Animation ---
    
    // 1. Select all image cards in the gallery
    const imageCards = document.querySelectorAll('.image-card');
    
    // 2. Define the delay increment (50ms between each image start)
    const delayIncrement = 50; 
    
    imageCards.forEach((card, index) => {
        // Calculate the delay for the current card
        const delay = index * delayIncrement; 
        
        // Use setTimeout to delay adding the animation class
        setTimeout(() => {
            // Add the class that triggers the CSS animation
            card.classList.add('animate-in');
        }, delay);
    });

});