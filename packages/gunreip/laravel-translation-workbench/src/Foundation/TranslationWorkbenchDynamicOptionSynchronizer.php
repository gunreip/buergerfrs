<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Persist observations, never translations or synthetic legacy entries. */
class TranslationWorkbenchDynamicOptionSynchronizer
{
    public function __construct(
        private readonly TranslationWorkbenchDynamicOptionAssignment $assignment,
        private readonly TranslationFingerprintFactory $fingerprints,
        private readonly TranslationWorkbenchTimelineRecorder $timeline,
    ) {}

    public function process(Collection $discoveries, bool $sync): array
    {
        $run = function () use ($discoveries, $sync): array {
            // Resolve here, not from a previously approved or externally supplied plan.
            $plan = $this->assignment->plan($discoveries);
            $summary = ['discoveries' => $discoveries->count(), 'observations_created' => 0,
                'observations_updated' => 0, 'observations_unchanged' => 0,
                'needs_review' => $discoveries->values()->filter(fn (array $discovery, int $index) =>
                    $plan['assignments'][$index]['status'] !== 'matched' || $discovery['options'] === []
                )->count()];
            $observations = [];
            $usedIds = [];
            $usedFingerprints = [];
            foreach ($discoveries->values() as $index => $discovery) {
                $fingerprint = $this->fingerprints->signature(array_map(fn ($field) => (string) $discovery[$field], [
                    'scope', 'suggested_key', 'suggested_dynamic_key', 'source_path', 'source_line',
                    'options_variable', 'key_variable', 'label_variable',
                ]));
                if (isset($usedFingerprints[$fingerprint])) {
                    throw new \LogicException('Duplicate option discovery in synchronization input: '.$discovery['source_path'].':'.$discovery['source_line']);
                }
                $usedFingerprints[$fingerprint] = true;
                $row = $this->existingObservation($discovery, $plan['assignments'][$index], $fingerprint, $sync);
                if ($row !== null && isset($usedIds[$row->id])) {
                    throw new \LogicException('Multiple option discoveries claim observation '.$row->id.'. Review the source identity before synchronization.');
                }
                if ($row !== null) {
                    $usedIds[$row->id] = true;
                }
                $observations[$index] = [$row, $fingerprint];
            }
            if (! $sync) {
                return ['summary' => $summary, 'plan' => $plan];
            }
            foreach ($discoveries->values() as $index => $discovery) {
                $assignment = $plan['assignments'][$index];
                [$row, $fingerprint] = $observations[$index];
                $meta = $row?->meta ?? [];
                if ($assignment['status'] === 'ambiguous_source' && $row !== null && ! isset($meta['options_before_source_ambiguity'])) {
                    $meta['options_before_source_ambiguity'] = [
                        'options' => $row->options, 'source_reference' => $row->source_reference,
                    ];
                }
                if ($discovery['options'] === [] && $row !== null && $row->options !== []) {
                    $meta['options_before_unresolved'] = [
                        'options' => $row->options, 'source_reference' => $row->source_reference,
                    ];
                }
                unset($meta['matched_entry_translation_key'], $meta['matched_entry_status']);
                $attributes = [
                    'fingerprint' => $fingerprint,
                    'matched_entry_id' => null,
                    'scope' => $discovery['scope'], 'suggested_key' => $discovery['suggested_key'],
                    'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                    'workbench_suggested_key' => null, 'suggested_state' => $assignment['status'],
                    'source_path' => $discovery['source_path'], 'source_line' => $discovery['source_line'],
                    'options_variable' => $discovery['options_variable'], 'key_variable' => $discovery['key_variable'],
                    'label_variable' => $discovery['label_variable'], 'label_usage' => $discovery['label_usage'],
                    'source_type' => $discovery['source_type'],
                    'source_reference' => $discovery['source_reference'] ?? $discovery['source_path'].':'.$discovery['source_line'],
                    'options_count' => count($discovery['options']), 'options' => $discovery['options'],
                    'status' => $assignment['status'] === 'matched' && $discovery['options'] !== [] ? 'open' : 'needs_review',
                    'meta' => [...$meta, 'source' => 'translation-workbench:discover-dynamic-options', 'foundation_assignment' => $assignment],
                ];
                $old = $row?->only(array_keys($attributes));
                $changed = $old === null || collect($attributes)->contains(fn ($value, $field) => ($old[$field] ?? null) !== $value);
                if ($row === null) {
                    $row = TranslationWorkbenchOptionDiscovery::create([
                        'fingerprint' => $fingerprint, ...$attributes, 'first_seen_at' => now(), 'last_seen_at' => now(),
                    ]);
                    $summary['observations_created']++;
                } else {
                    $row->forceFill([...$attributes, 'last_seen_at' => now()])->save();
                    $summary[$changed ? 'observations_updated' : 'observations_unchanged']++;
                }
                if ($changed) {
                    $this->timeline->record(
                        eventType: 'dynamic_option_observation_recorded',
                        key: $assignment['key_id'] ? TranslationWorkbenchKey::find($assignment['key_id']) : null,
                        finding: $assignment['finding_id'] ? TranslationWorkbenchFinding::find($assignment['finding_id']) : null,
                        oldValues: $old, newValues: $attributes,
                        context: ['source' => 'translation-workbench:discover-dynamic-options', 'option_discovery_id' => $row->id],
                    );
                }
            }
            $plan['mode'] = 'persisted_observations';

            return ['summary' => $summary, 'plan' => $plan];
        };

        return $sync ? DB::transaction($run) : $run();
    }

    /** A line move may reuse a proven identity, but must never guess a historical assignment. */
    private function existingObservation(array $discovery, array $assignment, string $fingerprint, bool $lock): ?TranslationWorkbenchOptionDiscovery
    {
        $query = TranslationWorkbenchOptionDiscovery::query();
        if ($lock) {
            $query->lockForUpdate();
        }
        $exact = (clone $query)->where('fingerprint', $fingerprint)->first();
        if ($exact !== null) {
            return $exact;
        }
        $query->where('status', '<>', 'obsolete');
        foreach (['source_path', 'options_variable', 'key_variable', 'label_variable'] as $field) {
            $query->where($field, $discovery[$field]);
        }
        $candidates = $query->get()->filter(function ($row) use ($assignment) {
            $previous = $row->meta['foundation_assignment'] ?? null;

            return $previous === null || (($assignment['status'] ?? null) === 'matched'
                && ($previous['status'] ?? null) === 'matched'
                && in_array($previous['finding_id'] ?? null, TranslationWorkbenchFindingVersioner::lineageIds((int) $assignment['finding_id']), true)
                && ($previous['key_id'] ?? null) === $assignment['key_id']);
        });
        if ($candidates->isEmpty()) {
            return null;
        }
        if ($candidates->count() !== 1 || ! isset($candidates->sole()->meta['foundation_assignment'])) {
            throw new \LogicException('Option observation identity requires review: '.$discovery['source_path']
                .' ($'.$discovery['options_variable'].'), existing IDs: '.$candidates->pluck('id')->implode(', ')
                .'. No duplicate observation was created.');
        }

        return $candidates->sole();
    }

    /** Historical observations keep their existing matching until explicitly rescanned. */
    public static function appliesTo(object $discovery, int $findingId, ?int $keyId): bool
    {
        $meta = is_string($discovery->meta) ? json_decode($discovery->meta, true) : $discovery->meta;
        if (! is_array($meta) || ! array_key_exists('foundation_assignment', $meta)) {
            return true;
        }
        $assignment = $meta['foundation_assignment'];

        return is_array($assignment) && ($assignment['status'] ?? null) === 'matched'
            && (int) ($assignment['finding_id'] ?? 0) === $findingId
            && $keyId !== null && (int) ($assignment['key_id'] ?? 0) === $keyId;
    }
}
