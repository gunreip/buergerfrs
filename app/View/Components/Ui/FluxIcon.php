<?php

// app/View/Components/Ui/FluxIcon.php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FluxIcon extends Component
{
    public bool $shouldReport;

    public function __construct(
        public ?string $name = '',
        public ?string $fallback = 'file-x',
        public ?string $variant = 'outline',
        bool|string|int|null $report = true,
    ) {
        $this->shouldReport = filter_var($report, FILTER_VALIDATE_BOOLEAN);
    }

    public function requestedIcon(): string
    {
        return trim((string) $this->name);
    }

    public function fallbackIcon(): string
    {
        $fallback = trim((string) $this->fallback);

        return $fallback !== '' ? $fallback : 'file-x';
    }

    public function iconView(): string
    {
        $requestedView = $this->requestedView();

        if ($requestedView !== null && \Illuminate\Support\Facades\View::exists($requestedView)) {
            return $requestedView;
        }

        $fallbackView = $this->iconViewName($this->fallbackIcon());

        if (\Illuminate\Support\Facades\View::exists($fallbackView)) {
            return $fallbackView;
        }

        return 'flux.icon.file-x';
    }

    public function requestedView(): ?string
    {
        $icon = $this->requestedIcon();

        if ($icon === '' || ! $this->isValidIconName($icon)) {
            return null;
        }

        return $this->iconViewName($icon);
    }

    public function isFallback(): bool
    {
        $requestedIcon = $this->requestedIcon();
        $requestedView = $this->requestedView();

        if ($requestedIcon === '' || $requestedView === null) {
            return false;
        }

        return $this->iconView() !== $requestedView;
    }

    public function reportIfFallback(): void
    {
        if (! $this->shouldReport || ! $this->isFallback()) {
            return;
        }

        app(\App\Support\Fallbacks\FallbackReporter::class)->report(
            type: 'missing_flux_icon',
            key: $this->requestedIcon(),
            fallback: $this->fallbackIcon(),
            context: [
                'component' => 'x-ui.flux-icon',
                'variant' => (string) $this->variant,
                'requested_view' => $this->requestedView(),
                'resolved_view' => $this->iconView(),
            ],
        );
    }

    private function iconViewName(string $icon): string
    {
        return 'flux.icon.' . $icon;
    }

    private function isValidIconName(string $icon): bool
    {
        return preg_match('/^[a-z0-9][a-z0-9-]*$/', $icon) === 1;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.flux-icon');
    }
}
