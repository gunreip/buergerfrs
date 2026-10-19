<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;
use LogicException;

/** Explicit reviewed source transitions; never infer identity from key or text alone. */
class TranslationWorkbenchOptionSourceTransition
{
    public function __construct(
        private readonly TranslationWorkbenchScanner $scanner,
        private readonly TranslationWorkbenchTimelineRecorder $timeline,
    ) {}

    /** @param array<int, array{source_id: int, old_finding_id: int, target_finding_id: int, key_id: int, options: array, file_hash: string, old_observation_id?: int, target_observation_id?: int}> $requests */
    public function process(array $requests, bool $apply = false): array
    {
        return DB::transaction(function () use ($requests, $apply) {
            $result = [];
            $seen = [];
            foreach ($requests as $request) {
                $source = DB::table('translation_workbench_dynamic_sources')->where('id', $request['source_id'])->when($apply, fn ($query) => $query->lockForUpdate())->first();
                $old = TranslationWorkbenchFinding::with('sourceFile')->find($request['old_finding_id']);
                $target = TranslationWorkbenchFinding::with(['sourceFile', 'keys' => fn ($q) => $q->where('translation_workbench_key_findings.status', 'active')->where('translation_workbench_keys.status', '<>', 'obsolete')])->find($request['target_finding_id']);
                $observation = $source ? TranslationWorkbenchOptionDiscovery::whereKey($source->option_discovery_id)->when($apply, fn ($query) => $query->lockForUpdate())->first() : null;
                $require = static function (bool $condition, string $reason) use ($request): void {
                    if (! $condition) {
                        throw new LogicException('Option source '.$request['source_id'].': '.$reason);
                    }
                };
                $require(! isset($seen[$request['source_id']]), 'duplicate transition request');
                $seen[$request['source_id']] = true;
                $require($source !== null && $old !== null && $target !== null && $observation !== null, 'missing source, finding or observation');
                $rebind = isset($request['old_observation_id']) || isset($request['target_observation_id']);
                $previousObservation = $observation;
                if ($rebind) {
                    $require(isset($request['old_observation_id'], $request['target_observation_id'])
                        && $request['old_observation_id'] !== $request['target_observation_id'], 'explicit old and different target observation required');
                    $require(in_array($source->option_discovery_id, [$request['old_observation_id'], $request['target_observation_id']], true), 'source observation changed since review');
                    $previousObservation = TranslationWorkbenchOptionDiscovery::whereKey($request['old_observation_id'])->when($apply, fn ($q) => $q->lockForUpdate())->first();
                    $observation = TranslationWorkbenchOptionDiscovery::whereKey($request['target_observation_id'])->when($apply, fn ($q) => $q->lockForUpdate())->first();
                    $require($previousObservation !== null && $observation !== null && $observation->status !== 'obsolete', 'missing or obsolete target observation');
                    $targetAssignment = $observation->meta['foundation_assignment'] ?? [];
                    $require(($targetAssignment['status'] ?? null) === 'matched'
                        && ($targetAssignment['finding_id'] ?? null) === $target->id
                        && ($targetAssignment['key_id'] ?? null) === $source->key_id, 'target observation must already have an exact Foundation assignment');
                }
                $require($source->status === 'active' && $old->status === 'obsolete' && $target->status === 'active', 'unexpected source/finding status');
                $require($source->key_id === $request['key_id'] && $target->keys->count() === 1 && $target->keys->sole()->id === $request['key_id'], 'key identity changed (source: '.var_export($source->key_id, true).', requested: '.var_export($request['key_id'], true).', targets: '.$target->keys->pluck('id')->implode(', ').')');
                $path = $target->sourceFile?->path;
                $require($path !== null && $target->sourceFile->status === 'active' && $old->sourceFile?->path === $path && $observation->source_path === $path && $source->source_path === $path, 'source paths differ');
                $require(is_file(base_path($path)) && hash_file('sha256', base_path($path)) === $request['file_hash'], 'source file changed since review');
                $calls = $this->scanner->scan([$path])->filter(fn ($item) => $item->rawExpression === $target->raw_expression && ($item->meta['source_context'] ?? null) !== 'commented_out');
                $require($calls->count() === 1, 'current target call is not unique');
                $call = $calls->sole();
                $require(($call->meta['option_owner_key'] ?? null) === $target->keys->sole()->translation_key, 'explicit owner key differs');
                $context = $call->meta['dynamic_option_context'] ?? [];
                $previous = $old->meta['dynamic_option_context'] ?? [];
                if ($rebind && $previous === [] && preg_match('/^__\(\$(\w+)\[\$(\w+)\]\s*\?\?\s*\$\w+\)$/', $old->raw_expression, $indexed)) {
                    $previous = ['options_variable' => $indexed[1], 'key_variable' => $indexed[2], 'label_variable' => '',
                        'argument_variable_role' => 'label', 'source_expression_type' => 'indexed_option'];
                }
                foreach (['options_variable', 'key_variable', 'label_variable', 'argument_variable_role', 'source_expression_type'] as $field) {
                    $require(isset($context[$field], $previous[$field]) && $context[$field] === $previous[$field], 'option context differs: '.$field);
                }
                foreach (['options_variable', 'key_variable', 'label_variable'] as $field) {
                    $require($observation->{$field} === $context[$field], 'observation context differs: '.$field);
                }
                $normalize = static function (array $values): array { ksort($values); return $values; };
                $values = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->where('status', 'active')->pluck('native_label', 'value_key')->all();
                $require($normalize($values) === $normalize($request['options']) && $normalize($observation->options ?? []) === $normalize($request['options']), 'reviewed options changed');
                if ($rebind) {
                    $require($previousObservation->options_variable === $context['options_variable']
                        && $normalize($previousObservation->options ?? []) === $normalize($request['options']), 'previous observation payload differs');
                }
                $require(! DB::table('translation_workbench_dynamic_sources')->where('id', '<>', $source->id)->where('finding_id', $target->id)->where('key_id', $source->key_id)
                    ->when(! $rebind, fn ($q) => $q->where('option_discovery_id', $observation->id))
                    ->when($rebind, fn ($q) => $q->whereNotIn('source_type', ['runtime_options', 'runtime_db_options']))->exists(), 'target source already exists');
                $already = $source->finding_id === $target->id;
                $require($already || $source->finding_id === $old->id, 'source finding changed since review');
                if ($rebind) {
                    $require($source->option_discovery_id === ($already ? $observation->id : $previousObservation->id), 'partial observation transition requires review');
                }
                $assignment = $observation->meta['foundation_assignment'] ?? null;
                $require($assignment === null || (($assignment['status'] ?? null) === 'matched'
                    && in_array($assignment['finding_id'] ?? null, [$old->id, $target->id], true)
                    && ($assignment['key_id'] ?? null) === $source->key_id), 'observation assignment differs');
                $links = DB::table('translation_workbench_dynamic_source_candidates')->where('candidate_source_type', 'related_dynamic_source')->where('candidate_reference', 'dynamic_source:'.$source->id)->get();
                foreach ($links as $link) {
                    $require(in_array($link->finding_id, [$old->id, $target->id], true) && $link->key_id === $source->key_id, 'candidate link has a different finding/key');
                }
                if ($apply && ! $already) {
                    $observation->forceFill(['matched_entry_id' => null, 'meta' => [
                        ...($observation->meta ?? []),
                        'foundation_assignment' => ['status' => 'matched', 'finding_id' => $target->id, 'key_id' => $source->key_id,
                            'candidate_finding_ids' => [$target->id], 'candidate_key_ids' => [$source->key_id],
                            'source' => 'explicit_reviewed_transition'],
                    ]])->save();
                    DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->update([
                        'finding_id' => $target->id, 'option_discovery_id' => $observation->id, 'source_line' => $call->sourceLine,
                        'source_expression' => $call->rawExpression, 'updated_at' => now(),
                    ]);
                    foreach ($links as $link) {
                        DB::table('translation_workbench_dynamic_source_candidates')->where('id', $link->id)->update(['finding_id' => $target->id, 'updated_at' => now()]);
                    }
                    $this->timeline->record(eventType: 'dynamic_option_source_transitioned', key: $target->keys->sole(), finding: $target,
                        oldValues: ['finding_id' => $old->id, 'option_discovery_id' => $source->option_discovery_id, 'source_line' => $source->source_line, 'source_expression' => $source->source_expression, 'foundation_assignment' => $assignment],
                        newValues: ['finding_id' => $target->id, 'option_discovery_id' => $observation->id, 'source_line' => $call->sourceLine, 'source_expression' => $call->rawExpression],
                        context: ['source_id' => $source->id, 'observation_id' => $observation->id, 'candidate_ids' => $links->pluck('id')->all(), 'reviewed_file_hash' => $request['file_hash']]);
                }
                $result[] = ['source_id' => $source->id, 'target_finding_id' => $target->id, 'status' => $already ? 'already_transitioned' : ($apply ? 'transitioned' : 'ready'), 'candidate_ids' => $links->pluck('id')->all()];
            }
            return $result;
        });
    }
}
