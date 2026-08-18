// resources/js/app.js

import './forms/focus-field';
import './forms/sync-copyable-field-heights';
import './notices/validation-notices';

import { setupGlobalTooltips } from './tooltips/global-tooltip';
import { setupShowHideLayoutRefresh } from './helper/show-hide';
import { setupTwGraphDevTools } from './helper/tw-graph-dev';

document.addEventListener('DOMContentLoaded', function () {
    setupGlobalTooltips();
    setupShowHideLayoutRefresh();
    setupTwGraphDevTools();
});
