function install() {
    window.Livewire.interceptRequest(({ onError }) => {
        onError(({ response, body, preventDefault }) => {
            if (response.status !== 409 || response.headers.get('X-Workbench-Mode-Changed') !== '1') return;

            const notice = JSON.parse(body);
            preventDefault();
            document.dispatchEvent(new CustomEvent('toast-show', { detail: {
                duration: 0,
                slots: { heading: notice.heading, text: notice.message },
                dataset: { variant: 'warning', noticeScope: 'workbench-mode', replaceNoticeScope: 'workbench-mode' },
                action: { label: notice.action, event: 'workbench-reload-page' },
            } }));
        });
    });
}

if (window.Livewire) install();
else document.addEventListener('livewire:init', install, { once: true });

window.addEventListener('workbench-reload-page', () => window.location.reload());
