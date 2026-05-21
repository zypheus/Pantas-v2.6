// Function to handle the intersection of elements
const revealElements = (entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            // Stop observing once the animation has triggered
            observer.unobserve(entry.target);
        }
    });
};

// Create the observer
const options = {
    threshold: 0.1 // Triggers when 10% of the element is visible
};

const observer = new IntersectionObserver(revealElements, options);

// Target all elements with the 'reveal' class
document.querySelectorAll('.reveal').forEach(el => {
    observer.observe(el);
});