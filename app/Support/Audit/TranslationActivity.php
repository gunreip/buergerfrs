<?php

namespace App\Support\Audit;

use Illuminate\Database\Eloquent\Model;

class TranslationActivity
{
    private const LOG_NAME = 'translations';

    /**
     * Record one user-facing translation workflow action.
     *
     * Fine-grained entity history remains in translation_audit_events. This
     * activity entry intentionally summarizes the initiating admin action.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @param  array<string, mixed>  $properties
     */
    public function record(
        string $event,
        string $description,
        ?Model $subject = null,
        array $before = [],
        array $after = [],
        array $properties = [],
    ): void {
        $logger = activity(self::LOG_NAME)
            ->event($event)
            ->withProperties(array_merge($properties, [
                'before' => $before,
                'after' => $after,
                'source' => [
                    'route' => request()?->route()?->getName(),
                    'url' => request()?->fullUrl(),
                    'component' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'] ?? null,
                ],
            ]));

        if (auth()->check()) {
            $logger->causedBy(auth()->user());
        }

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        $logger->log($description);
    }
}
