<?php

namespace Gunreip\TranslationWorkbench\Livewire;

class TranslationWorkbenchRawDataNew extends TranslationWorkbenchRawData
{
    /**
     * @var array<int, string>
     */
    public array $tables = [
        'translation_workbench_source_files',
        'translation_workbench_event_types',
        'translation_workbench_findings',
        'translation_workbench_keys',
        'translation_workbench_key_findings',
        'translation_workbench_key_values',
        'translation_workbench_dynamic_key_values',
        'translation_workbench_lang_values',
        'translation_workbench_reviews',
        'translation_workbench_timeline_events',
    ];

    public string $activeTable = 'translation_workbench_source_files';

    public string $pageTitle = 'Translation Workbench Raw-Data New';

    public string $pageDescription = 'Raw database table output for the new Translation Workbench foundation tables.';
}
