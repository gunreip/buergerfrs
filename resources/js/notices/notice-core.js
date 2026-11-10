// Application notices use Flux's renderer, actions and dismissal controls.
// Scoped notices replace previous messages for the same form, without clearing
// unrelated success/publication notifications.
export function clearNotices(scope = null) {
    document.querySelectorAll('[data-flux-toast-dialog][data-notice-scope]').forEach((toast) => {
        if (scope !== null && toast.dataset.noticeScope !== scope) return;
        toast.querySelector('ui-close button')?.click();
    });
}

export function removeNotice(id) {
    document.querySelectorAll('[data-flux-toast-dialog][data-notice-id]').forEach((toast) => {
        if (toast.dataset.noticeId === id) toast.querySelector('ui-close button')?.click();
    });
}

export function renderNotice(notice) {
    removeNotice(notice.id);
    const action = notice.actions?.[0];
    document.dispatchEvent(new CustomEvent('toast-show', { detail: {
        duration: notice.persistent ? 0 : 5000,
        slots: { heading: notice.title, text: notice.messages.join('\n') },
        dataset: {
            variant: notice.severity === 'error' ? 'danger' : notice.severity,
            noticeScope: notice.scope,
            noticeId: notice.id,
        },
        ...(action?.inputId ? { action: {
            label: action.label,
            event: 'buergerfrs:focus-field',
            params: { inputId: action.inputId, tab: action.tab ?? null },
        } } : {}),
    } }));
}

// Capture runs before Flux renders the new toast at document level.
window.addEventListener('toast-show', (event) => {
    const scope = event.detail?.dataset?.replaceNoticeScope;
    if (scope) clearNotices(scope);
}, true);
window.addEventListener('buergerfrs:clear-notices', (event) => {
    if (event.detail?.scope) clearNotices(event.detail.scope);
});
document.addEventListener('livewire:navigating', () => clearNotices());

// A visible popover outside a modal dialog is still inert. Keep the Flux host
// inside the active dialog so real pointer/keyboard actions reach its buttons.
const toastHomes = new Map();
function placeToastHosts() {
    const dialogs = [...document.querySelectorAll('dialog[open]')].filter(dialog => dialog.matches(':modal'));
    const dialog = dialogs.at(-1);
    for (const [host, home] of toastHomes) {
        if (!host.isConnected && home.isConnected) home.after(host);
    }
    for (const host of document.querySelectorAll('ui-toast-group')) {
        if (!toastHomes.has(host)) {
            const home = document.createComment('Toast host');
            host.before(home);
            toastHomes.set(host, home);
        }
        const home = toastHomes.get(host);
        const parent = dialog ?? home.parentNode;
        if (!parent) continue;
        const wasOpen = host.matches(':popover-open');
        if (host.parentNode !== parent) {
            if (dialog) dialog.append(host);
            else home.after(host);
        }
        // A Livewire DOM update can detach the host, which dismisses its native
        // popover before this observer runs. Restore the top layer for live
        // Flux toasts as well, rather than leaving them positioned in the dialog.
        if ((wasOpen || host.toasts?.length > 0) && !host.matches(':popover-open')) host.showPopover();
    }
    for (const [host, home] of toastHomes) {
        if (!host.isConnected) { home.remove(); toastHomes.delete(host); }
    }
}
window.addEventListener('toast-show', placeToastHosts, true);
new MutationObserver(records => {
    if (records.some(record => record.target instanceof HTMLDialogElement
        || [...record.removedNodes].some(node => node instanceof Element && (node.matches('dialog, ui-toast-group') || node.querySelector('dialog, ui-toast-group'))))) placeToastHosts();
}).observe(document.documentElement, { subtree: true, childList: true, attributes: true, attributeFilter: ['open'] });
document.addEventListener('livewire:navigating', () => {
    for (const [host, home] of toastHomes) if (home.isConnected) home.after(host);
});
