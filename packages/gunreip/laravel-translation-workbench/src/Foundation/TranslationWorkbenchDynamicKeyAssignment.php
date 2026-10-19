<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Automatic source processing must retain an unambiguous, active owner. */
class TranslationWorkbenchDynamicKeyAssignment
{
    /** Caller must already hold the source row lock inside its write transaction. */
    public function lockCurrentSourceOwner(object $source): void
    {
        if (DB::transactionLevel() === 0) {
            throw new \LogicException('Source owner validation requires a write transaction.');
        }
        $invalid = static function (): never {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'dynamic_source' => __('The finding, source file or key assignment changed during resolution. Refresh the source context before retrying.'),
            ]);
        };
        $finding = DB::table('translation_workbench_findings')->where('id', $source->finding_id)->lockForUpdate()->first();
        if (! $finding || $finding->status !== 'active') {
            $invalid();
        }
        $file = DB::table('translation_workbench_source_files')->where('id', $finding->source_file_id)->lockForUpdate()->first();
        if (! $file || $file->status !== 'active'
            || (isset($source->finding_source_file_id) && (int) $source->finding_source_file_id !== (int) $file->id)
            || (isset($source->finding_source_path) && $source->finding_source_path !== $file->path)) {
            $invalid();
        }
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)
            ->where('status', 'active')->orderBy('id')->lockForUpdate()->get();
        $keys = DB::table('translation_workbench_keys')->whereIn('id', $links->pluck('key_id'))
            ->orderBy('id')->lockForUpdate()->get()->where('status', '<>', 'obsolete');
        if ($keys->count() !== 1) {
            $invalid();
        }
        $keyId = (int) $keys->sole()->id;
        if (($source->key_id !== null && (int) $source->key_id !== $keyId)
            || (property_exists($source, 'assigned_key_id') && (int) $source->assigned_key_id !== $keyId)) {
            $invalid();
        }
    }

    /** @return Collection<int, array{finding_id: int, key_ids: array<int, int>}> */
    public function conflicts(array $findingIds): Collection
    {
        if ($findingIds === []) {
            return collect();
        }

        return DB::table('translation_workbench_key_findings as links')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'links.key_id')
            ->whereIn('links.finding_id', array_unique($findingIds))
            ->where('links.status', 'active')->where('keys.status', '<>', 'obsolete')
            ->orderBy('links.finding_id')->orderBy('links.key_id')
            ->get(['links.finding_id', 'links.key_id'])
            ->groupBy('finding_id')
            ->map(fn (Collection $links, $findingId): array => [
                'finding_id' => (int) $findingId,
                'key_ids' => $links->pluck('key_id')->map(fn ($id): int => (int) $id)->unique()->values()->all(),
            ])
            ->filter(fn (array $row): bool => count($row['key_ids']) > 1)->values();
    }
}
