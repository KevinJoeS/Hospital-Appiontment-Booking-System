document.addEventListener('DOMContentLoaded', () => {
    console.log("Hospital System JS initialized.");

    // 1. Auto-dismiss alerts & add close button
    const alerts = document.querySelectorAll('.error-alert, .success-alert');
    alerts.forEach(alert => {
        // Add a close button if the alert doesn't already have one
        const closeBtn = document.createElement('span');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.marginLeft = 'auto';
        closeBtn.style.fontSize = '18px';
        closeBtn.style.fontWeight = 'bold';

        // Ensure the alert uses flexbox to position the close button correctly
        alert.style.display = 'flex';
        alert.style.alignItems = 'center';
        alert.style.justifyContent = 'space-between';

        alert.appendChild(closeBtn);

        closeBtn.addEventListener('click', () => {
            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'scale(0.95)';
            setTimeout(() => alert.remove(), 300);
        });

        // Auto fade out after 5 seconds
        setTimeout(() => {
            if (document.body.contains(alert)) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });

    // 2. Form Validation (Password Match on Registration)
    const regForm = document.querySelector('form');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (regForm && passwordInput && confirmPasswordInput) {
        regForm.addEventListener('submit', (e) => {
            if (passwordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
                confirmPasswordInput.focus();
                confirmPasswordInput.style.borderColor = 'var(--danger)';
            }
        });

        confirmPasswordInput.addEventListener('input', () => {
            if (passwordInput.value === confirmPasswordInput.value) {
                confirmPasswordInput.style.borderColor = 'var(--success)';
            } else {
                confirmPasswordInput.style.borderColor = 'var(--danger)';
            }
        });
    }

    // 3. Date input restriction (prevent selecting past dates)
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        const today = new Date().toISOString().split('T')[0];
        // Only set if min is not already set
        if (!input.hasAttribute('min')) {
            input.setAttribute('min', today);
        }
    });

    // 4. Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== "#") {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // 5. Input focus animation effects
    const inputs = document.querySelectorAll('.form-group input, .form-group select');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            const group = input.closest('.form-group');
            if (group) {
                group.style.transform = 'translateY(-2px)';
                group.style.transition = 'transform 0.3s ease';
            }
        });
        input.addEventListener('blur', () => {
            const group = input.closest('.form-group');
            if (group) {
                group.style.transform = 'translateY(0)';
            }
        });
    });

    // 6. Button click ripple/scale effect
    const buttons = document.querySelectorAll('.btn, .login-button, .register-button');
    buttons.forEach(btn => {
        btn.addEventListener('mousedown', () => {
            btn.style.transform = 'scale(0.96)';
        });
        btn.addEventListener('mouseup', () => {
            btn.style.transform = 'translateY(-2px)'; // Return to hover state
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = ''; // Clear inline transform when mouse leaves
        });
    });

    // 7. Page Load Fade-In Animation (Staggered)
    // Select main containers that should animate in on page load
    const mainElements = document.querySelectorAll('.login-card, .register-card, .booking-card, .panel, .hero');
    mainElements.forEach((el, index) => {
        // Initial state
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px) scale(0.98)';
        el.style.transition = 'opacity 0.8s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)';
        
        // Trigger animation with a stagger delay
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0) scale(1)';
        }, 150 + (index * 120));
    });

    // 8. Scroll Reveal Animation (Intersection Observer)
    // Select elements to reveal on scroll
    const revealElements = document.querySelectorAll('.feature-card, .info-box');
    
    // Set initial state for reveal elements
    // Set initial state for reveal elements with alternating directions
    revealElements.forEach((el, index) => {
        el.style.opacity = '0';
        const slideDir = (index % 2 === 0) ? -30 : 30; // Alternate left/right
        el.style.transform = `translateX(${slideDir}px) translateY(20px) scale(0.95)`;
        el.style.transition = 'opacity 0.7s cubic-bezier(0.23, 1, 0.32, 1), transform 0.7s cubic-bezier(0.23, 1, 0.32, 1)';
    });

    // Create the observer
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0) translateY(0) scale(1)';
                // Unobserve after revealing to animate only once
                observer.unobserve(entry.target);
            }
        });
    }, {
        root: null, // viewport
        threshold: 0.1, // trigger when 10% visible
        rootMargin: "0px 0px -50px 0px" // trigger slightly before it hits the bottom
    });

    // Start observing
    revealElements.forEach(el => {
        revealObserver.observe(el);
    });

    // 9. Premium Card Hover Parallax Effect
    const cards = document.querySelectorAll('.feature-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
            card.style.transition = 'transform 0.1s ease';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0)';
            card.style.transition = 'transform 0.5s ease';
        });
    });

    // 10. Smooth Page Transition (Fade-out on navigation)
    const navLinks = document.querySelectorAll('a:not([href^="#"]):not([target="_blank"])');
    
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            
            // Don't intercept if it's a javascript: link or empty
            if (!href || href.startsWith('javascript:')) return;

            e.preventDefault();
            
            // Create a fade-out effect on the body
            document.body.style.transition = 'opacity 0.4s ease-in-out, transform 0.4s ease-in-out';
            document.body.style.opacity = '0';
            document.body.style.transform = 'translateY(-10px)';

            // Navigate after the animation finishes
            setTimeout(() => {
                window.location.href = href;
            }, 400);
        });
    });

    // Handle bfcache (ensure page is visible when navigating back)
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            document.body.style.opacity = '1';
            document.body.style.transform = 'translateY(0)';
        }
    });
});
