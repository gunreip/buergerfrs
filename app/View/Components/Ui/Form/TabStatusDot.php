<?php

// app/View/Components/Ui/Form/TabStatusDot.php

namespace App\View\Components\Ui\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TabStatusDot extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $meta = [],
        public ?string $label = null,
        public ?string $icon = null,
        public bool $toggleable = false,
    ) {
        //
    }

    public function status(): string
    {
        $status = $this->meta['status'] ?? 'empty';

        return is_string($status) && $status !== '' ? $status : 'empty';
    }

    public function total(): int
    {
        return $this->fieldTotal();
    }

    public function filled(): int
    {
        return $this->fieldFilled();
    }

    public function fieldTotal(): int
    {
        return (int) ($this->meta['field_total'] ?? $this->meta['total'] ?? 0);
    }

    public function fieldFilled(): int
    {
        return (int) ($this->meta['field_filled'] ?? $this->meta['filled'] ?? 0);
    }

    public function optionalTotal(): int
    {
        return (int) ($this->meta['optional_total'] ?? 0);
    }

    public function optionalFilled(): int
    {
        return (int) ($this->meta['optional_filled'] ?? 0);
    }

    public function requiredTotal(): int
    {
        return (int) ($this->meta['required_total'] ?? 0);
    }

    public function requiredFilled(): int
    {
        return (int) ($this->meta['required_filled'] ?? 0);
    }

    public function statusLabel(?string $status = null): string
    {
        return match ($status ?? $this->status()) {
            'error' => __('view.components.ui.form.tab_status_dot.validation_errors'),
            'missing-required' => __('view.components.ui.form.tab_status_dot.missing_required_fields'),
            'complete' => __('view.components.ui.form.tab_status_dot.complete'),
            'partial' => __('view.components.ui.form.tab_status_dot.partially_filled'),
            default => __('view.components.ui.form.tab_status_dot.empty'),
        };
    }

    public function statusDescription(?string $status = null): string
    {
        return match ($status ?? $this->status()) {
            'error' => __('view.components.ui.form.tab_status_dot.this_tab_contains_validation_errors'),
            'missing-required' => __('view.components.ui.form.tab_status_dot.required_fields_are_missing'),
            'complete' => __('view.components.ui.form.tab_status_dot.all_relevant_fields_are_filled'),
            'partial' => __('view.components.ui.form.tab_status_dot.some_fields_are_filled'),
            default => __('view.components.ui.form.tab_status_dot.no_relevant_field_is_filled'),
        };
    }

    public function statusClasses(?string $status = null): string
    {
        return match ($status ?? $this->status()) {
            'error' => 'bg-red-500 ring-red-200 dark:ring-red-900/60',
            'missing-required' => 'bg-amber-500 ring-amber-200 dark:ring-amber-900/60',
            'complete' => 'bg-green-500 ring-green-200 dark:ring-green-900/60',
            'partial' => 'bg-blue-500 ring-blue-200 dark:ring-blue-900/60',
            default => 'bg-zinc-300 ring-zinc-200 dark:bg-zinc-600 dark:ring-zinc-700',
        };
    }

    public function legendStatuses(): array
    {
        return [
            'error',
            'missing-required',
            'complete',
            'partial',
            'empty',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.form.tab-status-dot');
    }
}
