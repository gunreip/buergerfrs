<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Presentation of current workflow facts, without maintaining a second status ledger. */
final class WorkflowProgress
{
    public function steps(array $finding, array $languages, bool $sourceSet, bool $scanConfirmed, ?string $sourceIssue = null): array
    {
        $steps = [
            ['id' => 'capture', 'label' => __('Finding captured'), 'done' => true, 'languages' => []],
            ['id' => 'assignment', 'label' => __('Translation key assigned'), 'done' => !empty($finding['key_id']), 'languages' => []],
        ];
        foreach ([
            'draft' => [__('Translation saved'), ['draft', 'review', 'approved', 'published', 'existing_publication']],
            'review' => [__('Translation reviewed'), ['approved', 'published']],
            'publication' => [__('Translation published'), ['published', 'existing_publication']],
        ] as $id => [$label, $completedStates]) {
            $locales = [];
            foreach ($languages as $language) {
                $locales[] = $language + ['review_unrecorded' => $id === 'review' && $language['state'] === 'existing_publication', 'done' => in_array($language['state'], $completedStates, true)];
            }
            $steps[] = ['id' => $id, 'label' => $label, 'done' => $locales !== [] && !in_array(false, array_column($locales, 'done'), true), 'languages' => $locales];
        }
        foreach ($steps as &$step) {
            if ($step['id'] !== 'review') continue;
            $unrecorded = array_filter($step['languages'], fn ($language) => $language['review_unrecorded']);
            if ($unrecorded !== []) {
                $step['hint'] = __('These translations already exist in the language files. No review was recorded for the imported values.');
                $step['resolved'] = !array_filter($step['languages'], fn ($language) => !$language['done'] && !$language['review_unrecorded']);
                if ($step['resolved']) $step['summary'] = __('Review not recorded');
            }
        }
        unset($step);
        $descriptions = [
            'capture' => __('translation-workbench.workflow.progress.the_scanner_recorded_this_source_occurrence_its_position_and_the_captured_source'),
            'assignment' => __('This finding is linked to a translation key. Multiple findings can use the same key and translations. Assignment alone does not change source code or language files.'),
            'draft' => __('Translation revisions are saved in the database for the assigned key and language. Saving a draft does not write lang/* files or change the source code.'),
            'review' => __('The saved revision is reviewed and approved for publication. Approval is recorded in the database; language files and source code are not written here.'),
            'publication' => __('Publication writes approved translations to the corresponding lang/* files. The status reflects completed publication records or verified imported file values. File paths and current file observations are available in each language tooltip. Source code replacement is a separate step.'),
            'source' => __('The source occurrence uses the assigned translation key instead of the captured literal. Preview and confirm the replacement separately; publishing language files alone does not change this occurrence.'),
            'scan' => __('A completed scan confirms the source occurrence and its assigned translation key against the source snapshot. A scan does not publish translations.'),
        ];
        $steps[] = ['id' => 'source', 'label' => __('Assigned key used in source'), 'done' => $sourceSet, 'languages' => [], 'hint' => $sourceIssue, 'scan_action' => $sourceIssue !== null && str_contains(strtolower($sourceIssue), 'scan'),
            'action' => __('Open Source replacement, select the key and inspect the source. Resolve any reported prerequisite, then preview and confirm the replacement.')];
        $steps[] = ['id' => 'scan', 'label' => __('Source confirmed by scan'), 'done' => $scanConfirmed, 'languages' => [],
            'hint' => $scanConfirmed ? null : __('A completed scan must confirm this source occurrence with its assigned translation key.'),
            'scan_action' => !$scanConfirmed,
            'action' => __('After the assigned key is present in the source, preview the scan and then save its results. Reopen this overview afterwards.')];
        $current = false;
        foreach ($steps as &$step) {
            $step['description'] = $descriptions[$step['id']];
            $step['status'] = $step['done'] ? 'complete' : (!$current ? 'current' : 'incomplete');
            $step['color'] = $step['done'] ? 'green' : (!$current ? 'blue' : 'zinc');
            $step['icon'] = $step['done'] ? 'check' : (!$current ? 'arrow-right' : 'minus');
            if (!$step['done'] && !($step['resolved'] ?? false)) $current = true;
            if ($step['resolved'] ?? false) {
                $step['status'] = 'incomplete';
                $step['color'] = 'amber';
                $step['icon'] = 'information-circle';
            }
            if ($step['id'] === 'source' && $sourceIssue) {
                $step['color'] = 'amber';
                $step['icon'] = 'exclamation-triangle';
            }
            if ($step['id'] === 'scan' && !$step['done']) {
                $step['icon'] = 'x-mark';
                $step['color'] = 'red';
            }
            if ($finding['status'] !== 'active' && !$step['done']) {
                $step['status'] = 'incomplete';
                $step['color'] = 'zinc';
                $step['icon'] = 'minus';
            }
        }
        unset($step);
        return $steps;
    }
}
