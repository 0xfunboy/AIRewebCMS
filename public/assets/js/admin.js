document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('[data-toggle]');
    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const target = document.getElementById(toggle.getAttribute('data-toggle') || '');
            if (target) {
                target.classList.toggle('hidden');
            }
        });
    });

    const toSlug = (value) =>
        value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 150);

    document.querySelectorAll('[data-slug-source]').forEach((input) => {
        const targetId = input.getAttribute('data-slug-source');
        if (!targetId) {
            return;
        }
        const target = document.getElementById(targetId);
        if (!target) {
            return;
        }

        const maybeSync = () => {
            if (target.dataset.slugDirty === 'true') {
                return;
            }
            target.value = toSlug(input.value);
        };

        if (!target.value) {
            maybeSync();
        } else {
            target.dataset.slugDirty = 'true';
        }

        input.addEventListener('input', maybeSync);
        target.addEventListener('input', () => {
            target.dataset.slugDirty = target.value ? 'true' : '';
        });
    });

    const attachRemoveHandler = (scope) => {
        scope.querySelectorAll('[data-repeat-remove]').forEach((button) => {
            button.addEventListener('click', () => {
                const item = button.closest('[data-repeat-item]');
                if (item && item.parentElement && item.parentElement.children.length > 1) {
                    item.remove();
                } else if (item) {
                    item.querySelectorAll('input, textarea').forEach((field) => {
                        field.value = '';
                    });
                }
            });
        });
    };

    document.querySelectorAll('[data-repeat-add]').forEach((button) => {
        const root = button.closest('[data-repeat-root]');
        if (!root) {
            return;
        }
        const container = root.querySelector('[data-repeat-container]');
        if (!container) {
            return;
        }
        const templateId = button.getAttribute('data-repeat-template');
        const template = templateId ? document.getElementById(templateId) : null;
        if (!template || !(template instanceof HTMLTemplateElement)) {
            return;
        }

        const name = button.getAttribute('data-repeat-name') || 'items';

        const initIndex = () => {
            const fields = container.querySelectorAll(`[name^="${name}["]`);
            let max = 0;
            fields.forEach((field) => {
                const match = field.getAttribute('name')?.match(/\[(\d+)\]/);
                if (match) {
                    const value = parseInt(match[1], 10);
                    if (!Number.isNaN(value)) {
                        max = Math.max(max, value + 1);
                    }
                }
            });
            container.dataset.repeatIndex = String(max);
        };

        if (!container.dataset.repeatIndex) {
            initIndex();
        }

        button.addEventListener('click', () => {
            const index = parseInt(container.dataset.repeatIndex || '0', 10);
            container.dataset.repeatIndex = String(index + 1);
            const fragment = template.content.cloneNode(true);
            fragment.querySelectorAll('[data-repeat-field]').forEach((field) => {
                const key = field.getAttribute('data-repeat-field');
                if (!key) {
                    return;
                }
                field.setAttribute('name', `${name}[${index}][${key}]`);
                if (field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement) {
                    field.value = '';
                }
            });
            container.appendChild(fragment);
            attachRemoveHandler(container);
        });

        attachRemoveHandler(container);
    });

    document.querySelectorAll('[data-copy-url]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.getAttribute('data-copy-url') || '';
            if (!value) {
                return;
            }
            const absolute = value.startsWith('http') ? value : `${window.location.origin}${value}`;
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(absolute);
                } else {
                    const temp = document.createElement('textarea');
                    temp.value = absolute;
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                }
                const original = button.textContent;
                button.textContent = 'Copied!';
                button.disabled = true;
                setTimeout(() => {
                    button.textContent = original || 'Copy URL';
                    button.disabled = false;
                }, 1600);
            } catch (error) {
                console.error('Unable to copy media URL', error);
            }
        });
    });

    const makeMediaUrl = (value) => {
        if (!value) {
            return '';
        }
        const trimmed = value.trim();
        if (!trimmed) {
            return '';
        }
        if (/^https?:\/\//i.test(trimmed) || trimmed.startsWith('/')) {
            return trimmed;
        }
        return `/media/${trimmed.replace(/^\/+/u, '')}`;
    };

    document.querySelectorAll('[data-media-input]').forEach((wrapper) => {
        const urlInput = wrapper.querySelector('[data-media-url]');
        const fileInput = wrapper.querySelector('[data-media-file]');
        const preview = wrapper.querySelector('[data-media-preview]');
        const placeholder = wrapper.querySelector('[data-media-placeholder]');
        const link = wrapper.querySelector('[data-media-link]');
        const uploadLabel = wrapper.querySelector('[data-media-upload-label]');
        let objectUrl = null;

        const disableLink = () => {
            if (!link) {
                return;
            }
            link.href = '#';
            link.textContent = 'No file selected';
            link.classList.remove('text-cy');
            link.classList.add('text-muted', 'pointer-events-none');
            link.classList.remove('hover:underline');
            link.setAttribute('aria-disabled', 'true');
        };

        const enableLink = (href, label) => {
            if (!link) {
                return;
            }
            link.href = href;
            link.textContent = label;
            link.classList.remove('text-muted', 'pointer-events-none');
            link.classList.add('text-cy');
            link.classList.add('hover:underline');
            link.removeAttribute('aria-disabled');
        };

        const applyPreview = (src, pending = false) => {
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }
            if (pending && src && src.startsWith('blob:')) {
                objectUrl = src;
            }

            if (preview) {
                if (src) {
                    preview.src = src;
                    preview.classList.remove('hidden');
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                    if (placeholder) {
                        placeholder.classList.remove('hidden');
                    }
                }
            }

            if (uploadLabel) {
                uploadLabel.classList.toggle('hidden', !pending);
            }

            if (src) {
                enableLink(src, pending ? 'Preview upload' : 'Open current');
            } else {
                disableLink();
            }
        };

        if (!preview || (preview instanceof HTMLImageElement && preview.src === '')) {
            const initialUrl = urlInput ? makeMediaUrl(urlInput.value) : '';
            if (initialUrl) {
                applyPreview(initialUrl, false);
            } else {
                applyPreview('', false);
            }
        }

        if (urlInput) {
            const updateFromUrl = () => {
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    return;
                }
                const normalized = makeMediaUrl(urlInput.value);
                applyPreview(normalized, false);
            };
            urlInput.addEventListener('input', updateFromUrl);
            urlInput.addEventListener('blur', updateFromUrl);
        }

        if (fileInput) {
            fileInput.addEventListener('change', () => {
                const file = fileInput.files && fileInput.files[0];
                if (file) {
                    const blobUrl = URL.createObjectURL(file);
                    applyPreview(blobUrl, true);
                } else {
                    const fallback = urlInput ? makeMediaUrl(urlInput.value) : '';
                    applyPreview(fallback, false);
                }
            });
        }

        if (!link) {
            return;
        }

        if (!link.getAttribute('href') || link.getAttribute('href') === '#') {
            disableLink();
        }
    });
});
