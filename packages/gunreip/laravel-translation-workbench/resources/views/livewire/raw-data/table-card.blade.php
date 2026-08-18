{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php --}}

{{-- Card --}}
<flux:card>
    {{-- Card Header --}}
    @php
        $tableStorageSize = $tableStorageSize ?? ['bytes' => null, 'pretty' => '—'];
        $tableStorageSizeText = (string) ($tableStorageSize['pretty'] ?? '—');
    @endphp
    <x-ui.headers.card
        class="tabular-nums"
        :title="$table"
    >
        <x-slot:descriptionSlot>
            <span class="inline-flex items-center gap-1">
                <span>{{ count($columns) }} {{ __('columns') }}</span>
                <span>·</span>
                <span>{{ __('ui.storage.storage') }}: {{ $tableStorageSizeText }}</span>
                <x-ui.tooltip.simple
                    :title="__('Database table storage')"
                    :text="__(
                        'PostgreSQL total relation size for this table, including table data, indexes and auxiliary storage where available.',
                    )"
                />
            </span>
        </x-slot:descriptionSlot>

        <x-ui.table.per-page-selector
            id="translation-workbench-raw-data-per-page"
            name="translation-workbench-raw-data-per-page"
            model="perPage"
        />
    </x-ui.headers.card>

    @if ($table === 'translation_workbench_source_files')
        @include('translation-workbench::livewire.raw-data.filters-source-files')
    @endif

    @if ($table === 'translation_workbench_event_types')
        @include('translation-workbench::livewire.raw-data.filters-event-types')
    @endif

    @if ($table === 'translation_workbench_findings')
        @include('translation-workbench::livewire.raw-data.filters-findings')
    @endif

    @if ($table === 'translation_workbench_keys')
        @include('translation-workbench::livewire.raw-data.filters-keys')
    @endif

    @if ($table === 'translation_workbench_key_findings')
        @include('translation-workbench::livewire.raw-data.filters-key-findings')
    @endif

    @if ($table === 'translation_workbench_key_values')
        @include('translation-workbench::livewire.raw-data.filters-key-values')
    @endif

    @if ($table === 'translation_workbench_dynamic_key_values')
        @include('translation-workbench::livewire.raw-data.filters-dynamic-key-values')
    @endif

    @if ($table === 'translation_workbench_dynamic_sources')
        @include('translation-workbench::livewire.raw-data.filters-dynamic-sources')
    @endif

    @if ($table === 'translation_workbench_dynamic_source_candidates')
        @include('translation-workbench::livewire.raw-data.filters-dynamic-source-candidates')
    @endif

    @if ($table === 'translation_workbench_dynamic_source_values')
        @include('translation-workbench::livewire.raw-data.filters-dynamic-source-values')
    @endif

    @if ($table === 'translation_workbench_lang_values')
        @include('translation-workbench::livewire.raw-data.filters-lang-values')
    @endif

    @if ($table === 'translation_workbench_reviews')
        @include('translation-workbench::livewire.raw-data.filters-reviews')
    @endif

    @if ($table === 'translation_workbench_shared_key_candidates')
        @include('translation-workbench::livewire.raw-data.filters-shared-key-candidates')
    @endif

    @if ($table === 'translation_workbench_key_inventory')
        @include('translation-workbench::livewire.raw-data.filters-key-inventory')
    @endif

    @if ($table === 'translation_workbench_timeline_events')
        @include('translation-workbench::livewire.raw-data.filters-timeline-events')
        @include('translation-workbench::livewire.raw-data.results-timeline-events')
    @endif

    @if ($table === 'translation_workbench_timeline_chains')
        @include('translation-workbench::livewire.raw-data.results-timeline-chains')
    @endif

    @php
        $rawDataSearch = trim(
            (string) match ($table) {
                'translation_workbench_source_files' => $sourceFilesSearch ?? '',
                'translation_workbench_event_types' => $eventTypesSearch ?? '',
                'translation_workbench_findings' => $findingsSearch ?? '',
                'translation_workbench_keys' => $keysSearch ?? '',
                'translation_workbench_key_findings' => $keyFindingsSearch ?? '',
                'translation_workbench_key_values' => $keyValuesSearch ?? '',
                'translation_workbench_dynamic_key_values' => $dynamicKeyValuesSearch ?? '',
                'translation_workbench_dynamic_sources' => $dynamicSourcesSearch ?? '',
                'translation_workbench_dynamic_source_candidates' => $dynamicSourceCandidatesSearch ?? '',
                'translation_workbench_dynamic_source_values' => $dynamicSourceValuesSearch ?? '',
                'translation_workbench_lang_values' => $langValuesSearch ?? '',
                'translation_workbench_reviews' => $reviewsSearch ?? '',
                'translation_workbench_shared_key_candidates' => $sharedKeyCandidatesSearch ?? '',
                'translation_workbench_key_inventory' => $keyInventorySearch ?? '',
                'translation_workbench_timeline_events' => $timelineEventsSearch ?? '',
                default => '',
            },
        );
    @endphp

    <flux:pagination
        class="my-4"
        :paginator="$rows"
    />

    {{-- Table --}}
    <flux:table container:class="max-h-[100vh] overflow-x-auto">
        {{-- Table Header --}}
        <flux:table.columns sticky>
            @foreach ($columns as $column)
                @php
                    $presentation = $columnPresentation[$column] ?? [];
                    $metadata = $columnMetadata[$column] ?? [];
                    $foreignKey = $foreignKeyMetadata[$column] ?? null;
                    $isForeignKey = (bool) ($foreignKey['is_foreign_key'] ?? false);
                    $isSortable = in_array($column, $sortableColumns ?? [], true);
                    $isSorted = $sortField === $column;
                    $foreignTarget =
                        $foreignKey['foreign_table'] ?? null
                            ? $foreignKey['foreign_table'] .
                                '.' .
                                implode(',', (array) ($foreignKey['foreign_columns'] ?? []))
                            : ($isForeignKey
                                ? __('name-based fallback')
                                : '—');
                    $tooltipRows = [
                        __(
                            'packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.table_card.column',
                        ) => $column,
                        __('ui.type') => $metadata['type'] ?? 'unknown',
                        __('Nullable') => $metadata['nullable'] ?? false ? __('ui.filters.yes') : __('no'),
                        __('Default') =>
                            ($metadata['default'] ?? null) !== null ? (string) $metadata['default'] : 'NULL',
                        __('Auto increment') => $metadata['auto_increment'] ?? false ? __('ui.filters.yes') : __('no'),
                        __('Foreign key') => $isForeignKey ? __('ui.filters.yes') : __('no'),
                        __('FK detected by') =>
                            $foreignKey['is_schema_foreign_key'] ?? false
                                ? __('Database schema constraint')
                                : ($isForeignKey
                                    ? __('Package metadata fallback')
                                    : '—'),
                        __('FK target') => $foreignTarget,
                        __('FK constraint') => $foreignKey['name'] ?? '—',
                        __('Sortable') => $isSortable ? __('ui.filters.yes') : __('no'),
                    ];
                @endphp

                <flux:table.column
                    :sticky="$loop->first"
                    @class([
                        'font-mono',
                        'ml-2 bg-white dark:bg-zinc-600' => $loop->first,
                        'text-red-700 dark:text-red-300' => $isForeignKey,
                        $presentation['header_class'] ?? '' => filled(
                            $presentation['header_class'] ?? ''),
                    ])
                    :sortable="$isSortable"
                    :sorted="$isSorted"
                    :direction="$sortDirection"
                    wire:click="sortBy('{{ $column }}')"
                >
                    <div class="flex items-center gap-1">
                        {{-- Tooltip --}}
                        <flux:tooltip
                            position="right"
                            align="center"
                        >
                            {{-- Tolltip Header --}}
                            <span>{{ $column }}</span>

                            @if ($isForeignKey)
                                <flux:badge
                                    class="tabular-nums"
                                    size="sm"
                                    color="red"
                                >
                                    FK
                                </flux:badge>
                            @endif

                            {{-- Tooltip Content --}}
                            <flux:tooltip.content class="w-96 max-w-[calc(100vw-2rem)] text-start">
                                <div class="mb-2 truncate font-mono text-sm font-semibold text-white">
                                    {{ $column }}
                                </div>

                                <div class="grid grid-cols-3 gap-x-3 gap-y-1 text-xs">
                                    @foreach ($tooltipRows as $label => $value)
                                        <div class="font-semibold text-zinc-400">
                                            {{ $label }}
                                        </div>
                                        <div class="col-span-2 break-all font-mono text-zinc-100">
                                            {{ $value }}
                                        </div>
                                    @endforeach
                                </div>
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </div>
                </flux:table.column>
            @endforeach
        </flux:table.columns>

        {{-- Table Rows --}}
        <flux:table.rows>
            @forelse ($rows as $row)
                {{-- Table Row --}}
                <flux:table.row
                    wire:key="translation-workbench-raw-{{ $table }}-{{ $row->id ?? $loop->index }}">
                    @foreach ($columns as $column)
                        @php
                            $presentation = $columnPresentation[$column] ?? [];
                            $value = $row->{$column} ?? null;
                            $displayValue = is_string($value)
                                ? $value
                                : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            $decodedJsonValue = is_string($value) ? json_decode($value, true) : null;
                            $isJsonValue =
                                is_string($value) &&
                                json_last_error() === JSON_ERROR_NONE &&
                                is_array($decodedJsonValue);
                            $prettyDisplayValue = $isJsonValue
                                ? json_encode(
                                    $decodedJsonValue,
                                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                                )
                                : $displayValue;
                            $jsonPreview = $isJsonValue ? str($displayValue)->limit(80)->toString() : null;
                            $isSourceFilePath =
                                (($table === 'translation_workbench_source_files' && $column === 'path') ||
                                    ($table === 'translation_workbench_dynamic_sources' &&
                                        $column === 'source_path')) &&
                                is_string($value) &&
                                trim($value) !== '';
                            $isFindingSourceFileId =
                                $table === 'translation_workbench_findings' &&
                                $column === 'source_file_id' &&
                                $value !== null &&
                                isset($rawDataSourceFileLookup[(int) $value]);
                            $isKeyFindingKeyId =
                                $table === 'translation_workbench_key_findings' &&
                                $column === 'key_id' &&
                                $value !== null &&
                                isset($rawDataKeyLookup[(int) $value]);
                            $isKeyFindingFindingId =
                                $table === 'translation_workbench_key_findings' &&
                                $column === 'finding_id' &&
                                $value !== null &&
                                isset($rawDataFindingLookup[(int) $value]);
                            $sourceEditorUrl = null;
                            $findingSourcePath = null;
                            $keyFindingKeyContext = null;
                            $keyFindingKeyPreview = null;
                            $keyFindingFindingContext = null;
                            $keyFindingFindingPreview = null;

                            if ($isSourceFilePath) {
                                $sourceAbsolutePath = str_replace('\\', '/', base_path($value));
                                $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
                                $sourceEditorLine =
                                    isset($row->source_line) && $row->source_line ? ':' . $row->source_line : ':1';
                                $sourceEditorUrl =
                                    'vscode://vscode-remote/wsl+' .
                                    rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                                    $sourceEditorPath .
                                    $sourceEditorLine;
                            }

                            if ($isFindingSourceFileId) {
                                $findingSourcePath = $rawDataSourceFileLookup[(int) $value];
                                $findingSourcePathSegments = collect(
                                    explode('/', str_replace('\\', '/', $findingSourcePath)),
                                )
                                    ->filter(static fn(string $segment): bool => $segment !== '')
                                    ->values();
                                $findingSourcePathPreview =
                                    $findingSourcePathSegments->count() > 3
                                        ? '.../' . $findingSourcePathSegments->slice(-3)->implode('/')
                                        : $findingSourcePathSegments->implode('/');
                                $sourceAbsolutePath = str_replace('\\', '/', base_path($findingSourcePath));
                                $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
                                $sourceEditorLine =
                                    isset($row->source_line) && $row->source_line ? ':' . $row->source_line : ':1';
                                $sourceEditorUrl =
                                    'vscode://vscode-remote/wsl+' .
                                    rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                                    $sourceEditorPath .
                                    $sourceEditorLine;
                            }

                            if ($isKeyFindingKeyId) {
                                $keyFindingKeyContext = $rawDataKeyLookup[(int) $value];
                                $keyFindingKeyText =
                                    $keyFindingKeyContext['translation_key'] ?: $keyFindingKeyContext['suggested_key'];
                                if ($keyFindingKeyText) {
                                    $keyFindingKeySegments = collect(
                                        explode('.', str_replace('/', '.', $keyFindingKeyText)),
                                    )
                                        ->filter(static fn(string $segment): bool => $segment !== '')
                                        ->values();
                                    $keyFindingKeyPreview =
                                        $keyFindingKeySegments->count() > 4
                                            ? '...' . $keyFindingKeySegments->slice(-4)->implode('.')
                                            : $keyFindingKeySegments->implode('.');
                                } else {
                                    $keyFindingKeyPreview = __('No key text');
                                }
                            }

                            if ($isKeyFindingFindingId) {
                                $keyFindingFindingContext = $rawDataFindingLookup[(int) $value];
                                $keyFindingFindingText =
                                    $keyFindingFindingContext['literal_text'] ?:
                                    ($keyFindingFindingContext['suggested_key'] ?:
                                    $keyFindingFindingContext['raw_expression']);
                                $keyFindingFindingPreview = $keyFindingFindingText
                                    ? str($keyFindingFindingText)->limit(80)->toString()
                                    : __('No finding text');
                            }
                        @endphp
                        {{-- Table Cell --}}
                        <flux:table.cell
                            :sticky="$loop->first"
                            @class([
                                'align-top',
                                'bg-zinc-700' => $loop->first,
                                $presentation['cell_class'] ?? '' => filled(
                                    $presentation['cell_class'] ?? ''),
                            ])
                        >
                            @if ($isJsonValue)
                                <div
                                    class="max-w-md"
                                    x-data="{ expanded: false }"
                                >
                                    <x-ui.tooltip.simple
                                        :title="__('Cell value')"
                                        :text="$prettyDisplayValue"
                                    >
                                        <button
                                            class="flex w-full items-center justify-between gap-2 rounded border border-zinc-200 bg-zinc-50 px-2 py-1 text-left font-mono text-xs text-zinc-700 hover:border-sky-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500"
                                            type="button"
                                            x-on:click="expanded = ! expanded"
                                        >
                                            <span class="truncate">
                                                <x-translation-workbench::text.highlight
                                                    :value="$jsonPreview"
                                                    :search="$rawDataSearch"
                                                />
                                            </span>
                                            <flux:icon.chevron-down
                                                class="size-3.5 shrink-0 text-zinc-400 transition-transform"
                                                x-bind:class="{ 'rotate-180': expanded }"
                                            />
                                        </button>
                                    </x-ui.tooltip.simple>

                                    <pre
                                        class="mt-1 max-h-56 overflow-auto rounded border border-zinc-200 bg-white p-2 text-xs dark:border-zinc-700 dark:bg-zinc-950"
                                        x-show="expanded"
                                        x-cloak
                                    ><code><x-translation-workbench::text.highlight
:value="$prettyDisplayValue" :search="$rawDataSearch"/></code></pre>
                                </div>
                            @elseif ($isSourceFilePath)
                                <div
                                    class="{{ $presentation['source_path_wrapper_class'] ?? 'flex max-w-lg items-start gap-2' }}">
                                    <flux:tooltip content="{{ __('Open in VSC') }}">
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
                                    </flux:tooltip>

                                    <x-ui.tooltip.simple
                                        class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                        :title="__('ui.source.source-path')"
                                        :text="$prettyDisplayValue"
                                    >
                                        <x-translation-workbench::text.highlight
                                            :value="$prettyDisplayValue"
                                            :search="$rawDataSearch"
                                        />
                                    </x-ui.tooltip.simple>
                                </div>
                            @elseif ($isFindingSourceFileId)
                                <div
                                    class="{{ $presentation['finding_source_wrapper_class'] ?? 'flex min-w-[28rem] max-w-2xl items-start gap-2' }}">
                                    <flux:tooltip content="{{ __('Open in VSC') }}">
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
                                    </flux:tooltip>

                                    <div class="min-w-0 space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <flux:badge
                                                class="tabular-nums"
                                                size="sm"
                                                variant="subtle"
                                            >
                                                #{{ $prettyDisplayValue }}
                                            </flux:badge>

                                            <flux:tooltip>
                                                <flux:icon.info class="size-3.5 text-zinc-400" />
                                                <flux:tooltip.content class="w-96 max-w-[calc(100vw-2rem)] text-start">
                                                    <div class="mb-2 text-xs text-zinc-200">
                                                        {{ __('Only the FK ID is stored in this column. The path is resolved from translation_workbench_source_files for readability.') }}
                                                    </div>
                                                    <div class="break-all font-mono text-xs text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$findingSourcePath"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                </flux:tooltip.content>
                                            </flux:tooltip>
                                        </div>

                                        <x-ui.tooltip.simple
                                            class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                            :title="__('Resolved source path')"
                                            :text="$findingSourcePath"
                                        >
                                            <x-translation-workbench::text.highlight
                                                :value="$findingSourcePathPreview"
                                                :search="$rawDataSearch"
                                            />
                                        </x-ui.tooltip.simple>
                                    </div>
                                </div>
                            @elseif ($isKeyFindingKeyId)
                                <div class="min-w-0 space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <flux:badge
                                            class="tabular-nums"
                                            size="sm"
                                            variant="subtle"
                                        >
                                            #{{ $prettyDisplayValue }}
                                        </flux:badge>

                                        <flux:tooltip>
                                            <flux:icon.info class="size-3.5 text-zinc-400" />
                                            <flux:tooltip.content class="w-96 max-w-[calc(100vw-2rem)] text-start">
                                                <div class="mb-2 text-xs text-zinc-200">
                                                    {{ __('Only the FK ID is stored in this column. Key details are resolved from translation_workbench_keys for readability.') }}
                                                </div>
                                                <div class="grid grid-cols-3 gap-x-3 gap-y-1 text-xs">
                                                    <div class="font-semibold text-zinc-400">
                                                        {{ __('ui.translation.translation-key') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingKeyContext['translation_key'] ?:
                                                                'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                    <div class="font-semibold text-zinc-400">
                                                        {{ __('ui.key.suggested-key') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingKeyContext['suggested_key'] ?: 'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Namespace') }}
                                                    </div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingKeyContext['namespace'] ?: 'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Group') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingKeyContext['group'] ?: 'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                </div>
                                            </flux:tooltip.content>
                                        </flux:tooltip>
                                    </div>

                                    <x-ui.tooltip.simple
                                        class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                        :title="__('Resolved key preview')"
                                        :text="$keyFindingKeyPreview"
                                    >
                                        <x-translation-workbench::text.highlight
                                            :value="$keyFindingKeyPreview"
                                            :search="$rawDataSearch"
                                        />
                                    </x-ui.tooltip.simple>
                                </div>
                            @elseif ($isKeyFindingFindingId)
                                <div class="min-w-0 space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <flux:badge
                                            class="tabular-nums"
                                            size="sm"
                                            variant="subtle"
                                        >
                                            #{{ $prettyDisplayValue }}
                                        </flux:badge>

                                        <flux:tooltip>
                                            <flux:icon.info class="size-3.5 text-zinc-400" />
                                            <flux:tooltip.content class="w-96 max-w-[calc(100vw-2rem)] text-start">
                                                <div class="mb-2 text-xs text-zinc-200">
                                                    {{ __('Only the FK ID is stored in this column. Finding details are resolved from translation_workbench_findings for readability.') }}
                                                </div>
                                                <div class="grid grid-cols-3 gap-x-3 gap-y-1 text-xs">
                                                    <div class="font-semibold text-zinc-400">{{ __('Literal text') }}
                                                    </div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingFindingContext['literal_text'] ?:
                                                                'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                    <div class="font-semibold text-zinc-400">
                                                        {{ __('ui.key.suggested-key') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingFindingContext['suggested_key'] ?:
                                                                'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Raw expression') }}
                                                    </div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingFindingContext['raw_expression'] ?:
                                                                'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Source line') }}
                                                    </div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">
                                                        <x-translation-workbench::text.highlight
                                                            :value="$keyFindingFindingContext['source_line'] ??
                                                                'NULL'"
                                                            :search="$rawDataSearch"
                                                        />
                                                    </div>
                                                </div>
                                            </flux:tooltip.content>
                                        </flux:tooltip>
                                    </div>

                                    <x-ui.tooltip.simple
                                        class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                        :title="__('Resolved finding preview')"
                                        :text="$keyFindingFindingPreview"
                                    >
                                        <x-translation-workbench::text.highlight
                                            :value="$keyFindingFindingPreview"
                                            :search="$rawDataSearch"
                                        />
                                    </x-ui.tooltip.simple>
                                </div>
                            @else
                                <x-ui.tooltip.simple
                                    :title="__('Cell value')"
                                    :text="$prettyDisplayValue"
                                    @class([
                                        $presentation['content_class'] ?? 'max-w-md truncate font-mono text-xs',
                                        'text-sky-600 dark:text-sky-400' => $value === null,
                                    ])
                                >
                                    <x-translation-workbench::text.highlight
                                        :value="$prettyDisplayValue ?? 'NULL'"
                                        :search="$rawDataSearch"
                                    />
                                </x-ui.tooltip.simple>
                            @endif
                        </flux:table.cell>
                    @endforeach
                </flux:table.row>
            @empty
                {{-- Empty Table Row --}}
                <flux:table.row>
                    <flux:table.cell colspan="{{ max(count($columns), 1) }}">
                        <div class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No rows found.') }}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        <flux:pagination :paginator="$rows" />
    </div>

</flux:card>
