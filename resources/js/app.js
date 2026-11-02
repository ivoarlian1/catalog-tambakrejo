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

function setupImageCompression() {
    const maxFileSize = 2 * 1024 * 1024;
    const maxImageDimension = 2560;
    const compressionQualities = [0.86, 0.76, 0.66, 0.56];
    const fileInputs = [...document.querySelectorAll('[data-image-compress]')];

    if (fileInputs.length === 0) {
        return;
    }

    async function compressImage(file) {
        let image;

        if (typeof createImageBitmap === 'function') {
            image = await createImageBitmap(file);
        } else {
            image = await new Promise((resolve, reject) => {
                const objectUrl = URL.createObjectURL(file);
                const element = new Image();

                element.onload = () => {
                    URL.revokeObjectURL(objectUrl);
                    resolve(element);
                };
                element.onerror = () => {
                    URL.revokeObjectURL(objectUrl);
                    reject(new Error('Foto tidak dapat dibuka.'));
                };
                element.src = objectUrl;
            });
        }

        try {
            let width = image.width;
            let height = image.height;
            const scale = Math.min(1, maxImageDimension / Math.max(width, height));
            width = Math.max(1, Math.round(width * scale));
            height = Math.max(1, Math.round(height * scale));

            for (let attempt = 0; attempt < 12; attempt++) {
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext('2d');

                if (!context) {
                    throw new Error('Kompresi foto tidak dapat dijalankan.');
                }

                context.fillStyle = '#fff';
                context.fillRect(0, 0, width, height);
                context.drawImage(image, 0, 0, width, height);

                for (const quality of compressionQualities) {
                    const blob = await new Promise((resolve, reject) => {
                        canvas.toBlob((result) => {
                            if (result) {
                                resolve(result);
                            } else {
                                reject(new Error('Foto tidak dapat dikompres.'));
                            }
                        }, 'image/jpeg', quality);
                    });

                    if (blob.size <= maxFileSize) {
                        const filename = file.name.replace(/\.[^.]+$/, '') + '.jpg';

                        return new File([blob], filename, {
                            type: 'image/jpeg',
                            lastModified: file.lastModified,
                        });
                    }
                }

                width = Math.max(1, Math.floor(width * 0.8));
                height = Math.max(1, Math.floor(height * 0.8));
            }
        } finally {
            image.close?.();
        }

        throw new Error('Ukuran foto tetap lebih dari 2 MB setelah dikompres.');
    }

    fileInputs.forEach((input) => {
        const form = input.closest('form');

        if (!(input instanceof HTMLInputElement) || !form) {
            return;
        }

        const status = document.createElement('p');
        status.className = 'mt-1 text-sm text-sea';
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        input.insertAdjacentElement('afterend', status);

        form.addEventListener('submit', async (event) => {
            if (form.dataset.imageCompressionReady === 'true') {
                delete form.dataset.imageCompressionReady;

                return;
            }

            if (form.dataset.imageCompressing === 'true') {
                event.preventDefault();

                return;
            }

            const oversizedInputs = [...form.querySelectorAll('[data-image-compress]')]
                .filter((fileInput) => [...(fileInput.files ?? [])]
                    .some((file) => file.size > maxFileSize && /^image\/(jpeg|png|webp)$/i.test(file.type)));

            if (oversizedInputs.length === 0) {
                return;
            }

            event.preventDefault();
            form.dataset.imageCompressing = 'true';
            const submitButtons = [...form.querySelectorAll('button[type="submit"], button:not([type])')];
            const previousDisabledStates = submitButtons.map((button) => button.disabled);
            submitButtons.forEach((button) => {
                button.disabled = true;
            });

            try {
                for (const fileInput of oversizedInputs) {
                    const files = [...(fileInput.files ?? [])];
                    const compressedFiles = [];
                    const message = fileInput.parentElement?.querySelector('[data-image-compression-status]');
                    const statusElement = message ?? fileInput.nextElementSibling;

                    if (statusElement instanceof HTMLElement) {
                        statusElement.textContent = 'Foto besar sedang dikompres. Mohon tunggu...';
                    }

                    for (const file of files) {
                        compressedFiles.push(
                            file.size > maxFileSize && /^image\/(jpeg|png|webp)$/i.test(file.type)
                                ? await compressImage(file)
                                : file,
                        );
                    }

                    const transfer = new DataTransfer();
                    compressedFiles.forEach((file) => transfer.items.add(file));
                    fileInput.files = transfer.files;

                    if (statusElement instanceof HTMLElement) {
                        statusElement.textContent = 'Foto berhasil dikompres otomatis.';
                    }
                }

                submitButtons.forEach((button, index) => {
                    button.disabled = previousDisabledStates[index];
                });
                form.dataset.imageCompressionReady = 'true';
                form.requestSubmit(event.submitter instanceof HTMLElement ? event.submitter : undefined);
            } catch {
                const message = 'Foto tidak dapat dikompres otomatis. Pilih foto JPG, PNG, atau WEBP yang lebih kecil.';

                oversizedInputs.forEach((fileInput) => {
                    const statusElement = fileInput.nextElementSibling;

                    if (statusElement instanceof HTMLElement) {
                        statusElement.textContent = message;
                        statusElement.setAttribute('role', 'alert');
                    }
                });
                submitButtons.forEach((button, index) => {
                    button.disabled = previousDisabledStates[index];
                });
                delete form.dataset.imageCompressing;
            }
        });

        status.dataset.imageCompressionStatus = '';
    });

    document.addEventListener('submit', (event) => {
        if (event.target instanceof HTMLFormElement && event.target.dataset.imageCompressionReady === 'true') {
            delete event.target.dataset.imageCompressing;
        }
    }, true);
}

document.addEventListener('DOMContentLoaded', () => {
    setupMobileNav();
    setupPasswordToggles();
    setupImageViewer();
    setupCatalogGalleries();
    setupImageCompression();
});
