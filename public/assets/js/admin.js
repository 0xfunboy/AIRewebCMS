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
        const name = button.getAttribute('data-repeat-name') || 'items';
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
});
