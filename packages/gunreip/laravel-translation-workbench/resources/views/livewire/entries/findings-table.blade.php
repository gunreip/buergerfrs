{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings-table.blade.php --}}

<flux:card id="translation-workbench-findings">
    <x-ui.headers.card
        :title="__('Findings')"
        :description="__('Translation-capable code findings from the new Workbench data model.')"
    >
        {{-- Reset Button --}}
        <flux:button
            type="button"
            size="sm"
            variant="{{ $findingFiltersActive ? 'primary' : 'subtle' }}"
            color="{{ $findingFiltersActive ? 'cyan' : 'zinc' }}"
            icon="rotate-ccw"
            wire:click="resetFindingFilters"
        >
            {{ __('Reset') }}
        </flux:button>
    </x-ui.headers.card>

    {{-- Search Filters for Findings --}}
    <div class="grid w-full grid-cols-5 gap-3">
        {{-- Search Field --}}
        <flux:field class="col-span-2">
            {{-- Search Filter --}}
            <flux:label>{{ __('Search') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass />
                </flux:input.group.prefix>
                <flux:input
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="findingSearch"
                    placeholder="{{ __('Path, literal or key') }}"
                />
            </flux:input.group>
        </flux:field>

        {{-- Status Field --}}
        <flux:field class="col-span-1">
            {{-- Status Filter --}}
            <flux:label>{{ __('Status') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.pencil-sparkles />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingStatus"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.pencil-sparkles
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingStatusOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.pencil-sparkles
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Kind Field --}}
        <flux:field class="col-span-1">
            {{-- Kind Filter --}}
            <flux:label>{{ __('Kind') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingKind"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.tag
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingKindOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.tag
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Candidate Field --}}
        <flux:field class="col-span-1">
            {{-- Candidate Filter --}}
            <flux:label>{{ __('Candidate') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.shield-cog />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingCandidateType"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.shield-cog
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingCandidateTypeOptions as $option)
                        <flux:select.option value="{{ $option['value'] }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.shield-cog
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option['label'] }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Namespace Filter --}}
        <flux:field class="col-span-1">
            {{-- Namespace Filter --}}
            <flux:label>{{ __('Namespace') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.folder-tree />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingNamespace"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.folder-tree
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingNamespaceOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.folder-tree
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Group Filter --}}
        <flux:field class="col-span-1">
            {{-- Group Filter --}}
            <flux:label>{{ __('Group') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingGroup"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.tag
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingGroupOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.tag
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Key Relation Field --}}
        <flux:field class="col-span-1">
            {{-- Key Relation Filter --}}
            <flux:label>{{ __('Key relation') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.key />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingKeyRelation"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.key
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="linked">
                        <div class="flex items-center gap-2">
                            <flux:icon.link
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Linked') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="missing">
                        <div class="flex items-center gap-2">
                            <flux:icon.key
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Missing') }}
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Source Value Field --}}
        <flux:field class="col-span-1">
            {{-- Source Value Filter --}}
            <flux:label>{{ __('Source value') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.code />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingSourceValue"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.code
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="yes">
                        <div class="flex items-center gap-2">
                            <flux:icon.code
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Exists') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="no">
                        <div class="flex items-center gap-2">
                            <flux:icon.code
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Missing') }}
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- PerPage Selector Buttons --}}
        <x-ui.table.per-page-selector
            id="translation-workbench-findings-per-page"
            name="translation-workbench-findings-per-page"
            model="perPage"
        />
    </div>

    <flux:separator class="mt-4" />

    <div class="mt-4">
        {{-- Pagination Top --}}
        <flux:pagination :paginator="$findings" />
    </div>

    {{-- Table Findings --}}
    <flux:table
        class="mt-4"
        container:class="overflow-x-auto"
    >
        {{-- Table Findings Header Row --}}
        <flux:table.columns>
            {{-- Table Findings Header Column ID --}}
            <flux:table.column
                class="w-20 bg-white dark:bg-zinc-700"
                sticky
            >
                {{ __('ID') }}
            </flux:table.column>
            {{-- Table Findings Header Column Source --}}
            <flux:table.column
                sortable
                :sorted="$findingSortField === 'source'"
                :direction="$findingSortDirection"
                wire:click="sortFindingsBy('source')"
            >
                {{ __('Source') }}
            </flux:table.column>
            {{-- Table Findings Header Column Status --}}
            <flux:table.column>
                {{ __('Status') }}
            </flux:table.column>
            {{-- Table Findings Header Column Kind --}}
            <flux:table.column>
                {{ __('Kind') }}
            </flux:table.column>
            {{-- Table Findings Header Column Literal --}}
            <flux:table.column
                sortable
                :sorted="$findingSortField === 'literal'"
                :direction="$findingSortDirection"
                wire:click="sortFindingsBy('literal')"
            >
                {{ __('Literal') }}
            </flux:table.column>
            {{-- Table Findings Header Column Keys --}}
            <flux:table.column
                sortable
                :sorted="$findingSortField === 'keys'"
                :direction="$findingSortDirection"
                wire:click="sortFindingsBy('keys')"
            >
                {{ __('Keys') }}
            </flux:table.column>
            {{-- Table Findings Header Column Candidate --}}
            <flux:table.column>
                {{ __('Candidate') }}
            </flux:table.column>
            {{-- Table Findings Header Column State --}}
            <flux:table.column>
                {{ __('State') }}
            </flux:table.column>
            {{-- Table Findings Header Column Dynamic Context --}}
            <flux:table.column>
                {{ __('Dynamic context') }}
            </flux:table.column>
            {{-- Table Findings Header Column Actions --}}
            <flux:table.column>
                {{ __('Actions') }}
            </flux:table.column>
        </flux:table.columns>

        {{-- Table Findings Body Rows --}}
        <flux:table.rows>
            {{-- Table Findings Vars --}}
            @forelse ($findings as $finding)
                @php
                    $hasKey = $finding->key_id !== null;
                    $hasSourceValue = (bool) $finding->has_source_value;
                    $hasTargetValue = (bool) ($finding->has_target_value ?? false);
                    $sourceValueDiffers = (bool) ($finding->source_value_differs ?? false);
                    $literal = $finding->literal_text ?: $finding->literal_text_suggested;
                    $hasSourceLiteral = filled($literal);
                    $sourceLocaleLabel = strtoupper((string) ($sourceMainLocale ?? 'en'));
                    $targetLocaleLabel = strtoupper((string) ($targetMainLocale ?? app()->getLocale()));
                    $functionName = trim((string) ($finding->function_name ?? ''));
                    $translationKey = trim((string) ($finding->translation_key ?? ''));
                    $keySuggestedKey = trim((string) ($finding->key_suggested_key ?? ''));
                    $findingSuggestedKey = trim((string) ($finding->suggested_key ?? ''));
                    $existingKey = trim((string) ($finding->existing_key ?? ''));
                    $foundTranslationKey = trim((string) ($finding->found_translation_key ?? ''));
                    $hasTranslationKey = filled($translationKey);
                    $dynamicScope = trim((string) ($finding->dynamic_scope ?? ''));
                    $dynamicDataState = trim(
                        (string) ($finding->key_dynamic_data_state ?? '' ?: $finding->dynamic_data_state ?? ''),
                    );
                    $dynamicValueCount = (int) ($finding->dynamic_value_count ?? 0);
                    $dynamicTargetValueCount = (int) ($finding->dynamic_target_value_count ?? 0);
                    $dynamicSourceCount = (int) ($finding->dynamic_source_count ?? 0);
                    $dynamicMultiSourceCount = (int) ($finding->dynamic_multi_source_count ?? 0);
                    $dynamicUnresolvedSourceCount = (int) ($finding->dynamic_unresolved_source_count ?? 0);
                    $dynamicDiscoveryCount = (int) ($finding->dynamic_discovery_count ?? 0);
                    $dynamicOptionsCount = (int) ($finding->dynamic_options_count ?? 0);
                    $reviewStatus = trim((string) ($finding->review_status ?? ''));
                    $reviewStatusColor = match ($reviewStatus) {
                        'reviewed', 'approved' => 'green',
                        'pending' => 'red',
                        default => 'zinc',
                    };
                    $isUiState = (bool) ($finding->is_ui_key ?? false);
                    $dynamicMultiContext =
                        (bool) ($finding->is_dynamic_multi ?? false) ||
                        (bool) ($finding->reviewed_is_dynamic_multi ?? false) ||
                        $dynamicMultiSourceCount > 0 ||
                        $dynamicValueCount > 1;
                    $dynamicTranslationValuesComplete =
                        $dynamicValueCount > 0 &&
                        $dynamicTargetValueCount > 0 &&
                        $dynamicTargetValueCount >= $dynamicValueCount;
                    $isDynamicMultiState =
                        $dynamicMultiContext &&
                        $reviewStatus === 'reviewed' &&
                        $hasTranslationKey &&
                        $dynamicTranslationValuesComplete;
                    $isDynamicState =
                        $isDynamicMultiState ||
                        (!$dynamicMultiContext &&
                            ((bool) ($finding->is_dynamic_key ?? false) ||
                                (bool) ($finding->reviewed_is_dynamic_candidate ?? false)) &&
                            $reviewStatus === 'reviewed' &&
                            $hasTranslationKey &&
                            $hasTargetValue);
                    $isDynamicFinding =
                        $isDynamicState ||
                        $dynamicMultiContext ||
                        filled($dynamicDataState) ||
                        $dynamicSourceCount > 0 ||
                        ($finding->candidate_type ?? null) === 'dynamic' ||
                        ($finding->entry_type ?? null) === 'dynamic' ||
                        str_starts_with((string) ($finding->kind ?? ''), 'dynamic');
                    $dynamicSourceTypes = collect(explode(',', (string) ($finding->dynamic_source_types ?? '')))
                        ->map(static fn(string $sourceType): string => trim($sourceType))
                        ->filter(static fn(string $sourceType): bool => $sourceType !== '')
                        ->values();
                    $canEditFinding = $hasKey && $hasTranslationKey && $reviewStatus === 'reviewed';
                    $canOpenEditAction = $canEditFinding || $isDynamicFinding;
                    $hasHistory = (bool) ($finding->has_history ?? false);
                    $sourceAbsolutePath = str_replace('\\', '/', base_path($finding->source_path));
                    $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
                    $sourceEditorLine = $finding->source_line ? ':' . $finding->source_line : '';
                    $sourceEditorUrl =
                        'vscode://vscode-remote/wsl+' .
                        rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                        $sourceEditorPath .
                        $sourceEditorLine;
                @endphp

                {{-- Table Findings Row --}}
                <flux:table.row>
                    {{-- Table Findings Cell ID --}}
                    <flux:table.cell
                        class="bg-white font-mono text-xs tabular-nums dark:bg-zinc-700"
                        sticky
                    >
                        #{{ $finding->id }}
                    </flux:table.cell>
                    {{-- Table Findings Cell Source --}}
                    <flux:table.cell>
                        <div class="flex max-w-md items-start gap-2">
                            <flux:button
                                class="mt-0.5 h-5 w-5 shrink-0"
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="external-link"
                                icon:class="text-sky-500 dark:text-sky-400"
                                :href="$sourceEditorUrl"
                                :aria-label="__('Open source in VS Code')"
                            />

                            <div class="flex flex-wrap items-start gap-1.5">
                                <span
                                    class="wrap-anywhere text-wrap font-mono text-xs text-zinc-700 dark:text-zinc-300"
                                    title="{{ $finding->source_path }}"
                                >
                                    {{ $finding->source_path }}
                                </span>
                                <flux:badge
                                    class="shrink-0"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Line') }} {{ $finding->source_line ?? '-' }}
                                </flux:badge>
                            </div>
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell Status --}}
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="{{ $finding->status === 'active' ? 'green' : 'amber' }}"
                        >
                            {{ $finding->status }}
                        </flux:badge>
                    </flux:table.cell>
                    {{-- Table Findings Cell Kind --}}
                    <flux:table.cell>
                        <div class="space-y-1">
                            <flux:badge
                                size="sm"
                                color="sky"
                            >
                                {{ $finding->kind }}
                            </flux:badge>
                            @if (filled($functionName))
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ $functionName }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell Literal --}}
                    <flux:table.cell>
                        <div class="max-w-md hyphens-auto text-wrap text-sm">
                            {{ $literal ?: '-' }}
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell Translation Key --}}
                    <flux:table.cell>
                        <div class="max-w-md space-y-2">
                            <flux:badge
                                size="sm"
                                color="{{ $hasTranslationKey ? 'green' : 'amber' }}"
                            >
                                {{ $hasTranslationKey ? __('Translation key') : __('Translation key missing') }}
                            </flux:badge>

                            @if ($hasTranslationKey)
                                <div class="space-y-0.5">
                                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                                        {{ __('Translation') }}
                                    </div>
                                    <div class="wrap-anywhere text-wrap font-mono text-xs">
                                        {{ $translationKey }}
                                    </div>
                                </div>
                            @endif

                            @if (filled($keySuggestedKey))
                                <div class="space-y-0.5">
                                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                                        {{ __('Suggested') }}
                                    </div>
                                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                        {{ $keySuggestedKey }}
                                    </div>
                                </div>
                            @elseif (filled($findingSuggestedKey))
                                <div class="space-y-0.5">
                                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                                        {{ __('Finding suggested') }}
                                    </div>
                                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                        {{ $findingSuggestedKey }}
                                    </div>
                                </div>
                            @endif

                            @if (filled($existingKey) && $existingKey !== $translationKey)
                                <div class="space-y-0.5">
                                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                                        {{ __('Existing') }}
                                    </div>
                                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                        {{ $existingKey }}
                                    </div>
                                </div>
                            @endif

                            @if (filled($foundTranslationKey) && $foundTranslationKey !== $translationKey && $foundTranslationKey !== $existingKey)
                                <div class="space-y-0.5">
                                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                                        {{ __('Found') }}
                                    </div>
                                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                        {{ $foundTranslationKey }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell Candidate Type --}}
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @if ($isUiState)
                                <x-ui.tooltip.trigger
                                    :title="__('UI translation')"
                                    :text="__(
                                        'This finding was reviewed and confirmed as a reusable user-interface translation.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('Is UI') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($finding->candidate_type === 'ui')
                                <x-ui.tooltip.trigger
                                    :title="$isUiState
                                        ? __('UI candidate completed')
                                        : __('Possible UI translation')"
                                    :text="$isUiState
                                        ? __(
                                            'The scanner originally suggested a UI candidate. Review has confirmed the current UI state, so the candidate task is completed.',
                                        )
                                        : __(
                                            'The scanner suggests that this may be a reusable UI translation, but it has not been confirmed in review yet.',
                                        )"
                                >
                                    <span
                                        class="{{ $isUiState ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                    >
                                        <flux:badge
                                            size="sm"
                                            color="{{ $isUiState ? 'zinc' : 'violet' }}"
                                        >
                                            {{ __('UI candidate') }}
                                        </flux:badge>
                                    </span>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($isDynamicMultiState)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic option list')"
                                    :text="__(
                                        'This key was reviewed as dynamic and can have multiple option values that need their own translations.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('Is Dynamic Multi') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @elseif ($isDynamicState)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic translation')"
                                    :text="__(
                                        'This key was reviewed as dynamic. The displayed value is resolved from runtime data instead of a fixed text call only.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('Is Dynamic') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($finding->candidate_type === 'dynamic')
                                <x-ui.tooltip.trigger
                                    :title="$isDynamicState
                                        ? __('Dynamic candidate completed')
                                        : __('Possible dynamic translation')"
                                    :text="$isDynamicState
                                        ? __(
                                            'The scanner originally detected a dynamic candidate. Review has confirmed the current dynamic state, so the candidate task is completed.',
                                        )
                                        : __(
                                            'The scanner found signs of a dynamic value. Review must decide whether this is dynamic, dynamic options, or a normal translation.',
                                        )"
                                >
                                    <span
                                        class="{{ $isDynamicState ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                    >
                                        <flux:badge
                                            size="sm"
                                            color="{{ $isDynamicState ? 'zinc' : 'violet' }}"
                                        >
                                            {{ __('Dynamic candidate') }}
                                        </flux:badge>
                                    </span>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if (!$finding->is_ui_key && !$finding->is_dynamic_key && !$finding->is_dynamic_multi && !$finding->candidate_type)
                                <x-ui.tooltip.trigger
                                    :title="__('Normal translation')"
                                    :text="__(
                                        'No UI or dynamic candidate state is currently attached to this finding.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                    >
                                        {{ __('Normal') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell State --}}
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @if ($reviewStatus !== '')
                                <x-ui.tooltip.trigger
                                    :title="__('Review status')"
                                    :text="$reviewStatus === 'reviewed' ? __('This finding has passed the review step and can be edited.') : __('This finding still needs review before translation values should be edited.')"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="{{ $reviewStatusColor }}"
                                    >
                                        {{ $reviewStatus === 'reviewed' ? __('Reviewed') : str($reviewStatus)->headline() }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            <x-ui.tooltip.trigger
                                :title="$hasKey ? __('Key relation available') : __('Key relation missing')"
                                :text="$hasKey
                                    ? __('This finding is linked to a workbench translation key record.')
                                    : __(
                                        'This finding still needs a linked translation key record before values can be edited.',
                                    )"
                            >
                                <flux:badge
                                    size="sm"
                                    color="{{ $hasKey ? 'green' : 'amber' }}"
                                >
                                    {{ $hasKey ? __('Key linked') : __('Key missing') }}
                                </flux:badge>
                            </x-ui.tooltip.trigger>

                            <x-ui.tooltip.trigger
                                :title="$hasTargetValue
                                    ? __('Target translation available')
                                    : ($dynamicTranslationValuesComplete
                                        ? __('Target translation completed')
                                        : __('Target translation missing'))"
                                :text="$hasTargetValue
                                    ? __('A translation value exists for the active target language.')
                                    : ($dynamicTranslationValuesComplete
                                        ? __(
                                            'The normal target-value check is not applicable here, because this dynamic entry stores translated option values separately. The original missing state is therefore completed.',
                                        )
                                        : __('No translation value exists yet for the active target language.'))"
                            >
                                <span
                                    class="{{ !$hasTargetValue && $dynamicTranslationValuesComplete ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="{{ $hasTargetValue ? 'green' : 'amber' }}"
                                    >
                                        {{ $hasTargetValue ? __('Target :locale ready', ['locale' => $targetLocaleLabel]) : __('Target :locale missing', ['locale' => $targetLocaleLabel]) }}
                                    </flux:badge>
                                </span>
                            </x-ui.tooltip.trigger>

                            @if (!$hasTargetValue && $dynamicTranslationValuesComplete)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic target translations ready')"
                                    :text="__(
                                        'Target-language values exist for the dynamic option values of this finding.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('Target :locale ready', ['locale' => $targetLocaleLabel]) }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if (!$hasSourceLiteral)
                                <x-ui.tooltip.trigger
                                    :title="__('Source literal missing')"
                                    :text="__(
                                        'Neither a scanned source literal nor a suggested source literal is available. This should be checked before translating.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="red"
                                    >
                                        {{ __('Source :locale missing', ['locale' => $sourceLocaleLabel]) }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($sourceValueDiffers)
                                <x-ui.tooltip.trigger
                                    :title="__('Source value differs')"
                                    :text="__(
                                        'The stored source-language value differs from the currently scanned original literal.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="pink"
                                    >
                                        {{ __('Source :locale changed', ['locale' => $sourceLocaleLabel]) }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell Dynamic Context --}}
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @if ($isDynamicState)
                                <x-ui.tooltip.trigger
                                    :title="__('Current dynamic state')"
                                    :text="__(
                                        'Current reviewed/key state. This is the effective state used by the edit workflow and may differ from the scanner kind.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('State') }}:
                                        {{ $isDynamicMultiState ? __('DynamicMulti') : __('Dynamic') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @elseif ($isDynamicFinding)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic scanner candidate')"
                                    :text="__(
                                        'The scanner detected a dynamic candidate, but no explicit Dynamic/DynamicMulti state has been reviewed yet.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('State') }}: {{ __('Candidate') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if (filled($dynamicDataState))
                                <x-ui.tooltip.trigger
                                    :title="$dynamicTranslationValuesComplete
                                        ? __('Dynamic data state completed')
                                        : __('Dynamic data state')"
                                    :text="$dynamicTranslationValuesComplete
                                        ? __(
                                            'This was the original dynamic data state. The entry now has translated dynamic target values, so this task state is completed.',
                                        )
                                        : __(
                                            'This dynamic finding has not been normalized into dedicated dynamic translation value records yet. It is visible for review, but the dynamic edit workflow still needs structured data.',
                                        )"
                                >
                                    <span
                                        class="{{ $dynamicTranslationValuesComplete && $dynamicDataState !== 'structured' ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                    >
                                        <flux:badge
                                            size="sm"
                                            color="{{ $dynamicDataState === 'structured' ? 'green' : 'orange' }}"
                                        >
                                            {{ $dynamicDataState === 'structured' ? __('Data structured') : __('Data unstructured') }}
                                        </flux:badge>
                                    </span>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($dynamicUnresolvedSourceCount > 0)
                                <x-ui.tooltip.trigger
                                    :title="$dynamicTranslationValuesComplete
                                        ? __('Unresolved source completed')
                                        : __('Unresolved dynamic sources')"
                                    :text="$dynamicTranslationValuesComplete
                                        ? __(
                                            'Unresolved source markers are kept as original scanner context, but the translated dynamic target values are now complete.',
                                        )
                                        : __('Dynamic sources still reported unresolved scanner data.')"
                                >
                                    <span
                                        class="{{ $dynamicTranslationValuesComplete ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                    >
                                        <flux:badge
                                            size="sm"
                                            color="{{ $dynamicTranslationValuesComplete ? 'zinc' : 'red' }}"
                                        >
                                            {{ __('Unresolved') }}: {{ $dynamicUnresolvedSourceCount }}
                                        </flux:badge>
                                    </span>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($dynamicTranslationValuesComplete)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic sources resolved')"
                                    :text="__(
                                        'The dynamic entry has translated target values, so the unresolved source work state is complete for this finding.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('Resolved') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($dynamicTranslationValuesComplete)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic values translated')"
                                    :text="__(
                                        'Target-language values exist for the stored dynamic value keys of this finding.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >
                                        {{ __('Data translated') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>

                                <x-ui.tooltip.trigger
                                    :title="__('Saved to lang file')"
                                    :text="__(
                                        'The translated dynamic values are stored in the workbench database, but the lang file export/sync step has not written them to lang files yet.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="red"
                                    >
                                        {{ __('Saved to Langfile') }}: {{ __('No') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @foreach ($dynamicSourceTypes as $dynamicSourceType)
                                @continue($dynamicSourceType === 'unresolved')

                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic value source')"
                                    :text="__('Origin reported by the dynamic option discovery scanner.')"
                                >
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                    >
                                        {{ str($dynamicSourceType)->replace('_', ' ')->headline() }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endforeach

                            @if ($dynamicValueCount > 0)
                                <x-ui.tooltip.trigger
                                    :title="__('Stored dynamic values')"
                                    :text="__(
                                        'Number of value keys already stored for this dynamic translation key.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="{{ $dynamicTranslationValuesComplete ? 'green' : ($dynamicValueCount > 1 ? 'cyan' : 'sky') }}"
                                    >
                                        {{ __('Stored values') }}: {{ $dynamicValueCount }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($dynamicDiscoveryCount > 0)
                                <x-ui.tooltip.trigger
                                    :title="__('Dynamic discoveries')"
                                    :text="__(
                                        'Number of scanner discoveries that may describe dynamic options for this finding or key.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="{{ $dynamicTranslationValuesComplete ? 'green' : 'amber' }}"
                                    >
                                        {{ __('Discoveries') }}: {{ $dynamicDiscoveryCount }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            @if ($dynamicOptionsCount > 0)
                                <x-ui.tooltip.trigger
                                    :title="__('Possible option values')"
                                    :text="__(
                                        'Largest number of option values found by the dynamic option discovery scanner.',
                                    )"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="{{ $dynamicOptionsCount > 1 ? 'cyan' : 'sky' }}"
                                    >
                                        {{ __('Possible options') }}: {{ $dynamicOptionsCount }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endif

                            {{-- @if (filled($dynamicScope))
                                <flux:badge
                                    size="sm"
                                    color="violet"
                                >
                                    {{ __('scope') }}: {{ $dynamicScope }}
                                </flux:badge>
                            @endif --}}
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell Actions --}}
                    <flux:table.cell>
                        <div class="flex items-center gap-1.5">
                            <flux:button
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="badge-check"
                                :aria-label="__('Review finding')"
                                wire:click="openReviewModal({{ $finding->id }})"
                            />

                            <flux:button
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="square-pen"
                                :aria-label="$isDynamicFinding ? __('Review dynamic translation') : __('Edit translation values')"
                                :disabled="!$canOpenEditAction"
                                wire:click="openEditModal({{ $finding->id }})"
                            />

                            <flux:button
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="activity"
                                :aria-label="__('Show timeline')"
                                :disabled="!$hasHistory"
                                wire:click="openTimelineModal({{ $finding->id }})"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="10">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('No findings for the current filters.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{-- Pagination Bottom --}}
        <flux:pagination :paginator="$findings" />
    </div>
</flux:card>
