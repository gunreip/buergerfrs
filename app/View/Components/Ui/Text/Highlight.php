<?php

// app/View/Components/Ui/Text/Highlight.php

namespace App\View\Components\Ui\Text;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Highlight extends Component
{
    public string $value;

    public string $search;

    public string $markClass;

    public bool $caseSensitive;

    /**
     * Create a new component instance.
     */
    public function __construct(
        mixed $value = '',
        mixed $search = '',
        string $markClass = 'highlight',
        bool $caseSensitive = false,
    ) {
        $this->value = (string) $value;
        $this->search = trim((string) $search);
        $this->markClass = $markClass;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * Return the safely escaped value with highlighted search matches.
     */
    public function highlighted(): HtmlString
    {
        $escapedValue = e($this->value);

        if ($this->search === '') {
            return new HtmlString($escapedValue);
        }

        $escapedSearch = e($this->search);

        if ($escapedSearch === '') {
            return new HtmlString($escapedValue);
        }

        $modifiers = $this->caseSensitive ? 'u' : 'iu';

        $highlighted = Str::of($escapedValue)
            ->replaceMatches(
                '/' . preg_quote($escapedSearch, '/') . '/' . $modifiers,
                '<mark class="' . e($this->markClass) . '">$0</mark>',
            )
            ->toString();

        return new HtmlString($highlighted);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.text.highlight');
    }
}
