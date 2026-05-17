// resources/js/components/ui/global-tooltip.js

const TOOLTIP_TEMPLATE_ID = 'global-tooltip-template';
const DEFAULT_SHOW_DELAY = 2500;
const DEFAULT_HIDE_DELAY = 120;
const FADE_DURATION = 200;
const TOOLTIP_OFFSET = 8;
const VIEWPORT_PADDING = 12;
const TOOLTIP_MAX_WIDTH = 448;

let tooltipShowTimeout = null;
let tooltipHideTimeout = null;
let activeTooltip = null;
let activeAnchor = null;

export function setupGlobalTooltips() {
    document.body.addEventListener('mouseenter', function (e) {
        const trigger = e.target.closest('.tooltip-trigger');

        if (!trigger) {
            return;
        }

        scheduleShowGlobalTooltip(trigger, e);
    }, true);

    document.body.addEventListener('mouseleave', function (e) {
        const trigger = e.target.closest('.tooltip-trigger');

        if (!trigger) {
            return;
        }

        scheduleRemoveGlobalTooltip(trigger);
    }, true);

    document.body.addEventListener('click', function (e) {
        const trigger = e.target.closest('.tooltip-trigger');

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

    const template = document.getElementById(TOOLTIP_TEMPLATE_ID);

    if (!template) {
        return;
    }

    const content = anchorEl.dataset.tooltip || '';
    const title = anchorEl.dataset.tooltipTitle || '';
    const required = anchorEl.dataset.tooltipRequired === 'true';

    if (!content && !title) {
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

    if (titleEl) {
        titleEl.textContent = title;
    }

    if (contentEl) {
        contentEl.textContent = content;
    }

    if (requiredBadge) {
        requiredBadge.toggleAttribute('hidden', !required);
    }

    tooltip.style.visibility = 'hidden';
    tooltip.style.position = 'absolute';

    document.body.appendChild(tooltip);

    positionTooltip(tooltip, anchorEl, pointer);

    tooltip.classList.add('global-tooltip-active');
    tooltip.style.visibility = '';

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
        tooltip.classList.add('opacity-100');
        tooltip.classList.remove('opacity-0');
    }, showDelay);
}

function positionTooltip(tooltip, anchorEl, pointer = null) {
    const rect = anchorEl.getBoundingClientRect();
    const scrollY = window.scrollY;
    const scrollX = window.scrollX;

    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    const maxTooltipWidth = Math.min(TOOLTIP_MAX_WIDTH, viewportWidth - VIEWPORT_PADDING * 2);

    tooltip.style.width = 'max-content';
    tooltip.style.maxWidth = `${maxTooltipWidth}px`;
    tooltip.style.transform = 'none';

    const tooltipRect = tooltip.getBoundingClientRect();

    const preferredCenterX = pointer
        ? pointer.clientX
        : rect.left + rect.width / 2;

    const preferredLeft = preferredCenterX - tooltipRect.width / 2;
    const minLeft = VIEWPORT_PADDING;
    const maxLeft = viewportWidth - VIEWPORT_PADDING - tooltipRect.width;
    const left = clamp(preferredLeft, minLeft, Math.max(minLeft, maxLeft));

    const spaceAbove = rect.top;
    const spaceBelow = viewportHeight - rect.bottom;
    const shouldPlaceAbove = spaceAbove >= tooltipRect.height + TOOLTIP_OFFSET
        || spaceAbove > spaceBelow;

    const preferredTop = shouldPlaceAbove
        ? rect.top - tooltipRect.height - TOOLTIP_OFFSET
        : rect.bottom + TOOLTIP_OFFSET;

    const minTop = VIEWPORT_PADDING;
    const maxTop = viewportHeight - VIEWPORT_PADDING - tooltipRect.height;
    const top = clamp(preferredTop, minTop, Math.max(minTop, maxTop));

    tooltip.style.left = `${scrollX + left}px`;
    tooltip.style.top = `${scrollY + top}px`;
    tooltip.scrollTop = 0;
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

function fadeOutAndRemoveTooltip(tooltip) {
    tooltip.classList.remove('opacity-100');
    tooltip.classList.add('opacity-0');

    setTimeout(() => {
        tooltip.remove();
    }, FADE_DURATION);
}
