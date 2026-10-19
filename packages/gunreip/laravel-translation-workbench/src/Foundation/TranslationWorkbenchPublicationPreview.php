<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Read-only comparison against the existing exporter; not an executable publication plan. */
class TranslationWorkbenchPublicationPreview
{
    public function __construct(
        private readonly TranslationWorkbenchValueApproval $approvals,
        private readonly TranslationWorkbenchLangFileExporter $exporter,
    ) {}

    public function preview(string $kind, int $keyId, int $findingId, array $locales): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        return DB::transaction(function () use ($kind, $keyId, $findingId, $locales): array {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values, translation_workbench_dynamic_key_values, translation_workbench_reviews IN SHARE MODE');
            }

            return $this->build($kind, $keyId, $findingId, $locales);
        });
    }

    public function verify(string $kind, int $keyId, int $findingId, array $locales, string $expectedFingerprint): array
    {
        $current = $this->preview($kind, $keyId, $findingId, $locales);
        if (! hash_equals($expectedFingerprint, $current['fingerprint'])) {
            throw ValidationException::withMessages(['valueApproval' => __('The publication preview is out of date. Review a new preview before continuing.')]);
        }

        return $current;
    }

    private function build(string $kind, int $keyId, int $findingId, array $locales): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        [$key] = app(TranslationWorkbenchEditorAccess::class)->assignment($keyId, $findingId);
        if ($key->status === 'obsolete' || $key->review_status !== 'reviewed' || blank($key->translation_key)) {
            throw ValidationException::withMessages(['valueApproval' => __('Publication preview requires a reviewed translation key.')]);
        }
        if (count(TranslationWorkbenchKeyOwnership::ids($key->translation_key)) > 1) {
            throw ValidationException::withMessages(['valueApproval' => __('Multiple active records own this translation key. Resolve the assignments in the Workbench before publishing.')]);
        }
        if ($kind === 'scalar' && TranslationWorkbenchLangNodeClassifier::isRootScalarKey($key->translation_key)) {
            throw ValidationException::withMessages(['valueApproval' => __('Root scalar translation key: choose a nested key in the Workbench before publishing. No key has been renamed automatically.')]);
        }
        $locales = array_values(array_unique(array_filter($locales, fn ($locale) => is_string($locale) && trim($locale) !== '')));
        if ($locales === []) {
            throw ValidationException::withMessages(['valueApproval' => __('Select at least one language for the publication preview.')]);
        }
        sort($locales);
        $values = $this->approvals->inspect($kind, $keyId, $findingId, $locales);
        $indexed = [];
        foreach ($values as $value) {
            $snapshot = $value['snapshot'];
            $translationKey = $kind === 'option'
                ? TranslationWorkbenchOption::key($snapshot['translation_key'], $snapshot['value_key'])
                : $snapshot['translation_key'];
            $indexed[$snapshot['locale']][$translationKey] = $value;
        }
        try {
            $report = $this->exporter->export(locales: $locales, write: false, keys: [$key->translation_key], dynamicOptions: $kind === 'option');
        } catch (\InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['valueApproval' => $exception->getMessage()]);
        }
        $blocked = 0;
        foreach ($report['plans'] as &$plan) {
            foreach ($plan['changes'] as &$change) {
                $value = $indexed[$plan['locale']][$change['translation_key']] ?? null;
                // Attach approval information only when the planned text matches the inspected text.
                // PostgreSQL locks protect the DB reads; files remain external and are rechecked separately.
                $matches = $value !== null && $value['snapshot']['value_type'] === 'string'
                    && $value['snapshot']['value'] === $change['new_value'];
                $change['approval_state'] = $matches ? $value['state'] : 'unverified';
                $change['value_revision'] = $matches ? $value['snapshot']['value_revision'] : null;
                if ($change['approval_state'] !== 'approved') {
                    $blocked++;
                }
            }
            unset($change);
        }
        unset($plan);

        $this->exporter->assertSourceFilesUnchanged($report['plans']);
        $report += [
            'changes_without_current_approval' => $blocked,
            'pruning_requires_separate_review' => $report['values_pruned'],
            'unchanged_values_retained' => $report['values_unchanged'],
            'values_excluded_by_legacy_review_filter' => $kind === 'option'
                ? count(array_filter($values, fn ($value) => $value['legacy_reviewed_at'] === null)) : 0,
            'advisory_only' => true,
        ];
        $report['data_fingerprint'] = hash('sha256', json_encode([
            'kind' => $kind, 'key' => $key->toArray(), 'finding_id' => $findingId,
            'locales' => $locales, 'values' => $values, 'active_scope' => $report['active_scope']['locales'],
        ], JSON_THROW_ON_ERROR));
        $report['fingerprint'] = hash('sha256', json_encode([
            'version' => 1, 'kind' => $kind, 'key_id' => $keyId, 'finding_id' => $findingId,
            'locales' => $locales, 'values' => $values, 'report' => $report,
        ], JSON_THROW_ON_ERROR));

        return $report;
    }
}
