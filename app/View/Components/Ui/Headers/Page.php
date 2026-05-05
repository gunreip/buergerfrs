<?php

// app/View/Components/Ui/Headers/Page.php

namespace App\View\Components\Ui\Headers;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Page extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public ?string $description = null,
        public string $headingSize = 'xl',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.headers.page');
    }
}
