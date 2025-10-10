const ctx = window.ADMIN_CONTEXT;
if (!ctx) {
    throw new Error('ADMIN_CONTEXT missing.');
}

const state = {
    enabled: Boolean(ctx.enabled),
    csrf: ctx.csrf || '',
    initialized: false,
};

const endpoints = ctx.endpoints || {};
const body = document.body;

function qs(selector, scope = document) {
    return scope.querySelector(selector);
}

function qsa(selector, scope = document) {
    return Array.from(scope.querySelectorAll(selector));
}

function showToast(message, type = 'success') {
    let container = qs('.admin-toaster');
    if (!container) {
        container = document.createElement('div');
        container.className = 'admin-toaster';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `admin-toast ${type === 'error' ? 'error' : ''}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('admin-hidden');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

async function request(url, options) {
    const response = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            ...options?.headers,
        },
        body: options?.body,
    });

    const data = await response.json().catch(() => ({ ok: false, error: 'Invalid response from server.' }));
    if (!response.ok || !data.ok) {
        throw new Error(data.error || 'Unknown error.');
    }

    if (data.csrf) {
        state.csrf = data.csrf;
        updateMetaCsrf(data.csrf);
    }

    return data;
}

function updateMetaCsrf(token) {
    let meta = qs('meta[name="csrf-token"]');
    if (!meta) {
        meta = document.createElement('meta');
        meta.setAttribute('name', 'csrf-token');
        document.head.appendChild(meta);
    }
    meta.setAttribute('content', token);
}

function getCsrf() {
    if (!state.csrf) {
        const meta = qs('meta[name="csrf-token"]');
        state.csrf = meta ? meta.getAttribute('content') || '' : '';
    }
    return state.csrf;
}

function activateAdminMode() {
    body.classList.add('admin-mode-active');
    if (!state.initialized) {
        setupEditableElements();
        state.initialized = true;
    }
    toggleButtonsVisibility(true);
    updateToolbarStatus(true);
}

function deactivateAdminMode() {
    body.classList.remove('admin-mode-active');
    toggleButtonsVisibility(false);
    cancelAllEdits();
    updateToolbarStatus(false);
}

function toggleButtonsVisibility(show) {
    qsa('[data-admin-controls]').forEach((wrapper) => {
        wrapper.classList.toggle('admin-hidden', !show);
    });
}

function updateToolbarStatus(enabled) {
    const toolbar = qs('[data-admin-toolbar]');
    if (!toolbar) return;
    toolbar.dataset.enabled = enabled ? 'true' : 'false';

    const status = qs('[data-admin-status]');
    if (status) {
        status.textContent = enabled ? 'Inline editing enabled' : 'Inline editing disabled';
    }

    const toggle = qs('[data-admin-toggle]');
    if (toggle) {
        toggle.textContent = enabled ? 'Disable Admin Mode' : 'Enable Admin Mode';
    }
}

function cancelAllEdits() {
    qsa('[data-admin-editing="true"]').forEach((el) => {
        el.dataset.adminEditing = 'false';
        el.removeAttribute('contenteditable');
        if (el.dataset.adminOriginal) {
            if (el.dataset.fieldType === 'html') {
                el.innerHTML = el.dataset.adminOriginal;
            } else {
                el.textContent = el.dataset.adminOriginal;
            }
        }
        el.classList.remove('admin-editing-outline');
    });
}

function setupEditableElements() {
    const targets = qsa('[data-model][data-key]');
    targets.forEach((el) => {
        if (el.dataset.adminSetup === 'true') {
            return;
        }
        el.dataset.adminSetup = 'true';
        const type = el.dataset.fieldType || inferType(el);
        el.dataset.fieldType = type;
        wrapWithControls(el, type);
    });
}

function inferType(el) {
    if (el.tagName === 'IMG') {
        return 'image';
    }
    if (el.dataset.fieldType) {
        return el.dataset.fieldType;
    }
    return 'text';
}

function wrapWithControls(el, type) {
    const wrapper = document.createElement('div');
    wrapper.className = 'admin-edit-overlay';
    el.parentNode?.insertBefore(wrapper, el);
    wrapper.appendChild(el);

    const controls = document.createElement('div');
    controls.className = 'admin-action-buttons admin-hidden';
    controls.setAttribute('data-admin-controls', '');

    if (type === 'image') {
        const replaceBtn = createActionButton('Replace', () => {
            const currentTarget = wrapper.querySelector('[data-model][data-key]');
            if (currentTarget) {
                triggerImageReplace(currentTarget, wrapper);
            }
        });
        controls.appendChild(replaceBtn);
    } else if (type === 'url') {
        const editBtn = createActionButton('Edit URL', () => startEditing(el));
        controls.appendChild(editBtn);
    } else {
        const editBtn = createActionButton('Edit', () => startEditing(el));
        const saveBtn = createActionButton('Save', () => saveEditing(el));
        const cancelBtn = createActionButton('Cancel', () => cancelEditing(el));
        saveBtn.classList.add('admin-hidden');
        cancelBtn.classList.add('admin-hidden');
        controls.append(editBtn, saveBtn, cancelBtn);
        el.dataset.adminEditButton = 'true';
        el.dataset.adminSaveButtonId = Math.random().toString(36).slice(2);
        saveBtn.dataset.adminButtonType = 'save';
        cancelBtn.dataset.adminButtonType = 'cancel';
        saveBtn.dataset.buttonTarget = el.dataset.adminSaveButtonId;
        cancelBtn.dataset.buttonTarget = el.dataset.adminSaveButtonId;
        editBtn.dataset.buttonTarget = el.dataset.adminSaveButtonId;
    }

    wrapper.appendChild(controls);
}

function createActionButton(label, handler) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'admin-action-button';
    btn.textContent = label;
    btn.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        handler();
    });
    return btn;
}

function findControls(el) {
    const overlay = el.closest('.admin-edit-overlay');
    if (!overlay) return null;
    return qs('[data-admin-controls]', overlay);
}

function startEditing(el) {
    const type = el.dataset.fieldType;
    if (type === 'html') {
        openHtmlModal(el);
        return;
    }

    if (type === 'url') {
        const current = el.getAttribute('href') || '';
        const next = window.prompt('New URL', current) || '';
        if (!next || next === current) {
            return;
        }
        const payload = buildPayload(el, next);
        request(endpoints.update, {
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then((data) => {
                if (data.value) {
                    el.setAttribute('href', data.value);
                }
                showToast('Saved');
            })
            .catch((error) => {
                showToast(error.message, 'error');
            });
        return;
    }

    el.dataset.adminOriginal = el.textContent || '';
    el.dataset.adminEditing = 'true';
    el.setAttribute('contenteditable', 'true');
    el.focus();
    el.classList.add('admin-editing-outline');
    toggleButtons(el, { edit: false, save: true, cancel: true });
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
    toggleButtons(el, { edit: true, save: false, cancel: false });
}

async function saveEditing(el) {
    try {
        const payload = buildPayload(el, el.dataset.fieldType === 'html' ? el.innerHTML : el.textContent || '');
        const data = await request(endpoints.update, {
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        if (el.dataset.fieldType !== 'html') {
            el.textContent = data.value ?? (el.textContent || '');
        }
        el.dataset.adminEditing = 'false';
        el.removeAttribute('contenteditable');
        el.classList.remove('admin-editing-outline');
        toggleButtons(el, { edit: true, save: false, cancel: false });
        showToast('Saved');
    } catch (error) {
        showToast(error.message, 'error');
    }
}

function buildPayload(el, value) {
    return {
        model: el.dataset.model,
        key: el.dataset.key,
        id: el.dataset.id || null,
        value,
        csrf: getCsrf(),
    };
}

function triggerImageReplace(element, wrapper = null) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.png,.jpg,.jpeg,.webp,.svg,.ico';
    input.addEventListener('change', async () => {
        if (!input.files || !input.files[0]) return;
        try {
            const target = ensureImageElement(element, wrapper);
            if (!target) {
                throw new Error('Unable to determine image target.');
            }

            const formData = new FormData();
            formData.append('file', input.files[0]);
            formData.append('model', target.dataset.model || '');
            formData.append('key', target.dataset.key || '');
            if (target.dataset.id) {
                formData.append('id', target.dataset.id);
            }
            formData.append('csrf', getCsrf());

            const data = await request(endpoints.upload, {
                body: formData,
            });

            const cacheBuster = data.cache_buster || (`?v=${Date.now()}`);
            if (data.path) {
                target.src = `${data.path}${cacheBuster}`;
            }
            showToast('Image updated');
        } catch (error) {
            showToast(error.message, 'error');
        }
    });
    input.click();
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

    const img = document.createElement('img');
    img.className = element.className || 'h-9 w-auto';

    Array.from(element.attributes).forEach((attr) => {
        if (attr.name.startsWith('data-')) {
            img.setAttribute(attr.name, attr.value);
        }
    });

    img.dataset.fieldType = 'image';
    img.dataset.adminSetup = 'true';

    const alt = element.getAttribute('alt') || element.getAttribute('data-alt') || (element.textContent || '').trim();
    img.alt = alt || 'Site logo';

    if (element.parentNode) {
        element.parentNode.replaceChild(img, element);
    } else if (wrapper) {
        wrapper.insertBefore(img, wrapper.firstChild);
    }

    return img;
}

let htmlModal = null;

function openHtmlModal(el) {
    if (htmlModal) return;
    const modal = document.createElement('div');
    modal.className = 'admin-modal';

    const content = document.createElement('div');
    content.className = 'admin-modal__content';

    const textarea = document.createElement('textarea');
    textarea.value = el.innerHTML.trim();
    content.appendChild(textarea);

    const actions = document.createElement('div');
    actions.className = 'admin-modal__actions';

    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'admin-modal__button cancel';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.addEventListener('click', () => {
        closeHtmlModal();
    });

    const saveBtn = document.createElement('button');
    saveBtn.type = 'button';
    saveBtn.className = 'admin-modal__button save';
    saveBtn.textContent = 'Save';
    saveBtn.addEventListener('click', async () => {
        try {
            const payload = buildPayload(el, textarea.value);
            const data = await request(endpoints.update, {
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });
            el.innerHTML = data.value ?? textarea.value;
            showToast('Saved');
            closeHtmlModal();
        } catch (error) {
            showToast(error.message, 'error');
        }
    });

    actions.append(cancelBtn, saveBtn);
    content.appendChild(actions);
    modal.appendChild(content);
    document.body.appendChild(modal);
    htmlModal = modal;
}

function closeHtmlModal() {
    if (!htmlModal) return;
    htmlModal.remove();
    htmlModal = null;
}

async function onToggleClick() {
    try {
        const desired = !state.enabled;
        const data = await request(endpoints.toggle, {
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ enabled: desired, csrf: getCsrf() }),
        });
        state.enabled = Boolean(data.enabled);
        if (state.enabled) {
            activateAdminMode();
        } else {
            deactivateAdminMode();
        }
    } catch (error) {
        showToast(error.message, 'error');
    }
}

function initToolbar() {
    const toggle = qs('[data-admin-toggle]');
    if (toggle) {
        toggle.addEventListener('click', onToggleClick);
    }
    const logout = qs('[data-admin-logout]');
    if (logout) {
        logout.addEventListener('click', onLogoutClick);
    }
    if (state.enabled) {
        activateAdminMode();
    } else {
        deactivateAdminMode();
    }
}

async function onLogoutClick(event) {
    event.preventDefault();
    const confirmExit = window.confirm('Vuoi salvare le modifiche e tornare alla modalità utente?');
    if (!confirmExit) {
        return;
    }

    if (state.enabled) {
        try {
            const data = await request(endpoints.toggle, {
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ enabled: false, csrf: getCsrf() }),
            });
            state.enabled = Boolean(data.enabled);
            deactivateAdminMode();
        } catch (error) {
            showToast(error.message, 'error');
            return;
        }
    }

    window.location.href = '/auth/logout';
}

function toggleButtons(el, visibilityMap) {
    const overlay = el.closest('.admin-edit-overlay');
    if (!overlay) return;
    const buttons = overlay.querySelectorAll('.admin-action-button');
    buttons.forEach((btn) => {
        const label = btn.textContent?.toLowerCase() || '';
        if (label.includes('edit')) {
            btn.classList.toggle('admin-hidden', !visibilityMap.edit);
        } else if (label.includes('save')) {
            btn.classList.toggle('admin-hidden', !visibilityMap.save);
        } else if (label.includes('cancel')) {
            btn.classList.toggle('admin-hidden', !visibilityMap.cancel);
        }
    });
}

initToolbar();

window.addEventListener('beforeunload', () => {
    if (state.enabled) {
        cancelAllEdits();
    }
});
