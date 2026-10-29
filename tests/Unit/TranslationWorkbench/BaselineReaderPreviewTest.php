<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Support\BaselineReaderPreview;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

function baselinePreviewFixture(): array
{
    $published = ['value' => 'Saved German', 'state' => 'linked', 'path' => 'lang/de/ui.php', 'sha256' => str_repeat('a', 64)];

    return ['schema' => 1, 'mode' => 'isolated_baseline_preview', 'exported_at' => '2026-10-19 UTC',
        'chains' => ['de-at' => ['de-at', 'de', 'en'], 'de' => ['de', 'en'], 'en' => ['en']],
        'items' => ['ui.states.all' => ['key' => 'ui.states.all', 'key_id' => 17, 'locales' => [
            'de-at' => ['published' => null, 'editing' => ['value' => 'Unpublished draft', 'revision_id' => 4, 'approval' => 'approved', 'recorded_decision' => 'approved']],
            'de' => ['published' => $published, 'editing' => null],
            'en' => ['published' => [...$published, 'value' => 'Saved English', 'path' => 'lang/en/ui.php'], 'editing' => null],
        ]]]];
}

it('keeps fallback, draft and published values separate', function () {
    $service = new BaselineReaderPreview;
    $data = baselinePreviewFixture();
    $result = $service->selection($data, 'ui.states.all', 'de-at');
    expect($result['result']['value'])->toBe('Saved German')
        ->and($result['result']['fallback'])->toBeTrue()
        ->and($result['selected']['locales']['de-at']['editing']['value'])->toBe('Unpublished draft');
    $data['items']['ui.states.all']['locales']['de-at']['published'] = [...$data['items']['ui.states.all']['locales']['de']['published'], 'value' => ''];
    expect($service->selection($data, 'ui.states.all', 'de-at')['result']['value'])->toBe('');
    expect($service->selection($data, 'ui.states.all', 'bad')['chain'])->toBe([])
        ->and($service->selection($data, 'unknown.key', 'de-at')['result'])->toBeNull()
        ->and($service->selection(null, '', 'de-at')['snapshot'])->toBeNull();
});

it('renders the Flux preview and switches locale without a database connection', function () {
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn(baselinePreviewFixture());
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->set('activeTab', 'baseline-preview')
        ->assertSee('Read-only rehearsal snapshot')
        ->assertSee('Saved German')
        ->assertSee('Unpublished draft')
        ->assertDontSeeHtml('calloue.text')
        ->assertSee('No published value evidence')
        ->assertDontSee('No source file')
        ->assertSee('Approved, not yet published by this preview')
        ->set('baselineChain', 'en')
        ->assertSee('Saved English')
        ->assertDontSee('Unpublished draft')
        ->set('baselineKey', 'missing.key')
        ->assertSee('Select an available key and locale chain.');
});

it('handles a missing export without connecting to the rehearsal database', function () {
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn(null);
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')->assertSee('No baseline preview available');
});

it('shows evidenced option sources without claiming a scalar assignment', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.states.all']['locales']['de']['published']['state'] = 'unassigned_key';
    $data['items']['ui.states.all']['unassigned_context'] = [
        'kind' => 'option_source_evidenced', 'owner' => 'ui.option.group', 'identity' => 'sample',
        'sources' => [['path' => 'example.blade.php', 'line' => 42, 'identity_expression' => '$option', 'fallback_expression' => '$label']],
    ];
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')
        ->assertSee('Option source identified')->assertSee('ui.option.group')
        ->assertSee('example.blade.php:42')->assertSee('$option')->assertSee('$label')
        ->assertSee('Unassigned key');
});

it('does not equate an open source assignment with unused translations', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.states.all']['unassigned_context'] = ['kind' => 'source_assignment_open'];
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)->set('activeTab', 'baseline-preview')
        ->assertSee('Source assignment remains open')
        ->assertSee('This does not mean that the translation is unused or may be deleted.');
});

it('preserves multiline published values and drafts without adding formatting whitespace', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.states.all']['locales']['de']['published']['value'] = "First line\nSecond line";
    $data['items']['ui.states.all']['locales']['de-at']['editing']['value'] = "Draft first\nDraft second";
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    $html = Livewire::test(TranslationWorkbenchWorkflow::class)
        ->set('activeTab', 'baseline-preview')->html();

    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$html);
    $xpath = new DOMXPath($document);
    $values = $xpath->query('//textarea[@readonly]');
    expect($values->length)->toBe(1)
        ->and($values->item(0)->textContent)->toBe("First line\nSecond line");
    $displayed = [];
    foreach ($xpath->query('//*[@data-slot="text" and contains(concat(" ", normalize-space(@class), " "), " whitespace-pre-wrap ")]') as $value) {
        $displayed[] = trim($value->textContent);
    }
    expect($displayed)->toContain("First line\nSecond line", "Draft first\nDraft second");
});

it('groups key evidence independently of fallback and preserves mixed locale states', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.states.all']['locales']['en']['published']['state'] = 'unassigned_key';
    $service = new BaselineReaderPreview;
    $groups = $service->keyGroups($data);
    expect($groups['linked'])->toBe(['ui.states.all'])
        ->and($groups['source_assignment_open'])->toBe(['ui.states.all'])
        ->and($groups['blocked_structure'])->toBe([]);
    $data['items']['ui.states.all']['unassigned_context'] = ['kind' => 'option_source_evidenced'];
    expect($service->keyGroups($data)['option_source_evidenced'])->toBe(['ui.states.all'])
        ->and($service->selection($data, 'ui.states.all', 'de', 'option_source_evidenced')['result']['value'])->toBe('Saved German')
        ->and($service->selection($data, 'ui.states.all', 'de', 'blocked_structure')['selected'])->toBeNull()
        ->and($service->selection($data, '', 'de', 'unknown')['keys'])->toBe([]);
});

it('switches to a matching key and clears stale details when a status has no matches', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.type'] = $data['items']['ui.states.all'];
    $data['items']['ui.type']['key'] = 'ui.type';
    foreach (['de', 'en'] as $locale) {
        $data['items']['ui.type']['locales'][$locale]['published']['state'] = 'blocked_structure';
    }
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->set('activeTab', 'baseline-preview')
        ->set('baselineFilter', 'blocked_structure')->assertSet('baselineKey', 'ui.type')
        ->assertSee('Blocked key structure (1)')
        ->set('baselineChain', 'en')->assertSet('baselineKey', 'ui.type')
        ->set('baselineFilter', 'option_source_evidenced')->assertSet('baselineKey', '')
        ->assertSee('No keys match this assignment status in the snapshot.')
        ->assertDontSee('Saved English')
        ->set('baselineFilter', 'invalid')->assertSet('baselineFilter', 'all')
        ->assertSet('baselineKey', 'ui.states.all');
});

it('shows distinct blocked source occurrences without treating test IDs as production links', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.type'] = $data['items']['ui.states.all'];
    $data['items']['ui.type']['key'] = 'ui.type';
    $data['items']['ui.type']['locales']['en']['published']['state'] = 'blocked_structure';
    $data['items']['ui.type']['blocked_sources'] = [
        ['finding_id' => 101, 'path' => 'example.blade.php', 'line' => 4, 'column' => 7, 'expression' => "__('ui.type')"],
        ['finding_id' => 102, 'path' => 'example.blade.php', 'line' => 9, 'column' => 7, 'expression' => '<script>unsafe</script>'],
    ];
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->set('activeTab', 'baseline-preview')->assertDontSee('Key structure needs a decision')
        ->set('baselineFilter', 'blocked_structure')
        ->assertSee('Key structure needs a decision')->assertSee('Affected source occurrences')->assertSee('(2)')
        ->assertSee('#101')->assertSee('#102')->assertSee('4 / 7')->assertSee('9 / 7')
        ->assertSee('<script>unsafe</script>')->assertDontSeeHtml('<script>unsafe</script>')
        ->assertSee('Test finding IDs and source positions refer to the saved baseline, not the current production database or current source files.');
});

it('checks explicit correction proposals and clears results when their input changes', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.type'] = $data['items']['ui.states.all'];
    $data['items']['ui.type']['key'] = 'ui.type';
    $data['items']['ui.type']['locales']['en']['published']['state'] = 'blocked_structure';
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->set('activeTab', 'baseline-preview')->set('baselineFilter', 'blocked_structure')
        ->assertSet('baselineProposedKey', '')
        ->set('baselineProposedKey', 'ui.type.type')->call('checkBaselineProposal')
        ->assertSee('No conflict found in this snapshot')->assertSee('Current file comparison')
        ->set('baselineProposedKey', 'ui.states.all')->assertDontSee('No conflict found in this snapshot')
        ->assertDontSee('Current file comparison')
        ->call('checkBaselineProposal')->assertSee('The target conflicts with exported keys')
        ->set('baselineFilter', 'all')->assertSet('baselineProposedKey', '')
        ->assertDontSee('The target conflicts with exported keys');
});

it('shows additional exact locales without extending the chosen fallback chain', function () {
    $data = baselinePreviewFixture();
    $data['items']['ui.states.all']['locales']['de-ch'] = [
        'published' => [...$data['items']['ui.states.all']['locales']['de']['published'],
            'value' => 'Swiss-only value', 'path' => 'lang/de-ch/ui.php'], 'editing' => null,
    ];
    $data['items']['ui.states.all']['locales']['de']['published'] = null;
    $data['items']['ui.states.all']['locales']['en']['published'] = null;
    $service = Mockery::mock(BaselineReaderPreview::class)->makePartial();
    $service->shouldReceive('load')->andReturn($data);
    app()->instance(BaselineReaderPreview::class, $service);
    expect($service->selection($data, 'ui.states.all', 'en')['result'])->toBeNull();
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->set('activeTab', 'baseline-preview')->set('baselineChain', 'en')
        ->assertDontSee('Swiss-only value')->assertSee('No published value')
        ->set('baselineAllLocales', true)->assertSee('Swiss-only value')
        ->assertSee('lang/de-ch/ui.php')->assertSee('No published value')
        ->set('baselineAllLocales', false)->assertDontSee('Swiss-only value');
});
