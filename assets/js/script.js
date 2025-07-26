// Menu burger mobile avec animations
document.addEventListener('DOMContentLoaded', function() {
    const burger = document.querySelector('.burger');
    const mobileNav = document.querySelector('.mobile-nav');

    if (burger && mobileNav) {
        burger.addEventListener('click', () => {
            // Toggle des classes
            mobileNav.classList.toggle('active');
            burger.classList.toggle('active');
            
            // Animation du body (empêche le scroll quand le menu est ouvert)
            if (mobileNav.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        // Fermer le menu en cliquant sur un lien
        const mobileLinks = document.querySelectorAll('.mobile-nav a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.remove('active');
                burger.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        // Fermer le menu en cliquant à côté
        document.addEventListener('click', (e) => {
            if (!burger.contains(e.target) && !mobileNav.contains(e.target) && mobileNav.classList.contains('active')) {
                mobileNav.classList.remove('active');
                burger.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Animation au scroll pour les cartes
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
            }
        });
    }, observerOptions);

    // Observer toutes les cartes de section
    document.querySelectorAll('.section-card').forEach(card => {
        observer.observe(card);
    });

    // Observer d'autres éléments si nécessaire (pour les futures pages)
    document.querySelectorAll('.fade-in').forEach(element => {
        observer.observe(element);
    });
});