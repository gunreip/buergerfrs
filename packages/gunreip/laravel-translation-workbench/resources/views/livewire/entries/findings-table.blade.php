{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings-table.blade.php --}}

<flux:card id="translation-workbench-findings">
    <x-ui.headers.card
        :title="__('Findings')"
        :description="__('Translation-capable code findings from the new Workbench data model.')"
    >
        <flux:button
            type="button"
            size="sm"
            variant="subtle"
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
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.shield-cog
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

        {{-- Namespace Filter --}}
        <flux:field class="col-span-1">
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
        </flux:table.columns>

        {{-- Table Findings Body Rows --}}
        <flux:table.rows>
            {{-- Table Findings Vars --}}
            @forelse ($findings as $finding)
                @php
                    $hasKey = $finding->key_id !== null;
                    $hasSourceValue = (bool) $finding->has_source_value;
                    $literal = $finding->literal_text ?: $finding->literal_text_suggested;
                    $functionName = trim((string) ($finding->function_name ?? ''));
                    $translationKey = trim((string) ($finding->translation_key ?? ''));
                    $keySuggestedKey = trim((string) ($finding->key_suggested_key ?? ''));
                    $findingSuggestedKey = trim((string) ($finding->suggested_key ?? ''));
                    $existingKey = trim((string) ($finding->existing_key ?? ''));
                    $foundTranslationKey = trim((string) ($finding->found_translation_key ?? ''));
                    $hasTranslationKey = filled($translationKey);
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
                            @if ($finding->candidate_type)
                                <flux:badge
                                    size="sm"
                                    color="violet"
                                >
                                    {{ $finding->candidate_type }}
                                </flux:badge>
                            @else
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('None') }}
                                </flux:badge>
                            @endif

                            @if ($finding->is_ui_key)
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('UI') }}
                                </flux:badge>
                            @endif

                            @if ($finding->is_dynamic_key)
                                <flux:badge
                                    size="sm"
                                    color="teal"
                                >
                                    {{ __('Dynamic') }}
                                </flux:badge>
                            @endif

                            @if ($finding->is_dynamic_multi)
                                <flux:badge
                                    size="sm"
                                    color="cyan"
                                >
                                    {{ __('Multi') }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    {{-- Table Findings Cell State --}}
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="{{ $hasKey ? 'green' : 'amber' }}"
                            >
                                {{ $hasKey ? __('Key linked') : __('Key missing') }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="{{ $hasSourceValue ? 'green' : 'amber' }}"
                            >
                                {{ $hasSourceValue ? __('Source value') : __('Source missing') }}
                            </flux:badge>
                            @if ($finding->review_status)
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ $finding->review_status }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
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
