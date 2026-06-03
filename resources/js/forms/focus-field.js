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

    if (!inputId) {
        return;
    }

    const element = document.getElementById(inputId);

    if (!element) {
        return;
    }

    element.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'nearest',
    });

    window.setTimeout(() => {
        element.focus({ preventScroll: true });
    }, 250);
});

window.addEventListener('buergerfrs:focus-field-and-select', (event) => {
    const inputId = event.detail?.inputId;

    if (!inputId) {
        return;
    }

    const element = document.getElementById(inputId);

    if (!(element instanceof HTMLTextAreaElement) && !(element instanceof HTMLInputElement)) {
        return;
    }

    element.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'nearest',
    });

    window.setTimeout(() => {
        element.focus({ preventScroll: true });

        if (typeof element.select === 'function') {
            element.select();
        }
    }, 250);
});
