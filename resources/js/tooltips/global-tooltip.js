// resources/js/components/ui/global-tooltip.js

const TOOLTIP_TEMPLATE_ID = 'global-tooltip-template';
const DEFAULT_SHOW_DELAY = 2500;
const DEFAULT_HIDE_DELAY = 120;
const FADE_DURATION = 200;
const TOOLTIP_OFFSET = 8;
const VIEWPORT_PADDING = 12;
const TOOLTIP_MAX_WIDTH = 448;
const TOOLTIP_Z_INDEX = '2147483647';
const TOOLTIP_CONTEXT_CLASS_PREFIX = 'tooltip-context--';

let tooltipShowTimeout = null;
let tooltipHideTimeout = null;
let activeTooltip = null;
let activeAnchor = null;
let tooltipsInitialized = false;

export function setupGlobalTooltips() {
    if (tooltipsInitialized) {
        return;
    }

    tooltipsInitialized = true;

    document.addEventListener('pointerover', function (e) {
        const trigger = getTooltipTrigger(e.target);

        if (!trigger) {
            return;
        }

        if (isWithinElement(e.relatedTarget, trigger) || isWithinElement(e.relatedTarget, activeTooltip)) {
            return;
        }

        scheduleShowGlobalTooltip(trigger, e);
    }, true);

    document.addEventListener('pointerout', function (e) {
        const trigger = getTooltipTrigger(e.target);

        if (!trigger) {
            return;
        }

        if (isWithinElement(e.relatedTarget, trigger) || isWithinElement(e.relatedTarget, activeTooltip)) {
            return;
        }

        scheduleRemoveGlobalTooltip(trigger);
    }, true);

    document.addEventListener('click', function (e) {
        const trigger = getTooltipTrigger(e.target);

        if (!trigger) {
            return;
        }

        if (activeAnchor === trigger && activeTooltip) {
            removeGlobalTooltip();
            return;
        }

        showGlobalTooltip(trigger, 0, e);
    });

    window.addEventListener('scroll', removeGlobalTooltip, true);
    window.addEventListener('resize', removeGlobalTooltip);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            removeGlobalTooltip();
        }
    });
}

function getTooltipTrigger(target) {
    if (!(target instanceof Element)) {
        return null;
    }

    return target.closest('[data-tooltip-trigger="true"]');
}

function isWithinElement(target, element) {
    if (!(target instanceof Node) || !(element instanceof Element)) {
        return false;
    }

    return element === target || element.contains(target);
}

function scheduleShowGlobalTooltip(anchorEl, event = null) {
    clearTimeout(tooltipShowTimeout);
    clearTimeout(tooltipHideTimeout);

    const delay = getTooltipDelay(anchorEl);
    const pointer = getTooltipPointer(event);

    tooltipShowTimeout = setTimeout(() => {
        showGlobalTooltip(anchorEl, null, pointer);
    }, delay);
}

function scheduleRemoveGlobalTooltip(anchorEl) {
    clearTimeout(tooltipShowTimeout);
    clearTimeout(tooltipHideTimeout);

    tooltipHideTimeout = setTimeout(() => {
        if (!activeTooltip) {
            return;
        }

        if (anchorEl.matches(':hover') || activeTooltip.matches(':hover')) {
            return;
        }

        removeGlobalTooltip();
    }, DEFAULT_HIDE_DELAY);
}

function showGlobalTooltip(anchorEl, delay = null, pointer = null) {
    clearTimeout(tooltipShowTimeout);
    clearTimeout(tooltipHideTimeout);

    removeGlobalTooltip();

    const template = getTooltipTemplate();

    if (!template) {
        return;
    }

    const content = anchorEl.dataset.tooltip || '';
    const title = anchorEl.dataset.tooltipTitle || '';
    const required = anchorEl.dataset.tooltipRequired === 'true';
    const action = parseTooltipAction(anchorEl.dataset.tooltipAction || '');
    const context = normalizeTooltipContext(anchorEl.dataset.tooltipContext || '');
    const contentTemplate = getTooltipContentTemplate(anchorEl);

    if (!content && !title && !action && !contentTemplate) {
        return;
    }

    const node = template.content.cloneNode(true);
    const tooltip = node.querySelector('.my-tooltip');

    if (!tooltip) {
        return;
    }

    const titleEl = tooltip.querySelector('.tooltip-title');
    const contentEl = tooltip.querySelector('.tooltip-content');
    const requiredBadge = tooltip.querySelector('.tooltip-required-badge');
    const actionWrapEl = tooltip.querySelector('.tooltip-action');
    const actionTextEl = tooltip.querySelector('.tooltip-action-text');
    const actionButtonEl = tooltip.querySelector('.tooltip-action-button');

    applyTooltipContext(tooltip, context);

    if (titleEl) {
        titleEl.textContent = title;
    }

    if (contentEl) {
        renderTooltipContent(contentEl, content, contentTemplate);
    }

    if (requiredBadge) {
        requiredBadge.toggleAttribute('hidden', !required);
    }

    renderTooltipAction(actionWrapEl, actionTextEl, actionButtonEl, action);

    const tooltipContainer = resolveTooltipContainer(anchorEl);

    tooltip.style.visibility = 'hidden';
    tooltip.style.position = 'absolute';
    tooltip.style.zIndex = TOOLTIP_Z_INDEX;

    tooltipContainer.appendChild(tooltip);

    positionTooltip(tooltip, anchorEl, pointer, tooltipContainer);

    tooltip.classList.add('global-tooltip-active');
    tooltip.style.visibility = '';
    tooltip.style.opacity = '0';

    activeTooltip = tooltip;
    activeAnchor = anchorEl;

    tooltip.addEventListener('mouseenter', () => {
        clearTimeout(tooltipHideTimeout);
    });

    tooltip.addEventListener('mouseleave', () => {
        scheduleRemoveGlobalTooltip(anchorEl);
    });

    const showDelay = delay ?? 0;

    tooltipShowTimeout = setTimeout(() => {
        tooltip.style.opacity = '1';
    }, showDelay);
}

function resolveTooltipContainer(anchorEl) {
    const modalContainer = anchorEl.closest('dialog, [role="dialog"], [data-flux-modal], .flux-modal');

    return modalContainer instanceof HTMLElement ? modalContainer : document.body;
}

function getTooltipTemplate() {
    const existingTemplate = document.getElementById(TOOLTIP_TEMPLATE_ID);

    if (existingTemplate instanceof HTMLTemplateElement) {
        return existingTemplate;
    }

    const fallbackTemplate = document.createElement('template');
    fallbackTemplate.id = TOOLTIP_TEMPLATE_ID;
    fallbackTemplate.innerHTML = `
        <div class="my-tooltip" role="tooltip">
            <div class="my-tooltip-row">
                <span class="my-tooltip-icon" aria-hidden="true">i</span>
                <div class="my-tooltip-body">
                    <div class="my-tooltip-heading">
                        <div class="tooltip-title my-tooltip-title"></div>
                        <span class="tooltip-required-badge my-tooltip-required-badge" hidden>Required</span>
                    </div>
                    <div class="tooltip-content my-tooltip-content"></div>
                    <div class="tooltip-action my-tooltip-action" hidden>
                        <span class="tooltip-action-text my-tooltip-action-text"></span>
                        <button class="tooltip-action-button my-tooltip-action-button" type="button"></button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(fallbackTemplate);

    return fallbackTemplate;
}

function parseTooltipAction(rawValue) {
    const value = String(rawValue || '').trim();

    if (value === '') {
        return null;
    }

    try {
        const action = JSON.parse(value);

        if (!action || typeof action !== 'object') {
            return null;
        }

        const label = String(action.label || '').trim();
        const event = String(action.event || '').trim();

        if (label === '' || event === '') {
            return null;
        }

        return {
            label,
            text: String(action.text || '').trim(),
            event,
            detail: action.detail && typeof action.detail === 'object' ? action.detail : {},
        };
    } catch {
        return null;
    }
}

function renderTooltipAction(actionWrapEl, actionTextEl, actionButtonEl, action) {
    if (!(actionWrapEl instanceof HTMLElement) || !(actionTextEl instanceof HTMLElement) || !(actionButtonEl instanceof HTMLElement)) {
        return;
    }

    actionButtonEl.replaceWith(actionButtonEl.cloneNode(true));
    const freshActionButtonEl = actionWrapEl.querySelector('.tooltip-action-button');

    if (!(freshActionButtonEl instanceof HTMLElement)) {
        return;
    }

    if (!action) {
        actionWrapEl.hidden = true;
        actionTextEl.textContent = '';
        freshActionButtonEl.textContent = '';

        return;
    }

    actionWrapEl.hidden = false;
    actionTextEl.textContent = action.text;
    actionTextEl.hidden = action.text === '';
    freshActionButtonEl.textContent = action.label;

    freshActionButtonEl.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        removeGlobalTooltip();

        window.dispatchEvent(new CustomEvent(action.event, {
            detail: action.detail,
        }));
    });
}

function getTooltipContentTemplate(anchorEl) {
    if (!(anchorEl instanceof Element)) {
        return null;
    }

    if (anchorEl.dataset.tooltipContent !== 'slot') {
        return null;
    }

    const template = anchorEl.querySelector('template[data-tooltip-content-template]');

    return template instanceof HTMLTemplateElement ? template : null;
}

function positionTooltip(tooltip, anchorEl, pointer = null, tooltipContainer = document.body) {
    const rect = anchorEl.getBoundingClientRect();

    const isBodyContainer = tooltipContainer === document.body;
    const containerRect = isBodyContainer
        ? { left: 0, top: 0 }
        : tooltipContainer.getBoundingClientRect();
    const containerScrollX = isBodyContainer ? window.scrollX : tooltipContainer.scrollLeft;
    const containerScrollY = isBodyContainer ? window.scrollY : tooltipContainer.scrollTop;

    const viewportWidth = isBodyContainer ? window.innerWidth : tooltipContainer.clientWidth;
    const viewportHeight = isBodyContainer ? window.innerHeight : tooltipContainer.clientHeight;
    const viewportLeft = containerScrollX;
    const viewportTop = containerScrollY;
    const viewportRight = viewportLeft + viewportWidth;
    const viewportBottom = viewportTop + viewportHeight;

    const localAnchorLeft = rect.left - containerRect.left + containerScrollX;
    const localAnchorTop = rect.top - containerRect.top + containerScrollY;
    const localAnchorBottom = rect.bottom - containerRect.top + containerScrollY;

    const maxTooltipWidth = Math.max(
        160,
        Math.min(TOOLTIP_MAX_WIDTH, viewportWidth - VIEWPORT_PADDING * 2),
    );
    const maxTooltipHeight = Math.max(120, viewportHeight - VIEWPORT_PADDING * 2);

    tooltip.style.width = 'max-content';
    tooltip.style.maxWidth = `${maxTooltipWidth}px`;
    tooltip.style.maxHeight = `${maxTooltipHeight}px`;
    tooltip.style.overflowY = 'auto';
    tooltip.style.transform = 'none';

    const tooltipRect = tooltip.getBoundingClientRect();

    const preferredCenterX = pointer
        ? pointer.clientX - containerRect.left + containerScrollX
        : localAnchorLeft + rect.width / 2;

    const preferredLeft = preferredCenterX - tooltipRect.width / 2;
    const minLeft = viewportLeft + VIEWPORT_PADDING;
    const maxLeft = viewportRight - VIEWPORT_PADDING - tooltipRect.width;
    const left = clamp(preferredLeft, minLeft, Math.max(minLeft, maxLeft));

    const spaceAbove = localAnchorTop - viewportTop;
    const spaceBelow = viewportBottom - localAnchorBottom;
    const shouldPlaceAbove = spaceAbove >= tooltipRect.height + TOOLTIP_OFFSET
        || spaceAbove > spaceBelow;

    const preferredTop = shouldPlaceAbove
        ? localAnchorTop - tooltipRect.height - TOOLTIP_OFFSET
        : localAnchorBottom + TOOLTIP_OFFSET;

    const minTop = viewportTop + VIEWPORT_PADDING;
    const maxTop = viewportBottom - VIEWPORT_PADDING - tooltipRect.height;
    const top = clamp(preferredTop, minTop, Math.max(minTop, maxTop));

    tooltip.style.left = `${left}px`;
    tooltip.style.top = `${top}px`;
    tooltip.scrollTop = 0;
}

function renderTooltipContent(contentEl, content, contentTemplate = null) {
    contentEl.replaceChildren();
    contentEl.classList.remove('whitespace-pre-line', 'space-y-1.5', 'tooltip-content-rich');

    if (contentTemplate instanceof HTMLTemplateElement) {
        contentEl.appendChild(contentTemplate.content.cloneNode(true));
        contentEl.classList.add('tooltip-content-rich');

        return;
    }

    if (isLegendContent(content)) {
        renderLegendContent(contentEl, content);

        return;
    }

    contentEl.textContent = content;
    contentEl.classList.add('whitespace-pre-line');
}

function isLegendContent(content) {
    const lines = content
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line !== '');

    return lines.length > 0
        && lines.every((line) => line.split('|').length >= 4);
}

function renderLegendContent(contentEl, content) {
    contentEl.classList.add('space-y-1.5');

    content
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line !== '')
        .forEach((line) => {
            const [symbol, color, label, ...descriptionParts] = line.split('|');
            const description = descriptionParts.join('|');

            const row = document.createElement('div');
            row.className = 'flex items-start gap-2';

            const symbolEl = document.createElement('span');
            symbolEl.className = [
                'inline-flex',
                'w-6',
                'shrink-0',
                'justify-center',
                'pt-0.5',
                'font-mono',
                'text-sm',
                tooltipLegendColorClass(color),
            ].join(' ');
            symbolEl.textContent = symbol;

            const textEl = document.createElement('span');
            textEl.className = 'min-w-0 leading-6';

            const labelEl = document.createElement('strong');
            labelEl.className = 'font-semibold';
            labelEl.textContent = label;

            const separatorEl = document.createElement('span');
            separatorEl.textContent = description !== '' ? ': ' : '';

            const descriptionEl = document.createElement('span');
            descriptionEl.textContent = description;

            textEl.append(labelEl, separatorEl, descriptionEl);
            row.append(symbolEl, textEl);
            contentEl.append(row);
        });
}

function tooltipLegendColorClass(color) {
    return {
        red: 'text-red-400',
        amber: 'text-amber-300',
        green: 'text-green-400',
        zinc: 'text-zinc-300',
        sky: 'text-sky-300',
        blue: 'text-blue-300',
        violet: 'text-violet-300',
        purple: 'text-purple-300',
    }[color] || 'text-zinc-300';
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function getTooltipPointer(event) {
    if (!event || typeof event.clientX !== 'number' || typeof event.clientY !== 'number') {
        return null;
    }

    return {
        clientX: event.clientX,
        clientY: event.clientY,
    };
}

function getTooltipDelay(anchorEl) {
    const delay = Number.parseInt(anchorEl.dataset.tooltipDelay || '', 10);

    if (Number.isFinite(delay) && delay >= 0) {
        return delay;
    }

    return DEFAULT_SHOW_DELAY;
}

function removeGlobalTooltip() {
    clearTimeout(tooltipShowTimeout);
    clearTimeout(tooltipHideTimeout);

    document.querySelectorAll('.global-tooltip-active').forEach((tooltip) => {
        fadeOutAndRemoveTooltip(tooltip);
    });

    activeTooltip = null;
    activeAnchor = null;
}

function normalizeTooltipContext(value) {
    const normalized = String(value || '')
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9_-]+/g, '-')
        .replace(/^[-_]+|[-_]+$/g, '');

    return normalized;
}

function applyTooltipContext(tooltip, context) {
    if (!(tooltip instanceof HTMLElement)) {
        return;
    }

    tooltip.className = tooltip.className
        .split(/\s+/)
        .filter((className) => className !== '' && !className.startsWith(TOOLTIP_CONTEXT_CLASS_PREFIX))
        .join(' ');

    if (context === '') {
        tooltip.dataset.tooltipContext = 'default';

        return;
    }

    tooltip.dataset.tooltipContext = context;
    tooltip.classList.add(`${TOOLTIP_CONTEXT_CLASS_PREFIX}${context}`);
}

function fadeOutAndRemoveTooltip(tooltip) {
    tooltip.style.opacity = '0';

    setTimeout(() => {
        tooltip.remove();
    }, FADE_DURATION);
}
