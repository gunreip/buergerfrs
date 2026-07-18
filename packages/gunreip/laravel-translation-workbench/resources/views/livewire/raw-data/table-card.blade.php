{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php --}}

{{-- Card --}}
<flux:card>
    {{-- Card Header --}}
    <x-ui.headers.card
        class="tabular-nums"
        :title="$table"
        :description="count($columns) . ' ' . __('columns')"
    >
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

    @if ($table === 'translation_workbench_lang_values')
        @include('translation-workbench::livewire.raw-data.filters-lang-values')
    @endif

    @if ($table === 'translation_workbench_timeline_events')
        @include('translation-workbench::livewire.raw-data.filters-timeline-events')
        @include('translation-workbench::livewire.raw-data.results-timeline-events')
    @endif

    <div class="mt-4">
        <flux:pagination :paginator="$rows" />
    </div>

    {{-- Table --}}
    <flux:table
        class="mt-4"
        container:class="overflow-x-auto"
    >
        {{-- Table Header --}}
        <flux:table.columns class="">
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
                        __('Column') => $column,
                        __('Type') => $metadata['type'] ?? 'unknown',
                        __('Nullable') => $metadata['nullable'] ?? false ? __('yes') : __('no'),
                        __('Default') =>
                            ($metadata['default'] ?? null) !== null ? (string) $metadata['default'] : 'NULL',
                        __('Auto increment') => $metadata['auto_increment'] ?? false ? __('yes') : __('no'),
                        __('Foreign key') => $isForeignKey ? __('yes') : __('no'),
                        __('FK detected by') =>
                            $foreignKey['is_schema_foreign_key'] ?? false
                                ? __('Database schema constraint')
                                : ($isForeignKey
                                    ? __('Package metadata fallback')
                                    : '—'),
                        __('FK target') => $foreignTarget,
                        __('FK constraint') => $foreignKey['name'] ?? '—',
                        __('Sortable') => $isSortable ? __('yes') : __('no'),
                    ];
                @endphp

                <flux:table.column
                    :sticky="$loop->first"
                    @class([
                        'font-mono',
                        'ml-2 bg-white dark:bg-zinc-600' => $loop->first,
                        'text-red-700 dark:text-red-300' => $isForeignKey,
                        $presentation['header_class'] ?? '' => filled($presentation['header_class'] ?? ''),
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
                <flux:table.row wire:key="translation-workbench-raw-{{ $table }}-{{ $row->id ?? $loop->index }}">
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
                                (
                                    ($table === 'translation_workbench_source_files' && $column === 'path') ||
                                    ($table === 'translation_workbench_dynamic_sources' && $column === 'source_path')
                                ) &&
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
                                $findingSourcePathSegments = collect(explode('/', str_replace('\\', '/', $findingSourcePath)))
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
                                    $keyFindingKeySegments = collect(explode('.', str_replace('/', '.', $keyFindingKeyText)))
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
                                    $keyFindingFindingContext['literal_text']
                                        ?: ($keyFindingFindingContext['suggested_key'] ?: $keyFindingFindingContext['raw_expression']);
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
                                $presentation['cell_class'] ?? '' => filled($presentation['cell_class'] ?? ''),
                            ])
                        >
                            @if ($isJsonValue)
                                <div
                                    class="max-w-md"
                                    x-data="{ expanded: false }"
                                >
                                    <button
                                        class="flex w-full items-center justify-between gap-2 rounded border border-zinc-200 bg-zinc-50 px-2 py-1 text-left font-mono text-xs text-zinc-700 hover:border-sky-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500"
                                        type="button"
                                        title="{{ $prettyDisplayValue }}"
                                        x-on:click="expanded = ! expanded"
                                    >
                                        <span class="truncate">{{ $jsonPreview }}</span>
                                        <flux:icon.chevron-down
                                            class="size-3.5 shrink-0 text-zinc-400 transition-transform"
                                            x-bind:class="{ 'rotate-180': expanded }"
                                        />
                                    </button>

                                    <pre
                                        class="mt-1 max-h-56 overflow-auto rounded border border-zinc-200 bg-white p-2 text-xs dark:border-zinc-700 dark:bg-zinc-950"
                                        x-show="expanded"
                                        x-cloak
                                    ><code>{{ $prettyDisplayValue }}</code></pre>
                                </div>
                            @elseif ($isSourceFilePath)
                                <div class="{{ $presentation['source_path_wrapper_class'] ?? 'flex max-w-lg items-start gap-2' }}">
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

                                    <span
                                        class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                        title="{{ $prettyDisplayValue }}"
                                    >
                                        {{ $prettyDisplayValue }}
                                    </span>
                                </div>
                            @elseif ($isFindingSourceFileId)
                                <div class="{{ $presentation['finding_source_wrapper_class'] ?? 'flex min-w-[28rem] max-w-2xl items-start gap-2' }}">
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
                                                        {{ $findingSourcePath }}
                                                    </div>
                                                </flux:tooltip.content>
                                            </flux:tooltip>
                                        </div>

                                        <div
                                            class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                            title="{{ $findingSourcePath }}"
                                        >
                                            {{ $findingSourcePathPreview }}
                                        </div>
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
                                                    <div class="font-semibold text-zinc-400">{{ __('Translation key') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingKeyContext['translation_key'] ?: 'NULL' }}</div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Suggested key') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingKeyContext['suggested_key'] ?: 'NULL' }}</div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Namespace') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingKeyContext['namespace'] ?: 'NULL' }}</div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Group') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingKeyContext['group'] ?: 'NULL' }}</div>
                                                </div>
                                            </flux:tooltip.content>
                                        </flux:tooltip>
                                    </div>

                                    <div
                                        class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                        title="{{ $keyFindingKeyPreview }}"
                                    >
                                        {{ $keyFindingKeyPreview }}
                                    </div>
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
                                                    <div class="font-semibold text-zinc-400">{{ __('Literal text') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingFindingContext['literal_text'] ?: 'NULL' }}</div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Suggested key') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingFindingContext['suggested_key'] ?: 'NULL' }}</div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Raw expression') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingFindingContext['raw_expression'] ?: 'NULL' }}</div>
                                                    <div class="font-semibold text-zinc-400">{{ __('Source line') }}</div>
                                                    <div class="col-span-2 break-all font-mono text-zinc-100">{{ $keyFindingFindingContext['source_line'] ?? 'NULL' }}</div>
                                                </div>
                                            </flux:tooltip.content>
                                        </flux:tooltip>
                                    </div>

                                    <div
                                        class="{{ $presentation['content_class'] ?? 'wrap-anywhere text-wrap font-mono text-xs' }} text-zinc-700 dark:text-zinc-300"
                                        title="{{ $keyFindingFindingPreview }}"
                                    >
                                        {{ $keyFindingFindingPreview }}
                                    </div>
                                </div>
                            @else
                                <div
                                    title="{{ $prettyDisplayValue }}"
                                    @class([
                                        $presentation['content_class'] ?? 'max-w-md truncate font-mono text-xs',
                                        'text-sky-600 dark:text-sky-400' => $value === null,
                                    ])
                                >
                                    {{ $prettyDisplayValue ?? 'NULL' }}
                                </div>
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
