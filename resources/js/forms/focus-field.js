// resources/js/forms/focus-field.js

/*
|--------------------------------------------------------------------------
| Focus field event
|--------------------------------------------------------------------------
|
| Central browser-side focus helper for Livewire/Blade form flows.
|
| Dispatch:
|   window.dispatchEvent(new CustomEvent('buergerfrs:focus-field', {
|       detail: { inputId: 'create-person-email' },
|   }));
|
| The handler scrolls the target element into view and focuses it shortly
| afterwards. This is used by validation notices and can be reused by other
| form workflows.
|
*/

window.addEventListener('buergerfrs:focus-field', (event) => {
    const inputId = event.detail?.inputId;
    const tab = event.detail?.tab;

    if (!inputId) {
        return;
    }

    activateFormTab(tab);

    focusElementByIdWithRetry(inputId, false, tab ? 8 : 3);
});

window.addEventListener('buergerfrs:focus-field-and-select', (event) => {
    const inputId = event.detail?.inputId;
    const tab = event.detail?.tab;

    if (!inputId) {
        return;
    }

    activateFormTab(tab);

    focusElementByIdWithRetry(inputId, true, tab ? 8 : 3);
});

function activateFormTab(tab) {
    if (typeof tab !== 'string' || tab.trim() === '') {
        return;
    }

    const escapedTab = window.CSS?.escape
        ? CSS.escape(tab)
        : tab.replace(/["\\]/g, '\\$&');
    const trigger = document.querySelector(`[data-form-tab-trigger="${escapedTab}"]`);

    if (trigger instanceof HTMLElement) {
        trigger.click();
    }
}

function focusElementByIdWithRetry(inputId, select = false, attempts = 3) {
    const focused = focusElementById(inputId, select);

    if (focused || attempts <= 1) {
        return;
    }

    window.setTimeout(() => {
        focusElementByIdWithRetry(inputId, select, attempts - 1);
    }, 125);
}

function focusElementById(inputId, select = false) {
    const element = document.getElementById(inputId);

    if (!element) {
        return false;
    }

    const focusTarget = resolveFocusTarget(element);

    if (!focusTarget) {
        return false;
    }

    if (select && !(focusTarget instanceof HTMLTextAreaElement) && !(focusTarget instanceof HTMLInputElement)) {
        return false;
    }

    element.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'nearest',
    });

    window.setTimeout(() => {
        focusTarget.focus({ preventScroll: true });

        if (select && typeof focusTarget.select === 'function') {
            focusTarget.select();
        }

        if (element.matches('[data-flux-select]')) {
            openFluxSelect(element, focusTarget);
        }
    }, 250);

    return true;
}

function resolveFocusTarget(element) {
    if (isFocusableElement(element)) {
        return element;
    }

    if (element.matches('[data-flux-select], [data-flux-date-picker]')) {
        return element.querySelector('[data-flux-select-button], [data-flux-date-picker-button], [data-flux-group-target], input, button');
    }

    if (element.matches('[data-flux-radio-group], [data-flux-radio-group-segmented]')) {
        return element.querySelector('[data-checked], [data-flux-radio], ui-radio, input, button') ?? element;
    }

    return element.querySelector('input, textarea, select, button, [tabindex]:not([tabindex="-1"])') ?? element;
}

function openFluxSelect(element, focusTarget) {
    if (element.hasAttribute('disabled')) {
        return;
    }

    if (focusTarget instanceof HTMLElement && !focusTarget.hasAttribute('disabled')) {
        focusTarget.click();
    }

    focusFluxSelectSearch(element, 6);
}

function focusFluxSelectSearch(element, attempts = 3) {
    const searchInput = element.querySelector('[data-flux-select-search] input');

    if (searchInput instanceof HTMLInputElement) {
        searchInput.focus({ preventScroll: true });
        searchInput.select();
        return;
    }

    if (attempts <= 1) {
        return;
    }

    window.setTimeout(() => {
        focusFluxSelectSearch(element, attempts - 1);
    }, 75);
}

function isFocusableElement(element) {
    if (!(element instanceof HTMLElement)) {
        return false;
    }

    if (element.matches('input, textarea, select, button')) {
        return true;
    }

    const tabIndex = element.getAttribute('tabindex');

    return tabIndex !== null && tabIndex !== '-1';
}
