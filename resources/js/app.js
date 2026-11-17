import { setupTwGraphLoading } from './helper/tw-graph-loading';
// resources/js/app.js

import './forms/focus-field';
import './forms/translation-key-input';
import './forms/sync-copyable-field-heights';
import './notices/validation-notices';
import './notices/workbench-mode-changed';

import { setupGlobalTooltips } from './tooltips/global-tooltip';
import { setupShowHideLayoutRefresh } from './helper/show-hide';
import { setupTwGraphLineJumps } from './helper/tw-graph-line-jumps';
import { setupTwGraphBounds } from './helper/tw-graph-bounds';
import { setupTwGraphPerformance } from './helper/tw-graph-performance';
import { setupTwGraphDevTools } from './helper/tw-graph-dev';

setupTwGraphPerformance();
setupTwGraphLoading();

document.addEventListener('DOMContentLoaded', function () {
    setupGlobalTooltips();
    setupShowHideLayoutRefresh();
    setupTwGraphDevTools();
    setupTwGraphLineJumps();
    setupTwGraphBounds();
});

// Writing a watched source file can trigger Vite before Livewire delivers the result toast.
if (import.meta.hot) {
    import.meta.hot.on('vite:beforeFullReload', () => {
        const dialog = document.querySelector('dialog[open][data-awaiting-replacement-result], dialog[open][data-awaiting-publication-result]');
        if (dialog) return new Promise(resolve => dialog.addEventListener('close', resolve, { once: true }));
    });
}
