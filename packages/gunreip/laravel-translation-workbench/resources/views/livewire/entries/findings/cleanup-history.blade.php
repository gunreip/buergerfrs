{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/cleanup-history.blade.php --}}

@php
    $cleanupRows = collect($cleanupHistoryRows ?? []);
@endphp

<div class="mt-4 space-y-4">
    <flux:callout
        color="{{ $cleanupRows->isEmpty() ? 'zinc' : 'green' }}"
        icon="{{ $cleanupRows->isEmpty() ? 'info' : 'scissors' }}"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-2">
                <span>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.cleanup_history') }}</span>
                <flux:badge
                    size="sm"
                    color="{{ $cleanupRows->isEmpty() ? 'zinc' : 'green' }}"
                >
                    {{ number_format($cleanupRows->count()) }}
                </flux:badge>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('ui.remove.removed.shows-lang-file-values-that-were-actually-removed-by-a-write-export-run-source-data') }}
        </flux:callout.text>
    </flux:callout>

    {{-- TableCleanup History --}}
    <flux:table container:class="overflow-x-auto">
        {{-- Table Header Cleanup History --}}
        <flux:table.columns>
            {{-- Table Column Event --}}
            <flux:table.column class="w-24">
                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.event') }}
            </flux:table.column>
            {{-- Table Column Locales --}}
            <flux:table.column class="w-28">
                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.locales') }}
            </flux:table.column>
            {{-- Table Column Namespace --}}
            <flux:table.column class="w-36">
                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.namespace') }}
            </flux:table.column>
            {{-- Table Column Translation Key --}}
            <flux:table.column>
                {{ __('ui.translation.translation-key') }}
            </flux:table.column>
            {{-- Table Column Outcome --}}
            <flux:table.column>
                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.outcome') }}
            </flux:table.column>
            {{-- Table Column Lang Key --}}
            <flux:table.column>
                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.lang_key') }}
            </flux:table.column>
            {{-- Table Column Removed Value --}}
            <flux:table.column>
                {{ __('ui.remove.removed.removed-value') }}
            </flux:table.column>
            {{-- Table Column Reason --}}
            <flux:table.column>
                {{ __('Reason') }}
            </flux:table.column>
            {{-- Table Column Removed At --}}
            <flux:table.column>
                {{ __('ui.remove.removed.removed-at') }}
            </flux:table.column>
        </flux:table.columns>

        {{-- Table Body Cleanup History --}}
        <flux:table.rows>
            @forelse ($cleanupRows as $row)
                {{-- Table Row Cleanup History --}}
                <flux:table.row wire:key="translation-workbench-cleanup-history-{{ $row['id'] }}">
                    {{-- Table Cell Events --}}
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @if ($row['derived'])
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.derived') }}
                                </flux:badge>
                            @else
                                @foreach ($row['event_ids'] as $eventId)
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        T#{{ $eventId }}
                                    </flux:badge>
                                @endforeach
                            @endif

                            @foreach ($row['key_ids'] as $keyId)
                                <flux:badge size="sm">K#{{ $keyId }}</flux:badge>
                            @endforeach

                            @foreach ($row['review_ids'] as $reviewId)
                                <flux:badge size="sm">R#{{ $reviewId }}</flux:badge>
                            @endforeach
                        </div>
                    </flux:table.cell>

                    {{-- Table Cell Locales --}}
                    <flux:table.cell>
                        <div class="space-y-1">
                            @foreach ($row['locales'] as $localeRow)
                                <div class="items-center gap-1">
                                    <x-ui.locale.flag
                                        :locale="$localeRow['locale']"
                                        size="sm"
                                    />
                                    <span class="font-mono text-xs">{{ $localeRow['locale'] ?: '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    {{-- Table Cell Namespace --}}
                    <flux:table.cell>
                        <div class="wrap-anywhere font-mono text-xs">{{ $row['namespace'] ?: '—' }}</div>
                    </flux:table.cell>
                    {{-- Table Cell Translation Key / Moved To --}}
                    <flux:table.cell>
                        <div class="space-y-1">
                            <div class="wrap-anywhere max-w-lg text-wrap font-mono text-xs">
                                {{ $row['translation_key'] ?: '—' }}
                            </div>

                            @if ($row['moved_to_translation_key'])
                                <div class="wrap-anywhere max-w-lg text-wrap text-xs text-sky-600 dark:text-sky-400">
                                    <span class="font-medium">{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.moved_to') }}:</span>
                                    <span class="font-mono">{{ $row['moved_to_translation_key'] }}</span>
                                </div>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Cell Outcome --}}
                    <flux:table.cell>
                        <div class="space-y-1">
                            <flux:badge
                                size="sm"
                                color="{{ $row['outcome'] === 'moved' ? 'sky' : 'green' }}"
                            >
                                {{ $row['outcome'] === 'moved' ? __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.moved') : __('ui.remove.removed.removed') }}
                            </flux:badge>
                        </div>
                    </flux:table.cell>
                    {{-- Table Cell Lang Key --}}
                    <flux:table.cell>
                        <div class="wrap-anywhere max-w-md text-wrap font-mono text-xs">
                            {{ $row['lang_key'] ?: '—' }}
                        </div>
                    </flux:table.cell>
                    {{-- Table Cell Removed Value --}}
                    <flux:table.cell>
                        <div class="space-y-1">
                            @foreach ($row['locales'] as $localeRow)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="mt-0.5 inline-flex shrink-0 items-center gap-1">
                                        <x-ui.locale.flag
                                            :locale="$localeRow['locale']"
                                            size="sm"
                                        />
                                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $localeRow['locale'] ?: '—' }}
                                        </span>
                                    </span>
                                    <span class="wrap-anywhere max-w-md text-wrap">
                                        @if (is_array($localeRow['old_value']))
                                            <flux:badge
                                                size="sm"
                                                color="zinc"
                                            >
                                                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.array_value') }}
                                                ({{ number_format(count($localeRow['old_value'])) }})
                                            </flux:badge>
                                        @else
                                            {{ filled($localeRow['old_value']) ? str((string) $localeRow['old_value'])->limit(120) : '—' }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    {{-- Table Cell Reason --}}
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="{{ $row['reason'] === 'obsolete_lang_value' ? 'amber' : 'zinc' }}"
                        >
                            {{ $row['reason'] !== '' ? str($row['reason'])->replace('_', ' ')->headline() : __('Unknown') }}
                        </flux:badge>
                    </flux:table.cell>
                    {{-- Table Cell Removed At --}}
                    <flux:table.cell>
                        <div class="space-y-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                            <x-ui.date-time.date-time
                                :value="$row['removed_at']"
                                size="xs"
                            />
                            <x-ui.date-time.ago :value="$row['removed_at']" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Row Empty --}}
                <flux:table.row>
                    <flux:table.cell colspan="9">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('ui.remove.removed.no-lang-file-values-have-been-removed-yet') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
