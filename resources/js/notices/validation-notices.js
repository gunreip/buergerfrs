// resources/js/notices/validation-notices.js

import { clearNotices, renderNotice } from './notice-core';

/*
|--------------------------------------------------------------------------
| Validation notices
|--------------------------------------------------------------------------
|
| Converts Livewire/Laravel validation error payloads into browser-side notices.
|
| Expected incoming event:
|   buergerfrs:validation-errors
|
| Expected payload:
|   {
|       errors: [
|           {
|               field: 'email',
|               label: 'Email',
|               inputId: 'create-person-email',
|               messages: ['The email field is required.'],
|           },
|       ],
|   }
|
| Behaviour:
|   - 1..3 invalid fields: one notice per field
|   - more than 3 invalid fields: one summary notice
|   - action buttons dispatch buergerfrs:focus-field through notice-core
|
*/

/*
|--------------------------------------------------------------------------
| State / Configuration
|--------------------------------------------------------------------------
*/

const validationNoticeFieldLimit = 3;

/*
|--------------------------------------------------------------------------
| Event listeners
|--------------------------------------------------------------------------
*/

window.addEventListener('buergerfrs:validation-errors', (event) => {
    const errors = event.detail?.errors ?? [];

    if (!Array.isArray(errors)) {
        return;
    }

    if (errors.length === 0) {
        return;
    }

    const notices = errors.map((error) => normalizeValidationErrorNotice(error));

    window.dispatchEvent(new CustomEvent('buergerfrs:validation-errors-ready', {
        detail: {
            errors,
            notices,
        },
    }));
});

window.addEventListener('buergerfrs:validation-errors-ready', (event) => {
    const notices = event.detail?.notices ?? [];

    if (!Array.isArray(notices) || notices.length === 0) {
        return;
    }

    renderValidationNotices(notices);
});

/*
|--------------------------------------------------------------------------
| Notice normalization
|--------------------------------------------------------------------------
*/

/**
 * Normalize one Livewire validation error entry into a generic notice object.
 */
function normalizeValidationErrorNotice(error) {
    const field = error.field ?? 'unknown';
    const label = error.label ?? field;
    const inputId = error.inputId ?? null;
    const tab = error.tab ?? null;
    const messages = Array.isArray(error.messages)
        ? error.messages.filter((message) => typeof message === 'string' && message.trim() !== '')
        : [];

    return {
        id: `validation-create-person-${field}`,
        type: 'validation',
        severity: 'error',
        scope: 'create-person-form',
        field,
        label,
        inputId,
        tab,
        title: `Please check ${label}`,
        messages,
        actions: inputId
            ? [
                {
                    type: 'focus-field',
                    label: 'Go to field',
                    inputId,
                    tab,
                },
            ]
            : [],
        persistent: true,
        dismissAfterFocusMs: 5000,
    };
}

/*
|--------------------------------------------------------------------------
| Rendering
|--------------------------------------------------------------------------
*/

function renderValidationNotices(notices) {
    clearNotices();

    if (notices.length > validationNoticeFieldLimit) {
        const firstNotice = notices[0];

        renderNotice({
            id: 'validation-create-person-summary',
            type: 'validation',
            severity: 'error',
            scope: 'create-person-form',
            field: firstNotice?.field ?? null,
            label: 'Form',
            inputId: firstNotice?.inputId ?? null,
            title: 'Please check the form',
            messages: [
                `${notices.length} fields need your attention.`,
                firstNotice?.label ? `First field: ${firstNotice.label}.` : null,
            ].filter(Boolean),
            actions: firstNotice?.inputId
                ? [
                    {
                        type: 'focus-field',
                        label: 'Go to first field',
                        inputId: firstNotice.inputId,
                        tab: firstNotice.tab ?? null,
                    },
                ]
                : [],
            persistent: true,
            dismissAfterFocusMs: 5000,
        });

        return;
    }

    notices.forEach((notice) => {
        renderNotice(notice);
    });
}
