<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TwGraphDatasetDetails extends Component
{
    use WithPagination;

    #[Locked]
    public array $groupSource = [];

    #[Locked]
    public int $datasetId;

    public bool $show = false;

    #[Locked]
    public string $section = 'overview';

    #[Locked]
    public array $record = [];

    #[Locked]
    public string $sourceType = '';

    #[On('tw-graph-dataset-details')]
    public function openDetails(array $source, string $section = 'overview'): void
    {
        abort_unless(in_array($section, ['overview', 'values', 'context', 'technical'], true), 422);
        $dataset = DB::table('translation_workbench_timeline_chains')->find($this->datasetId);
        abort_unless($dataset !== null, 404);
        $type = (string) ($source['type'] ?? '');
        $id = (int) ($source['id'] ?? 0);
        if (in_array($type, ['event-group', 'finding-group', 'rekey-group', 'merge-group'], true)) {
            abort_unless($id === $this->datasetId, 404);
            $this->groupRecords($dataset, $source); // Validate before updating visible state.
            $this->groupSource = $source;
            $this->resetPage('detailsPage');
            $this->sourceType = $type;
            $this->section = $section;
            $this->record = [];
            $this->show = true;

            return;
        }
        $table = match ($type) {
            'dataset' => 'translation_workbench_timeline_chains',
            'key' => 'translation_workbench_keys',
            'finding' => 'translation_workbench_findings',
            'lang-value' => 'translation_workbench_lang_values',
            'review' => 'translation_workbench_reviews',
            'event' => 'translation_workbench_timeline_events',
            default => null,
        };
        abort_unless($table && $id > 0, 422);
        $query = DB::table($table)->where('id', $id);
        // Resolve only records belonging to the currently selected dataset.
        match ($type) {
            'dataset' => $query->where('id', $this->datasetId),
            'key' => $query->where('id', $dataset->root_key_id ?? 0),
            'finding' => $query->whereIn('id', $this->findingIds($dataset)),
            'lang-value' => $query->where('translation_key', $dataset->translation_key),
            'review' => $query->where('key_id', $dataset->root_key_id ?? 0),
            'event' => $query->whereIn('id', is_array($dataset->timeline_event_ids) ? $dataset->timeline_event_ids : (json_decode($dataset->timeline_event_ids ?? '[]', true) ?: [])),
        };
        $record = $query->first();
        abort_unless($record !== null, 404);
        $this->groupSource = [];
        $this->sourceType = $type;
        $this->section = $section;
        $this->record = collect((array) $record)->map(static function ($value) {
            if (is_string($value) && in_array(substr(ltrim($value), 0, 1), ['{', '['], true)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            return $value;
        })->all();
        $this->show = true;
    }

    private function jsonArray(mixed $value): array
    {
        return is_array($value) ? $value : (json_decode($value ?? '[]', true) ?: []);
    }

    private function findingIds(object $dataset): array
    {
        $ids = [...$this->jsonArray($dataset->finding_ids ?? null), $dataset->root_finding_id ?? 0,
            ...collect($this->jsonArray($dataset->meta ?? null)['shared_candidates'] ?? [])->pluck('finding_id')->all(),
        ];
        if (Schema::hasTable('translation_workbench_reviews')) {
            foreach (DB::table('translation_workbench_reviews')->where('key_id', $dataset->root_key_id ?? 0)
                ->where('decision', 'translation_key_bulk_equalized')->get(['finding_id', 'meta']) as $review) {
                $ids[] = $review->finding_id;
                $ids = [...$ids, ...($this->jsonArray($review->meta)['selected_finding_ids'] ?? [])];
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function groupRecords(object $dataset, array $source): LengthAwarePaginator
    {
        $page = $this->getPage('detailsPage');
        if ($source['type'] === 'event-group') {
            $query = DB::table('translation_workbench_timeline_events')
                ->whereIn('id', $this->jsonArray($dataset->timeline_event_ids ?? null))
                ->where('event_type', (string) ($source['event_type'] ?? ''));
            if (Schema::hasColumn('translation_workbench_timeline_events', 'event_classification')) {
                $query->where(fn ($q) => $q->whereNull('event_classification')->orWhere('event_classification', '!=', 'dead_dev_event'));
            }

            return $query->orderBy('id')->paginate(20, ['*'], 'detailsPage');
        }
        if ($source['type'] === 'finding-group') {
            $ids = array_values(array_unique(array_map('intval', (array) ($source['ids'] ?? []))));
            abort_unless($ids !== [] && array_diff($ids, $this->findingIds($dataset)) === [], 404);

            return DB::table('translation_workbench_findings')->whereIn('id', $ids)
                ->orderBy('id')->paginate(20, ['*'], 'detailsPage');
        }
        if ($source['type'] === 'merge-group') {
            $ids = array_values(array_unique(array_map('intval', (array) ($source['ids'] ?? []))));
            $candidateIds = array_values(array_unique(array_map('intval', (array) ($source['candidate_ids'] ?? []))));
            $candidates = collect($this->jsonArray($dataset->meta ?? null)['shared_candidates'] ?? [])
                ->filter(fn ($row) => is_array($row));
            abort_unless(($ids !== [] || $candidateIds !== [])
                && array_diff($ids, $this->findingIds($dataset)) === []
                && array_diff($candidateIds, $candidates->pluck('id')->all()) === [], 404);
            $relations = $candidates
                ->filter(fn ($row) => in_array((int) ($row['finding_id'] ?? 0), $ids, true)
                    || in_array((int) ($row['id'] ?? 0), $candidateIds, true))
                ->map(fn ($row) => [...$row, 'relation_kind' => __('Shared candidate'),
                    '_source' => ! empty($row['finding_id']) ? ['type' => 'finding', 'id' => (int) $row['finding_id']] : null])->values();
            if (Schema::hasTable('translation_workbench_reviews')) {
                $reviews = DB::table('translation_workbench_reviews')
                    ->where('key_id', $dataset->root_key_id ?? 0)
                    ->where('decision', 'translation_key_bulk_equalized')->orderBy('id')->get()
                    ->filter(function ($review) use ($ids): bool {
                        $selected = [...($this->jsonArray($review->meta)['selected_finding_ids'] ?? []), $review->finding_id];

                        return array_intersect($ids, array_map('intval', $selected)) !== [];
                    })
                    ->map(fn ($review) => [...(array) $review, 'relation_kind' => __('Merge review'),
                        '_source' => ['type' => 'review', 'id' => $review->id]]);
                $relations = $relations->concat($reviews)->values();
            }

            return new LengthAwarePaginator($relations->forPage($page, 20)->values(), $relations->count(), 20, $page, ['pageName' => 'detailsPage']);
        }
        $direction = $source['direction'] ?? '';
        abort_unless(in_array($direction, ['source', 'target'], true), 422);
        $relations = collect($this->jsonArray($dataset->meta ?? null)['moved_relations'] ?? [])
            ->filter(fn ($row) => is_array($row) && ($direction === 'source'
                ? ($row['rekeyed_to_translation_key'] ?? '') === $dataset->translation_key
                : ($row['translation_key'] ?? '') === $dataset->translation_key))->values();

        return new LengthAwarePaginator($relations->forPage($page, 20)->values(), $relations->count(), 20, $page, ['pageName' => 'detailsPage']);
    }

    public function render()
    {
        $overview = ['relation_kind', 'finding_id', 'key_id', 'matched_key_id', 'current_translation_key', 'matched_translation_key', 'id', 'translation_key', 'suggested_key', 'rekeyed_to_translation_key', 'event_type', 'locale', 'status', 'chain_type', 'chain_status', 'review_status', 'decision', 'review_type', 'created_at', 'updated_at', 'reviewed_at', 'created_by_user_id'];
        $values = ['old_values', 'new_values', 'value', 'native_text', 'translated_text', 'text', 'translation', 'source_text', 'literal_text', 'literal_text_suggested', 'raw_expression'];
        $fields = match ($this->section) {
            'overview' => array_intersect_key($this->record, array_flip($overview)),
            'values' => array_intersect_key($this->record, array_flip($values)),
            'context' => array_diff_key($this->record, array_flip(array_merge($overview, $values))),
            default => $this->record,
        };

        $entries = null;
        if ($this->groupSource !== []) {
            $dataset = DB::table('translation_workbench_timeline_chains')->find($this->datasetId);
            abort_unless($dataset !== null, 404);
            $entries = $this->groupRecords($dataset, $this->groupSource);
            $entries->through(function ($entry) use ($overview, $values): array {
                $entry = (array) $entry;
                $entrySource = $entry['_source'] ?? null;
                unset($entry['_source']);
                $entry = collect($entry)->map(function ($value) {
                    if (is_string($value) && in_array(substr(ltrim($value), 0, 1), ['{', '['], true)) {
                        $decoded = json_decode($value, true);

                        return is_array($decoded) ? $decoded : $value;
                    }

                    return $value;
                })->all();
                $fields = match ($this->section) {
                    'overview' => array_intersect_key($entry, array_flip($overview)),
                    'context' => array_diff_key($entry, array_flip(array_merge($overview, $values))),
                    default => $entry,
                };

                return ['source' => $entrySource, 'id' => $entry['id'] ?? null, 'title' => $entry['relation_kind'] ?? $entry['event_type'] ?? $entry['suggested_key'] ?? $entry['translation_key'] ?? '', 'fields' => $fields];
            });
        }

        return view('translation-workbench::livewire.tw-graph.data-driven.dataset-details', ['fields' => $fields, 'entries' => $entries]);
    }
}
