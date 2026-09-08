<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\MergeOutcomes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

it('returns unknown outcomes when finding lookup tables are not available', function (): void {
    Schema::shouldReceive('hasTable')
        ->once()
        ->with('translation_workbench_findings')
        ->andReturnFalse();

    $result = MergeOutcomes::from([
        'translation_key' => 'ui.states.all',
    ], new Collection([
        ['first_root' => 'finding #5486'],
        ['first_root' => 'finding #5487'],
    ]));

    expect($result)->toBe([
        'summary' => [
            'total' => 2,
            'source_active' => 0,
            'source_inactive' => 0,
            'unknown' => 2,
            'branch_candidates' => 0,
        ],
        'rows' => [],
    ]);
});

it('returns unknown outcomes when origin rows have no finding ids', function (): void {
    $result = MergeOutcomes::from([], new Collection([
        ['first_root' => 'root without numeric id'],
    ]));

    expect($result['summary']['total'])->toBe(1)
        ->and($result['summary']['unknown'])->toBe(1)
        ->and($result['rows'])->toBe([]);
});

it('returns an empty unknown summary for empty origin rows without touching schema', function (): void {
    Schema::shouldReceive('hasTable')->never();

    expect(MergeOutcomes::from([], new Collection()))->toBe([
        'summary' => [
            'total' => 0,
            'source_active' => 0,
            'source_inactive' => 0,
            'unknown' => 0,
            'branch_candidates' => 0,
        ],
        'rows' => [],
    ]);
});

it('classifies merge outcome groups from finding shared and origin states', function (): void {
    $classifier = new ReflectionMethod(MergeOutcomes::class, 'mergeOutcomeGroup');

    expect($classifier->invoke(null, 'active', 'obsolete', 'active'))->toBe('arrived at shared key')
        ->and($classifier->invoke(null, 'active', 'accepted', 'active'))->toBe('arrived at shared key')
        ->and($classifier->invoke(null, 'active', 'none', 'obsolete'))->toBe('active source, obsolete origin key')
        ->and($classifier->invoke(null, 'inactive', 'obsolete', 'obsolete'))->toBe('ended after merge')
        ->and($classifier->invoke(null, 'inactive', 'none', 'active'))->toBe('ended before target')
        ->and($classifier->invoke(null, 'active', 'pending', 'active'))->toBe('shared review pending')
        ->and($classifier->invoke(null, 'active', 'none', 'active'))->toBe('needs review');
});

it('extracts finding ids from root labels case insensitively', function (): void {
    $parser = new ReflectionMethod(MergeOutcomes::class, 'findingIdFromRoot');

    expect($parser->invoke(null, 'Finding #5486'))->toBe(5486)
        ->and($parser->invoke(null, 'root finding #5487'))->toBe(5487)
        ->and($parser->invoke(null, 'root key #5'))->toBeNull();
});

it('deduplicates finding lookups while preserving repeated origin rows in outcome counts', function (): void {
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_findings')
        ->once()
        ->andReturnTrue();
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_shared_key_candidates')
        ->once()
        ->andReturnTrue();
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_keys')
        ->once()
        ->andReturnTrue();

    DB::shouldReceive('table')
        ->with('translation_workbench_findings as findings')
        ->once()
        ->andReturn(new class {
            public function leftJoin(string $table, string $first, string $operator, string $second): self
            {
                expect([$table, $first, $operator, $second])->toBe([
                    'translation_workbench_source_files as source_files',
                    'source_files.id',
                    '=',
                    'findings.source_file_id',
                ]);

                return $this;
            }

            public function whereIn(string $column, array $values): self
            {
                expect($column)->toBe('findings.id')
                    ->and($values)->toBe([5486]);

                return $this;
            }

            public function get(array $columns): Collection
            {
                expect($columns)->toContain('findings.id')
                    ->toContain('source_files.path as source_path');

                return collect([
                    (object) [
                        'id' => 5486,
                        'status' => 'inactive',
                        'suggested_key' => 'ui.old.all',
                        'found_translation_key' => 'ui.old.all',
                        'existing_key' => null,
                        'first_seen_at' => '2026-08-04 09:22:00',
                        'last_seen_at' => '2026-08-05 10:11:00',
                        'updated_at' => null,
                        'source_path' => 'lang/en/ui.php',
                    ],
                ]);
            }
        });

    DB::shouldReceive('table')
        ->with('translation_workbench_shared_key_candidates')
        ->once()
        ->andReturn(new class {
            public function whereIn(string $column, array $values): self
            {
                expect($column)->toBe('finding_id')
                    ->and($values)->toBe([5486]);

                return $this;
            }

            public function orderByDesc(string $column): self
            {
                expect($column)->toBe('updated_at');

                return $this;
            }

            public function get(array $columns): Collection
            {
                expect($columns)->toContain('finding_id')
                    ->toContain('matched_key_id');

                return collect([
                    (object) [
                        'id' => 992,
                        'finding_id' => 5486,
                        'key_id' => 5,
                        'matched_key_id' => 5,
                        'current_translation_key' => 'ui.old.all',
                        'suggested_shared_translation_key' => 'ui.states.all',
                        'status' => 'obsolete',
                        'last_seen_at' => '2026-08-05 10:11:00',
                        'updated_at' => '2026-08-05 10:12:00',
                    ],
                ]);
            }
        });

    DB::shouldReceive('table')
        ->with('translation_workbench_keys')
        ->once()
        ->andReturn(new class {
            public function whereIn(string $column, array $values): self
            {
                expect($column)->toBe('translation_key')
                    ->and($values)->toBe(['ui.states.all', 'ui.old.all']);

                return $this;
            }

            public function orWhereIn(string $column, array $values): self
            {
                expect($column)->toBe('suggested_key')
                    ->and($values)->toBe(['ui.states.all', 'ui.old.all']);

                return $this;
            }

            public function get(array $columns): Collection
            {
                expect($columns)->toBe(['id', 'translation_key', 'suggested_key', 'status', 'review_status', 'updated_at']);

                return collect([
                    (object) [
                        'id' => 4,
                        'translation_key' => 'ui.old.all',
                        'suggested_key' => null,
                        'status' => 'obsolete',
                        'review_status' => 'accepted',
                        'updated_at' => '2026-08-05 10:12:00',
                    ],
                    (object) [
                        'id' => 5,
                        'translation_key' => 'ui.states.all',
                        'suggested_key' => null,
                        'status' => 'active',
                        'review_status' => 'accepted',
                        'updated_at' => '2026-08-05 10:12:00',
                    ],
                ]);
            }
        });

    $result = MergeOutcomes::from([
        'translation_key' => 'ui.states.all',
    ], new Collection([
        [
            'first_root' => 'Finding #5486',
            'first_origin_key' => 'ui.old.all',
            'first_timestamp' => '2026-08-04 09:22:00',
            'last_timestamp' => '2026-08-05 10:11:00',
        ],
        [
            'first_root' => 'finding #5486',
            'first_origin_key' => 'ui.old.all',
            'first_timestamp' => '2026-08-04 09:23:00',
            'last_timestamp' => '2026-08-05 10:12:00',
        ],
    ]));

    expect($result['summary'])
        ->toMatchArray([
            'total' => 2,
            'source_active' => 0,
            'source_inactive' => 2,
            'branch_candidates' => 2,
            'branch_candidate_findings' => 1,
            'ended_after_merge_findings' => 1,
            'ended_after_merge_rows' => 2,
        ])
        ->and($result['rows'])->toHaveCount(2)
        ->and(data_get($result, 'rows.0.matched_key_id'))->toBe(5)
        ->and(data_get($result, 'rows.0.outcome_group'))->toBe('ended after merge')
        ->and(data_get($result, 'rows.1.outcome_group'))->toBe('ended after merge');
});
