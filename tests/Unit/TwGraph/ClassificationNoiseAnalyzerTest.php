<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\ClassificationNoiseAnalyzer;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

it('skips the classification noise report when there are no potential dead dev events', function (): void {
    $report = ClassificationNoiseAnalyzer::report([
        'timeline_event_ids' => [1, 2, 3],
    ], classificationChangedEventCount: 1423, retainedClassificationEventCount: 4, potentialDeadDevEventCount: 0);

    expect($report)->toBeNull();
});

it('skips the classification noise report when the row has no valid timeline event ids', function (): void {
    $report = ClassificationNoiseAnalyzer::report([
        'timeline_event_ids' => ['foo', null, 0],
    ], classificationChangedEventCount: 1423, retainedClassificationEventCount: 4, potentialDeadDevEventCount: 1419);

    expect($report)->toBeNull();
});

it('builds a classification noise report from the timeline event summary', function (): void {
    $summaryQuery = new class((object) [
        'total' => 1423,
        'first_seen' => '2026-07-22 20:26:00',
        'last_seen' => '2026-07-24 09:17:00',
        'key_count' => 4,
        'finding_count' => 7,
        'context_count' => 11,
        'state_variant_count' => 3,
    ]) {
        public function __construct(private readonly object $row) {}

        public function whereIn(string $column, array $values): self
        {
            expect($column)->toBe('id');
            expect($values)->toBe([38512, 38513]);

            return $this;
        }

        public function where(string $column, string $value): self
        {
            expect($column)->toBe('event_type');
            expect($value)->toBe('dynamic_source_classification_changed');

            return $this;
        }

        public function selectRaw(string $expression): self
        {
            return $this;
        }

        public function first(): object
        {
            return $this->row;
        }
    };

    $sourceQuery = new class([
        (object) [
            'source_path' => 'resources/views/example.blade.php',
            'source_line' => '42',
            'total' => 900,
            'state_variant_count' => 2,
            'first_seen' => '2026-07-22 20:26:00',
            'last_seen' => '2026-07-23 08:10:00',
        ],
        (object) [
            'source_path' => null,
            'source_line' => null,
            'total' => 523,
            'state_variant_count' => 1,
            'first_seen' => null,
            'last_seen' => null,
        ],
    ]) {
        public function __construct(private readonly array $rows) {}

        public function whereIn(string $column, array $values): self
        {
            expect($column)->toBe('id');
            expect($values)->toBe([38512, 38513]);

            return $this;
        }

        public function where(string $column, string $value): self
        {
            expect($column)->toBe('event_type');
            expect($value)->toBe('dynamic_source_classification_changed');

            return $this;
        }

        public function selectRaw(string $expression): self
        {
            return $this;
        }

        public function groupBy(string $sourcePathColumn, string $sourceLineColumn): self
        {
            expect($sourcePathColumn)->toBe('source_path');
            expect($sourceLineColumn)->toBe('source_line');

            return $this;
        }

        public function orderByDesc(string $column): self
        {
            expect($column)->toBe('total');

            return $this;
        }

        public function limit(int $limit): self
        {
            expect($limit)->toBe(3);

            return $this;
        }

        public function get()
        {
            return collect($this->rows);
        }
    };

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_events')
        ->once()
        ->ordered()
        ->andReturn($summaryQuery);

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_events')
        ->once()
        ->ordered()
        ->andReturn($sourceQuery);

    $report = ClassificationNoiseAnalyzer::report([
        'timeline_event_ids' => [38512, '38513', 'foo', null, 0],
    ], classificationChangedEventCount: 1400, retainedClassificationEventCount: 4, potentialDeadDevEventCount: 1419);

    expect($report)->toMatchArray([
        'event_type' => 'dynamic_source_classification_changed',
        'total' => 1423,
        'retained' => 4,
        'candidate_dead_dev_events' => 1419,
        'first_seen' => '2026-07-22 20:26',
        'last_seen' => '2026-07-24 09:17',
        'key_count' => 4,
        'finding_count' => 7,
        'context_count' => 11,
        'state_variant_count' => 3,
    ]);

    expect($report['top_sources'])->toBe([
        [
            'source_path' => 'resources/views/example.blade.php',
            'source_line' => '42',
            'total' => 900,
            'state_variant_count' => 2,
            'first_seen' => '2026-07-22 20:26',
            'last_seen' => '2026-07-23 08:10',
        ],
        [
            'source_path' => '',
            'source_line' => '',
            'total' => 523,
            'state_variant_count' => 1,
            'first_seen' => '',
            'last_seen' => '',
        ],
    ]);
});

it('does not query top sources when the classification noise summary is unavailable', function (): void {
    $summaryQuery = new class {
        public function whereIn(string $column, array $values): self
        {
            expect($column)->toBe('id');
            expect($values)->toBe([38512]);

            return $this;
        }

        public function where(string $column, string $value): self
        {
            expect($column)->toBe('event_type');
            expect($value)->toBe('dynamic_source_classification_changed');

            return $this;
        }

        public function selectRaw(string $expression): self
        {
            return $this;
        }

        public function first(): ?object
        {
            return null;
        }
    };

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_events')
        ->once()
        ->andReturn($summaryQuery);

    expect(ClassificationNoiseAnalyzer::report([
        'timeline_event_ids' => [38512],
    ], classificationChangedEventCount: 1400, retainedClassificationEventCount: 4, potentialDeadDevEventCount: 1396))->toBeNull();
});
