<?php

namespace App\Support\Debugbar;

use Fruitcake\LaravelDebugbar\CollectorProviders\ViewsCollectorProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/** Keep grouped view diagnostics, without thousands of individual Overview timeline entries. */
class OverviewViewsCollectorProvider extends ViewsCollectorProvider
{
    public function __invoke(Dispatcher $events, array $options): void
    {
        if (self::isOverviewRequest(request())) {
            $options['timeline'] = false;
        }

        parent::__invoke($events, $options);
    }

    public static function isOverviewRequest(Request $request): bool
    {
        if (! $request->isMethod('POST') || ! $request->hasHeader('X-Livewire')) {
            return false;
        }

        foreach ((array) $request->input('components', []) as $component) {
            $snapshot = is_array($component) ? ($component['snapshot'] ?? null) : null;
            if (! is_string($snapshot)) {
                continue;
            }
            $snapshot = json_decode($snapshot, true);
            if (data_get($snapshot, 'memo.name') === 'translation-workbench.tw-graph.overview') {
                return true;
            }
        }

        return false;
    }
}
