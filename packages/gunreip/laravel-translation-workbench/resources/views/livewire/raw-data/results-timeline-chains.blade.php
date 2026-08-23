{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/results-timeline-chains.blade.php --}}

@php
    $sampleRows = collect($timelineChainSampleRows ?? []);
    $mainRow = $timelineChainMainRow ?? null;
    $rootRows = collect($timelineChainRootRows ?? []);
    $originRows = collect($timelineChainOriginRows ?? []);
    $previewOptions = collect($timelineChainPreviewOptions ?? []);
@endphp

<flux:callout
    class="mt-4"
    color="zinc"
    icon="flask-conical"
>
    <flux:callout.heading>
        <span class="flex w-full flex-wrap items-center justify-between gap-3">
            <span>{{ __('Timeline chain graph preview dataset') }}</span>

            <flux:field
                class="min-w-120"
                variant="inline"
            >
                <flux:select
                    wire:model.live="timelineChainPreviewId"
                    variant="combobox"
                >
                    <flux:select.option value="auto">
                        {{ __('Auto sample') }}
                    </flux:select.option>

                    @foreach ($previewOptions as $option)
                        @php
                            $optionLabel = trim(
                                '#' .
                                    (string) $option['id'] .
                                    ' · ' .
                                    str((string) $option['chain_type'])->headline() .
                                    ' · ' .
                                    str((string) $option['chain_status'])->headline() .
                                    ' · ' .
                                    str((string) $option['translation_key'])->limit(80),
                            );
                        @endphp
                        <flux:select.option value="{{ (string) $option['id'] }}">
                            {{ $optionLabel }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
        </span>
    </flux:callout.heading>

    <flux:callout.text>
        {{ __('Temporary review selector for testing the data-driven graph against different timeline-chain constellations before the renderer becomes more automatic.') }}
    </flux:callout.text>
</flux:callout>

@if ($mainRow)
    @php
        $mainRelatedKeys = collect($mainRow['related_translation_keys'] ?? [])
            ->filter()
            ->reject(static fn($key): bool => (string) $key === (string) ($mainRow['translation_key'] ?? ''))
            ->values();
        $mainEventSummary = collect($mainRow['timeline_event_summary'] ?? [])
            ->sortDesc()
            ->take(6);
        $mainLangValueSummary = collect($mainRow['lang_value_summary'] ?? []);
    @endphp

    @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-root')
    @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-roots')
    @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview')
    @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-focus')
@endif

@include('translation-workbench::livewire.raw-data.timeline-chains.samples')
