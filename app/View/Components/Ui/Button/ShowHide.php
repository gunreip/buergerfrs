<?php

// app/View/Components/Ui/Button/ShowHide.php

namespace App\View\Components\Ui\Button;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShowHide extends Component
{
    public function __construct(
        public string $state = 'open',
        public string $showLabel = 'Show',
        public string $hideLabel = 'Hide',
        public string $size = 'sm',
        public string $variant = 'filled',
        public ?string $width = 'w-28',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.ui.button.show-hide');
    }
}
