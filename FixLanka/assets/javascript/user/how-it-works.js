// How It Works Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll for home button
    const homeBtn = document.querySelector('.btn-home');
    if (homeBtn) {
        homeBtn.addEventListener('click', function(e) {
            // Smooth transition when going to home
            window.location.href = this.getAttribute('href');
        });
    }

    // Add animation on scroll for step cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all step cards
    const stepCards = document.querySelectorAll('.step-card');
    stepCards.forEach((card, index) => {
        // Add staggered animation delay
        card.style.transitionDelay = `${index * 0.1}s`;
        observer.observe(card);
    });

    // Smooth reveal for about section
    const aboutSection = document.querySelector('.about-section');
    if (aboutSection) {
        observer.observe(aboutSection);
    }

    // Smooth reveal for providers section
    const providersSection = document.querySelector('.providers-section');
    if (providersSection) {
        observer.observe(providersSection);
    }
});
