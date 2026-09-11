/**
 * TICKET-BRAND-004 — Interacciones JS del rediseño "Lujo Forjado"
 *
 * Comportamientos portados desde thor_homepage.html (mockup de referencia):
 *  1. Header pasa a estado "solid" al hacer scroll > 40px.
 *  2. Scroll-reveal: elementos .tma-rv aparecen (clase "in") al entrar en viewport.
 *  3. Chispas animadas dentro de contenedores .tma-sparks (hero, quote-band, cta-forjado).
 *  4. Marquee infinito de logos de clientes (.tma-marquee) duplicando su contenido.
 *
 * Respeta prefers-reduced-motion: no genera chispas ni transiciones de reveal
 * cuando el usuario ha solicitado movimiento reducido.
 *
 * Se encola únicamente en la portada (is_front_page()) — ver functions.php.
 */
(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* 1. Header sólido al hacer scroll ---------------------------------- */
    var header = document.querySelector('.tma-site-header');
    if (header) {
        var onScroll = function () {
            header.classList.toggle('solid', window.scrollY > 40);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* 2. Scroll-reveal (.tma-rv -> .tma-rv.in) --------------------------- */
    var revealEls = document.querySelectorAll('.tma-rv');
    if (revealEls.length) {
        if (prefersReducedMotion || typeof IntersectionObserver === 'undefined') {
            revealEls.forEach(function (el) {
                el.classList.add('in');
            });
        } else {
            var io = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('in');
                            io.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.12 }
            );
            revealEls.forEach(function (el, i) {
                el.style.transitionDelay = (i % 4) * 70 + 'ms';
                io.observe(el);
            });
        }
    }

    /* 3. Chispas animadas (.tma-sparks) ---------------------------------- */
    function makeSparks(el, n) {
        if (!el || prefersReducedMotion) {
            return;
        }
        for (var i = 0; i < n; i++) {
            var s = document.createElement('span');
            s.className = 'spark';
            s.style.left = Math.random() * 100 + '%';
            s.style.bottom = Math.random() * 45 + '%';
            s.style.animationDuration = 2.4 + Math.random() * 3 + 's';
            s.style.animationDelay = Math.random() * 4 + 's';
            el.appendChild(s);
        }
    }

    document.querySelectorAll('.tma-sparks').forEach(function (el) {
        makeSparks(el, 16);
    });

    /* 4. Marquee infinito de logos (.tma-marquee) ------------------------ */
    var marquee = document.querySelector('.tma-marquee');
    if (marquee && !prefersReducedMotion) {
        marquee.innerHTML += marquee.innerHTML;
    }
})();
