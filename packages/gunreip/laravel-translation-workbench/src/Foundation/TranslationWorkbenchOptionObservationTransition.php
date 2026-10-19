<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;
use LogicException;

/** Explicit preparation of reviewed observations, not a heuristic migration or pipeline step. */
class TranslationWorkbenchOptionObservationTransition
{
    public function __construct(
        private readonly TranslationWorkbenchScanner $scanner,
        private readonly TranslationWorkbenchDynamicOptionSynchronizer $synchronizer,
        private readonly TranslationWorkbenchTimelineRecorder $timeline,
    ) {}

    /**
     * Requests carry observation_id (null for a new indexed call), finding/key IDs, file_hash and reviewed discovery.
     * Populating a previously empty unresolved observation additionally requires resolve_empty_options:
     * observation_fingerprint, declaration_path and declaration_hash. Current static source is rechecked.
     * assign_unresolved=true assigns only the identity of an existing empty unresolved observation;
     * it does not copy runtime values or claim that a static payload has been resolved.
     */
    public function process(array $requests, bool $apply = false): array
    {
        return DB::transaction(function () use ($requests, $apply): array {
            $results = [];
            $seen = [];
            foreach ($requests as $request) {
                $require = static function (bool $condition, string $reason) use ($request): void {
                    if (! $condition) {
                        throw new LogicException('Option observation for finding '.$request['target_finding_id'].': '.$reason);
                    }
                };
                $require(! isset($seen[$request['target_finding_id']]), 'duplicate preparation request');
                $seen[$request['target_finding_id']] = true;
                $target = TranslationWorkbenchFinding::with(['sourceFile', 'keys' => fn ($q) => $q
                    ->where('translation_workbench_key_findings.status', 'active')->where('translation_workbench_keys.status', '<>', 'obsolete')])
                    ->when($apply, fn ($q) => $q->lockForUpdate())->find($request['target_finding_id']);
                $require($target !== null && $target->status === 'active' && $target->sourceFile?->status === 'active', 'inactive or missing target');
                $require($target->keys->count() === 1 && $target->keys->sole()->id === $request['key_id'], 'key identity differs');
                $path = $target->sourceFile->path;
                $require(is_file(base_path($path)) && hash_file('sha256', base_path($path)) === $request['file_hash'], 'source file changed');
                $calls = $this->scanner->scan([$path])->filter(fn ($item) => $item->rawExpression === $target->raw_expression
                    && ($item->meta['source_context'] ?? null) !== 'commented_out' && ($item->meta['translation_relevant'] ?? true) !== false);
                $require($calls->count() === 1, 'target call is not unique');
                $call = $calls->sole();
                $require(($call->meta['option_owner_key'] ?? null) === $target->keys->sole()->translation_key, 'explicit owner differs');
                $context = $call->meta['dynamic_option_context'] ?? [];
                $discovery = $request['discovery'];
                $assignUnresolved = ($request['assign_unresolved'] ?? false) === true;
                if ($assignUnresolved) {
                    $require($request['observation_id'] !== null && ! isset($request['resolve_empty_options'])
                        && $discovery['options'] === [] && $discovery['source_type'] === 'unresolved'
                        && ($discovery['source_resolution'] ?? null) === 'unresolved', 'identity-only assignment requires an empty unresolved observation');
                    $current = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)
                        ->collectStatic([$path])->filter(fn ($row) => $row['source_line'] === $discovery['source_line']
                            && $row['options_variable'] === $discovery['options_variable']
                            && $row['key_variable'] === $discovery['key_variable']
                            && $row['label_variable'] === $discovery['label_variable']);
                    $require($current->count() === 1 && $current->sole()['options'] === []
                        && $current->sole()['source_resolution'] === 'unresolved'
                        && $current->sole()['source_reference'] === $discovery['source_reference'], 'current source is not the reviewed unresolved observation');
                } else {
                    $require(($discovery['source_resolution'] ?? null) === 'view_bound_candidate' && $discovery['options'] !== [], 'reviewed resolved payload required');
                }
                $require($discovery['source_path'] === $path && $call->sourceLine >= $discovery['source_line']
                    && $call->sourceLine <= ($discovery['source_end_line'] ?? $discovery['source_line']), 'discovery does not cover target');
                foreach (['options_variable', 'key_variable', 'label_variable'] as $field) {
                    $require(isset($context[$field]) && $context[$field] === $discovery[$field], 'discovery context differs: '.$field);
                }
                $require(($context['source_expression_type'] ?? null) === ($discovery['source_expression_type'] ?? 'direct_variable'), 'expression type differs');
                $candidates = TranslationWorkbenchOptionDiscovery::where('status', '<>', 'obsolete')->where('source_path', $path);
                foreach (['options_variable', 'key_variable', 'label_variable'] as $field) {
                    $candidates->where($field, $discovery[$field]);
                }
                $candidates = $candidates->when($apply, fn ($q) => $q->lockForUpdate())->get();
                $observation = null;
                $emptyResolution = null;
                if ($request['observation_id'] !== null) {
                    $require($candidates->count() === 1 && $candidates->sole()->id === $request['observation_id'], 'existing observation identity differs');
                    $observation = $candidates->sole();
                    if ($assignUnresolved) {
                        $require($observation->options === [] && (int) $observation->options_count === 0
                            && $observation->source_type === 'unresolved'
                            && ! isset($observation->meta['options_before_unresolved'])
                            && ! isset($observation->meta['options_before_source_ambiguity']), 'existing observation has resolved or archived values');
                    }
                    $normalize = static function (array $values): array { ksort($values); return $values; };
                    $proof = $request['resolve_empty_options'] ?? null;
                    if ($normalize($observation->options ?? []) !== $normalize($discovery['options'])) {
                        $require(is_array($proof), 'stored options differ');
                        $require($observation->options === [] && (int) $observation->options_count === 0
                            && $observation->source_type === 'unresolved', 'only empty unresolved observations can be populated');
                        $require(! isset($observation->meta['options_before_unresolved'])
                            && ! isset($observation->meta['options_before_source_ambiguity']), 'historical resolved options require separate review');
                        $require(($proof['observation_fingerprint'] ?? null) === $observation->fingerprint, 'reviewed observation changed');
                        $emptyResolution = $proof;
                    }
                    if ($proof !== null) {
                        $require(is_array($proof), 'invalid empty-options review');
                        $declaration = $proof['declaration_path'] ?? '';
                        $require($declaration !== '' && $declaration === ($discovery['source_reference'] ?? null)
                            && is_file(base_path($declaration))
                            && hash_file('sha256', base_path($declaration)) === ($proof['declaration_hash'] ?? null), 'reviewed declaration changed');
                        $current = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)
                            ->collectStatic([$path])->filter(fn ($row) => $row['source_line'] === $discovery['source_line']
                                && $row['options_variable'] === $discovery['options_variable']
                                && $row['key_variable'] === $discovery['key_variable']
                                && $row['label_variable'] === $discovery['label_variable']);
                        $require($current->count() === 1 && $current->sole()['source_resolution'] === 'view_bound_candidate'
                            && $current->sole()['source_reference'] === $declaration
                            && $normalize($current->sole()['options']) === $normalize($discovery['options']), 'reviewed payload does not match static source');
                    }
                    $assignment = $observation->meta['foundation_assignment'] ?? null;
                    $require($assignment === null || (($assignment['status'] ?? null) === 'matched'
                        && ($assignment['finding_id'] ?? null) === $target->id && ($assignment['key_id'] ?? null) === $request['key_id']), 'existing assignment differs');
                } else {
                    // A repeat can find only the observation already proven to belong to this target.
                    $require($candidates->count() <= 1, 'multiple target observations');
                    if ($candidates->count() === 1) {
                        $observation = $candidates->sole();
                        $assignment = $observation->meta['foundation_assignment'] ?? [];
                        $require(($assignment['status'] ?? null) === 'matched' && ($assignment['finding_id'] ?? null) === $target->id
                            && ($assignment['key_id'] ?? null) === $request['key_id'], 'unreviewed existing observation');
                    }
                }
                if ($apply) {
                    if ($emptyResolution !== null) {
                        $previous = $observation->only(['options', 'options_count', 'source_type', 'source_reference']);
                        $observation->forceFill(['meta' => [...($observation->meta ?? []),
                            'reviewed_empty_options_resolution' => ['previous' => $previous, 'proof' => $emptyResolution]]])->save();
                        $this->timeline->record(eventType: 'dynamic_option_empty_payload_resolved', key: $target->keys->sole(), finding: $target,
                            oldValues: $previous, newValues: ['options' => $discovery['options'], 'source_reference' => $discovery['source_reference']],
                            context: ['observation_id' => $observation->id, 'reviewed_file_hash' => $request['file_hash'], ...$emptyResolution]);
                    }
                    $oldMeta = $target->meta ?? [];
                    $newMeta = [...$oldMeta, 'dynamic_option_context' => $context];
                    if ($newMeta !== $oldMeta) {
                        $target->forceFill(['meta' => $newMeta])->save();
                        $this->timeline->record(eventType: 'dynamic_option_context_refreshed', key: $target->keys->sole(), finding: $target,
                            oldValues: ['dynamic_option_context' => $oldMeta['dynamic_option_context'] ?? null],
                            newValues: ['dynamic_option_context' => $context], context: ['reviewed_file_hash' => $request['file_hash']]);
                    }
                    if ($observation !== null && ! isset($observation->meta['foundation_assignment'])) {
                        $oldMeta = $observation->meta ?? [];
                        $assignment = ['status' => 'matched', 'finding_id' => $target->id, 'key_id' => $request['key_id'],
                            'source' => 'explicit_reviewed_preparation'];
                        $observation->forceFill(['meta' => [...$oldMeta, 'foundation_assignment' => $assignment]])->save();
                        $this->timeline->record(eventType: 'dynamic_option_observation_prepared', key: $target->keys->sole(), finding: $target,
                            oldValues: ['foundation_assignment' => null], newValues: ['foundation_assignment' => $assignment],
                            context: ['observation_id' => $observation->id, 'reviewed_file_hash' => $request['file_hash']]);
                    }
                    $result = $this->synchronizer->process(collect([$discovery]), true);
                    $require($result['plan']['summary']['matched'] === 1, 'synchronization did not retain exact assignment');
                    $saved = TranslationWorkbenchOptionDiscovery::where('meta->foundation_assignment->finding_id', $target->id)
                        ->where('meta->foundation_assignment->key_id', $request['key_id'])->where('status', '<>', 'obsolete')->get();
                    $require($saved->count() === 1 && ($observation === null || $saved->sole()->id === $observation->id), 'observation identity was not retained');
                    $observation = $saved->sole();
                }
                $results[] = ['target_finding_id' => $target->id, 'observation_id' => $observation?->id,
                    'status' => $apply ? 'prepared' : ($observation === null ? 'ready_to_create' : 'ready_to_refresh')];
            }
            return $results;
        });
    }
}
