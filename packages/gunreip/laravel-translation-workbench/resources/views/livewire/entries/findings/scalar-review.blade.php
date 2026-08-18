{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/scalar-review.blade.php --}}

@php
    $paginator = $scalarReviewRows instanceof \Illuminate\Pagination\LengthAwarePaginator ? $scalarReviewRows : null;
    $rows = $paginator ? $paginator->getCollection() : collect($scalarReviewRows ?? []);
    $totalCount = $paginator ? $paginator->total() : $rows->count();
    $highRiskCount = $rows->where('risk', 'high')->count();
    $proposalCount = $rows->whereNotNull('suggested_translation_key')->count();
    $selectedCount = count($scalarReviewSelectedLangValueIds ?? []);
@endphp

<div class="mt-4 space-y-4">
    <flux:callout
        color="{{ $totalCount === 0 ? 'zinc' : 'amber' }}"
        icon="git-branch"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-2">
                <span>{{ __('Scalar key review') }}</span>
                <flux:badge
                    size="sm"
                    color="{{ $totalCount === 0 ? 'zinc' : 'amber' }}"
                >
                    {{ number_format($totalCount) }}
                </flux:badge>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Lists active scalar language values that may benefit from a more explicit array-style translation key. This is read-only analysis; no keys are changed here.') }}
        </flux:callout.text>
    </flux:callout>

    <div class="grid grid-cols-3 gap-3">
        <flux:callout
            color="amber"
            icon="triangle-alert"
        >
            <flux:callout.heading>{{ __('High risk') }}</flux:callout.heading>
            <flux:callout.text>
                {{ number_format($highRiskCount) }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="sky"
            icon="lightbulb"
        >
            <flux:callout.heading>{{ __('With proposal') }}</flux:callout.heading>
            <flux:callout.text>
                {{ number_format($proposalCount) }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="zinc"
            icon="info"
        >
            <flux:callout.heading>{{ __('Scope') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Source locale plus current target locale') }}
            </flux:callout.text>
        </flux:callout>
    </div>

    <flux:separator text="{{ __('Scalar review filters') }}" />

    <div class="grid w-full grid-cols-6 items-end gap-3">
        <flux:field class="col-span-3">
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Search') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Scalar review search')"
                        :text="__(
                            'Searches current translation keys, source and target values, leaf segments and suggested grouping keys inside Scalar review.',
                        )"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass />
                </flux:input.group.prefix>
                <flux:input
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="scalarReviewSearch"
                    placeholder="{{ __('Key, value or proposal') }}"
                />
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-1">
            <flux:label>{{ __('Risk') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.umbrella-off />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="scalarReviewRisk"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.umbrella-off
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All risks') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="high">
                        <div class="flex items-center gap-2">
                            <flux:icon.umbrella-off
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('High') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="medium">
                        <div class="flex items-center gap-2">
                            <flux:icon.umbrella-off
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Medium') }}
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-1">
            <flux:label>{{ __('Namespace') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.cube />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="scalarReviewNamespace"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.cube
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All namespaces') }}
                        </div>
                    </flux:select.option>
                    @foreach ($scalarReviewNamespaceOptions as $namespace)
                        <flux:select.option value="{{ $namespace }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.cube
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $namespace }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-1">
            <flux:label>{{ __('Proposal') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.light-bulb />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="scalarReviewProposal"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.light-bulb
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All proposals') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="with">
                        <div class="flex items-center gap-2">
                            <flux:icon.light-bulb
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('With proposal') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="without">
                        <div class="flex items-center gap-2">
                            <flux:icon.light-bulb
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Needs review') }}
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:separator class="col-span-6 mt-3" />

        <div class="col-span-6 flex justify-end">
            <x-ui.table.per-page-selector
                id="translation-workbench-scalar-review-per-page"
                wire:model.live="perPage"
            />
        </div>
    </div>

    @if ($paginator && $paginator->hasPages())
        <flux:pagination :paginator="$paginator" />
    @endif

    <flux:table container:class="overflow-x-auto">
        <flux:table.columns>
            <flux:table.column
                class="w-24"
                sortable
                sticky
                :sorted="$scalarReviewSortField === 'risk'"
                :direction="$scalarReviewSortDirection"
                wire:click="sortScalarReviewBy('risk')"
            >
                {{ __('Risk') }}
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$scalarReviewSortField === 'translation_key'"
                :direction="$scalarReviewSortDirection"
                wire:click="sortScalarReviewBy('translation_key')"
            >
                {{ __('Current translation key') }}
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$scalarReviewSortField === 'values'"
                :direction="$scalarReviewSortDirection"
                wire:click="sortScalarReviewBy('values')"
            >
                {{ __('Values') }}
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$scalarReviewSortField === 'proposal'"
                :direction="$scalarReviewSortDirection"
                wire:click="sortScalarReviewBy('proposal')"
            >
                {{ __('Suggested grouping') }}
            </flux:table.column>
            <flux:table.column
                class="w-40"
                sortable
                :sorted="$scalarReviewSortField === 'usage'"
                :direction="$scalarReviewSortDirection"
                wire:click="sortScalarReviewBy('usage')"
            >
                {{ __('Usage') }}
            </flux:table.column>
            <flux:table.column
                class="w-48 min-w-48"
                align="center"
            >
                <div class="flex min-w-48 flex-nowrap items-center justify-between gap-2 whitespace-nowrap">
                    <div class="justify-end! flex w-8 shrink-0">
                        @if ($selectedCount > 0)
                            <x-ui.tooltip.simple
                                class="inline-flex"
                                :title="__('Transform selected keys')"
                                :text="__(
                                    'Opens a focused review modal to transform the selected scalar translation keys into array-style translation keys.',
                                )"
                            >
                                <flux:button.group>
                                    <flux:button
                                        class="h-6 w-6"
                                        type="button"
                                        size="xs"
                                        variant="primary"
                                        color="amber"
                                        icon="git-branch"
                                        wire:click="openScalarTransformModal"
                                    />
                                    <flux:button
                                        class="h-6 w-6"
                                        type="button"
                                        size="xs"
                                        variant="primary"
                                        color="amber"
                                        wire:click="openScalarTransformModal"
                                    >
                                        {{ number_format($selectedCount) }}
                                    </flux:button>
                                </flux:button.group>
                            </x-ui.tooltip.simple>
                        @endif
                    </div>

                    <span class="inline-flex min-w-0 flex-1 items-center justify-center gap-1">
                        <span>
                            {{ __('ui.table.headers.actions') }}
                        </span>
                        <x-ui.tooltip.simple
                            class="inline-flex"
                            :header="__('Scalar review actions')"
                            :text="__(
                                'Select scalar candidates for the next focused review step. No translation keys are changed from this table.',
                            )"
                        />
                    </span>

                    <div class="flex min-w-0 shrink-0 justify-start">
                        @if ($selectedCount > 0)
                            <x-ui.tooltip.simple
                                class="inline-flex"
                                :title="__('Clear scalar selection')"
                                :text="__(
                                    'Removes the selected scalar candidates from the current review selection.',
                                )"
                            >
                                <flux:button
                                    class="h-6 w-6"
                                    type="button"
                                    size="xs"
                                    variant="subtle"
                                    color="zinc"
                                    icon="x"
                                    wire:click="clearScalarReviewSelection"
                                />
                            </x-ui.tooltip.simple>
                        @endif
                    </div>
                </div>
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($rows as $row)
                <flux:table.row wire:key="translation-workbench-scalar-review-{{ $row['id'] }}">
                    <flux:table.cell>
                        <div class="space-y-1">
                            <flux:badge
                                size="sm"
                                color="{{ $row['risk'] === 'high' ? 'amber' : 'zinc' }}"
                            >
                                {{ $row['risk'] === 'high' ? __('High') : __('Medium') }}
                            </flux:badge>
                            <div class="font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                {{ number_format($row['segment_count']) }} {{ __('segments') }}
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="space-y-1">
                            <div class="wrap-anywhere font-mono text-xs">
                                {{ $row['translation_key'] }}
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <flux:badge size="sm">{{ $row['namespace'] ?: __('No namespace') }}</flux:badge>

                                @if ($row['is_ui'])
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >
                                        {{ __('UI') }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="space-y-1">
                            <div class="flex items-start gap-2">
                                <x-ui.locale.flag
                                    :locale="$row['source_locale']"
                                    size="sm"
                                />
                                <span class="wrap-anywhere max-w-md text-wrap text-sm">
                                    {{ str($row['source_value'])->limit(90) }}
                                </span>
                            </div>
                            @if ($row['target_locale'] !== '')
                                <div class="flex items-start gap-2 text-zinc-500 dark:text-zinc-400">
                                    <x-ui.locale.flag
                                        :locale="$row['target_locale']"
                                        size="sm"
                                    />
                                    <span class="wrap-anywhere max-w-md text-wrap text-sm">
                                        {{ str($row['target_value'])->limit(90) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="space-y-1">
                            @if ($row['suggested_translation_key'])
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ $row['suggested_group'] }}
                                </flux:badge>
                                <div class="wrap-anywhere font-mono text-xs text-sky-600 dark:text-sky-400">
                                    {{ $row['suggested_translation_key'] }}
                                </div>
                            @else
                                <flux:badge
                                    size="sm"
                                    color="zinc"
                                >
                                    {{ __('Needs review') }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="space-y-1">
                            <flux:badge size="sm">
                                {{ __('Findings') }}: {{ number_format($row['finding_active_count']) }}
                            </flux:badge>
                            <flux:badge size="sm">
                                {{ __('Locales') }}: {{ number_format($row['lang_file_locale_count']) }}
                            </flux:badge>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <flux:field variant="inline">
                            <flux:checkbox
                                value="{{ $row['id'] }}"
                                wire:key="translation-workbench-scalar-review-checkbox-{{ $row['id'] }}"
                                wire:model.live="scalarReviewSelectedLangValueIds"
                            />
                            <flux:label>
                                <x-ui.tooltip.simple
                                    :title="__('Select scalar candidate')"
                                    :text="__(
                                        'Adds this scalar language value to the selection for the upcoming scalar key review modal.',
                                    )"
                                />
                            </flux:label>
                        </flux:field>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('No scalar grouping candidates found.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($paginator && $paginator->hasPages())
        <flux:pagination :paginator="$paginator" />
    @endif
</div>
