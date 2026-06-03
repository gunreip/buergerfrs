// resources/js/app.js

import './forms/focus-field';
import './forms/sync-copyable-field-heights';
import './notices/validation-notices';

import { setupGlobalTooltips } from './tooltips/global-tooltip';

document.addEventListener('DOMContentLoaded', function () {
    setupGlobalTooltips();
});
