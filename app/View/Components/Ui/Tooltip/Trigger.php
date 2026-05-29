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
        public ?string $actionText = null,
        public ?string $field = null,
        bool|string|int|null $required = false,
        public ?int $delay = null,
        public array|string|null $action = null,
    ) {
        $this->isRequired = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Normalized tooltip action payload for optional confirmation/action UI.
     *
     * @return array<string, mixed>|null
     */
    public function tooltipAction(): ?array
    {
        $action = $this->action;

        if (is_string($action)) {
            $decoded = json_decode($action, true);
            $action = is_array($decoded) ? $decoded : null;
        }

        if (! is_array($action)) {
            return null;
        }

        $label = trim((string) ($action['label'] ?? ''));
        $text = trim((string) ($this->actionText ?? ($action['text'] ?? '')));
        $event = trim((string) ($action['event'] ?? ''));

        if ($label === '' || $event === '') {
            return null;
        }

        return [
            'label' => html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'text' => html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'event' => $event,
            'detail' => is_array($action['detail'] ?? null) ? $action['detail'] : [],
        ];
    }

    public function tooltipActionJson(): ?string
    {
        $action = $this->tooltipAction();

        if ($action === null) {
            return null;
        }

        return json_encode($action, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
