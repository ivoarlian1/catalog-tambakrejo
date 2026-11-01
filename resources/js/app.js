function setupMobileNav() {
    const toggle = document.querySelector('[data-nav-toggle]');
    const panel = document.querySelector('[data-nav-panel]');

    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', () => {
        setMobileNavOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    function setMobileNavOpen(isOpen) {
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
        panel.classList.toggle('hidden', !isOpen);
    }

    panel.addEventListener('click', (event) => {
        if (event.target instanceof HTMLAnchorElement) {
            setMobileNavOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setMobileNavOpen(false);
            toggle.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (event.target instanceof Node && !toggle.contains(event.target) && !panel.contains(event.target)) {
            setMobileNavOpen(false);
        }
    });
}

function setupPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        const input = document.getElementById(toggle.dataset.passwordToggle);

        if (!input) {
            return;
        }

        toggle.addEventListener('click', () => {
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            toggle.setAttribute('aria-pressed', String(!isVisible));
            toggle.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
            toggle.setAttribute('title', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
            toggle.querySelector('.sr-only').textContent = isVisible ? 'Tampilkan password' : 'Sembunyikan password';
        });
    });
}

function setupImageViewer() {
    const dialog = document.querySelector('[data-image-viewer]');
    const image = dialog?.querySelector('[data-image-viewer-image]');
    const closeButton = dialog?.querySelector('[data-image-viewer-close]');

    if (!(dialog instanceof HTMLDialogElement) || !(image instanceof HTMLImageElement) || !(closeButton instanceof HTMLButtonElement)) {
        return;
    }

    document.addEventListener('click', (event) => {
        const trigger = event.target instanceof Element ? event.target.closest('[data-image-preview]') : null;

        if (!(trigger instanceof HTMLButtonElement)) {
            return;
        }

        image.src = trigger.dataset.imageSrc ?? '';
        image.alt = trigger.dataset.imageAlt ?? '';
        dialog.showModal();
    });

    closeButton.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
}

function setupCatalogGalleries() {
    document.querySelectorAll('[data-gallery]').forEach((gallery) => {
        const track = gallery.querySelector('[data-gallery-track]');
        const slides = [...gallery.querySelectorAll('[data-gallery-slide]')];
        const dots = [...gallery.querySelectorAll('[data-gallery-dot]')];
        const previousButton = gallery.querySelector('[data-gallery-previous]');
        const nextButton = gallery.querySelector('[data-gallery-next]');
        const counter = gallery.querySelector('[data-gallery-counter]');
        const openButton = gallery.querySelector('[data-gallery-open]');
        const viewer = gallery.querySelector('[data-gallery-viewer]');
        const viewerClose = viewer?.querySelector('[data-gallery-close]');

        if (!(track instanceof HTMLElement) || slides.length < 2) {
            return;
        }

        let currentIndex = 0;

        function updateCurrentSlide() {
            if (track.clientWidth === 0) {
                return;
            }

            currentIndex = Math.min(
                slides.length - 1,
                Math.max(0, Math.round(track.scrollLeft / track.clientWidth)),
            );

            updateControls();
        }

        function updateControls() {
            dots.forEach((dot, index) => {
                const isCurrent = index === currentIndex;
                dot.setAttribute('aria-pressed', String(isCurrent));
                dot.classList.toggle('scale-110', isCurrent);
                dot.classList.toggle('border-sea', isCurrent);
                dot.classList.toggle('bg-sea', isCurrent);
                dot.classList.toggle('ring-2', isCurrent);
                dot.classList.toggle('ring-sea/30', isCurrent);
                dot.classList.toggle('border-white', !isCurrent);
                dot.classList.toggle('bg-slate-400', !isCurrent);
            });

            if (counter) {
                counter.textContent = `${currentIndex + 1} / ${slides.length}`;
            }
        }

        function goToSlide(index) {
            if (index < 0 || index >= slides.length || track.clientWidth === 0) {
                return;
            }

            currentIndex = index;
            track.scrollLeft = currentIndex * track.clientWidth;
            updateControls();
        }

        previousButton?.addEventListener('click', () => goToSlide(currentIndex - 1));
        nextButton?.addEventListener('click', () => goToSlide(currentIndex + 1));

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });

        if (openButton instanceof HTMLButtonElement && viewer instanceof HTMLDialogElement) {
            openButton.addEventListener('click', () => {
                viewer.showModal();
            });

            viewer.addEventListener('click', (event) => {
                if (event.target === viewer) {
                    viewer.close();
                }
            });
        }

        if (viewer instanceof HTMLDialogElement && viewerClose instanceof HTMLButtonElement) {
            viewerClose.addEventListener('click', () => viewer.close());
        }

        track.addEventListener('scroll', updateCurrentSlide, { passive: true });
        window.addEventListener('resize', updateCurrentSlide);
        updateCurrentSlide();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupMobileNav();
    setupPasswordToggles();
    setupImageViewer();
    setupCatalogGalleries();
});
