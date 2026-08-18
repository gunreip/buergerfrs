<?php

namespace Gunreip\TranslationWorkbench\Support;

class RawDataColumnPresentation
{
    /**
     * Central presentation map for the generic raw-data table renderer.
     * Keep table/column-specific width and wrapping rules here so raw table
     * views stay generic while each table can still be tuned individually.
     *
     * @param  array<int, string>  $columns
     * @return array<string, array<string, string>>
     */
    public static function forTable(string $table, array $columns): array
    {
        $default = collect($columns)
            ->mapWithKeys(function (string $column): array {
                $isId = $column === 'id' || str_ends_with($column, '_id');
                $isBoolean = str_starts_with($column, 'is_') || str_starts_with($column, 'has_');
                $isTimestamp = str_ends_with($column, '_at');
                $isStatus = in_array($column, ['status', 'review_status', 'severity', 'category', 'source_type', 'target_type'], true);

                return [
                    $column => [
                        'header_class' => $isId || $isBoolean || $isTimestamp || $isStatus ? 'whitespace-nowrap' : '',
                        'cell_class' => $isId || $isBoolean || $isTimestamp || $isStatus ? 'whitespace-nowrap' : '',
                        'content_class' => match (true) {
                            $isId || $isBoolean => 'max-w-28 truncate font-mono text-xs tabular-nums',
                            $isTimestamp => 'max-w-44 truncate font-mono text-xs tabular-nums',
                            $isStatus => 'max-w-40 truncate font-mono text-xs',
                            default => 'max-w-md truncate font-mono text-xs',
                        },
                    ],
                ];
            })
            ->all();

        foreach (self::tablePresentation($table) as $column => $presentation) {
            if (! in_array($column, $columns, true)) {
                continue;
            }

            $default[$column] = array_merge($default[$column] ?? [], $presentation);
        }

        return $default;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function tablePresentation(string $table): array
    {
        return match ($table) {
            'translation_workbench_source_files' => self::sourceFiles(),
            'translation_workbench_event_types' => self::eventTypes(),
            'translation_workbench_findings' => self::findings(),
            'translation_workbench_keys' => self::keys(),
            'translation_workbench_key_findings' => self::keyFindings(),
            'translation_workbench_dynamic_sources' => self::dynamicSources(),
            'translation_workbench_lang_values' => self::langValues(),
            'translation_workbench_shared_key_candidates' => self::sharedKeyCandidates(),
            'translation_workbench_key_inventory' => self::keyInventory(),
            'translation_workbench_timeline_chains' => self::timelineChains(),
            'translation_workbench_pipeline_runs' => self::pipelineRuns(),
            'translation_workbench_pipeline_run_steps' => self::pipelineRunSteps(),
            default => [],
        };
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function sourceFiles(): array
    {
        return [
            'path' => [
                'header_class' => 'min-w-[28rem]',
                'cell_class' => 'min-w-[28rem]',
                'content_class' => 'max-w-3xl wrap-anywhere text-wrap font-mono text-xs',
                'source_path_wrapper_class' => 'flex max-w-3xl items-start gap-2',
            ],
            'source_root' => ['content_class' => 'min-w-36 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'source_area' => ['content_class' => 'min-w-44 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'package_vendor' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'package_name' => ['content_class' => 'min-w-56 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'path_domain' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'path_section' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'path_context' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'path_scope' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'path_extra' => ['content_class' => 'min-w-48 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'filename' => ['content_class' => 'min-w-48 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function eventTypes(): array
    {
        return [
            'key' => ['content_class' => 'min-w-64 max-w-lg font-mono text-xs'],
            'label' => ['content_class' => 'min-w-56 max-w-lg wrap-anywhere text-wrap font-mono text-xs'],
            'description' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function findings(): array
    {
        return [
            'source_file_id' => [
                'header_class' => 'min-w-112',
                'cell_class' => 'min-w-112',
                'content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs',
                'finding_source_wrapper_class' => 'flex min-w-112 items-start gap-2',
            ],
            'fingerprint' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'source_signature' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'source_fingerprint' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'expression_fingerprint' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'semantic_fingerprint' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'kind' => ['content_class' => 'min-w-36 wrap-anywhere text-wrap font-mono text-xs'],
            'entry_type' => ['content_class' => 'min-w-40 wrap-anywhere text-wrap font-mono text-xs'],
            'raw_expression' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'literal_text' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'literal_text_suggested' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'found_translation_key' => ['content_class' => 'min-w-80 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'existing_key' => ['content_class' => 'min-w-80 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'suggested_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'path_key' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'candidate_reason' => ['content_class' => 'min-w-64 max-w-lg wrap-anywhere text-wrap font-mono text-xs'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function keys(): array
    {
        return [
            'fingerprint' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'suggested_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'namespace' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'group' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'path_key' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'scope' => ['content_class' => 'min-w-48 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'key_segment_domain' => ['content_class' => 'min-w-44 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'key_segment_section' => ['content_class' => 'min-w-44 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'key_segment_context' => ['content_class' => 'min-w-44 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'key_segment_extra' => ['content_class' => 'min-w-44 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'key_segment_name' => ['content_class' => 'min-w-44 max-w-md wrap-anywhere text-wrap font-mono text-xs'],
            'key_type' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'lang_node_type' => ['content_class' => 'min-w-36 max-w-44 truncate font-mono text-xs'],
            'status' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'review_status' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function keyFindings(): array
    {
        return [
            'key_id' => [
                'header_class' => 'min-w-80',
                'cell_class' => 'min-w-80',
                'content_class' => 'max-w-80 wrap-anywhere text-wrap font-mono text-xs',
            ],
            'finding_id' => [
                'header_class' => 'min-w-96',
                'cell_class' => 'min-w-96',
                'content_class' => 'max-w-96 wrap-anywhere text-wrap font-mono text-xs',
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function dynamicSources(): array
    {
        return [
            'source_path' => [
                'header_class' => 'min-w-[28rem]',
                'cell_class' => 'min-w-[28rem]',
                'content_class' => 'max-w-3xl wrap-anywhere text-wrap font-mono text-xs',
                'source_path_wrapper_class' => 'flex max-w-3xl items-start gap-2',
            ],
            'source_expression' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'source_reference' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'fingerprint' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function langValues(): array
    {
        return [
            'lang_key' => ['content_class' => 'min-w-80 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'value' => ['content_class' => 'min-w-96 max-w-2xl text-wrap wrap-normal hyphens-auto font-sans text-xs'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function sharedKeyCandidates(): array
    {
        return [
            'normalized_literal' => ['content_class' => 'min-w-56 max-w-lg wrap-anywhere text-wrap font-mono text-xs'],
            'literal_text' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'current_translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'suggested_shared_translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'matched_finding_ids' => ['cell_class' => 'min-w-96'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function keyInventory(): array
    {
        return [
            'translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'normalized_translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'namespace' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'group' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'key_type' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'inventory_status' => ['content_class' => 'min-w-36 max-w-44 truncate font-mono text-xs'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function timelineChains(): array
    {
        return [
            'chain_key' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'normalized_translation_key' => ['content_class' => 'min-w-112 wrap-anywhere text-wrap font-mono text-xs'],
            'namespace' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'group' => ['content_class' => 'min-w-40 max-w-sm wrap-anywhere text-wrap font-mono text-xs'],
            'chain_type' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'chain_status' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'key_ids' => ['cell_class' => 'min-w-80'],
            'finding_ids' => ['cell_class' => 'min-w-80'],
            'review_ids' => ['cell_class' => 'min-w-80'],
            'timeline_event_ids' => ['cell_class' => 'min-w-80'],
            'lang_value_ids' => ['cell_class' => 'min-w-80'],
            'related_translation_keys' => ['cell_class' => 'min-w-96'],
            'relation_summary' => ['cell_class' => 'min-w-96'],
            'lang_value_summary' => ['cell_class' => 'min-w-96'],
            'timeline_event_summary' => ['cell_class' => 'min-w-96'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function pipelineRuns(): array
    {
        return [
            'command' => ['content_class' => 'min-w-64 max-w-lg wrap-anywhere text-wrap font-mono text-xs'],
            'options' => ['cell_class' => 'min-w-96'],
            'status' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'current_step_label' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'current_step_command' => ['content_class' => 'min-w-72 max-w-xl wrap-anywhere text-wrap font-mono text-xs'],
            'error_message' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'summary' => ['cell_class' => 'min-w-96'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function pipelineRunSteps(): array
    {
        return [
            'pipeline_run_id' => [
                'header_class' => 'min-w-36',
                'cell_class' => 'min-w-36',
                'content_class' => 'max-w-36 truncate font-mono text-xs tabular-nums',
            ],
            'label' => [
                'header_class' => 'min-w-96',
                'cell_class' => 'min-w-96',
                'content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs',
            ],
            'command' => [
                'header_class' => 'min-w-96',
                'cell_class' => 'min-w-96',
                'content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs',
            ],
            'arguments' => ['cell_class' => 'min-w-96'],
            'status' => ['content_class' => 'min-w-32 max-w-40 truncate font-mono text-xs'],
            'error_message' => ['content_class' => 'min-w-96 max-w-2xl wrap-anywhere text-wrap font-mono text-xs'],
            'summary' => ['cell_class' => 'min-w-96'],
            'meta' => ['cell_class' => 'min-w-96'],
        ];
    }
}
