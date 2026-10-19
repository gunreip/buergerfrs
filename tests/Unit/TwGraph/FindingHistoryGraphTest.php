<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDatasetDetails;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\FindingEventLabel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->previousConnection = config('database.default');
    config(['database.default' => 'finding_graph_test', 'database.connections.finding_graph_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
    DB::purge('finding_graph_test');
    Schema::create('translation_workbench_timeline_chains', function ($table) {
        $table->id();
        foreach (['translation_key', 'chain_type', 'chain_status', 'root_key_id', 'root_finding_id', 'key_count', 'finding_count', 'active_finding_count', 'obsolete_finding_count', 'commented_out_finding_count', 'review_count', 'timeline_event_count', 'lang_value_count', 'shared_candidate_count', 'bulk_review_count', 'key_ids', 'finding_ids', 'review_ids', 'lang_value_ids', 'related_translation_keys', 'relation_summary', 'lang_value_summary', 'timeline_event_summary', 'timeline_event_ids', 'meta', 'first_seen_at', 'last_seen_at', 'created_at', 'updated_at'] as $column) {
            $table->text($column)->nullable();
        }
    });
    Schema::create('translation_workbench_findings', function ($table) { $table->id(); });
    Schema::create('translation_workbench_timeline_events', function ($table) {
        $table->id();
        $table->integer('finding_id');
        $table->integer('key_id')->nullable();
        $table->string('event_type');
        $table->text('old_values')->nullable();
        $table->text('new_values')->nullable();
        $table->timestamps();
    });
    DB::table('translation_workbench_timeline_chains')->insert(['id' => 252, 'root_key_id' => 1236, 'root_finding_id' => 891, 'finding_ids' => '[891,892]', 'timeline_event_ids' => '[]']);
    DB::table('translation_workbench_findings')->insert([['id' => 891], ['id' => 892]]);
    foreach (range(1, 22) as $id) {
        DB::table('translation_workbench_timeline_events')->insert(['id' => $id, 'finding_id' => 891, 'key_id' => 1236, 'event_type' => 'finding_changed', 'created_at' => '2026-10-18 12:00:00']);
    }
    DB::table('translation_workbench_timeline_events')->insert(['id' => 99, 'finding_id' => 892, 'key_id' => 1236, 'event_type' => 'finding_changed']);
});

afterEach(function () {
    DB::purge('finding_graph_test');
    config(['database.default' => $this->previousConnection]);
});

it('shows current finding events only and paginates without losing older history', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 252;
    $component->graphScope = 'finding';
    $data = $component->render()->getData();
    expect($data['finding']->id)->toBe(891)
        ->and($data['findingEvents']->total())->toBe(22)
        ->and($data['findingEvents']->count())->toBe(20)
        ->and($data['findingEvents']->pluck('finding_id')->unique()->all())->toBe([891])
        ->and($data['rootRows'])->toBe([])->and($data['originRows'])->toBe([]);
    $html = view('translation-workbench::livewire.tw-graph.data-driven.finding-graph', [...$data, 'reloadTick' => 0])->render();
    expect($html)->toContain('Finding #891', 'Key #1236', 'finding-history.event-1')->not->toContain('<x-translation-workbench');
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $host = $component->render()->with('graphScope', 'finding')->render();
    expect($host)->toContain('wire:model.live="graphScope"', 'Finding history', 'data-flux-callout');
    $component->setPage(2, 'findingEventsPage');
    expect($component->render()->getData()['findingEvents']->pluck('id')->all())->toBe([21, 22]);
});

it('allows current finding event details absent from the old snapshot but rejects foreign events', function () {
    $details = new TwGraphDatasetDetails;
    $details->datasetId = 252;
    $details->findingId = 891;
    $details->openDetails(['type' => 'event', 'id' => 22]);
    expect($details->record['id'])->toBe(22);
    expect(fn () => $details->openDetails(['type' => 'event', 'id' => 99]))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('labels recorded identity and assignment changes without inventing a new finding identity', function () {
    $lines = FindingEventLabel::lines((object) ['id' => 12, 'finding_id' => 891, 'key_id' => 1236, 'event_type' => 'finding_assignment_selected', 'created_at' => '2026-10-18', 'old_values' => '{"key_id":1858}', 'new_values' => '{"key_id":1236}']);
    expect($lines)->toContain('Event #12 · Finding #891 · Key #1236', 'key_id: 1858 → 1236')->toHaveCount(4);
});

it('preserves a searched member finding on refresh and resets it when the dataset changes', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->datasetIdType = 'finding';
    $component->datasetIdInput = '892';
    $component->loadDataset();
    expect($component->timelineChainId)->toBe(252)->and($component->graphFindingId)->toBe(892);
    $component->reloadDataset();
    expect($component->graphFindingId)->toBe(892);
    $component->updatedSelectedHistoryId(253);
    expect($component->graphFindingId)->toBeNull();
});

it('shows a key ID alongside Key created without changing the event title', function () {
    $method = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RenderPreviewBuilder::class, 'regularEventLabelItems');
    $items = $method->invoke(null, collect([['event' => 'Key created', 'dataset_source' => ['type' => 'key', 'id' => 1236], '_timestamp_group' => 'now', 'state' => 'active']]));
    expect($items[0]['labels'][0]['text'])->toContain('Key created', 'Key #1236');
});
