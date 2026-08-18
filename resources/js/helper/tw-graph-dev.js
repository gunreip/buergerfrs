// resources/js/helper/tw-graph-dev.js

export function setupTwGraphDevTools() {
    document.addEventListener('click', (event) => {
        const graphElement = event.target.closest('.tw-graph-dev [data-tw-graph-path]');

        if (!graphElement) {
            return;
        }

        const graphPath = graphElement.dataset.twGraphPath;

        if (!graphPath) {
            return;
        }

        if (!navigator.clipboard?.writeText) {
            console.info('[tw-graph-dev] Clipboard API unavailable:', graphPath);

            return;
        }

        navigator.clipboard
            .writeText(graphPath)
            .then(() => console.info('[tw-graph-dev] Copied graph path:', graphPath))
            .catch((error) => console.warn('[tw-graph-dev] Could not copy graph path:', graphPath, error));
    });
}
