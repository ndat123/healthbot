// Advanced Animation System
// Smooth, modern animations for enhanced user experience

// Initialize animations when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all animation components
    initAnimations();

    // Initialize scroll-based animations
    initScrollAnimations();

    // Initialize interactive elements
    initInteractiveElements();
});

// Core animation initialization
function initAnimations() {
    // Fade-in elements
    const fadeElements = document.querySelectorAll('[data-animate="fade-in"]');
    fadeElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.8s ease-out';

        // Trigger animation after small delay
        setTimeout(() => {
            el.style.opacity = '1';
        }, 100);
    });

    // Slide-up elements
    const slideElements = document.querySelectorAll('[data-animate="slide-up"]');
    slideElements.forEach(el => {
        el.style.transform = 'translateY(20px)';
        el.style.opacity = '0';
        el.style.transition = 'transform 0.8s ease-out, opacity 0.8s ease-out';

        // Trigger animation after small delay
        setTimeout(() => {
            el.style.transform = 'translateY(0)';
            el.style.opacity = '1';
        }, 100);
    });

    // Bounce elements
    const bounceElements = document.querySelectorAll('[data-animate="bounce"]');
    bounceElements.forEach(el => {
        el.style.animation = 'bounceGentle 2s infinite';
    });
}

// Scroll-based animations
function initScrollAnimations() {
    // Check if IntersectionObserver is supported
    if (!('IntersectionObserver' in window)) {
        // Fallback for older browsers
        document.querySelectorAll('[data-scroll-animate]').forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
        return;
    }

    // Create observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const animationType = el.getAttribute('data-scroll-animate');

                switch(animationType) {
                    case 'fade-in':
                        el.style.opacity = '1';
                        el.style.transition = 'opacity 0.8s ease-out';
                        break;
                    case 'slide-up':
                        el.style.transform = 'translateY(0)';
                        el.style.opacity = '1';
                        el.style.transition = 'transform 0.8s ease-out, opacity 0.8s ease-out';
                        break;
                    case 'scale-up':
                        el.style.transform = 'scale(1)';
                        el.style.opacity = '1';
                        el.style.transition = 'transform 0.8s ease-out, opacity 0.8s ease-out';
                        break;
                }

                // Stop observing once animated
                observer.unobserve(el);
            }
        });
    }, {
        threshold: 0.1
    });

    // Observe all elements with scroll animations
    document.querySelectorAll('[data-scroll-animate]').forEach(el => {
        // Set initial styles
        if (el.getAttribute('data-scroll-animate') === 'fade-in') {
            el.style.opacity = '0';
        } else if (el.getAttribute('data-scroll-animate') === 'slide-up') {
            el.style.transform = 'translateY(20px)';
            el.style.opacity = '0';
        } else if (el.getAttribute('data-scroll-animate') === 'scale-up') {
            el.style.transform = 'scale(0.95)';
            el.style.opacity = '0';
        }

        // Start observing
        observer.observe(el);
    });
}

// Interactive element animations
function initInteractiveElements() {
    // Button hover effects
    const buttons = document.querySelectorAll('.btn-animate');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.1)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });

    // Card hover effects
    const cards = document.querySelectorAll('.card-animate');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 20px 30px rgba(0, 0, 0, 0.1)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });

    // Feature hover effects
    const features = document.querySelectorAll('.feature-animate');
    features.forEach(feature => {
        feature.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.feature-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease-out';
            }
        });

        feature.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.feature-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });
}

// Custom animation definitions
const style = document.createElement('style');
style.textContent = `
    @keyframes bounceGentle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes scaleUp {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
`;
document.head.appendChild(style);