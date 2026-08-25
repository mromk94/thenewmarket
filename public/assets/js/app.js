// Minimal global JS for The New Age Marketplace
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!prefersReduced) {
            document.body.classList.add('motion-ok');
        }

        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('mobile-menu');

        if (menuToggle && menu) {
            menuToggle.addEventListener('click', function () {
                const isHidden = menu.style.display === 'none' || getComputedStyle(menu).display === 'none';
                menu.style.display = isHidden ? 'block' : 'none';
                menuToggle.setAttribute('aria-expanded', String(isHidden));
            });
        }

        // Auto-dismiss toast messages
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(function (toast) {
            setTimeout(function () {
                toast.style.opacity = '0';
                setTimeout(function () {
                    toast.remove();
                }, 300);
            }, 5000);
        });

        // Hero slider
        const heroSlider = document.querySelector('[data-hero-slider]');
        if (heroSlider) {
            const track = heroSlider.querySelector('.hero-track');
            const dots = heroSlider.querySelectorAll('[data-hero-dot]');
            const slides = track ? track.children.length : 0;
            let current = 0;
            let interval;

            function goToSlide(index) {
                if (!track) return;
                current = index;
                track.style.transform = 'translateX(-' + (current * 100) + '%)';
                dots.forEach(function (dot, i) {
                    dot.classList.toggle('active', i === current);
                });
            }

            function next() { goToSlide((current + 1) % slides); }
            function prev() { goToSlide((current - 1 + slides) % slides); }

            const prevBtn = heroSlider.querySelector('[data-hero-prev]');
            const nextBtn = heroSlider.querySelector('[data-hero-next]');
            if (prevBtn) prevBtn.addEventListener('click', function () { clearInterval(interval); prev(); interval = setInterval(next, 6000); });
            if (nextBtn) nextBtn.addEventListener('click', function () { clearInterval(interval); next(); interval = setInterval(next, 6000); });

            dots.forEach(function (dot, i) {
                dot.addEventListener('click', function () { clearInterval(interval); goToSlide(i); interval = setInterval(next, 6000); });
            });

            goToSlide(0);
            if (slides > 1) {
                interval = setInterval(next, 6000);
            }
        }

        // Product carousels
        document.querySelectorAll('[data-carousel]').forEach(function (carousel) {
            const track = carousel.querySelector('.carousel-track');
            const cards = track ? track.children.length : 0;
            const prev = carousel.querySelector('[data-carousel-prev]');
            const next = carousel.querySelector('[data-carousel-next]');
            if (!track || cards === 0 || !prev || !next) return;

            let visible = 4;
            if (window.innerWidth <= 1024) visible = 3;
            if (window.innerWidth <= 768) visible = 2;
            if (window.innerWidth <= 480) visible = 1;

            let current = 0;
            const max = Math.max(0, cards - visible);

            function move() {
                const pct = (current / visible) * 100;
                track.style.transform = 'translateX(-' + current * (100 / visible) + '%)';
            }

            next.addEventListener('click', function () {
                current = Math.min(current + 1, max);
                move();
            });

            prev.addEventListener('click', function () {
                current = Math.max(current - 1, 0);
                move();
            });
        });
    });
})();
