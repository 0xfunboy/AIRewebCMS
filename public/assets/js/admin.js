document.addEventListener('DOMContentLoaded', function () {
    function qs(selector, scope) {
        var context = scope || document;
        return context ? context.querySelector(selector) : null;
    }

    function qsa(selector, scope) {
        var context = scope || document;
        if (!context) {
            return [];
        }
        return Array.prototype.slice.call(context.querySelectorAll(selector));
    }

    function parseJsonSafely(text) {
        if (!text) {
            return {};
        }
        try {
            return JSON.parse(text);
        } catch (err) {
            return null;
        }
    }

    function toNumber(value, fallback) {
        var number = Number(value);
        return isFinite(number) ? number : (typeof fallback === 'number' ? fallback : 0);
    }

    function safeMessage(value, fallback) {
        if (typeof value === 'string' && value.trim()) {
            return value.trim();
        }
        return typeof fallback === 'string' ? fallback : '';
    }

    function formatBytes(bytes) {
        var value = Number(bytes);
        if (!value || value < 0) {
            return '0 B';
        }
        if (value < 1024) {
            return value + ' B';
        }
        if (value < 1024 * 1024) {
            return (value / 1024).toFixed(1) + ' KB';
        }
        return (value / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function formatDimensions(width, height) {
        var w = Number(width);
        var h = Number(height);
        if (isFinite(w) && isFinite(h) && w > 0 && h > 0) {
            return 'W ' + w + ' × H ' + h + ' px';
        }
        return 'W n/a × H n/a px';
    }

    function formatTimestamp(timestamp) {
        var value = Number(timestamp);
        if (!value || value <= 0) {
            return 'Unknown';
        }
        var date = new Date(value * 1000);
        if (isNaN(date.getTime())) {
            return 'Unknown';
        }
        function pad(input) {
            var str = String(input);
            return str.length === 1 ? '0' + str : str;
        }
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
            ' ' + pad(date.getHours()) + ':' + pad(date.getMinutes());
    }

    var csrfToken = (function () {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.content) {
            return meta.content;
        }
        var library = qs('[data-media-library]');
        if (library && library.getAttribute('data-csrf-token')) {
            return library.getAttribute('data-csrf-token');
        }
        return '';
    })();

    function ensureCsrf(formData) {
        if (csrfToken && !formData.has('csrf_token')) {
            formData.append('csrf_token', csrfToken);
        }
        return formData;
    }

    function postForm(url, formData) {
        var payload = ensureCsrf(formData || new FormData());
        if (window.fetch) {
            return fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' },
                body: payload
            }).then(function (response) {
                return response.text().then(function (text) {
                    var data = parseJsonSafely(text);
                    if (response.ok) {
                        return data || {};
                    }
                    var message = (data && data.error) ? data.error : 'Request failed (' + response.status + ')';
                    var error = new Error(message);
                    error.payload = data;
                    throw error;
                });
            });
        }

        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.withCredentials = true;
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    var data = parseJsonSafely(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(data || {});
                    } else {
                        var err = new Error((data && data.error) ? data.error : 'Request failed (' + xhr.status + ')');
                        err.payload = data;
                        reject(err);
                    }
                }
            };
            xhr.send(payload);
        });
    }

    function postJson(url, payload) {
        var body = payload || {};
        if (csrfToken && !('csrf_token' in body)) {
            body.csrf_token = csrfToken;
        }
        if (window.fetch) {
            return fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'fetch'
                },
                body: JSON.stringify(body)
            }).then(function (response) {
                return response.text().then(function (text) {
                    var data = parseJsonSafely(text);
                    if (response.ok) {
                        return data || {};
                    }
                    var message = (data && data.error) ? data.error : 'Request failed (' + response.status + ')';
                    var error = new Error(message);
                    error.payload = data;
                    throw error;
                });
            });
        }

        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.withCredentials = true;
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    var data = parseJsonSafely(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(data || {});
                    } else {
                        var err = new Error((data && data.error) ? data.error : 'Request failed (' + xhr.status + ')');
                        err.payload = data;
                        reject(err);
                    }
                }
            };
            xhr.send(JSON.stringify(body));
        });
    }

    function fetchJson(url) {
        if (window.fetch) {
            return fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' },
                credentials: 'same-origin'
            }).then(function (response) {
                return response.text().then(function (text) {
                    var data = parseJsonSafely(text);
                    if (!response.ok) {
                        var message = (data && data.error) ? data.error : 'Request failed (' + response.status + ')';
                        var error = new Error(message);
                        error.payload = data;
                        throw error;
                    }
                    return data || {};
                });
            });
        }

        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.withCredentials = true;
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    var data = parseJsonSafely(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(data || {});
                    } else {
                        var err = new Error((data && data.error) ? data.error : 'Request failed (' + xhr.status + ')');
                        err.payload = data;
                        reject(err);
                    }
                }
            };
            xhr.send(null);
        });
    }

    function summarizeResponse(payload, fallbackAction) {
        var steps = Array.isArray(payload && payload.steps) ? payload.steps : [];
        var processed = toNumber(payload && payload.processed, steps.length);
        var total = toNumber(payload && payload.total, steps.length);
        var errors = toNumber(payload && payload.errors, 0);
        var action = safeMessage(payload && payload.action, fallbackAction);
        var message = safeMessage(payload && payload.message, action + ' completed.');
        return {
            action: action,
            processed: processed,
            total: total,
            errors: errors,
            message: message,
            steps: steps
        };
    }

    var btnMirror = document.getElementById('btn-mirror');
    var btnOptimize = document.getElementById('btn-optimize');
    var fileUpload = document.getElementById('file-upload');
    var mediaLog = document.getElementById('media-log');
    var mediaExplorer = document.getElementById('media-explorer');
    var busy = false;

    function setBusy(state) {
        busy = state;
        [btnMirror, btnOptimize].forEach(function (button) {
            if (button) {
                button.disabled = state;
                button.classList.toggle('opacity-60', state);
            }
        });
    }

    function logLine(message) {
        if (!mediaLog) {
            return;
        }
        if (mediaLog.classList.contains('hidden')) {
            mediaLog.classList.remove('hidden');
            mediaLog.textContent = '';
        }
        var text = (typeof message === 'string') ? message : JSON.stringify(message);
        mediaLog.textContent += text + '\n';
        mediaLog.scrollTop = mediaLog.scrollHeight;
    }

    function clearLog() {
        if (mediaLog) {
            mediaLog.textContent = '';
        }
    }

    function renderMediaCards(items) {
        if (!mediaExplorer) {
            return;
        }
        mediaExplorer.innerHTML = '';
        if (!items || !items.length) {
            var empty = document.createElement('div');
            empty.className = 'card text-sm text-muted';
            empty.textContent = 'No assets available. Use Upload image or Local Mirror Images to populate the library.';
            mediaExplorer.appendChild(empty);
            return;
        }

        var grid = document.createElement('div');
        grid.className = 'grid gap-4 md:grid-cols-2 xl:grid-cols-3';
        items.forEach(function (item) {
            var card = document.createElement('article');
            card.className = 'card space-y-3';
            card.setAttribute('data-media-item', 'true');
            card.setAttribute('data-path', item.path || '');

            var thumb = document.createElement('div');
            thumb.className = 'bg-bg2 border border-stroke rounded-lg overflow-hidden aspect-video flex items-center justify-center';
            if (item.type && ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'ico'].indexOf(String(item.type).toLowerCase()) !== -1) {
                var img = document.createElement('img');
                img.src = item.url || '';
                img.alt = item.path || '';
                img.className = 'max-h-full max-w-full object-contain';
                thumb.appendChild(img);
            } else {
                var placeholder = document.createElement('span');
                placeholder.className = 'text-xs text-muted uppercase tracking-wide';
                placeholder.textContent = (item.type || '').toUpperCase();
                thumb.appendChild(placeholder);
            }
            card.appendChild(thumb);

            var body = document.createElement('div');
            body.className = 'space-y-2';

            var header = document.createElement('div');
            header.className = 'flex items-center justify-between gap-2';

            var path = document.createElement('p');
            path.className = 'text-sm font-semibold text-acc break-all';
            path.textContent = item.path || '';
            header.appendChild(path);

            var badge = document.createElement('span');
            var inUse = !!item.in_use;
            badge.className = 'media-card__badge ' + (inUse ? 'media-card__badge--used' : 'media-card__badge--unused');
            badge.innerHTML = '<span class="media-card__badge-dot"></span>' + (inUse ? 'In use' : 'Not in use');
            header.appendChild(badge);
            body.appendChild(header);

            var meta = document.createElement('p');
            meta.className = 'text-xs text-muted';
            meta.textContent = formatDimensions(item.width, item.height) + ' · ' + formatBytes(item.size || 0) + ' · Updated ' + formatTimestamp(item.modified);
            body.appendChild(meta);

            card.appendChild(body);

            var actions = document.createElement('div');
            actions.className = 'flex flex-wrap items-center gap-2 text-sm';

            var copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'media-card__action';
            copyBtn.setAttribute('data-action', 'copy');
            copyBtn.setAttribute('data-url', item.url || '');
            copyBtn.textContent = 'Copy URL';
            actions.appendChild(copyBtn);

            var replaceBtn = document.createElement('button');
            replaceBtn.type = 'button';
            replaceBtn.className = 'media-card__action';
            replaceBtn.setAttribute('data-action', 'replace');
            replaceBtn.textContent = 'Replace';
            actions.appendChild(replaceBtn);

            var deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'media-card__action danger';
            deleteBtn.setAttribute('data-action', 'delete');
            deleteBtn.textContent = 'Delete';
            actions.appendChild(deleteBtn);

            var replaceInput = document.createElement('input');
            replaceInput.type = 'file';
            replaceInput.accept = '.png,.jpg,.jpeg,.webp,.svg,.ico';
            replaceInput.className = 'hidden media-replace-input';
            replaceInput.setAttribute('data-path', item.path || '');
            actions.appendChild(replaceInput);

            card.appendChild(actions);
            grid.appendChild(card);
        });

        mediaExplorer.appendChild(grid);
    }

    function refreshMediaGrid() {
        return fetchJson('/admin/media/list?format=json').then(function (data) {
            renderMediaCards(data.media || []);
        }).catch(function (error) {
            renderMediaCards([]);
            logLine('Unable to refresh media list: ' + error.message);
        });
    }

    function handleSummary(action, payload) {
        var summary = summarizeResponse(payload, action);
        logLine(summary.message + ' · processed ' + summary.processed + '/' + summary.total + ' (errors: ' + summary.errors + ')');
        if (summary.steps && summary.steps.length) {
            summary.steps.forEach(function (step) {
                var label = step && step.message ? step.message : 'Step completed';
                var status = step && step.status ? '[' + step.status + ']' : '';
                logLine(' • ' + label + ' ' + status);
            });
        }
    }

    function doMirror() {
        if (busy) {
            logLine('Mirror already running.');
            return;
        }
        setBusy(true);
        logLine('Starting Local Mirror Images…');
        clearLog();
        var formData = ensureCsrf(new FormData());
        postForm('/admin/media/mirror', formData).then(function (payload) {
            handleSummary('mirror', payload);
            return refreshMediaGrid();
        }).catch(function (error) {
            logLine('Mirror failed: ' + error.message);
            alert('Mirror failed: ' + error.message);
        }).finally(function () {
            setBusy(false);
        });
    }

    function doOptimize() {
        if (busy) {
            logLine('Optimize already running.');
            return;
        }
        setBusy(true);
        logLine('Starting Optimize to WebP…');
        clearLog();
        var formData = ensureCsrf(new FormData());
        postForm('/admin/media/optimize', formData).then(function (payload) {
            handleSummary('optimize', payload);
            return refreshMediaGrid();
        }).catch(function (error) {
            logLine('Optimize failed: ' + error.message);
            alert('Optimize failed: ' + error.message);
        }).finally(function () {
            setBusy(false);
        });
    }

    function doUpload(file) {
        if (!file) {
            return Promise.resolve();
        }
        var formData = new FormData();
        formData.append('file', file);
        ensureCsrf(formData);
        return postForm('/admin/media/upload', formData).then(function (payload) {
            var path = payload && payload.path ? payload.path : file.name;
            logLine('Uploaded ' + path + '.');
            return refreshMediaGrid();
        }).catch(function (error) {
            logLine('Upload failed: ' + error.message);
            alert('Upload failed: ' + error.message);
        });
    }

    function doReplace(path, file) {
        if (!path || !file) {
            return Promise.resolve();
        }
        var formData = new FormData();
        formData.append('path', path);
        formData.append('file', file);
        ensureCsrf(formData);
        return postForm('/admin/media/replace', formData).then(function (payload) {
            logLine('Replaced ' + (payload && payload.path ? payload.path : path) + '.');
            return refreshMediaGrid();
        }).catch(function (error) {
            logLine('Replace failed: ' + error.message);
            alert('Replace failed: ' + error.message);
        });
    }

    function doDelete(path) {
        if (!path) {
            return;
        }
        if (!window.confirm('Delete this media file permanently? This cannot be undone.')) {
            return;
        }
        var payload = { path: path };
        postJson('/admin/media/delete', payload).then(function () {
            logLine('Deleted ' + path + '.');
            return refreshMediaGrid();
        }).catch(function (error) {
            logLine('Delete failed: ' + error.message);
            alert('Delete failed: ' + error.message);
        });
    }

    if (btnMirror) {
        btnMirror.addEventListener('click', function () {
            doMirror();
        });
    }

    if (btnOptimize) {
        btnOptimize.addEventListener('click', function () {
            doOptimize();
        });
    }

    if (fileUpload) {
        fileUpload.addEventListener('change', function (event) {
            var file = event.target && event.target.files ? event.target.files[0] : null;
            if (!file) {
                return;
            }
            doUpload(file).finally(function () {
                event.target.value = '';
            });
        });
    }

    if (mediaExplorer) {
        mediaExplorer.addEventListener('click', function (event) {
            var trigger = event.target.closest('[data-action]');
            if (!trigger) {
                return;
            }
            var card = trigger.closest('[data-media-item]');
            var path = card ? card.getAttribute('data-path') : '';
            var action = trigger.getAttribute('data-action');
            if (action === 'copy') {
                var url = trigger.getAttribute('data-url') || (card ? card.getAttribute('data-url') : '');
                var value = url || (path ? '/' + path.replace(/^\/+/, '') : '');
                navigator.clipboard.writeText(value).then(function () {
                    logLine('Copied ' + value);
                }).catch(function (err) {
                    logLine('Copy failed: ' + (err && err.message ? err.message : err));
                });
            }
            if (action === 'replace') {
                var input = card ? card.querySelector('.media-replace-input') : null;
                if (input) {
                    input.click();
                }
            }
            if (action === 'delete') {
                doDelete(path);
            }
        });

        mediaExplorer.addEventListener('change', function (event) {
            var input = event.target;
            if (!(input && input.classList.contains('media-replace-input'))) {
                return;
            }
            var file = input.files && input.files[0];
            var path = input.getAttribute('data-path');
            input.value = '';
            if (!file) {
                return;
            }
            doReplace(path, file);
        });
    }

    refreshMediaGrid();

    /* Inline editing (toolbar + mode) -------------------------------------------------- */

    var inlineState = {
        enabled: false,
        csrf: csrfToken,
        initialized: false
    };

    function updateInlineCsrf(token) {
        if (token) {
            inlineState.csrf = token;
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) {
                meta.setAttribute('content', token);
            }
        }
    }

    function getInlineCsrf() {
        if (inlineState.csrf) {
            return inlineState.csrf;
        }
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') || '' : '';
    }

    function toggleInlineControls(show) {
        qsa('[data-admin-controls]').forEach(function (wrapper) {
            wrapper.classList.toggle('admin-hidden', !show);
        });
    }

    function updateToolbarStatus(enabled) {
        var toolbar = qs('[data-admin-toolbar]');
        if (!toolbar) {
            return;
        }
        toolbar.setAttribute('data-enabled', enabled ? 'true' : 'false');
        var status = qs('[data-admin-status]');
        if (status) {
            status.textContent = enabled ? 'Inline editing enabled' : 'Inline editing disabled';
        }
        var toggle = qs('[data-admin-toggle]');
        if (toggle) {
            toggle.textContent = enabled ? 'Disable Admin Mode' : 'Enable Admin Mode';
        }
    }

    function cancelAllEdits() {
        qsa('[data-admin-editing="true"]').forEach(function (el) {
            el.dataset.adminEditing = 'false';
            el.removeAttribute('contenteditable');
            if (el.dataset.adminOriginal !== undefined) {
                if (el.dataset.fieldType === 'html') {
                    el.innerHTML = el.dataset.adminOriginal;
                } else {
                    el.textContent = el.dataset.adminOriginal;
                }
            }
            el.classList.remove('admin-editing-outline');
        });
    }

    function inferFieldType(el) {
        if (!el) {
            return 'text';
        }
        if (el.dataset && el.dataset.fieldType) {
            return el.dataset.fieldType;
        }
        if (el.tagName === 'IMG') {
            return 'image';
        }
        if (el.tagName === 'A') {
            return 'url';
        }
        return 'text';
    }

    function createActionButton(label, handler) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'admin-action-button';
        button.textContent = label;
        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            handler();
        });
        return button;
    }

    function findControls(el) {
        var overlay = el.closest('.admin-edit-overlay');
        if (!overlay) {
            return null;
        }
        return qs('[data-admin-controls]', overlay);
    }

    function toggleInlineButtons(el, options) {
        var controls = findControls(el);
        if (!controls) {
            return;
        }
        qsa('.admin-action-button', controls).forEach(function (btn) {
            var label = (btn.textContent || '').toLowerCase();
            if (label.indexOf('edit') !== -1) {
                btn.classList.toggle('admin-hidden', !options.edit);
            } else if (label.indexOf('save') !== -1) {
                btn.classList.toggle('admin-hidden', !options.save);
            } else if (label.indexOf('cancel') !== -1) {
                btn.classList.toggle('admin-hidden', !options.cancel);
            }
        });
    }

    function buildPayload(el, value) {
        return {
            model: el.dataset.model,
            key: el.dataset.key,
            id: el.dataset.id || null,
            value: value,
            csrf_token: getInlineCsrf()
        };
    }

    function ensureImageElement(element, wrapper) {
        if (!element) {
            return null;
        }
        if (element.tagName === 'IMG') {
            element.dataset.fieldType = 'image';
            element.dataset.adminSetup = 'true';
            return element;
        }
        var img = document.createElement('img');
        img.className = element.className || 'h-9 w-auto';
        Array.prototype.slice.call(element.attributes).forEach(function (attr) {
            if (attr && attr.name && attr.name.indexOf('data-') === 0) {
                img.setAttribute(attr.name, attr.value);
            }
        });
        img.dataset.fieldType = 'image';
        img.dataset.adminSetup = 'true';
        var alt = element.getAttribute('alt') || element.getAttribute('data-alt') || (element.textContent || '').trim();
        img.alt = alt || 'Site image';
        if (element.parentNode) {
            element.parentNode.replaceChild(img, element);
        } else if (wrapper) {
            wrapper.insertBefore(img, wrapper.firstChild);
        }
        return img;
    }

    function triggerImageReplace(element, wrapper, endpoints) {
        var input = document.createElement('input');
        input.type = 'file';
        input.accept = '.png,.jpg,.jpeg,.webp,.svg,.ico';
        input.addEventListener('change', function () {
            if (!input.files || !input.files[0]) {
                return;
            }
            var target = ensureImageElement(element, wrapper);
            if (!target) {
                showToast('Unable to determine image target.', 'error');
                return;
            }
            var formData = new FormData();
            formData.append('file', input.files[0]);
            formData.append('model', target.dataset.model || '');
            formData.append('key', target.dataset.key || '');
            if (target.dataset.id) {
                formData.append('id', target.dataset.id);
            }
            ensureCsrf(formData);
            postForm(endpoints.upload, formData).then(function (data) {
                if (data && data.csrf) {
                    updateInlineCsrf(data.csrf);
                }
                var cacheBuster = data && data.cache_buster ? data.cache_buster : ('?v=' + Date.now());
                if (data && data.path) {
                    target.src = data.path + cacheBuster;
                }
                showToast('Image updated');
            }).catch(function (error) {
                showToast(error.message, 'error');
            });
        });
        input.click();
    }

    var htmlModal = null;
    function openHtmlModal(el, endpoints) {
        if (htmlModal) {
            return;
        }
        var modal = document.createElement('div');
        modal.className = 'admin-modal';

        var content = document.createElement('div');
        content.className = 'admin-modal__content';

        var textarea = document.createElement('textarea');
        textarea.value = (el.innerHTML || '').trim();
        content.appendChild(textarea);

        var actions = document.createElement('div');
        actions.className = 'admin-modal__actions';

        var cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'admin-modal__button cancel';
        cancelBtn.textContent = 'Cancel';
        cancelBtn.addEventListener('click', function () {
            closeHtmlModal();
        });

        var saveBtn = document.createElement('button');
        saveBtn.type = 'button';
        saveBtn.className = 'admin-modal__button save';
        saveBtn.textContent = 'Save';
        saveBtn.addEventListener('click', function () {
            var payload = buildPayload(el, textarea.value);
            postJson(endpoints.update, payload).then(function (data) {
                if (data && data.csrf) {
                    updateInlineCsrf(data.csrf);
                }
                el.innerHTML = (data && data.value !== undefined) ? data.value : textarea.value;
                showToast('Saved');
                closeHtmlModal();
            }).catch(function (error) {
                showToast(error.message, 'error');
            });
        });

        actions.appendChild(cancelBtn);
        actions.appendChild(saveBtn);
        content.appendChild(actions);
        modal.appendChild(content);
        document.body.appendChild(modal);
        htmlModal = modal;
    }

    function closeHtmlModal() {
        if (!htmlModal) {
            return;
        }
        htmlModal.remove();
        htmlModal = null;
    }

    var logoutModal = null;
    function openLogoutModal(form, endpoints) {
        if (logoutModal) {
            return;
        }
        var modal = document.createElement('div');
        modal.className = 'admin-modal';

        var content = document.createElement('div');
        content.className = 'admin-modal__content';

        var message = document.createElement('p');
        message.className = 'admin-modal__message';
        message.textContent = 'Save changes and return to user mode?';
        content.appendChild(message);

        var actions = document.createElement('div');
        actions.className = 'admin-modal__actions';

        var stayButton = document.createElement('button');
        stayButton.type = 'button';
        stayButton.className = 'admin-modal__button cancel';
        stayButton.textContent = 'Stay';
        stayButton.addEventListener('click', function () {
            closeLogoutModal();
        });

        var confirmButton = document.createElement('button');
        confirmButton.type = 'button';
        confirmButton.className = 'admin-modal__button save';
        confirmButton.textContent = 'Save & Logout';
        confirmButton.addEventListener('click', function () {
            confirmButton.disabled = true;
            postJson(endpoints.toggle, { enabled: false, csrf_token: getInlineCsrf() }).then(function (data) {
                if (data && data.csrf) {
                    updateInlineCsrf(data.csrf);
                }
                inlineState.enabled = Boolean(data && data.enabled);
                deactivateAdminMode();
                closeLogoutModal();
                form.submit();
            }).catch(function (error) {
                confirmButton.disabled = false;
                showToast(error.message, 'error');
            });
        });

        actions.appendChild(stayButton);
        actions.appendChild(confirmButton);
        content.appendChild(actions);
        modal.appendChild(content);
        document.body.appendChild(modal);
        logoutModal = modal;
    }

    function closeLogoutModal() {
        if (!logoutModal) {
            return;
        }
        logoutModal.remove();
        logoutModal = null;
    }

    function startEditing(el, endpoints) {
        var type = inferFieldType(el);
        if (type === 'html') {
            openHtmlModal(el, endpoints);
            return;
        }
        if (type === 'url') {
            var current = el.getAttribute('href') || '';
            var next = window.prompt('New URL', current) || '';
            if (!next || next === current) {
                return;
            }
            postJson(endpoints.update, buildPayload(el, next)).then(function (data) {
                if (data && data.csrf) {
                    updateInlineCsrf(data.csrf);
                }
                if (data && data.value) {
                    el.setAttribute('href', data.value);
                } else {
                    el.setAttribute('href', next);
                }
                showToast('Saved');
            }).catch(function (error) {
                showToast(error.message, 'error');
            });
            return;
        }
        el.dataset.adminOriginal = el.textContent || '';
        el.dataset.adminEditing = 'true';
        el.setAttribute('contenteditable', 'true');
        el.classList.add('admin-editing-outline');
        el.focus();
        toggleInlineButtons(el, { edit: false, save: true, cancel: true });
    }

    function cancelEditing(el) {
        if (el.dataset.fieldType === 'html') {
            closeHtmlModal();
            return;
        }
        if (el.dataset.adminOriginal !== undefined) {
            el.textContent = el.dataset.adminOriginal;
        }
        el.dataset.adminEditing = 'false';
        el.removeAttribute('contenteditable');
        el.classList.remove('admin-editing-outline');
        toggleInlineButtons(el, { edit: true, save: false, cancel: false });
    }

    function saveEditing(el, endpoints) {
        var value = el.dataset.fieldType === 'html' ? el.innerHTML : (el.textContent || '');
        postJson(endpoints.update, buildPayload(el, value)).then(function (data) {
            if (data && data.csrf) {
                updateInlineCsrf(data.csrf);
            }
            if (el.dataset.fieldType !== 'html') {
                el.textContent = (data && data.value !== undefined) ? data.value : value;
            }
            el.dataset.adminEditing = 'false';
            el.removeAttribute('contenteditable');
            el.classList.remove('admin-editing-outline');
            toggleInlineButtons(el, { edit: true, save: false, cancel: false });
            showToast('Saved');
        }).catch(function (error) {
            showToast(error.message, 'error');
        });
    }

    function setupEditableElements(endpoints) {
        qsa('[data-model][data-key]').forEach(function (el) {
            if (el.dataset.adminSetup === 'true') {
                return;
            }
            el.dataset.adminSetup = 'true';
            var type = inferFieldType(el);
            el.dataset.fieldType = type;
            var parent = el.parentNode;
            var wrapper = document.createElement('div');
            wrapper.className = 'admin-edit-overlay';
            if (parent) {
                parent.insertBefore(wrapper, el);
            }
            wrapper.appendChild(el);

            var controls = document.createElement('div');
            controls.className = 'admin-action-buttons admin-hidden';
            controls.setAttribute('data-admin-controls', 'true');

            if (type === 'image') {
                var replaceBtn = createActionButton('Replace', function () {
                    triggerImageReplace(el, wrapper, endpoints);
                });
                controls.appendChild(replaceBtn);
            } else if (type === 'url') {
                var editUrlBtn = createActionButton('Edit URL', function () {
                    startEditing(el, endpoints);
                });
                controls.appendChild(editUrlBtn);
            } else {
                var editBtn = createActionButton('Edit', function () {
                    startEditing(el, endpoints);
                });
                var saveBtn = createActionButton('Save', function () {
                    saveEditing(el, endpoints);
                });
                var cancelBtn = createActionButton('Cancel', function () {
                    cancelEditing(el);
                });
                saveBtn.classList.add('admin-hidden');
                cancelBtn.classList.add('admin-hidden');
                controls.appendChild(editBtn);
                controls.appendChild(saveBtn);
                controls.appendChild(cancelBtn);
            }

            wrapper.appendChild(controls);
        });
    }

    function activateAdminMode(endpoints) {
        document.body.classList.add('admin-mode-active');
        if (!inlineState.initialized) {
            setupEditableElements(endpoints);
            inlineState.initialized = true;
        }
        toggleInlineControls(true);
        updateToolbarStatus(true);
    }

    function deactivateAdminMode() {
        document.body.classList.remove('admin-mode-active');
        toggleInlineControls(false);
        cancelAllEdits();
        updateToolbarStatus(false);
    }

    function initInlineEditing(context) {
        if (!context || !context.endpoints) {
            return;
        }
        inlineState.enabled = Boolean(context.enabled);
        if (context.csrf) {
            updateInlineCsrf(context.csrf);
        }
        var endpoints = context.endpoints;
        var toggleBtn = qs('[data-admin-toggle]');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (event) {
                event.preventDefault();
                var desired = !inlineState.enabled;
                postJson(endpoints.toggle, { enabled: desired, csrf_token: getInlineCsrf() }).then(function (data) {
                    if (data && data.csrf) {
                        updateInlineCsrf(data.csrf);
                    }
                    inlineState.enabled = Boolean(data && data.enabled);
                    if (inlineState.enabled) {
                        activateAdminMode(endpoints);
                    } else {
                        deactivateAdminMode();
                    }
                }).catch(function (error) {
                    showToast(error.message, 'error');
                });
            });
        }

        var logoutForm = qs('.admin-toolbar__form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function (event) {
                event.preventDefault();
                openLogoutModal(logoutForm, endpoints);
            });
        }

        if (inlineState.enabled) {
            activateAdminMode(endpoints);
        } else {
            deactivateAdminMode();
        }

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('[data-copy-url]');
            if (!trigger) {
                return;
            }
            event.preventDefault();
            var value = trigger.getAttribute('data-copy-url') || '';
            copyToClipboard(value, function () {
                showToast('Copied to clipboard.');
            }, function () {
                showToast('Unable to copy URL.', 'error');
            });
        });

        window.addEventListener('beforeunload', function () {
            if (inlineState.enabled) {
                cancelAllEdits();
            }
        });
    }

    if (window.ADMIN_CONTEXT) {
        initInlineEditing(window.ADMIN_CONTEXT);
    }
});

