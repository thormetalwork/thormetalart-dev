document.addEventListener('DOMContentLoaded', () => {
    const desktopMenu = document.querySelector('.tma-main-nav--desktop .tma-services-menu');
    if (!desktopMenu) return;

    const isDesktop = () => window.matchMedia('(min-width: 961px)').matches;

    desktopMenu.addEventListener('mouseenter', () => {
        if (isDesktop()) desktopMenu.open = true;
    });

    desktopMenu.addEventListener('mouseleave', () => {
        if (isDesktop() && !desktopMenu.contains(document.activeElement)) desktopMenu.open = false;
    });

    desktopMenu.addEventListener('focusin', () => {
        if (isDesktop()) desktopMenu.open = true;
    });

    desktopMenu.addEventListener('focusout', () => {
        window.requestAnimationFrame(() => {
            if (isDesktop() && !desktopMenu.contains(document.activeElement)) desktopMenu.open = false;
        });
    });

    desktopMenu.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            desktopMenu.open = false;
            desktopMenu.querySelector('summary')?.focus();
        }
    });
});
