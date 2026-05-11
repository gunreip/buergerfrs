// resources/js/app.js

import './forms/focus-field';
import './notices/validation-notices';

import { setupGlobalTooltips } from './tooltips/global-tooltip';

document.addEventListener('DOMContentLoaded', function () {
    setupGlobalTooltips();
});
