// resources/js/notices/notice-core.js

/*
|--------------------------------------------------------------------------
| Notice core
|--------------------------------------------------------------------------
|
| Generic browser-side notice renderer.
|
| This module is intentionally not tied to Laravel validation. It renders
| notice objects with severity, title, messages, actions and optional timeout
| progress. Domain-specific modules, such as validation-notices.js, normalize
| their own payloads and call renderNotice().
|
| Supported severities:
|   error, warning, info, success
|
*/

/*
|--------------------------------------------------------------------------
| State / Configuration
|--------------------------------------------------------------------------
*/

const noticeRegistry = new Map();

const noticeSeverityStyles = {
    error: {
        container: 'border-red-500/40',
        icon: 'text-red-400',
        button: 'bg-red-500 hover:bg-red-400',
        progressTrack: 'bg-red-500/20',
        progressFill: 'bg-red-400/60',
    },
    warning: {
        container: 'border-amber-500/40',
        icon: 'text-amber-400',
        button: 'bg-amber-500 hover:bg-amber-400',
        progressTrack: 'bg-amber-500/20',
        progressFill: 'bg-amber-400/60',
    },
    info: {
        container: 'border-sky-500/40',
        icon: 'text-sky-400',
        button: 'bg-sky-500 hover:bg-sky-400',
        progressTrack: 'bg-sky-500/20',
        progressFill: 'bg-sky-400/60',
    },
    success: {
        container: 'border-emerald-500/40',
        icon: 'text-emerald-400',
        button: 'bg-emerald-500 hover:bg-emerald-400',
        progressTrack: 'bg-emerald-500/20',
        progressFill: 'bg-emerald-400/60',
    },
};

/*
|--------------------------------------------------------------------------
| Rendering API
|--------------------------------------------------------------------------
*/

/**
 * Render a single notice object.
 *
 * Expected minimum shape:
 * {
 *     id: string,
 *     severity: 'error' | 'warning' | 'info' | 'success',
 *     title: string,
 *     messages: string[],
 *     actions: array,
 *     dismissAfterFocusMs: number
 * }
 */
export function renderNotice(notice) {
    const container = ensureNoticeContainer();
    const styles = getNoticeSeverityStyles(notice.severity);

    const element = document.createElement('div');
    element.dataset.noticeId = notice.id;
    element.className = `rounded-xl border ${styles.container} bg-zinc-800 px-4 py-3 text-sm text-zinc-100 shadow-xl ring-1 ring-white/10`;

    const heading = document.createElement('div');
    heading.className = 'flex items-start justify-between gap-3';

    const titleGroup = document.createElement('div');
    titleGroup.className = 'flex min-w-0 items-start gap-2';

    const icon = document.createElement('span');
    icon.className = getNoticeIconClassName(notice.severity);
    icon.setAttribute('aria-hidden', 'true');
    icon.innerHTML = getNoticeIconSvg(notice.severity);

    const title = document.createElement('div');
    title.className = 'min-w-0 font-semibold text-zinc-50';
    title.textContent = notice.title;

    titleGroup.appendChild(icon);
    titleGroup.appendChild(title);

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'shrink-0 text-zinc-400 hover:text-zinc-100';
    closeButton.setAttribute('aria-label', 'Dismiss notice');
    closeButton.textContent = '×';
    closeButton.addEventListener('click', () => {
        removeNotice(notice.id);
    });

    heading.appendChild(titleGroup);
    heading.appendChild(closeButton);

    const messageList = document.createElement('div');
    messageList.className = 'mt-2 space-y-1 text-zinc-300';

    notice.messages.forEach((message) => {
        const messageElement = document.createElement('div');
        messageElement.textContent = message;
        messageList.appendChild(messageElement);
    });

    element.appendChild(heading);
    element.appendChild(messageList);

    const progressBar = createNoticeProgressBar(notice.severity);

    if (notice.actions.length > 0) {
        const actions = document.createElement('div');
        actions.className = 'mt-3 flex justify-end';

        notice.actions.forEach((action) => {
            if (action.type !== 'focus-field' || !action.inputId) {
                return;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = `rounded-lg ${styles.button} px-3 py-1.5 text-xs font-semibold text-white`;
            button.textContent = action.label;
            button.addEventListener('click', () => {
                window.dispatchEvent(new CustomEvent('buergerfrs:focus-field', {
                    detail: {
                        inputId: action.inputId,
                    },
                }));

                button.disabled = true;
                button.classList.add('cursor-not-allowed', 'opacity-70');

                startNoticeProgressBar(progressBar, notice.dismissAfterFocusMs);

                window.setTimeout(() => {
                    removeNotice(notice.id);
                }, notice.dismissAfterFocusMs);
            });

            actions.appendChild(button);
        });

        element.appendChild(actions);
        element.appendChild(progressBar);
    }

    noticeRegistry.set(notice.id, element);
    container.appendChild(element);
}

export function clearNotices() {
    noticeRegistry.forEach((element) => {
        element.remove();
    });

    noticeRegistry.clear();
}

export function removeNotice(noticeId) {
    const element = noticeRegistry.get(noticeId);

    if (!element) {
        return;
    }

    element.remove();
    noticeRegistry.delete(noticeId);
}

/*
|--------------------------------------------------------------------------
| Container
|--------------------------------------------------------------------------
*/

function ensureNoticeContainer() {
    let container = document.getElementById('buergerfrs-validation-notices');

    if (container) {
        return container;
    }

    container = document.createElement('div');
    container.id = 'buergerfrs-validation-notices';
    container.className = 'fixed right-6 top-6 z-50 flex w-[min(24rem,calc(100vw-3rem))] flex-col gap-3';

    document.body.appendChild(container);

    return container;
}

/*
|--------------------------------------------------------------------------
| Severity styles
|--------------------------------------------------------------------------
*/

function getNoticeSeverityStyles(severity) {
    return noticeSeverityStyles[severity] ?? noticeSeverityStyles.error;
}

/*
|--------------------------------------------------------------------------
| Icons
|--------------------------------------------------------------------------
*/

function getNoticeIconClassName(severity) {
    const styles = getNoticeSeverityStyles(severity);

    return `mt-0.5 inline-flex size-4 shrink-0 ${styles.icon}`;
}

function getNoticeIconSvg(severity) {
    if (severity === 'warning') {
        return '<svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l7.072 12.25c.673 1.167-.17 2.625-1.515 2.625H2.928c-1.345 0-2.188-1.458-1.515-2.625l7.072-12.25ZM10 6a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>';
    }

    if (severity === 'info') {
        return '<svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0ZM9.25 8.5a.75.75 0 0 1 1.5 0v5a.75.75 0 0 1-1.5 0v-5ZM10 6.75a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>';
    }

    if (severity === 'success') {
        return '<svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.236 4.45-1.77-1.77a.75.75 0 1 0-1.06 1.061l2.4 2.4a.75.75 0 0 0 1.137-.089l3.743-5.17Z" clip-rule="evenodd" /></svg>';
    }

    return '<svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0ZM10 5.75a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0v-4A.75.75 0 0 1 10 5.75ZM10 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>';
}

/*
|--------------------------------------------------------------------------
| Progress
|--------------------------------------------------------------------------
*/

function createNoticeProgressBar(severity) {
    const styles = getNoticeSeverityStyles(severity);

    const track = document.createElement('div');
    track.className = `mt-3 hidden h-0.5 w-full overflow-hidden rounded-full ${styles.progressTrack}`;

    const fill = document.createElement('div');
    fill.className = `h-full w-full rounded-full ${styles.progressFill}`;
    fill.style.width = '100%';

    track.appendChild(fill);

    return track;
}

function startNoticeProgressBar(progressBar, durationMs) {
    const fill = progressBar.firstElementChild;

    if (!fill) {
        return;
    }

    progressBar.classList.remove('hidden');

    fill.style.transition = 'none';
    fill.style.width = '100%';

    window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
            fill.style.transition = `width ${durationMs}ms linear`;
            fill.style.width = '0%';
        });
    });
}
