{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php --}}

<flux:separator class="mt-4" />
{{--
TODO:: DEV-Section delete --}}
{{-- <flux:field class="my-4">
    <flux:heading
        class="mb-2"
        size="lg"
    >
        {{ __('DEV: Workbench Findings Table') }}
    </flux:heading>
    <flux:text class="text-sm text-zinc-500">
        {{ __('This is a development view of the Workbench findings table. It is used for testing and debugging purposes.') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.literal.literal') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.literal.literal') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.literal.literal-testing') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.literal.literal-testing') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.literal.literal-testing-2') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.literal.literal-testing-2') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.label.label-testing-2') }}
    </flux:text>
    <flux:text class="text-sm text-zinc-500">
        {{ __('ui.label.label-testing-2') }}
    </flux:text>
</flux:field> --}}

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
    @php
        $bulkSelectedLiteral = $bulkEqualizeContext['normalized_literal'] ?? null;
    @endphp

    @include('translation-workbench::livewire.entries.findings.table-work-findings.header')

    {{-- Table Findings Body Rows --}}
    <flux:table.rows>
        {{-- Table Findings Vars --}}
        @forelse ($findings as $finding)
            @php
                $hasKey = $finding->key_id !== null;
                $hasSourceValue = (bool) $finding->has_source_value;
                $hasTargetValue = (bool) ($finding->has_target_value ?? false);
                $sourceTranslationValue = trim((string) ($finding->source_translation_value ?? ''));
                $targetTranslationValue = trim((string) ($finding->target_translation_value ?? ''));
                $hasSourceTranslationValue = filled($sourceTranslationValue);
                $hasTargetTranslationValue = filled($targetTranslationValue);
                $sourceTranslationOrigin = trim((string) ($finding->source_translation_origin ?? ''));
                $targetTranslationOrigin = trim((string) ($finding->target_translation_origin ?? ''));
                $sourceValueDiffers = (bool) ($finding->source_value_differs ?? false);
                $literal = $finding->literal_text ?: $finding->literal_text_suggested;
                $hasSourceLiteral = filled($literal);
                $bulkLiteral = trim((string) ($finding->bulk_literal ?? ''));
                $bulkLiteralCount = (int) ($finding->bulk_literal_count ?? 0);
                $bulkSelectableByLiteral =
                    $hasSourceLiteral &&
                    $bulkLiteralCount > 1 &&
                    ($bulkSelectedLiteral === null || $bulkSelectedLiteral === $bulkLiteral);
                $bulkSelected = in_array((int) $finding->id, $bulkEqualizeContext['selected_ids'] ?? [], true);
                $hasSourceTranslationContext = $hasSourceValue || $hasSourceLiteral;
                $sourceLocaleLabel = strtoupper((string) ($sourceMainLocale ?? 'en'));
                $targetLocaleLabel = strtoupper((string) ($targetMainLocale ?? app()->getLocale()));
                $functionName = trim((string) ($finding->function_name ?? ''));
                $translationKey = trim((string) ($finding->translation_key ?? ''));
                $wasBulkEqualized = (bool) ($finding->was_bulk_equalized ?? false);
                $keySuggestedKey = trim((string) ($finding->key_suggested_key ?? ''));
                $findingSuggestedKey = trim((string) ($finding->suggested_key ?? ''));
                $literalText = trim((string) ($finding->literal_text ?? ''));
                $literalTextSuggested = trim((string) ($finding->literal_text_suggested ?? ''));
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
                $isDynamicNumeric =
                    ($finding->kind ?? null) === 'dynamic_numeric' ||
                    ($finding->entry_type ?? null) === 'dynamic_numeric';
                $kindLabel = match ((string) ($finding->kind ?? '')) {
                    'dynamic_multi' => __('Dynamic values'),
                    'dynamic_numeric' => __('Numeric dynamic'),
                    default => str((string) ($finding->kind ?? ''))->headline()->toString(),
                };
                $kindTooltip = match ((string) ($finding->kind ?? '')) {
                    'dynamic_multi' => __(
                        'Technical scanner kind: dynamic_multi. This is a translation-relevant dynamic-values finding, even if the current runtime data contains only one value.',
                    ),
                    'dynamic_numeric' => __(
                        'Technical scanner kind: dynamic_numeric. This is a numeric or technical dynamic value and is not sent into translation editing.',
                    ),
                    default => __('Technical scanner kind: :kind.', ['kind' => (string) ($finding->kind ?? '-')]),
                };
                $kindColor = match ((string) ($finding->kind ?? '')) {
                    'dynamic_multi' => 'violet',
                    'dynamic_numeric' => 'zinc',
                    default => 'sky',
                };
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
                    !$isDynamicNumeric &&
                    ($isDynamicState ||
                        $dynamicMultiContext ||
                        filled($dynamicDataState) ||
                        $dynamicSourceCount > 0 ||
                        ($finding->candidate_type ?? null) === 'dynamic' ||
                        ($finding->entry_type ?? null) === 'dynamic' ||
                        str_starts_with((string) ($finding->kind ?? ''), 'dynamic'));
                $translationValuesComplete = $isDynamicFinding
                    ? $dynamicTranslationValuesComplete
                    : $hasSourceTranslationValue && $hasTargetTranslationValue;
                $translationWorkflowComplete =
                    $reviewStatus === 'reviewed' && $hasTranslationKey && $translationValuesComplete;
                $sourceLangFileImported = $sourceTranslationOrigin === 'translation-workbench:import-lang-values';
                $targetLangFileImported = $targetTranslationOrigin === 'translation-workbench:import-lang-values';
                $translationLangFileSynced =
                    $translationWorkflowComplete && $sourceLangFileImported && $targetLangFileImported;
                $translationWorkflowState = match (true) {
                    !$translationWorkflowComplete => 'inProgress',
                    !$isDynamicFinding && $translationLangFileSynced => 'progressWritten',
                    default => 'progressEdited',
                };
                $translationWorkflowEdited = in_array(
                    $translationWorkflowState,
                    ['progressEdited', 'progressWritten'],
                    true,
                );
                $translationWorkflowWritten = $translationWorkflowState === 'progressWritten';
                $dynamicSourceTypes = collect(explode(',', (string) ($finding->dynamic_source_types ?? '')))
                    ->map(static fn(string $sourceType): string => trim($sourceType))
                    ->filter(static fn(string $sourceType): bool => $sourceType !== '')
                    ->values();
                $canEditFinding = $hasKey && $hasTranslationKey && $reviewStatus === 'reviewed';
                $canOpenEditAction = $canEditFinding && !$isDynamicNumeric;
                $editActionColor = 'zinc';

                if ($canOpenEditAction) {
                    $editActionColor = $isDynamicFinding
                        ? ($dynamicTranslationValuesComplete
                            ? 'green'
                            : 'amber')
                        : ($hasSourceTranslationContext && $hasTargetValue
                            ? 'green'
                            : ($hasSourceTranslationContext
                                ? 'amber'
                                : 'red'));
                }
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
            <flux:table.row wire:key="translation-workbench-finding-row-{{ $finding->id }}">
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-id')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-source')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-status')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-kind')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-literal')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-translation-key')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-candidate')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-state')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-dynamic-context')
                @include('translation-workbench::livewire.entries.findings.table-work-findings.cell-actions')
            </flux:table.row>
        @empty
            {{-- Table Row No Findings Message --}}
            <flux:table.row wire:key="translation-workbench-finding-row-empty">
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
