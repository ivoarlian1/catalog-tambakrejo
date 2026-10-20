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

document.addEventListener('DOMContentLoaded', () => {
    setupMobileNav();
    setupPasswordToggles();
    setupImageViewer();
});
