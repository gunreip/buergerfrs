<?php

use App\Models\User;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Livewire\SourceCaseReview;
use Gunreip\TranslationWorkbench\Support\GenerationCaseDecisions;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('saves a source decision and shows it again after reload without claiming a translation is completed', function () {
    $id = str_repeat('a', 64);
    $case = ['capture_id' => $id, 'source_path' => 'composer.json', 'source_sha256' => hash_file('sha256', base_path('composer.json')), 'source_line' => 4, 'raw_expression' => '<script>unsafe()</script>'];
    $snapshot = ['cases' => [$id => $case]];
    $db = new PDO('sqlite::memory:');
    $saved = [];
    $service = Mockery::mock(GenerationCaseDecisions::class);
    $service->shouldReceive('snapshot')->andReturn($snapshot);
    $service->shouldReceive('connection')->with($snapshot)->andReturn($db);
    // Capture by reference so fresh component renders observe the persisted result.
    $service->shouldReceive('decisions')->with($db)->andReturnUsing(function () use (&$saved) {
        return $saved;
    });
    $service->shouldReceive('save')->once()->with($db, $case, 'database_value', 'User selected unchanged output', 7, 0)
        ->andReturnUsing(function () use (&$saved, $id) {
            return $saved[$id] = ['id' => 1, 'decision' => 'database_value', 'reason' => 'User selected unchanged output', 'actor_id' => 7, 'occurred_at' => '2026-10-21'];
        });
    app()->instance(GenerationCaseDecisions::class, $service);
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize')->times(4);
    app()->instance(TranslationWorkbenchEditorAccess::class, $access);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(SourceCaseReview::class)->assertSee('Review source usage')->assertDontSeeHtml('<script>unsafe()</script>')
        ->call('open', $id)->assertSet('modalOpen', true)->assertDontSee('This source file has changed since capture.')
        ->set('decision', 'database_value')
        ->call('openWorkflowReason', 'reason', 'User selected unchanged output')
        ->call('createWorkflowReason')->assertSet('reason', 'User selected unchanged output')
        ->call('save')
        ->assertHasNoErrors()->assertSet('modalOpen', false)->set('filter', 'all')->assertSee('DatabaseValue — review complete');
    Livewire::test(SourceCaseReview::class)->set('filter', 'all')->assertSee('DatabaseValue — review complete');
});

it('requires editor access for mutations', function () {
    $service = Mockery::mock(GenerationCaseDecisions::class);
    $service->shouldReceive('snapshot')->andReturn(['cases' => []]);
    $db = new PDO('sqlite::memory:');
    $service->shouldReceive('connection')->andReturn($db);
    $service->shouldReceive('decisions')->andReturn([]);
    $service->shouldNotReceive('save');
    app()->instance(GenerationCaseDecisions::class, $service);
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize')->andThrow(new AuthorizationException);
    app()->instance(TranslationWorkbenchEditorAccess::class, $access);
    Livewire::test(SourceCaseReview::class)->call('save')->assertForbidden();
});

it('sorts source cases before pagination while retaining identities and filters', function () {
    $cases = [];
    for ($line = 12; $line >= 1; $line--) {
        $cases['case-'.$line] = ['capture_id' => 'case-'.$line, 'source_path' => 'example.php',
            'source_line' => $line, 'raw_expression' => $line === 12 ? 'Alpha' : 'Zulu'];
    }
    $db = new PDO('sqlite::memory:');
    $service = Mockery::mock(GenerationCaseDecisions::class);
    $service->shouldReceive('snapshot')->andReturn(['cases' => $cases]);
    $service->shouldReceive('connection')->andReturn($db);
    $service->shouldReceive('decisions')->andReturn([
        'case-1' => ['decision' => 'database_value'], 'case-2' => ['decision' => 'translate'],
    ]);
    app()->instance(GenerationCaseDecisions::class, $service);
    $keys = fn ($expected) => fn ($rows) => array_keys($rows->items()) === $expected;
    $test = Livewire::test(SourceCaseReview::class)->set('filter', 'all');
    foreach (['source', 'expression', 'status'] as $column) {
        expect(html_entity_decode($test->html(), ENT_QUOTES | ENT_HTML5))
            ->toContain('wire:click="sortBy(\''.$column.'\')"');
    }
    $test->call('setPage', 2, 'sourceCasesPage')->set('perPage', 25)
        ->assertSet('paginators.sourceCasesPage', 1)->assertSet('filter', 'all')
        ->assertViewHas('rows', fn ($rows) => $rows->perPage() === 25 && count($rows->items()) === 12)
        ->set('perPage', 0)->assertSet('perPage', 10);
    $test->assertViewHas('rows', $keys(array_map(fn ($n) => 'case-'.$n, range(1, 10))))
        ->call('setPage', 2, 'sourceCasesPage')->assertViewHas('rows', $keys(['case-11', 'case-12']))
        ->call('sortBy', 'source')->assertSet('sortDirection', 'desc')->assertSet('paginators.sourceCasesPage', 1)
        ->assertViewHas('rows', $keys(array_map(fn ($n) => 'case-'.$n, range(12, 3))))
        ->call('sortBy', 'expression')->assertSet('sortDirection', 'asc')
        ->assertViewHas('rows', fn ($rows) => array_key_first($rows->items()) === 'case-12')
        ->call('sortBy', 'status')->assertViewHas('rows', $keys(array_map(fn ($n) => 'case-'.$n, range(3, 12))))
        ->call('setPage', 2, 'sourceCasesPage')->assertViewHas('rows', $keys(['case-2', 'case-1']))
        ->call('sortBy', 'status')->assertViewHas('rows', fn ($rows) => array_slice(array_keys($rows->items()), 0, 2) === ['case-1', 'case-2'])
        ->set('search', 'Alpha')->assertViewHas('rows', $keys(['case-12']))
        ->call('sortBy', 'source')->assertSet('search', 'Alpha')->assertViewHas('rows', $keys(['case-12']))
        ->call('sortBy', 'invalid')->assertSet('sortColumn', 'source')
        ->set('search', '')->set('filter', 'open')
        ->assertViewHas('rows', fn ($rows) => $rows->total() === 10 && ! isset($rows->items()['case-1']) && ! isset($rows->items()['case-2']));
});
