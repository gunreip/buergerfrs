{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/lang-cleanup.blade.php --}}

@php
    $sourceLocaleHeader = strtoupper((string) ($sourceMainLocale ?? 'en'));
    $targetLocaleHeader = strtoupper((string) ($targetMainLocale ?? app()->getLocale()));
    $langCleanupRows = $langCleanupCandidates->items();
    $langCleanupLoadingTargets = implode(', ', [
        'findingsActiveTab',
        'langCleanupSearch',
        'langCleanupNamespace',
        'langCleanupGroup',
        'langCleanupKeyType',
        'langCleanupContext',
        'langCleanupUsage',
        'langCleanupValueState',
        'resetLangCleanupFilters',
        'gotoPage',
        'nextPage',
        'previousPage',
    ]);
@endphp

<div class="mt-4 space-y-4">
    <flux:callout
        color="{{ ($langCleanupCandidateCount ?? 0) > 0 ? 'amber' : 'green' }}"
        icon="{{ ($langCleanupCandidateCount ?? 0) > 0 ? 'triangle-alert' : 'check-circle' }}"
    >
        <flux:callout.heading>{{ __('Lang cleanup candidates') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Established translation keys that still have lang values but no active code usage. Nothing is deleted here; this tab only makes cleanup candidates visible for review.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="sky"
        icon="clock"
    >
        <flux:callout.heading>{{ __('Inventory report') }}</flux:callout.heading>
        <flux:callout.text>
            <span class="inline-flex flex-wrap items-center gap-x-2 gap-y-1">
                <span>{{ __('Generated') }}:</span>
                <x-ui.date-time.date-time
                    color="inherit"
                    :value="$langCleanupInventoryReport['generated_at'] ?? null"
                />
                <span>·</span>
                <x-ui.date-time.ago
                    color="inherit"
                    :value="$langCleanupInventoryReport['generated_at'] ?? null"
                />
                @if (($langCleanupInventoryReport['rows'] ?? null) !== null)
                    <span>·</span>
                    <span>{{ __('Rows') }}: {{ number_format((int) $langCleanupInventoryReport['rows']) }}</span>
                @endif
                @if (($langCleanupInventoryReport['synced'] ?? null) !== null)
                    <span>·</span>
                    <span>{{ __('Synced') }}:
                        {{ $langCleanupInventoryReport['synced'] ?? false ? __('ui.filters.yes') : __('no') }}</span>
                @endif
            </span>
        </flux:callout.text>
    </flux:callout>

    @include('translation-workbench::livewire.entries.findings.lang-cleanup.filters')

    <x-ui.loading.lazy-indicator
        :target="$langCleanupLoadingTargets"
        text="{{ __('Loading lang cleanup candidates...') }}"
    />

    <div>
        <flux:pagination :paginator="$langCleanupCandidates" />
    </div>

    <div
        class="transition-opacity"
        wire:loading.delay.class="opacity-50"
        wire:target="{{ $langCleanupLoadingTargets }}"
    >
        {{-- Table for Lang Cleanup Candidates --}}
        <flux:table container:class="overflow-x-auto">
            {{-- Table Header Columns --}}
            <flux:table.columns>
                {{-- Column Header Cleanup Candidate Key --}}
                <flux:table.column>
                    <span class="inline-flex items-center gap-1">
                        <span>{{ __('ui.translation.translation-key') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Cleanup candidate key')"
                            :text="__(
                                'Translation key aggregated by the inventory command. These rows still have lang values but no active code usage according to the latest inventory.',
                            )"
                        />
                    </span>
                </flux:table.column>
                {{-- Column Header Namespace --}}
                <flux:table.column>
                    {{ __('Namespace') }}
                </flux:table.column>
                {{-- Column Header Group --}}
                <flux:table.column>
                    {{ __('Group') }}
                </flux:table.column>
                {{-- Column Header Values --}}
                <flux:table.column>
                    <span class="inline-flex items-center gap-1">
                        <span>{{ __('Values') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Lang values')"
                            :text="__(
                                'Shows source and active target-language values. Counts indicate how many active source, target and locale values are known for this key.',
                            )"
                        />
                    </span>
                </flux:table.column>
                {{-- Column Header Usage --}}
                <flux:table.column>
                    <span class="inline-flex items-center gap-1">
                        <span>{{ __('Usage') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Code usage')"
                            :text="__(
                                'Active usage must be zero for cleanup candidates. Commented-out usage blocks automatic cleanup because the code may be restored later.',
                            )"
                        />
                    </span>
                </flux:table.column>
                {{-- Column Header Context --}}
                <flux:table.column>
                    {{ __('Context') }}
                </flux:table.column>
                {{-- Column Header History --}}
                <flux:table.column>
                    <span class="inline-flex items-center gap-1">
                        <span>{{ __('History') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Cleanup row history')"
                            :text="__(
                                'Shows database created and updated timestamps for the inventory row and its source/target lang values. First/last seen are scanner inventory timestamps and are shown in the row tooltip.',
                            )"
                        />
                    </span>
                </flux:table.column>
                {{-- Column Header Actions --}}
                <flux:table.column align="center">{{ __('ui.table.headers.actions') }}</flux:table.column>
            </flux:table.columns>

            {{-- Table Body Rows --}}
            <flux:table.rows>
                @forelse ($langCleanupRows as $row)
                    {{-- Table Body Row --}}
                    <flux:table.row wire:key="translation-workbench-lang-cleanup-{{ $row['id'] }}">
                        {{-- Table Row Cell Translation Key --}}
                        <flux:table.cell>
                            @php
                                $hasNoSourceUsage =
                                    (int) $row['finding_active_count'] === 0 &&
                                    (int) $row['finding_commented_out_count'] === 0;
                            @endphp
                            <div class="flex max-w-lg min-w-0 items-start gap-2">
                                <div class="shrink-0">
                                    <x-ui.tooltip.simple
                                        class="inline-flex"
                                        :header="__('Copy translation key')"
                                        :text="__(
                                            'Copies this translation key to the clipboard so it can be pasted into VS Code search or another review tool.',
                                        )"
                                    >
                                        <flux:button
                                            class="mt-0.5 h-5 w-5"
                                            type="button"
                                            size="xs"
                                            variant="ghost"
                                            icon="copy"
                                            icon:class="text-sky-500 dark:text-sky-400"
                                            x-data
                                            x-on:click="navigator.clipboard?.writeText($el.dataset.translationKey)"
                                            data-translation-key="{{ $row['translation_key'] }}"
                                            :aria-label="__('Copy translation key')"
                                        />
                                    </x-ui.tooltip.simple>
                                </div>

                                <div class="min-w-0 space-y-1.5">
                                    <div class="wrap-anywhere min-w-0 text-wrap font-mono text-xs">
                                        {{ $row['translation_key'] }}
                                    </div>

                                    @if ($hasNoSourceUsage)
                                        <x-ui.tooltip.simple
                                            class="inline-flex"
                                            :header="__('No source usage')"
                                            :text="__(
                                                'This translation key is currently not found in active or commented-out source code. The lang values exist only in the lang-value inventory and are cleanup candidates.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                color="red"
                                            >
                                                {{ __('No source') }}
                                            </flux:badge>
                                        </x-ui.tooltip.simple>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Cell Namespace --}}
                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                variant="subtle"
                            >
                                {{ $row['namespace'] ?: __('None') }}
                            </flux:badge>
                        </flux:table.cell>
                        {{-- Table Row Cell Group --}}
                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                variant="subtle"
                            >
                                {{ $row['group'] ?: __('None') }}
                            </flux:badge>
                        </flux:table.cell>
                        {{-- Table Row Cell Values --}}
                        <flux:table.cell>
                            <div class="space-y-2">
                                <div class="flex min-w-0 items-start gap-2">
                                    <x-ui.locale.flag
                                        class="mt-0.5 size-4"
                                        :locale="$sourceMainLocale"
                                    />
                                    <div
                                        class="min-w-0 max-w-md space-y-1 text-wrap text-sm text-zinc-700 dark:text-zinc-200">
                                        {{ $row['source_value'] !== '' ? $row['source_value'] : __('No source value') }}
                                        @if ($row['source_value_status'] !== '')
                                            <div>
                                                <flux:badge
                                                    size="sm"
                                                    color="{{ $row['source_value_status'] === 'active' ? 'green' : ($row['source_value_status'] === 'obsolete' ? 'amber' : 'zinc') }}"
                                                >
                                                    {{ __('Source') }}: {{ $row['source_value_status'] }}
                                                </flux:badge>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex min-w-0 items-start gap-2">
                                    <x-ui.locale.flag
                                        class="mt-0.5 size-4"
                                        :locale="$targetMainLocale"
                                    />
                                    <div
                                        class="min-w-0 max-w-md space-y-1 text-wrap text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $row['target_value'] !== '' ? $row['target_value'] : __('No target value') }}
                                        @if ($row['target_value_status'] !== '')
                                            <div>
                                                <flux:badge
                                                    size="sm"
                                                    color="{{ $row['target_value_status'] === 'active' ? 'green' : ($row['target_value_status'] === 'obsolete' ? 'amber' : 'zinc') }}"
                                                >
                                                    {{ __('Target') }}: {{ $row['target_value_status'] }}
                                                </flux:badge>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Cell Active --}}
                        <flux:table.cell>
                            <div class="flex max-w-xs flex-wrap gap-1">
                                <flux:badge
                                    size="sm"
                                    color="{{ (int) $row['finding_active_count'] > 0 ? 'red' : 'green' }}"
                                >
                                    {{ __('Active') }}: {{ number_format((int) $row['finding_active_count']) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="{{ (int) $row['finding_commented_out_count'] > 0 ? 'amber' : 'zinc' }}"
                                >
                                    {{ __('Commented') }}:
                                    {{ number_format((int) $row['finding_commented_out_count']) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Obsolete') }}: {{ number_format((int) $row['finding_obsolete_count']) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Relations') }}: {{ number_format((int) $row['relation_active_count']) }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Cell Context --}}
                        <flux:table.cell>
                            <div class="flex max-w-xs flex-wrap gap-1">
                                @if ($row['cleanup_decision'] !== '')
                                    <flux:badge
                                        size="sm"
                                        color="{{ $row['cleanup_decision'] === 'obsolete' ? 'amber' : ($row['cleanup_decision'] === 'keep' ? 'green' : 'sky') }}"
                                    >
                                        {{ __('Cleanup') }}:
                                        {{ str($row['cleanup_decision'])->replace('_', ' ')->headline() }}
                                    </flux:badge>
                                @endif
                                @if ($row['is_orphaned_lang_value'])
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Orphaned lang value') }}
                                    </flux:badge>
                                @endif
                                @if ($row['is_shared'])
                                    <flux:badge
                                        size="sm"
                                        color="cyan"
                                    >
                                        {{ __('Shared') }}
                                    </flux:badge>
                                @endif
                                @if ($row['is_ui'])
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >
                                        {{ __('UI') }}
                                    </flux:badge>
                                @endif
                                @if ($row['is_dynamic'])
                                    <flux:badge
                                        size="sm"
                                        color="{{ $row['is_dynamic_multi'] ? 'violet' : 'sky' }}"
                                    >
                                        {{ $row['is_dynamic_multi'] ? __('Dynamic multi') : __('Dynamic') }}
                                    </flux:badge>
                                @endif
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Key type') }}: {{ $row['key_type'] ?: __('None') }}
                                </flux:badge>
                                @if ($row['cleanup_decision'] === 'obsolete')
                                    <x-ui.tooltip.simple
                                        :title="__('ui.remove.removed.would-be-removed-from-lang-files')"
                                        :text="__(
                                            'This cleanup decision marks the lang values as obsolete. They remain in the database for history, but the next lang-file write will prune them from the generated lang files.',
                                        )"
                                    >
                                        <flux:badge
                                            size="sm"
                                            color="red"
                                        >
                                            {{ __('ui.remove.removed.would-be-removed-from-lang-files') }}
                                        </flux:badge>
                                    </x-ui.tooltip.simple>
                                @endif
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Cell History --}}
                        <flux:table.cell>
                            @php
                                $historyCreatedAt = collect([
                                    $row['source_value_created_at'] ?? null,
                                    $row['target_value_created_at'] ?? null,
                                ])
                                    ->filter()
                                    ->sort()
                                    ->first();
                                $historyUpdatedAt = collect([
                                    $row['source_value_updated_at'] ?? null,
                                    $row['target_value_updated_at'] ?? null,
                                ])
                                    ->filter()
                                    ->sortDesc()
                                    ->first();
                            @endphp
                            <div class="space-y-0.5 text-sm text-zinc-500">
                                <div class="flex items-center gap-1">
                                    <flux:text class="min-w-20 text-zinc-400">{{ __('Created') }}:</flux:text>
                                    <x-ui.date-time.ago :value="$historyCreatedAt" />
                                </div>
                                <div class="flex items-center gap-1">
                                    <flux:text class="min-w-20 text-zinc-400">{{ __('Last update') }}:</flux:text>
                                    <x-ui.date-time.ago :value="$historyUpdatedAt" />
                                    <x-ui.tooltip.simple :header="__('Lang value timestamps')">
                                        <x-slot:content>
                                            <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-xs">
                                                {{-- Source --}}
                                                <flux:heading class="col-span-2 flex items-center gap-2">
                                                    <x-ui.locale.flag
                                                        class="size-4"
                                                        :locale="$sourceMainLocale"
                                                    />
                                                    {{ $sourceLocaleHeader }}
                                                </flux:heading>
                                                <flux:text class="text-zinc-400">
                                                    {{ __('Value created') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['source_value_created_at'] ?? null"
                                                    size="xs"
                                                />
                                                <flux:text class="col-span-1 text-zinc-400">
                                                    {{ __('Value updated') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['source_value_updated_at'] ?? null"
                                                    size="xs"
                                                />
                                                {{-- Target --}}
                                                <flux:heading class="col-span-2 flex items-center gap-2">
                                                    <x-ui.locale.flag
                                                        class="size-4"
                                                        :locale="$targetMainLocale"
                                                    />
                                                    {{ $targetLocaleHeader }}
                                                </flux:heading>
                                                <flux:text class="text-zinc-400">
                                                    {{ __('Value created') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['target_value_created_at'] ?? null"
                                                    size="xs"
                                                />
                                                <flux:text class="text-zinc-400">
                                                    {{ __('Value updated') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['target_value_updated_at'] ?? null"
                                                    size="xs"
                                                />
                                                <flux:heading class="col-span-2 flex items-center gap-2">
                                                    <flux:icon.shelving-unit
                                                        class="size-4"
                                                        inline
                                                    />
                                                    {{ __('Inventory') }}
                                                </flux:heading>
                                                <flux:text class="text-zinc-400">
                                                    {{ __('Created') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['inventory_created_at'] ?? null"
                                                    size="xs"
                                                />
                                                <flux:text class="text-zinc-400">
                                                    {{ __('Updated') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['inventory_updated_at'] ?? null"
                                                    size="xs"
                                                />
                                                <flux:text class="text-zinc-400">
                                                    {{ __('First seen') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['first_seen_at'] ?? null"
                                                    size="xs"
                                                />
                                                <flux:text class="text-zinc-400">
                                                    {{ __('Last seen') }}
                                                </flux:text>
                                                <x-ui.date-time.date-time
                                                    :value="$row['last_seen_at'] ?? null"
                                                    size="xs"
                                                />
                                            </div>
                                        </x-slot:content>
                                    </x-ui.tooltip.simple>
                                </div>
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Cell Actions --}}
                        <flux:table.cell align="center">
                            <div class="flex justify-center gap-1">
                                <x-ui.tooltip.simple
                                    :title="__('Review lang cleanup')"
                                    :text="__(
                                        'Opens a focused review for this cleanup candidate before deciding whether the lang values should stay, need later review, or become obsolete.',
                                    )"
                                >
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        variant="primary"
                                        color="amber"
                                        icon="clipboard-check"
                                        :aria-label="__('Review lang cleanup')"
                                        wire:click="openLangCleanupReview({{ (int) $row['id'] }})"
                                    />
                                </x-ui.tooltip.simple>

                                {{--
                                TODO: Re-enable only after Lang cleanup has a dedicated, reliable code/context lookup.
                                The generic Work findings search can legitimately return no rows for cleanup-only
                                inventory entries, which makes the button feel broken in this tab. --}}
                                {{-- <x-ui.tooltip.simple
                                    :title="__('Show in Work findings')"
                                    :text="__(
                                        'Filters Work findings by this translation key and includes obsolete findings so existing code/history context can be reviewed before cleanup.',
                                    )"
                                >
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        variant="subtle"
                                        color="pink"
                                        icon="list-filter"
                                        :aria-label="__('Show in Work findings')"
                                        wire:click="showExportReportKeyInWorkFindingsFromBase64('{{ base64_encode((string) $row['translation_key']) }}')"
                                    />
                                </x-ui.tooltip.simple> --}}
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    {{-- Table Row Empty --}}
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <flux:text class="text-sm text-zinc-500">
                                {{ __('No lang cleanup candidates in the current inventory.') }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <div>
        <flux:pagination :paginator="$langCleanupCandidates" />
    </div>
</div>
