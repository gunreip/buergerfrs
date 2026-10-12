<div>
    <flux:modal
        class="md:w-3xl xl:w-5xl w-full"
        wire:model.self="show"
        flyout
        variant="floating"
    >
        <flux:heading size="lg">{{ __('Dataset information') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Dataset #:id', ['id' => $datasetId]) }} · {{ $sourceType }}
            @if (isset($record['id'])) #{{ $record['id'] }} @endif</flux:text>
        <flux:separator class="my-4" />
        <flux:heading>
            {{ match ($section) {
                'overview' => __('Overview'),
                'values' => __('Values and changes'),
                'context' => __('Context and relationships'),
                default => __('Technical details'),
            } }}
        </flux:heading>
        <div class="mt-4">
            @if ($entries !== null)
                <flux:text class="mb-3">{{ __('Entries: :count', ['count' => $entries->total()]) }}</flux:text>
                @if ($entries->total() === 0)
                    <flux:text>{{ __('No information available in this section for this entry.') }}</flux:text>
                @endif
                <flux:accordion>
                    @foreach ($entries as $entry)
                        <flux:accordion.item wire:key="details-{{ $sourceType }}-{{ $section }}-{{ $entries->currentPage() }}-{{ $loop->index }}">
                            <flux:accordion.heading>
                                {{ $entry['id'] ? '#'.$entry['id'].' · ' : '' }}{{ $entry['title'] }}
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                @include('translation-workbench::livewire.tw-graph.data-driven.dataset-detail-fields', ['fields' => $entry['fields']])
                                @if ($entry['source'] ?? null)
                                    <flux:button size="sm" class="mt-3"
                                        wire:click="openDetails({{ \Illuminate\Support\Js::from($entry['source']) }}, 'technical')">
                                        {{ $entry['source']['type'] === 'review' ? __('Open review') : __('Open finding') }}
                                    </flux:button>
                                @endif
                                @if ($entry['id'] && in_array($sourceType, ['event-group', 'finding-group'], true))
                                    <flux:button size="sm" class="mt-3"
                                        wire:click="openDetails({{ \Illuminate\Support\Js::from(['type' => $sourceType === 'event-group' ? 'event' : 'finding', 'id' => $entry['id']]) }}, 'values')">
                                        {{ __('Values and changes') }}
                                    </flux:button>
                                @endif
                            </flux:accordion.content>
                        </flux:accordion.item>
                    @endforeach
                </flux:accordion>
                <flux:pagination :paginator="$entries" />
            @elseif ($fields !== [])
                @include('translation-workbench::livewire.tw-graph.data-driven.dataset-detail-fields', ['fields' => $fields])
            @else
                <flux:text>{{ __('No information available in this section for this entry.') }}</flux:text>
            @endif
        </div>
    </flux:modal>
</div>
