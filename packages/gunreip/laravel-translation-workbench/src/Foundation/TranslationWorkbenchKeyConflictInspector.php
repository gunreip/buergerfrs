<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Facades\DB;

/** Read-only snapshot. Shared values belong to the key text, not an individual owner. */
class TranslationWorkbenchKeyConflictInspector
{
    /** Read-only scope of separating one owner; deliberately not a write authorization. */
    public function separation(int $keyId, int $findingId, string $target): ?array
    {
        $access = app(TranslationWorkbenchEditorAccess::class);
        $access->authorize();
        [$key, $finding] = $access->assignment($keyId, $findingId);
        $source = (string) $key->translation_key;
        $target = trim($target);
        if ($source === '' || $target === $source || count(TranslationWorkbenchKeyOwnership::ids($source)) < 2) {
            return null;
        }
        $snapshot = $this->inspect($source, $target);
        $group = $snapshot['groups'][0];
        $targetGroup = collect($snapshot['groups'])->firstWhere('key', $target);
        $links = collect($group['findings'])->where('key_id', $keyId)->where('link_status', 'active')->values();
        $issues = [];
        if ($key->status === 'obsolete' || $finding->status !== 'active' || $key->is_dynamic_key) {
            $issues[] = __('This preview requires an active scalar key and finding.');
        }
        if (! preg_match('/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*){2,}$/', $target)) {
            $issues[] = __('Enter a nested scalar translation key, for example ui.buttons.save.');
        }
        if (array_intersect($links->pluck('id')->all(), $group['ambiguous_finding_ids'])) {
            $issues[] = __('Resolve multiple assignments of the affected findings first.');
        }
        if ($links->contains(fn ($link) => $link['status'] !== 'active')) {
            $issues[] = __('An active link points to an inactive finding. Review these links first.');
        }
        if ($targetGroup && ($targetGroup['owners'] || $targetGroup['values'])) {
            $issues[] = __('The target already has key records or stored values. Their conflicts need a separate decision.');
        }
        if (app(TranslationWorkbenchLangNodeClassifier::class)->reviewCandidate($target, $keyId)['is_blocked']) {
            $issues[] = __('The target is a container path and cannot be used as a scalar translation key.');
        }
        return ['key_id' => $keyId, 'source' => $source, 'target' => $target,
            'remaining_owner_ids' => array_values(array_diff(array_column($group['owners'], 'id'), [$keyId])),
            'findings' => $links->all(), 'shared_values' => $group['values'], 'issues' => $issues];
    }

    /** Active owners of the saved key and their active finding assignments. */
    public function sources(int $keyId): array
    {
        $selected = DB::table('translation_workbench_keys')->find($keyId);
        if (! $selected) {
            return [];
        }
        $owners = TranslationWorkbenchKeyOwnership::activeKeys();
        if (trim((string) $selected->translation_key) === '') {
            $owners->where('ownership_keys.id', $keyId);
        } else {
            $owners->where('ownership_keys.translation_key', $selected->translation_key);
        }
        $ids = $owners->orderBy('ownership_keys.id')->pluck('ownership_keys.id');
        $rows = DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_findings as findings', 'findings.id', '=', 'links.finding_id')
            ->leftJoin('translation_workbench_source_files as files', 'files.id', '=', 'findings.source_file_id')
            ->whereIn('links.key_id', $ids)->where('links.status', 'active')
            ->orderBy('links.key_id')->orderBy('findings.id')
            ->get(['links.key_id', 'findings.id', 'findings.status', 'findings.source_line', 'files.path'])
            ->groupBy('key_id');

        return $ids->map(fn ($id) => [
            'key_id' => (int) $id,
            'findings' => ($rows->get($id) ?? collect())->map(fn ($row) => (array) $row)->all(),
        ])->all();
    }

    public function inspect(string $source, string $target): array
    {
        $keys = array_values(array_unique(array_filter([trim($source), trim($target)])));
        $groups = [];
        foreach ($keys as $text) {
            $owners = TranslationWorkbenchKeyOwnership::activeKeys()
                ->where('ownership_keys.translation_key', $text)->orderBy('ownership_keys.id')
                ->get(['ownership_keys.id', 'ownership_keys.status', 'ownership_keys.suggested_key',
                    'ownership_keys.review_status', 'ownership_keys.reviewed_at']);
            $findings = DB::table('translation_workbench_key_findings as links')
                ->join('translation_workbench_findings as findings', 'findings.id', '=', 'links.finding_id')
                ->leftJoin('translation_workbench_source_files as files', 'files.id', '=', 'findings.source_file_id')
                ->whereIn('links.key_id', $owners->pluck('id'))->orderBy('links.key_id')->orderBy('findings.id')
                ->get(['links.key_id', 'links.status as link_status', 'links.relation_type',
                    'findings.id', 'findings.status', 'findings.source_line', 'files.path']);
            $values = DB::table('translation_workbench_lang_values')->where('translation_key', $text)
                ->orderBy('locale')->orderBy('id')->get(['id', 'locale', 'value', 'status']);
            $activeFindings = $findings->where('link_status', 'active')->where('status', 'active');
            $groups[] = [
                'key' => $text,
                'ownership_conflict' => $owners->count() > 1,
                'ambiguous_finding_ids' => $activeFindings->groupBy('id')
                    ->filter(fn ($links) => $links->pluck('key_id')->unique()->count() > 1)
                    ->keys()->map(fn ($id) => (int) $id)->sort()->values()->all(),
                'owners' => $owners->map(fn ($row) => (array) $row)->all(),
                'findings' => $findings->map(fn ($row) => (array) $row)->all(),
                'values' => $values->map(fn ($row) => (array) $row)->all(),
            ];
        }

        return ['source' => trim($source), 'target' => trim($target), 'groups' => $groups];
    }
}
