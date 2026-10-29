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
            'draft' => [__('Translation saved'), ['draft', 'review', 'approved', 'published']],
            'review' => [__('Translation reviewed'), ['approved', 'published']],
            'publication' => [__('Translation published'), ['published']],
        ] as $id => [$label, $completedStates]) {
            $locales = [];
            foreach ($languages as $language) {
                $locales[] = $language + ['done' => in_array($language['state'], $completedStates, true)];
            }
            $steps[] = ['id' => $id, 'label' => $label, 'done' => $locales !== [] && !in_array(false, array_column($locales, 'done'), true), 'languages' => $locales];
        }
        $steps[] = ['id' => 'source', 'label' => __('Assigned key used in source'), 'done' => $sourceSet, 'languages' => [], 'hint' => $sourceIssue, 'scan_action' => $sourceIssue !== null && str_contains(strtolower($sourceIssue), 'scan'),
            'action' => __('Open Edit finding → Inspect source replacement. Resolve the reported prerequisite, then review and confirm the replacement.')];
        $steps[] = ['id' => 'scan', 'label' => __('Source confirmed by scan'), 'done' => $scanConfirmed, 'languages' => [],
            'hint' => $scanConfirmed ? null : __('A completed scan must confirm this source occurrence with its assigned translation key.'),
            'scan_action' => !$scanConfirmed,
            'action' => __('After the assigned key is present in the source, preview the scan and then save its results. Reopen this overview afterwards.')];
        $current = false;
        foreach ($steps as &$step) {
            $step['status'] = $step['done'] ? 'complete' : (!$current ? 'current' : 'incomplete');
            $step['color'] = $step['done'] ? 'green' : (!$current ? 'blue' : 'zinc');
            $step['icon'] = $step['done'] ? 'check' : (!$current ? 'arrow-right' : 'minus');
            if (!$step['done']) $current = true;
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
