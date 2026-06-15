// resources/js/helper/show-hide.js

function refreshShowHideLayout() {
    window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
            window.dispatchEvent(new Event('resize'));

            window.dispatchEvent(
                new CustomEvent('buergerfrs:show-hide-layout-refreshed', {
                    bubbles: true,
                }),
            );
        });
    });
}

export function setupShowHideLayoutRefresh() {
    window.buergerfrsRefreshShowHideLayout = refreshShowHideLayout;

    document.addEventListener('buergerfrs:refresh-show-hide-layout', refreshShowHideLayout);
}
