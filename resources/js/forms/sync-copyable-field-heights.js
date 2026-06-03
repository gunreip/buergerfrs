// resources/js/forms/sync-copyable-field-heights.js

/*
|--------------------------------------------------------------------------
| Synced copyable field heights
|--------------------------------------------------------------------------
|
| Keeps grouped read-only copyable-field textareas at the same visual height.
| This is used for paired review fields such as "Translation key" and
| "Suggested key", where manual textarea resizing should affect both sides.
|
*/

const copyableFieldSyncSelector = '[data-copyable-field-sync-group]';
let activeSyncedTextarea = null;

initializeSyncedCopyableFieldHeights();

document.addEventListener('DOMContentLoaded', () => {
    initializeSyncedCopyableFieldHeights();
});

window.addEventListener('mouseup', finishActiveTextareaResizeSync);
window.addEventListener('touchend', finishActiveTextareaResizeSync, { passive: true });

if (document.body) {
    const copyableFieldMutationObserver = new MutationObserver(() => {
        initializeSyncedCopyableFieldHeights();
    });

    copyableFieldMutationObserver.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

function initializeSyncedCopyableFieldHeights() {
    const groups = new Set();

    getSyncedTextareaEntries().forEach(({ group, textarea }) => {
        groups.add(group);

        if (textarea.dataset.copyableFieldSyncBound === 'true') {
            return;
        }

        textarea.dataset.copyableFieldSyncBound = 'true';
        textarea.style.resize = 'vertical';

        const markActiveTextarea = () => {
            activeSyncedTextarea = textarea;
        };

        textarea.addEventListener('mousedown', markActiveTextarea);
        textarea.addEventListener('touchstart', markActiveTextarea, { passive: true });
    });

    groups.forEach((group) => {
        syncGroupToTallestField(group);
    });
}

function finishActiveTextareaResizeSync() {
    if (!(activeSyncedTextarea instanceof HTMLTextAreaElement) || !activeSyncedTextarea.isConnected) {
        activeSyncedTextarea = null;

        return;
    }

    requestAnimationFrame(() => {
        syncGroupFromField(activeSyncedTextarea);
        activeSyncedTextarea = null;
    });
}

function syncGroupFromField(field) {
    const group = resolveSyncGroup(field);

    if (!group) {
        return;
    }

    applyGroupHeight(group, normalizeTextareaHeight(field));
}

function syncGroupToTallestField(group) {
    const textareas = getGroupTextareas(group);

    if (textareas.length === 0) {
        return;
    }

    const tallestHeight = Math.max(...textareas.map((textarea) => normalizeTextareaHeight(textarea)));

    applyGroupHeight(group, tallestHeight);
}

function applyGroupHeight(group, height) {
    getGroupTextareas(group).forEach((textarea) => {
        textarea.style.resize = 'vertical';
        textarea.style.height = `${height}px`;
        textarea.style.minHeight = `${height}px`;
    });
}

function getGroupTextareas(group) {
    return getSyncedTextareaEntries()
        .filter((entry) => entry.group === group)
        .map((entry) => entry.textarea);
}

function getSyncedTextareaEntries() {
    return Array.from(document.querySelectorAll(copyableFieldSyncSelector))
        .map((container) => {
            const group = container.dataset.copyableFieldSyncGroup?.trim() ?? '';
            const textarea = container.querySelector('textarea');

            return {
                group,
                textarea,
            };
        })
        .filter((entry) => entry.group !== '' && entry.textarea instanceof HTMLTextAreaElement);
}

function resolveSyncGroup(field) {
    return field.closest(copyableFieldSyncSelector)?.dataset.copyableFieldSyncGroup?.trim() ?? '';
}

function normalizeTextareaHeight(field) {
    return Math.max(
        Math.ceil(field.getBoundingClientRect().height),
        field.scrollHeight,
    );
}
