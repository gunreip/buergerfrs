{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/json.blade.php --}}

<div class="tw-graph-protocol-json-accordion min-w-72 max-w-xl">
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading class="rounded rounded-b-md bg-blue-800 p-2">
                <span class="inline-flex items-center gap-2">
                    <span>{{ __('Geometry cache JSON') }}</span>
                    <flux:badge
                        size="sm"
                        color="cyan"
                    >
                        {{ number_format(data_get($graphV2, 'segmentCount', 0)) }}
                    </flux:badge>
                </span>
            </flux:accordion.heading>
            <flux:accordion.content>
                <pre class="tw-graph-protocol-json">{{ json_encode(data_get($graphV2, 'protocol', []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
