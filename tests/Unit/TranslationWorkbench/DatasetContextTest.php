<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDatasetDetails;
use Gunreip\TranslationWorkbench\Support\DatasetLabelContext;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders context menus only for labels with explicit information references and retains normal click copying', function () {
    $menu = DatasetLabelContext::menu(['type' => 'event', 'id' => 7]);
    $html = Blade::render('<x-translation-workbench::ui.tw-graph><x-translation-workbench::ui.tw-graph.segments.label id="test.label" :label="[\'text\' => \'Event\', \'contextMenu\' => $menu]" /></x-translation-workbench::ui.tw-graph>', compact('menu'));
    expect($html)->toContain('data-flux-context', 'tw-graph-dataset-details', 'navigator.clipboard?.writeText', 'More information: right-click');
    $plain = Blade::render('<x-translation-workbench::ui.tw-graph.primitives.text text="Plain label" />');
    expect($plain)->not->toContain('data-flux-context', 'More information: right-click');
    expect(DatasetLabelContext::menu([]))->toBe([]);
});

it('loads details independently and rejects records outside the selected dataset', function () {
    config(['database.default' => 'context_test', 'database.connections.context_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
    DB::purge('context_test');
    Schema::create('translation_workbench_timeline_chains', function (Blueprint $table) {
        $table->id();
        $table->string('translation_key');
        $table->integer('root_key_id')->nullable();
        $table->integer('root_finding_id')->nullable();
        $table->text('timeline_event_ids');
    });
    Schema::create('translation_workbench_timeline_events', function (Blueprint $table) {
        $table->id();
        $table->string('event_type');
        $table->text('old_values');
        $table->text('new_values');
    });
    DB::table('translation_workbench_timeline_chains')->insert(['id' => 1, 'translation_key' => 'ui.test', 'timeline_event_ids' => '[7]']);
    DB::table('translation_workbench_timeline_events')->insert(['id' => 7, 'event_type' => 'changed', 'old_values' => '{"value":"before"}', 'new_values' => '{"value":"after"}']);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->assertSet('record', [])
        ->dispatch('tw-graph-dataset-details', source: ['type' => 'event', 'id' => 7], section: 'values')
        ->assertSet('show', true)->assertSet('record.id', 7)->assertSee('before')->assertSee('after');
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'event', 'id' => 8], 'overview')->assertStatus(404);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'users', 'id' => 1], 'overview')->assertStatus(422);
});

it('loads paginated event and relationship groups only from the selected dataset', function () {
    config(['database.default' => 'context_groups', 'database.connections.context_groups' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
    DB::purge('context_groups');
    Schema::create('translation_workbench_timeline_chains', function (Blueprint $table) {
        $table->id();
        $table->string('translation_key');
        $table->text('timeline_event_ids');
        $table->text('finding_ids');
        $table->text('meta');
    });
    Schema::create('translation_workbench_timeline_events', function (Blueprint $table) {
        $table->id();
        $table->string('event_type');
        $table->string('event_classification')->nullable();
    });
    Schema::create('translation_workbench_findings', function (Blueprint $table) {
        $table->id();
        $table->string('suggested_key');
    });
    DB::table('translation_workbench_timeline_chains')->insert([
        'id' => 1, 'translation_key' => 'ui.current', 'timeline_event_ids' => json_encode(range(1, 23)), 'finding_ids' => '[5]',
        'meta' => json_encode(['moved_relations' => [
            ['translation_key' => 'ui.old', 'rekeyed_to_translation_key' => 'ui.current'],
            ['translation_key' => 'ui.current', 'rekeyed_to_translation_key' => 'ui.new'],
        ]]),
    ]);
    foreach (range(1, 24) as $id) {
        DB::table('translation_workbench_timeline_events')->insert(['id' => $id, 'event_type' => $id === 22 ? 'other' : 'changed', 'event_classification' => $id === 23 ? 'dead_dev_event' : null]);
    }
    DB::table('translation_workbench_findings')->insert(['id' => 5, 'suggested_key' => 'ui.origin']);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'event-group', 'id' => 1, 'event_type' => 'changed'])
        ->assertSet('show', true)->assertSee('Entries: 21')
        ->assertViewHas('entries', fn ($entries) => $entries->count() === 20 && $entries->total() === 21)
        ->call('setPage', 2, 'detailsPage')
        ->assertViewHas('entries', fn ($entries) => $entries->count() === 1 && $entries->first()['id'] === 21);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'finding-group', 'id' => 1, 'ids' => [5]])->assertSee('ui.origin');
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'finding-group', 'id' => 1, 'ids' => [6]])->assertStatus(404);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'rekey-group', 'id' => 1, 'direction' => 'source'])
        ->assertSee('ui.old')->assertDontSee('ui.new');
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'event-group', 'id' => 2])->assertStatus(404);
});

it('preserves authored label props when adding relationship references', function () {
    $labels = ['start' => ['text' => ['Origin'], 'width' => 'long'], 1 => ['left' => ['First seen'], 'right' => ['text' => 'Literal', 'color' => 'red']], 'length' => '4rem'];
    $result = DatasetLabelContext::labels($labels, ['type' => 'finding', 'id' => 5]);
    expect($result['start']['width'])->toBe('long')
        ->and($result[1]['left']['contextMenu'][0]['detail']['source']['id'])->toBe(5)
        ->and($result[1]['right']['color'])->toBe('red')
        ->and($result['length'])->toBe('4rem')
        ->and(DatasetLabelContext::labels($labels, []))->toBe($labels);
});

it('resolves only matching merge relationships and allows opening their review', function () {
    config(['database.default' => 'merge_context', 'database.connections.merge_context' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
    DB::purge('merge_context');
    Schema::create('translation_workbench_timeline_chains', function (Blueprint $table) {
        $table->id();
        $table->integer('root_key_id');
        $table->string('translation_key');
        $table->text('finding_ids');
        $table->text('meta');
    });
    Schema::create('translation_workbench_reviews', function (Blueprint $table) {
        $table->id();
        $table->integer('key_id');
        $table->integer('finding_id')->nullable();
        $table->string('decision');
        $table->text('meta');
    });
    DB::table('translation_workbench_timeline_chains')->insert(['id' => 1, 'root_key_id' => 10, 'translation_key' => 'ui.target', 'finding_ids' => '[]',
        'meta' => json_encode(['shared_candidates' => [['id' => 90, 'finding_id' => 5, 'matched_translation_key' => 'ui.target'], ['id' => 91, 'finding_id' => 6, 'matched_translation_key' => 'ui.other']]]),
    ]);
    DB::table('translation_workbench_reviews')->insert([
        ['id' => 20, 'key_id' => 10, 'finding_id' => null, 'decision' => 'translation_key_bulk_equalized', 'meta' => '{"selected_finding_ids":[5]}'],
        ['id' => 21, 'key_id' => 10, 'finding_id' => 6, 'decision' => 'translation_key_bulk_equalized', 'meta' => '{}'],
        ['id' => 22, 'key_id' => 11, 'finding_id' => 5, 'decision' => 'translation_key_bulk_equalized', 'meta' => '{}'],
    ]);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'merge-group', 'id' => 1, 'ids' => [5]])
        ->assertViewHas('entries', fn ($entries) => $entries->total() === 2 && collect($entries->items())->pluck('id')->all() === [90, 20])
        ->assertSee('Open review')->assertSee('Open finding')->assertDontSee('ui.other')
        ->call('openDetails', ['type' => 'review', 'id' => 20], 'technical')->assertSet('record.id', 20);
    Livewire::test(TwGraphDatasetDetails::class, ['datasetId' => 1])
        ->call('openDetails', ['type' => 'merge-group', 'id' => 1, 'ids' => [999]])->assertStatus(404);
    $menu = DatasetLabelContext::menu(['type' => 'finding', 'id' => 5, 'dataset_id' => 1]);
    expect(collect($menu)->last()['detail']['source'])->toBe(['type' => 'merge-group', 'id' => 1, 'ids' => [5]]);
});

it('provides information references for canonical root and key history in raw-data graphs', function () {
    Schema::shouldReceive('hasTable')->with('translation_workbench_keys')->andReturnTrue();
    Schema::shouldReceive('hasTable')->with('translation_workbench_lang_values')->andReturnFalse();
    Schema::shouldReceive('hasTable')->with('translation_workbench_reviews')->andReturnFalse();
    $query = Mockery::mock(\Illuminate\Database\Query\Builder::class);
    DB::shouldReceive('table')->with('translation_workbench_keys')->andReturn($query);
    $query->shouldReceive('find')->with(9)->andReturn((object) [
        'id' => 9, 'created_at' => '2026-01-01', 'updated_at' => '2026-01-03',
        'reviewed_at' => '2026-01-02', 'status' => 'active', 'review_status' => 'approved',
    ]);
    $host = new class extends \Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawData {
        protected function timelineChainMainRow(string $table): ?array
        {
            return ['id' => 1, 'root_key_id' => 9, 'translation_key' => 'ui.example',
                'updated_at' => '2026-01-04', 'chain_type' => 'single', 'chain_status' => 'active'];
        }

        public function rootRowsForTest(): array
        {
            return $this->timelineChainRootRows('translation_workbench_timeline_chains');
        }
    };
    $rows = $host->rootRowsForTest();
    expect($rows)->toHaveCount(4);
    foreach ($rows as $row) {
        expect($row['dataset_source'])->toBe($row['role'] === 'root'
            ? ['type' => 'dataset', 'id' => 1] : ['type' => 'key', 'id' => 9]);
    }
});
