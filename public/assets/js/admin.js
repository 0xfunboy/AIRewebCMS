document.addEventListener('DOMContentLoaded', function adminInit() {
    function qs(selector, scope) {
        var context = scope || document;
        return context ? context.querySelector(selector) : null;
    }

    function qsa(selector, scope) {
        var context = scope || document;
        if (!context) {
            return [];
        }
        var nodeList = context.querySelectorAll(selector);
        return Array.prototype.slice.call(nodeList);
    }

    const ensureToaster = () => {
        let container = qs('.admin-toaster');
        if (!container) {
            container = document.createElement('div');
            container.className = 'admin-toaster';
            document.body.appendChild(container);
        }
        return container;
    };

    const toaster = ensureToaster();

    const showToast = (message, type = 'info') => {
        if (!message) {
            return;
        }
        const toast = document.createElement('div');
        toast.className = 'admin-toast';
        if (type === 'error') {
            toast.classList.add('error');
        }
        toast.textContent = message;
        toaster.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('fade');
        }, 2600);
        setTimeout(() => {
            toast.remove();
        }, 3400);
    };

    const copyToClipboard = async (value) => {
        if (!value) {
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(value);
            return;
        }
        const temp = document.createElement('textarea');
        temp.value = value;
        temp.style.position = 'fixed';
        temp.style.opacity = '0';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
    };

    const flashCopyState = (button, fallback = 'Copy URL') => {
        if (!button) {
            return;
        }
        const original = button.textContent || fallback;
        button.textContent = 'Copied!';
        button.disabled = true;
        setTimeout(() => {
            button.textContent = original;
            button.disabled = false;
        }, 1400);
    };

    const bindCopyButton = (button, getValue, fallbackLabel = 'Copy URL') => {
        if (!button || button.dataset.copyBound === 'true') {
            return;
        }
        button.dataset.copyBound = 'true';
        button.addEventListener('click', async () => {
            try {
                const value = getValue();
                if (!value) {
                    return;
                }
                await copyToClipboard(value);
                flashCopyState(button, fallbackLabel);
                showToast('Media URL copied to clipboard.');
            } catch (error) {
                console.error('Unable to copy media URL', error);
                showToast('Unable to copy URL.', 'error');
            }
        });
    };

    const formatBytes = (bytes) => {
        const value = Number(bytes) || 0;
        if (value < 1024) {
            return `${value} B`;
        }
        if (value < 1024 * 1024) {
            return `${(value / 1024).toFixed(1)} KB`;
        }
        return `${(value / (1024 * 1024)).toFixed(2)} MB`;
    };

    const formatDimensions = (width, height) => {
        const w = Number(width);
        const h = Number(height);
        if (Number.isFinite(w) && Number.isFinite(h) && w > 0 && h > 0) {
            return `W ${w} × H ${h} px`;
        }
        return 'W n/a × H n/a px';
    };

    const formatTimestamp = (timestamp) => {
        const value = Number(timestamp);
        if (!Number.isFinite(value) || value <= 0) {
            return 'Unknown';
        }
        const date = new Date(value * 1000);
        if (Number.isNaN(date.getTime())) {
            return 'Unknown';
        }
        const pad = (input) => String(input).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    };

    const isImageType = (type) => ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'ico'].includes(String(type).toLowerCase());

    const initToggles = () => {
        qsa('[data-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const id = toggle.getAttribute('data-toggle') || '';
                const target = document.getElementById(id);
                if (target) {
                    target.classList.toggle('hidden');
                }
            });
        });
    };

    const toSlug = (value) =>
        value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 150);

    const initSlugSync = () => {
        qsa('[data-slug-source]').forEach((input) => {
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
    };

    const attachRemoveHandler = (scope) => {
        qsa('[data-repeat-remove]', scope).forEach((button) => {
            button.addEventListener('click', () => {
                const item = button.closest('[data-repeat-item]');
                if (item && item.parentElement && item.parentElement.children.length > 1) {
                    item.remove();
                } else if (item) {
                    qsa('input, textarea', item).forEach((field) => {
                        field.value = '';
                    });
                }
            });
        });
    };

    const initRepeaters = () => {
        qsa('[data-repeat-add]').forEach((button) => {
            const root = button.closest('[data-repeat-root]');
            if (!root) {
                return;
            }
            const container = qs('[data-repeat-container]', root);
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
                const fields = qsa(`[name^="${name}["]`, container);
                let max = 0;
                fields.forEach((field) => {
                    const nameAttr = field.getAttribute('name') || '';
                    const match = nameAttr.match(/\[(\d+)\]/);
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
                qsa('[data-repeat-field]', fragment).forEach((field) => {
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
    };

    const initCopyButtons = (scope = document) => {
        qsa('[data-copy-url]', scope).forEach((button) => {
            bindCopyButton(button, () => {
                const raw = button.getAttribute('data-copy-url') || '';
                return raw.startsWith('http') ? raw : `${window.location.origin}${raw}`;
            });
        });

        qsa('[data-media-copy]', scope).forEach((button) => {
            const wrapper = button.closest('[data-media-input]');
            const input = wrapper ? qs('[data-media-url]', wrapper) : null;
            bindCopyButton(
                button,
                () => {
                    if (!input || !input.value) {
                        return '';
                    }
                    const raw = input.value;
                    return raw.startsWith('http') ? raw : `${window.location.origin}${raw}`;
                },
                'Copy URL'
            );
        });
    };

    const fetchMediaList = async () => {
        const response = await fetch('/admin/media/list?format=json', {
            method: 'GET',
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.ok) {
            throw new Error(data.error || data.message || 'Unable to load media list.');
        }
        return Array.isArray(data.media) ? data.media : [];
    };

    class MediaPicker {
        constructor({ fetchMedia }) {
            this.fetchMedia = fetchMedia;
            this.media = [];
            this.modal = null;
            this.listEl = null;
            this.loadingEl = null;
            this.searchInput = null;
            this.filterSelect = null;
            this.onSelect = null;
        }

        async open({ onSelect } = {}) {
            this.onSelect = onSelect || null;
            if (!this.modal) {
                this.buildModal();
            }
            this.modal.classList.remove('hidden');
            document.body.classList.add('admin-modal-open');
            await this.load(true);
            this.updateList();
        }

        close() {
            if (this.modal) {
                this.modal.classList.add('hidden');
            }
            document.body.classList.remove('admin-modal-open');
            this.onSelect = null;
        }

        async load(force = false) {
            if (!force && this.media.length > 0) {
                return;
            }
            if (this.loadingEl) {
                this.loadingEl.classList.remove('hidden');
            }
            try {
                this.media = await this.fetchMedia();
                this.updateFilterOptions();
            } catch (error) {
                console.error('Unable to load media list', error);
                showToast(error.message || 'Unable to load media list.', 'error');
                this.media = [];
            } finally {
                if (this.loadingEl) {
                    this.loadingEl.classList.add('hidden');
                }
            }
        }

        setMedia(list) {
            this.media = Array.isArray(list) ? list.slice() : [];
            this.updateFilterOptions();
            if (this.modal && !this.modal.classList.contains('hidden')) {
                this.updateList();
            }
        }

        updateFilterOptions() {
            if (!this.filterSelect) {
                return;
            }
            const previous = this.filterSelect.value || 'all';
            const types = Array.from(
                new Set(
                    this.media
                        .map((item) => (item.type || '').toLowerCase())
                        .filter((value) => value)
                )
            ).sort();

            this.filterSelect.innerHTML = '';

            const allOption = document.createElement('option');
            allOption.value = 'all';
            allOption.textContent = 'All types';
            this.filterSelect.appendChild(allOption);

            types.forEach((type) => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = `.${type.toUpperCase()}`;
                this.filterSelect.appendChild(option);
            });

            if (types.includes(previous)) {
                this.filterSelect.value = previous;
            } else {
                this.filterSelect.value = 'all';
            }
        }

        buildModal() {
            const overlay = document.createElement('div');
            overlay.className = 'admin-modal media-picker hidden';

            const content = document.createElement('div');
            content.className = 'admin-modal__content media-picker__content';
            overlay.appendChild(content);

            const header = document.createElement('div');
            header.className = 'media-picker__header';

            const title = document.createElement('h2');
            title.className = 'media-picker__title';
            title.textContent = 'Select media';
            header.appendChild(title);

            const controls = document.createElement('div');
            controls.className = 'media-picker__controls';

            this.searchInput = document.createElement('input');
            this.searchInput.type = 'search';
            this.searchInput.placeholder = 'Search media…';
            this.searchInput.className = 'media-picker__search';
            controls.appendChild(this.searchInput);

            this.filterSelect = document.createElement('select');
            this.filterSelect.className = 'media-picker__filter';
            controls.appendChild(this.filterSelect);

            header.appendChild(controls);
            content.appendChild(header);

            this.loadingEl = document.createElement('div');
            this.loadingEl.className = 'media-picker__loading hidden';
            this.loadingEl.textContent = 'Loading media…';
            content.appendChild(this.loadingEl);

            this.listEl = document.createElement('div');
            this.listEl.className = 'media-picker__grid';
            content.appendChild(this.listEl);

            const footer = document.createElement('div');
            footer.className = 'media-picker__footer';

            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'media-picker__button';
            cancelButton.textContent = 'Cancel';
            footer.appendChild(cancelButton);

            content.appendChild(footer);

            document.body.appendChild(overlay);
            this.modal = overlay;

            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    this.close();
                }
            });

            cancelButton.addEventListener('click', () => this.close());
            this.searchInput.addEventListener('input', () => this.updateList());
            this.filterSelect.addEventListener('change', () => this.updateList());
        }

        filterMedia() {
            const searchValue = this.searchInput ? this.searchInput.value : '';
            const term = (searchValue || '').toLowerCase().trim();
            const filter = this.filterSelect ? this.filterSelect.value : 'all';

            return this.media.filter((item) => {
                if (filter && filter !== 'all' && (item.type || '').toLowerCase() !== filter) {
                    return false;
                }
                if (!term) {
                    return true;
                }
                const haystack = `${item.path || ''} ${item.url || ''}`.toLowerCase();
                return haystack.includes(term);
            });
        }

        updateList() {
            if (!this.listEl) {
                return;
            }
            this.listEl.innerHTML = '';
            const items = this.filterMedia();
            if (!items.length) {
                const empty = document.createElement('p');
                empty.className = 'media-picker__empty';
                empty.textContent = 'No media found.';
                this.listEl.appendChild(empty);
                return;
            }

            items.forEach((item) => {
                const card = document.createElement('div');
                card.className = 'media-picker__item';

                const thumb = document.createElement('div');
                thumb.className = 'media-picker__thumb';
                if (isImageType(item.type)) {
                    const img = document.createElement('img');
                    img.src = item.url;
                    img.alt = item.path || 'Media preview';
                    thumb.appendChild(img);
                } else {
                    const badge = document.createElement('span');
                    badge.className = 'media-picker__type';
                    badge.textContent = (item.type || '').toUpperCase();
                    thumb.appendChild(badge);
                }
                card.appendChild(thumb);

                const details = document.createElement('div');
                details.className = 'media-picker__details';

                const name = document.createElement('p');
                name.className = 'media-picker__name';
                name.textContent = item.path || '';
                details.appendChild(name);

                const meta = document.createElement('p');
                meta.className = 'media-picker__meta';
                meta.textContent = `${formatDimensions(item.width, item.height)} · ${formatBytes(item.size || 0)}`;
                details.appendChild(meta);

                const badge = document.createElement('span');
                badge.className = `media-picker__badge ${item.in_use ? 'is-used' : 'is-free'}`;
                badge.textContent = item.in_use ? 'In use' : 'Not in use';
                details.appendChild(badge);

                card.appendChild(details);

                const actions = document.createElement('div');
                actions.className = 'media-picker__actions';

                const selectButton = document.createElement('button');
                selectButton.type = 'button';
                selectButton.className = 'media-picker__select';
                selectButton.textContent = 'Select';
                selectButton.addEventListener('click', () => {
                    if (this.onSelect) {
                        const path = item.path || '';
                        this.onSelect({
                            ...item,
                            path,
                            url: item.url || (path ? `/${path.replace(/^\/+/u, '')}` : ''),
                        });
                    }
                    this.close();
                });

                actions.appendChild(selectButton);
                card.appendChild(actions);

                this.listEl.appendChild(card);
            });
        }
    }

    const initMediaInputs = (mediaPicker) => {
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

        qsa('[data-media-input]').forEach((wrapper) => {
            const urlInput = qs('[data-media-url]', wrapper);
            const fileInput = qs('[data-media-file]', wrapper);
            const preview = qs('[data-media-preview]', wrapper);
            const placeholder = qs('[data-media-placeholder]', wrapper);
            const link = qs('[data-media-link]', wrapper);
            const uploadLabel = qs('[data-media-upload-label]', wrapper);
            const uploadButton = qs('[data-media-upload]', wrapper);
            const selectButton = qs('[data-media-select]', wrapper);
            let objectUrl = null;

            const disableLink = () => {
                if (!link) {
                    return;
                }
                link.href = '#';
                link.textContent = 'No file selected';
                link.classList.remove('text-cy', 'hover:underline');
                link.classList.add('text-muted', 'pointer-events-none');
                link.setAttribute('aria-disabled', 'true');
            };

            const enableLink = (href, label) => {
                if (!link) {
                    return;
                }
                link.href = href;
                link.textContent = label;
                link.classList.add('text-cy', 'hover:underline');
                link.classList.remove('text-muted', 'pointer-events-none');
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

            const applyUrlValue = (value) => {
                if (urlInput) {
                    urlInput.value = value;
                }
                const normalized = makeMediaUrl(value);
                applyPreview(normalized, false);
            };

            if (!preview || (preview instanceof HTMLImageElement && preview.src === '')) {
                const initialUrl = urlInput ? makeMediaUrl(urlInput.value) : '';
                if (initialUrl) {
                    applyPreview(initialUrl, false);
                } else {
                    applyPreview('', false);
                }
            }

            if (uploadButton && fileInput) {
                uploadButton.addEventListener('click', () => {
                    fileInput.click();
                });
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

            if (selectButton) {
                selectButton.addEventListener('click', async () => {
                    if (!mediaPicker) {
                        return;
                    }
                    try {
                        await mediaPicker.open({
                            onSelect: (item) => {
                                const newValue = item.path
                                    ? item.path.startsWith('/')
                                        ? item.path
                                        : `/${item.path.replace(/^\/+/u, '')}`
                                    : '';
                                if (fileInput) {
                                    fileInput.value = '';
                                }
                                if (uploadLabel) {
                                    uploadLabel.classList.add('hidden');
                                }
                                applyUrlValue(newValue);
                            },
                        });
                    } catch (error) {
                        console.error('Unable to open media picker', error);
                        showToast(error.message || 'Unable to open media picker.', 'error');
                    }
                });
            }

            disableLink();
            initCopyButtons(wrapper);
        });
    };

    const initMediaLibrary = (libraryEl, fetchMediaFn, csrfToken, mediaPicker) => {
        if (!libraryEl) {
            return {
                refresh: async () => [],
            };
        }

        const gridEl = qs('[data-media-grid]', libraryEl);
        const emptyEl = qs('[data-media-empty]', libraryEl);

        const buildMediaCard = (item) => {
            const card = document.createElement('article');
            card.className = 'media-card card space-y-3';
            card.setAttribute('data-media-card', '');
            card.dataset.mediaType = item.type || '';
            card.dataset.mediaVariants = Object.keys(item.variants || {}).join(',');
            card.dataset.mediaPath = item.path || '';
            card.dataset.mediaUrl = item.url || '';
            card.dataset.mediaWidth = item.width != null ? String(item.width) : '';
            card.dataset.mediaHeight = item.height != null ? String(item.height) : '';
            card.dataset.mediaInUse = item.in_use ? '1' : '0';

            const thumb = document.createElement('div');
            thumb.className = 'media-card__thumb bg-bg2 border border-stroke rounded-lg overflow-hidden aspect-video flex items-center justify-center';
            if (isImageType(item.type)) {
                const img = document.createElement('img');
                img.src = item.url;
                img.alt = item.path || 'Media preview';
                img.className = 'max-h-full max-w-full object-contain media-card__image';
                thumb.appendChild(img);
            } else {
                const span = document.createElement('span');
                span.className = 'text-xs text-muted uppercase tracking-wide';
                span.textContent = (item.type || '').toUpperCase();
                thumb.appendChild(span);
            }
            card.appendChild(thumb);

            const body = document.createElement('div');
            body.className = 'space-y-2';

            const pathEl = document.createElement('p');
            pathEl.className = 'media-card__path text-sm font-semibold text-acc break-all';
            pathEl.textContent = item.path || '';
            body.appendChild(pathEl);

            const meta = document.createElement('div');
            meta.className = 'media-card__meta text-xs text-muted space-y-1';

            const dimsEl = document.createElement('p');
            dimsEl.className = 'media-card__dimensions';
            dimsEl.textContent = formatDimensions(item.width, item.height);
            meta.appendChild(dimsEl);

            const sizeLine = document.createElement('p');
            const sizeSpan = document.createElement('span');
            sizeSpan.className = 'media-card__filesize';
            sizeSpan.textContent = formatBytes(item.size || 0);
            sizeLine.appendChild(sizeSpan);
            sizeLine.appendChild(document.createTextNode(' · '));
            const updatedSpan = document.createElement('span');
            updatedSpan.className = 'media-card__updated';
            updatedSpan.textContent = `Updated ${formatTimestamp(item.modified)}`;
            sizeLine.appendChild(updatedSpan);
            meta.appendChild(sizeLine);

            body.appendChild(meta);

            const statusRow = document.createElement('div');
            statusRow.className = 'media-card__status flex items-center gap-2 text-xs';
            const badge = document.createElement('span');
            badge.className = `media-card__badge ${item.in_use ? 'media-card__badge--used' : 'media-card__badge--unused'}`;
            badge.setAttribute('data-media-inuse-label', '');
            const dot = document.createElement('span');
            dot.className = 'media-card__badge-dot';
            badge.appendChild(dot);
            const label = document.createElement('span');
            label.textContent = item.in_use ? 'In use' : 'Not in use';
            badge.appendChild(label);
            statusRow.appendChild(badge);
            body.appendChild(statusRow);

            card.appendChild(body);

            const variants = item.variants || {};
            if (Object.keys(variants).length > 0) {
                const variantsWrap = document.createElement('div');
                variantsWrap.className = 'flex flex-wrap gap-2 text-xs media-card__variants';
                Object.entries(variants).forEach(([variantType, variant]) => {
                    const pill = document.createElement('div');
                    pill.className = 'media-variant-pill';

                    const labelEl = document.createElement('span');
                    labelEl.className = 'media-variant-label';
                    labelEl.textContent = variantType.toUpperCase();
                    pill.appendChild(labelEl);

                    const sizeEl = document.createElement('span');
                    sizeEl.className = 'media-variant-size';
                    sizeEl.textContent = formatBytes(variant.size || 0);
                    pill.appendChild(sizeEl);

                    const openEl = document.createElement('a');
                    openEl.className = 'media-variant-open';
                    openEl.href = variant.url;
                    openEl.target = '_blank';
                    openEl.rel = 'noopener';
                    openEl.textContent = 'Open';
                    pill.appendChild(openEl);

                    const copyEl = document.createElement('button');
                    copyEl.type = 'button';
                    copyEl.className = 'media-variant-copy';
                    copyEl.setAttribute('data-copy-url', variant.url);
                    copyEl.textContent = 'Copy';
                    pill.appendChild(copyEl);

                    variantsWrap.appendChild(pill);
                });
                card.appendChild(variantsWrap);
            }

            const actions = document.createElement('div');
            actions.className = 'media-card__actions flex flex-wrap items-center gap-2 text-sm';

            const openLink = document.createElement('a');
            openLink.href = item.url;
            openLink.target = '_blank';
            openLink.rel = 'noopener';
            openLink.className = 'media-card__action';
            openLink.textContent = 'Open';
            actions.appendChild(openLink);

            const copyButton = document.createElement('button');
            copyButton.type = 'button';
            copyButton.className = 'media-card__action';
            copyButton.setAttribute('data-copy-url', item.url);
            copyButton.textContent = 'Copy URL';
            actions.appendChild(copyButton);

            const replaceButton = document.createElement('button');
            replaceButton.type = 'button';
            replaceButton.className = 'media-card__action';
            replaceButton.setAttribute('data-media-replace', '');
            replaceButton.textContent = 'Replace';
            actions.appendChild(replaceButton);

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'media-card__action danger';
            deleteButton.setAttribute('data-media-delete', '');
            deleteButton.textContent = 'Delete';
            actions.appendChild(deleteButton);

            const replaceInput = document.createElement('input');
            replaceInput.type = 'file';
            replaceInput.accept = '.png,.jpg,.jpeg,.webp,.svg,.ico';
            replaceInput.className = 'hidden';
            replaceInput.setAttribute('data-media-replace-input', '');
            actions.appendChild(replaceInput);

            card.appendChild(actions);

            return card;
        };

        const deleteMedia = async (card) => {
            if (!csrfToken) {
                throw new Error('Missing CSRF token.');
            }
            const path = card.dataset.mediaPath || '';
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('path', path);

            const response = await fetch('/admin/media/delete', {
                method: 'POST',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                body: formData,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok) {
                throw new Error(data.error || data.message || 'Unable to delete media.');
            }
            return data;
        };

        const replaceMedia = async (card, file) => {
            if (!csrfToken) {
                throw new Error('Missing CSRF token.');
            }
            const path = card.dataset.mediaPath || '';
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('path', path);
            formData.append('file', file, file.name);

            const response = await fetch('/admin/media/replace', {
                method: 'POST',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                body: formData,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok) {
                throw new Error(data.error || data.message || 'Replace operation failed.');
            }
            return data;
        };

        const bindCardActions = (card) => {
            const replaceButton = qs('[data-media-replace]', card);
            const replaceInput = qs('[data-media-replace-input]', card);
            const deleteButton = qs('[data-media-delete]', card);

            if (replaceButton && replaceInput) {
                replaceButton.addEventListener('click', () => {
                    replaceInput.click();
                });
                replaceInput.addEventListener('change', async () => {
                    try {
                        const file = replaceInput.files && replaceInput.files[0];
                        if (!file) {
                            return;
                        }
                        card.classList.add('media-card--loading');
                        await replaceMedia(card, file);
                        showToast('Media replaced.');
                        await refresh();
                    } catch (error) {
                        console.error('Replace failed', error);
                        showToast(error.message || 'Unable to replace media.', 'error');
                    } finally {
                        replaceInput.value = '';
                        card.classList.remove('media-card--loading');
                    }
                });
            }

            if (deleteButton) {
                deleteButton.addEventListener('click', async () => {
                    const confirmed = window.confirm('Delete this media file permanently? This can’t be undone.');
                    if (!confirmed) {
                        return;
                    }
                    try {
                        card.classList.add('media-card--loading');
                        await deleteMedia(card);
                        showToast('Media deleted.');
                        await refresh();
                    } catch (error) {
                        console.error('Delete failed', error);
                        showToast(error.message || 'Unable to delete media.', 'error');
                    } finally {
                        card.classList.remove('media-card--loading');
                    }
                });
            }

            initCopyButtons(card);
        };

        const renderMediaGrid = (items) => {
            if (!gridEl || !emptyEl) {
                return;
            }

            if (!items.length) {
                gridEl.innerHTML = '';
                gridEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                return;
            }

            emptyEl.classList.add('hidden');
            gridEl.classList.remove('hidden');
            gridEl.innerHTML = '';
            items.forEach((item) => {
                const card = buildMediaCard(item);
                gridEl.appendChild(card);
                bindCardActions(card);
            });
        };

        const refresh = async () => {
            const items = await fetchMediaFn();
            renderMediaGrid(items);
            if (mediaPicker && typeof mediaPicker.setMedia === 'function') {
                mediaPicker.setMedia(items);
            }
            return items;
        };

        qsa('[data-media-card]', gridEl).forEach((card) => {
            bindCardActions(card);
        });
        initCopyButtons(gridEl);

        return {
            refresh,
        };
    };

    const initMediaTools = (mediaLibrary) => {
        const mediaTools = qs('[data-media-tools]');
        if (!mediaTools) {
            return;
        }

        const statusBox = qs('[data-media-status]', mediaTools);
        const summary = qs('[data-media-summary]', mediaTools);
        const logList = qs('[data-media-log]', mediaTools);

        qsa('form[data-media-action]', mediaTools).forEach((form) => {
            const action = form.getAttribute('data-media-action') || 'optimize';
            const button = qs('button[type="submit"]', form);
            const fileInput = qs('input[type="file"]', form);

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (action === 'upload' && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
                    if (summary) {
                        summary.textContent = 'Select a file to upload.';
                    }
                    return;
                }

                if (button) {
                    button.disabled = true;
                    button.dataset.originalLabel = button.textContent || '';
                    button.textContent =
                        action === 'mirror'
                            ? 'Mirroring…'
                            : action === 'upload'
                            ? 'Uploading…'
                            : 'Optimizing…';
                }

                if (statusBox) {
                    statusBox.classList.remove('hidden');
                }
                if (summary) {
                    summary.textContent =
                        action === 'mirror'
                            ? 'Phase 1 – mirroring remote assets…'
                            : action === 'upload'
                            ? 'Uploading media…'
                            : 'Phase 2 – converting images to WebP…';
                }
                if (logList) {
                    logList.innerHTML = '';
                }

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.getAttribute('action') || '/admin/media/optimize', {
                        method: 'POST',
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                        body: formData,
                    });
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok || !data.ok) {
                        const message = data.error || data.message || 'Unexpected error while processing media.';
                        if (summary) {
                            summary.textContent = message;
                        }
                        showToast(message, 'error');
                        return;
                    }

                    if (logList) {
                        const steps = Array.isArray(data.steps) ? data.steps : [];
                        logList.innerHTML = '';
                        steps.forEach((step) => {
                            const item = document.createElement('li');
                            item.textContent = `Phase ${step.phase}: ${step.message} (${step.current}/${step.total})`;
                            if (step.status === 'error') {
                                item.classList.add('media-optimize-status__item--error');
                            } else if (step.status === 'skip') {
                                item.classList.add('media-optimize-status__item--skip');
                            }
                            logList.appendChild(item);
                        });
                    }

                    if (summary) {
                        if (data.message) {
                            summary.textContent = data.message;
                        } else if (action === 'mirror') {
                            summary.textContent = `Mirrored ${data.processed}/${data.total} assets (errors: ${data.errors}).`;
                        } else if (action === 'upload') {
                            summary.textContent = 'Upload complete.';
                        } else {
                            summary.textContent = `Converted ${data.processed}/${data.total} files to WebP (errors: ${data.errors}).`;
                        }
                    }

                    showToast(data.message || (action === 'upload' ? 'Upload complete.' : 'Media task finished.'));
                    await mediaLibrary.refresh();
                } catch (error) {
                    console.error('Media action failed', error);
                    const message = (error && error.message) ? error.message : 'Operation failed.';
                    if (summary) {
                        summary.textContent = message;
                    }
                    showToast(message, 'error');
                } finally {
                    if (button) {
                        button.disabled = false;
                        button.textContent = button.dataset.originalLabel || button.textContent;
                        delete button.dataset.originalLabel;
                    }
                    if (fileInput) {
                        fileInput.value = '';
                    }
                }
            });
        });
    };

    initToggles();
    initSlugSync();
    initRepeaters();

    const mediaLibraryEl = qs('[data-media-library]');
    const csrfToken =
        mediaLibraryEl && mediaLibraryEl.getAttribute
            ? mediaLibraryEl.getAttribute('data-csrf-token') || ''
            : '';

    const mediaPicker = new MediaPicker({ fetchMedia: fetchMediaList });

    initMediaInputs(mediaPicker);

    const mediaLibrary = initMediaLibrary(mediaLibraryEl, fetchMediaList, csrfToken, mediaPicker);
    initMediaTools(mediaLibrary);

    initCopyButtons();
});
