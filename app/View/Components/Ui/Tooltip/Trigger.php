<?php

// app/View/Components/Ui/Tooltip/Trigger.php

namespace App\View\Components\Ui\Tooltip;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Trigger extends Component
{
    /**
     * Create a new component instance.
     */
    public bool $isRequired;

    public function __construct(
        public ?string $title = null,
        public ?string $text = null,
        public ?string $field = null,
        bool|string|int|null $required = false,
        public ?int $delay = null,
    ) {
        $this->isRequired = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    }

    public function tooltipTitle(): string
    {
        return $this->normalizeTooltipValue($this->title, 'No tooltip-title!');
    }

    public function tooltipText(): string
    {
        return $this->normalizeTooltipValue($this->text, 'No tooltip-text');
    }

    private function normalizeTooltipValue(?string $value, string $fallback): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function tooltipRequired(): bool
    {
        return $this->isRequired;
    }

    public function tooltipDelay(): ?int
    {
        return $this->delay;
    }

    public function tooltipField(): ?string
    {
        $field = trim((string) $this->field);

        return $field !== '' ? $field : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.tooltip.trigger');
    }
}
