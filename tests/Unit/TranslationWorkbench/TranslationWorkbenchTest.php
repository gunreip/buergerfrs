<?php

use App\Models\User;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementWriter;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbench;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

function translationWorkbenchFixture(): array
{
    $userSummary = Mockery::mock(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class);
    $userSummary->shouldReceive('find')->andReturn(null)->byDefault();
    app()->instance(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class, $userSummary);
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize');
    app()->instance(TranslationWorkbenchEditorAccess::class, $access);
    $locales = Mockery::mock(TranslationWorkbenchEditorLocales::class);
    $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => ['de-AT']]);
    app()->instance(TranslationWorkbenchEditorLocales::class, $locales);
    $publisher = Mockery::mock(GenerationPublisher::class);
    $publisher->shouldReceive('recent')->andReturn([]);
    app()->instance(GenerationPublisher::class, $publisher);
    $sourceWriter = Mockery::mock(SourceReplacementWriter::class);
    $sourceWriter->shouldReceive('recent')->andReturn([]);
    app()->instance(SourceReplacementWriter::class, $sourceWriter);
    $editor = Mockery::mock(GenerationEditor::class);
    $editor->shouldReceive('sharedSummaries')->andReturn([])->byDefault();
    $editor->shouldReceive('workflowStates')->andReturn([])->byDefault();
    $editor->shouldReceive('reasonLocales')->andReturn([])->byDefault();
    $editor->shouldReceive('changeReasons')->andReturn(['Previously saved change reason'])->byDefault();
    $editor->shouldReceive('workflowReasons')->andReturn([])->byDefault();
    $editor->shouldReceive('assignmentReasons')->andReturn(['Previously saved reason'])->byDefault();
    $editor->shouldReceive('recentScans')->andReturn([])->byDefault();
    $editor->shouldReceive('scanUsageReasons')->andReturn([])->byDefault();
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([], 0, 25))->byDefault();
    $editor->shouldReceive('totals')->andReturn(['database' => 'fixture', 'active' => 1, 'archived' => 0, 'keys' => 1, 'revisions' => 0]);
    $editor->shouldReceive('keys')->andReturn([['key_id' => 8, 'namespace' => '', 'full_name' => 'ui.button.save']]);
    app()->instance(GenerationEditor::class, $editor);
    $row = ['id' => 11, 'path' => 'example.blade.php', 'source_line' => 2, 'source_column' => 3, 'raw_expression' => "__('<script>test</script>')", 'status' => 'active', 'predecessor_id' => null, 'key_id' => 8, 'link_id' => 4, 'full_name' => 'ui.button.save', 'raw_source_code_literal' => 'ui.button.save', 'raw_literal' => null, 'suggested_literal' => 'Save', 'suggested_key' => 'fixture.original.save'];
    $context = ['finding' => $row, 'revision' => null, 'published' => [['value' => '0', 'path' => 'lang/en/ui.php', 'current' => true]], 'usages' => [], 'usageCount' => 1, 'successor' => null, 'history' => []];

    return [$editor, $context];
}

it('opens the new editor and saves a draft without publishing or conflating evidence and revisions', function () {
    [$editor,$context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturnUsing(function () use (&$context) {
        return $context;
    });
    $editor->shouldReceive('saveDrafts')->once()->with(11, 8, 4, ['en' => ['value' => 'Save', 'revision' => null], 'de' => ['value' => '0', 'revision' => null]], 7, 'Explicit draft')->andReturnUsing(function () use (&$context) {
        $context['revision'] = ['id' => 19, 'value' => '0'];

        return ['revision' => 19];
    });
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->assertSee('current_generation_v2')
        ->call('open', 11)->assertSet('editorOpen', true)->assertSet('value', '')->assertDontSeeHtml('<script>test</script>')
        ->call('copyPublished')->assertSet('value', '0')->set('reason', 'Explicit draft')->call('saveDraft')
        ->assertHasNoErrors()->assertSet('context.revision.id', 19)->assertSee('Publication is a separate step.');
});

it('requires a confirmed valid assignment context and displays a stale assignment error', function () {
    [$editor,$context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('assign')->once()->with(11, 8, 4, 7, 'Reviewed assignment', null)->andThrow(new RuntimeException('The key assignment changed.'));
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('assignmentReason', 'Reviewed assignment')->call('confirmAssignment')->assertSet('confirmOpen', true)
        ->call('assign')->assertHasErrors('assignment')->assertSee('The key assignment changed.');
});

it('denies the editor to users without editing permission', function () {
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize')->andThrow(new AuthorizationException);
    app()->instance(TranslationWorkbenchEditorAccess::class, $access);
    Livewire::test(TranslationWorkbench::class)->assertForbidden();
});

it('routes the normal workbench to the new generation editor', function () {
    $route = app('router')->getRoutes()->getByName('admin.translation-workbench');
    expect($route->getActionName())->toBe(TranslationWorkbench::class);
});

function generationReviewFixture(): array
{
    [$editor, $context] = translationWorkbenchFixture();
    $context['revision'] = ['id' => 19, 'value' => '0'];
    $context['review'] = ['fingerprint' => str_repeat('a', 64), 'approved' => false, 'latest' => null, 'context' => ['usages' => [['finding' => 11, 'link' => 4]]]];
    $user = new User;
    $user->id = 7;
    auth()->setUser($user);

    return [$editor, $context];
}

it('confirms the saved revision through its own modal and records an approval', function () {
    [$editor, $context] = generationReviewFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('review')->once()->with(19, str_repeat('a', 64), true, 7, 'Checked English wording')->andReturn(['review' => 1]);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->call('confirmReview', true)
        ->assertSet('reviewOpen', true)->assertSee('Confirm revision approval')->assertSee('Active linked findings')
        ->set('reviewReason', 'Checked English wording')->call('review')->assertHasNoErrors()
        ->assertSet('reviewOpen', false)->assertSee('no language files have been written.');
});

it('blocks unsaved values and missing review confirmation', function () {
    [$editor, $context] = generationReviewFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('review');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->set('value', 'Unsaved')
        ->call('confirmReview', true)->assertHasErrors('review')->assertSet('reviewOpen', false)
        ->set('reviewReason', 'No confirmation')->call('review')->assertHasErrors('review');
});

it('keeps a stale confirmation open with an error and permits explicit revocation', function () {
    [$editor, $context] = generationReviewFixture();
    $context['review']['approved'] = true;
    $context['review']['latest'] = ['decision' => 'approved', 'actor_id' => 7, 'occurred_at' => '2026-10-21', 'reason' => 'Initial review'];
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('review')->once()->with(19, str_repeat('a', 64), false, 7, 'Recheck this value')->andThrow(new RuntimeException('Review context changed'));
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->assertSee('Revoke approval')->call('confirmReview', false)
        ->set('reviewReason', 'Recheck this value')->call('review')->assertHasErrors('review')->assertSet('reviewOpen', true)
        ->assertSee('Review context changed');
});

function generationPublicationFixture(): array
{
    [$editor, $context] = generationReviewFixture();
    $context['review']['approved'] = true;
    $publisher = app(GenerationPublisher::class);
    $plan = ['run' => '11111111-1111-4111-8111-111111111111', 'fingerprint' => str_repeat('b', 64), 'state' => 'prepared', 'actor' => 7, 'reason' => 'Publish reviewed English', 'error' => null,
        'changes' => [['path' => 'en/ui.php', 'key_id' => $context['finding']['key_id'], 'key' => 'ui.button.save', 'locale' => 'en', 'revision' => 19, 'before' => 'Old', 'after' => '0']],
        'files' => ['en/ui.php' => ['before' => 'old', 'after' => 'new', 'current' => 'old']]];
    $editor->shouldReceive('inspect')->andReturn($context);

    return [$publisher, $plan];
}

it('prepares a preview before explicitly writing and shows the completed receipt', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $publisher->shouldReceive('prepare')->once()->withArgs(fn ($run, $revisions, $actor, $reason, $fingerprints) => $revisions === [19] && $actor === 7 && $reason === 'Publish reviewed English' && $fingerprints === [str_repeat('a', 64)])
        ->andReturn(['run' => $plan['run'], 'fingerprint' => $plan['fingerprint']]);
    $publisher->shouldReceive('inspect')->with($plan['run'])->andReturnUsing(function () use (&$plan) {
        return $plan;
    });
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('publicationReason', 'Publish reviewed English')->call('preparePublication')
        ->assertHasNoErrors()->assertSet('publicationOpen', true)->assertSee('lang/en/ui.php')->assertSee('Confirm / resume publication');
    $editorPath = str_replace('%2F', '/', rawurlencode(str_replace('\\', '/', lang_path('en/ui.php'))));
    $component->assertSeeHtml('href="vscode://vscode-remote/wsl+'.rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')).$editorPath.':1:1"');
    $publisher->shouldReceive('publish')->once()->with($plan['run'], $plan['fingerprint'])->andReturnUsing(function () use (&$plan) {
        $plan['state'] = 'complete';
        $plan['files']['en/ui.php']['current'] = 'new';

        return ['kind' => 'completed'];
    });
    $component->call('publish')->assertHasNoErrors()->assertSet('publication.state', 'complete')->assertSee('File matches the publication result.');
});

it('renders a distinct VSC tooltip for the first and subsequent publication entries after reopening', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $plan['changes'][] = array_replace($plan['changes'][0], ['path' => 'de/ui.php', 'locale' => 'de', 'revision' => 20]);
    $plan['files']['de/ui.php'] = $plan['files']['en/ui.php'];
    $publisher->shouldReceive('inspect')->with($plan['run'])->andReturn($plan);
    $component = Livewire::test(TranslationWorkbench::class);
    for ($open = 0; $open < 2; $open++) {
        $component->call('openPublication', $plan['run'])->assertHasNoErrors();
        $dom = new DOMDocument;
        @$dom->loadHTML($component->html());
        $xpath = new DOMXPath($dom);
        $tooltips = $xpath->query('//ui-tooltip[a[@aria-label="Open language file in VS Code"]]');
        expect($tooltips->length)->toBe(2);
        foreach ($tooltips as $index => $tooltip) {
            expect($tooltip->getAttribute('wire:key'))->toBe('publication-vsc-'.$plan['run'].'-'.(19 + $index));
            $content = $xpath->query('./div[@data-flux-tooltip-content]', $tooltip);
            expect($content->length)->toBe(1)
                ->and($content->item(0)->textContent)->toContain($index === 0 ? '/en/ui.php' : '/de/ui.php');
        }
        $component->set('publicationOpen', false);
    }
});

it('shows the preparing account rather than the viewer and retains a missing account ID', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $plan['actor'] = 9;
    $publisher->shouldReceive('inspect')->with($plan['run'])->andReturn($plan);
    $summary = app(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class);
    $summary->shouldReceive('find')->with(9)->andReturn(
        ['id' => 9, 'name' => 'Preparing Person', 'email' => 'preparer@example.test', 'roles' => ['Admin']],
        null,
    );
    $component = Livewire::test(TranslationWorkbench::class)
        ->call('openPublication', $plan['run'])->assertSee('Preparing Person')->assertSee('preparer@example.test')
        ->assertSee('Current roles')->assertSee('Admin')->assertSee('#9');
    $component->call('openPublication', $plan['run'])->assertSee('Account no longer available')->assertSee('#9');
});

it('blocks publication of unsaved changes and another users prepared run', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $publisher->shouldNotReceive('prepare');
    $publisher->shouldNotReceive('publish');
    $plan['actor'] = 9;
    $publisher->shouldReceive('inspect')->andReturn($plan);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->set('value', 'Unsaved')
        ->set('publicationReason', 'Publish reviewed English')->call('preparePublication')->assertHasErrors('publication')
        ->call('openPublication', $plan['run'])->call('publish')->assertHasErrors('publication');
});

it('requires a fresh closure preview before releasing a publication', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $publisher->shouldReceive('inspect')->andReturn($plan);
    $publisher->shouldReceive('closurePreview')->once()->with($plan['run'])->andReturn(['fingerprint' => 'closure-hash', 'snapshot' => []]);
    $publisher->shouldReceive('close')->once()->with($plan['run'], 'closure-hash', 7, 'Cancel this plan')->andReturn(['kind' => 'closed_without_writing']);
    Livewire::test(TranslationWorkbench::class)->call('openPublication', $plan['run'])
        ->set('closureReason', 'Cancel this plan')->call('closePublication')->assertHasErrors('publication')
        ->call('previewClosure')->call('closePublication')->assertHasNoErrors();
});

it('shows a read-only exact source preview using the configured source locale', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('sourcePreview')->once()->with(11, 8, 4, 'en')->andReturn([
        'finding' => 11, 'key_id' => 8, 'key' => 'ui.button.save', 'path' => 'example.blade.php', 'line' => 2, 'column' => 3,
        'before' => "__('Save')", 'after' => "__('ui.button.save')", 'already_set' => false, 'blocker' => null,
        'language_path' => 'en/ui.php', 'source_locale' => 'en', 'published_value' => 'Save', 'ready' => true,
    ]);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->set('locale', 'de')->call('previewSource')
        ->assertHasNoErrors()->assertSet('sourcePreviewOpen', true)->assertSee('Source replacement preview')
        ->assertSee('Exact source preview')->assertSee('Preparation records a plan without writing the file.');
});

it('shows a stale source error without leaving an earlier preview visible', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('sourcePreview')->once()->andThrow(new RuntimeException('Source occurrence changed or moved.'));
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->call('previewSource')
        ->assertHasErrors('sourcePreview')->assertSet('sourcePreviewOpen', false)->assertSet('sourcePreview', []);
});

it('distinguishes source preview states and displays their next steps', function () {
    foreach ([
        [['already_set' => true, 'ready' => false, 'blocker' => 'The selected source occurrence already uses its assigned key.'], 'Assigned key already present', 'No source replacement is needed'],
        [['already_set' => false, 'ready' => true, 'blocker' => null], 'Ready for source replacement', 'Choose a reason'],
        [['ready' => false, 'blocker' => 'Save and approve a revision in the source language before replacing the literal.'], 'Source replacement blocked', 'Save and approve the source-language text'],
        [['ready' => false, 'blocker' => 'Publish the approved source-language revision first. The file does not contain that value.'], 'Source replacement blocked', 'Publish the approved source-language revision'],
        [['ready' => false, 'blocker' => 'Source or language file is missing or unreadable.'], 'Source replacement blocked', 'Check the source-language file'],
    ] as [$previewStatus, $heading, $nextStep]) {
        $html = view('translation-workbench::livewire.translation-workbench.shared.source-preview-status', compact('previewStatus'))->render();
        expect($html)->toContain($heading, $nextStep)->not->toContain('wire:click');
    }
});

it('prepares a durable source plan without applying it and opens a separate confirmation', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $preview = ['finding' => 11, 'key_id' => 8, 'key' => 'ui.button.save', 'path' => 'example.blade.php', 'line' => 2, 'column' => 3,
        'before' => "__('Save')", 'after' => "__('ui.button.save')", 'already_set' => false, 'blocker' => null,
        'language_path' => 'en/ui.php', 'source_locale' => 'en', 'published_value' => 'Save', 'ready' => true, 'fingerprint' => 'source-hash'];
    $editor->shouldReceive('sourcePreview')->andReturn($preview);
    $writer = app(SourceReplacementWriter::class);
    $writer->shouldReceive('prepare')->once()->with(11, 'en', 'source-hash', 7, 'Use approved key')->andReturn('plan-id');
    $writer->shouldReceive('inspect')->with('plan-id')->andReturn(['run' => 'plan-id', 'actor' => 7, 'reason' => 'Use approved key', 'preview' => $preview, 'state' => 'pending', 'file_state' => 'original', 'result' => null, 'impacts' => [[
        'finding' => 91, 'kind' => 'Position changed', 'key_id' => 37, 'key' => 'ui.independent.label',
        'before_line' => 4, 'before_column' => 12, 'after_line' => 4, 'after_column' => 23,
    ]]]);
    $writer->shouldNotReceive('apply');
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->call('previewSource')->set('sourceReason', 'Use approved key')->call('prepareSource')
        ->assertHasNoErrors()->assertSet('sourcePreviewOpen', false)->assertSet('sourceRunOpen', true)->assertSee('Confirm source replacement / resume')->assertSee('Affected findings')->assertSee('ui.independent.label')->assertSee('4:12')->assertSee('4:23');
});

it('opens the successor after source confirmation without losing the recorded plan', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->with(12, 'de')->andReturn($context);
    $writer = app(SourceReplacementWriter::class);
    $plan = ['run' => 'plan-id', 'actor' => 7, 'reason' => 'Use approved key', 'preview' => ['finding' => 11, 'path' => 'example.blade.php', 'key' => 'ui.button.save', 'before' => "__('Save')", 'after' => "__('ui.button.save')"], 'state' => 'pending', 'file_state' => 'written', 'result' => null];
    $writer->shouldReceive('inspect')->with('plan-id')->andReturnUsing(function () use (&$plan) {
        return $plan;
    });
    $writer->shouldReceive('apply')->once()->with('plan-id', 7)->andReturnUsing(function () use (&$plan) {
        $plan['state'] = 'complete';
        $plan['result'] = ['successors' => [['predecessor' => 11, 'successor' => 12]]];

        return $plan['result'];
    });
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('openSourceRun', 'plan-id')->set('editorOpen', true)->call('applySource')
        ->assertHasNoErrors()->assertSet('selected', 12)->assertSet('sourceRun.state', 'complete');
});


it('shows navigable predecessor and successor findings in source history', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $context['history'] = [['id' => 42, 'event_type' => 'source_rewrite_reconciled', 'actor_id' => 7,
        'reason' => 'Confirmed source change', 'occurred_at' => '2026-10-21 12:00:00',
        'successors' => [['predecessor' => 91, 'successor' => 92]]]];
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSee('Source replacement recorded')->assertSee('wire:click="open(91)"', false)->assertSee('wire:click="open(92)"', false);
});

it('identifies scanner receipts without inventing an actor', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $context['history'] = [['id' => 'scan-receipt', 'event_type' => 'scan_finding_removed', 'actor_id' => null,
        'reason' => 'Source occurrence absent from the completed scan scope; finding archived.',
        'occurred_at' => '2026-10-21 12:00:00', 'successors' => []]];
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSee('Scanner: finding archived')->assertSee('scan-receipt')
        ->assertSee('Source occurrence absent from the completed scan scope; finding archived.');
});


it('opens saved scan results with independent pagination and finding navigation', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([
        ['id' => 11, 'predecessor' => null, 'path' => 'example.blade.php', 'source_line' => 2],
    ], 1, 25, 1, ['pageName' => 'scanResultsPage']));
    $editor->shouldReceive('scanPage')->with('scan-id', 'removed', 1)->andReturn(new LengthAwarePaginator([], 0, 25, 1, ['pageName' => 'scanResultsPage']));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')
        ->assertSet('scanOpen', true)->assertSee('example.blade.php:2')
        ->set('scanCategory', 'removed')->assertSee('No results in this category.');
});


it('explains why a scan file was preserved without inventing a finding', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'review_files', 1)->andReturn(new LengthAwarePaginator([
        ['id' => null, 'predecessor' => null, 'path' => 'pending.blade.php', 'source_line' => null, 'preserved_count' => 3,
            'issues' => [['line' => 17, 'column' => 9, 'reason' => 'key_like_argument_without_lang_evidence']]],
    ], 1, 25, 1, ['pageName' => 'scanResultsPage']));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->set('scanCategory', 'review_files')
        ->assertSee('pending.blade.php')->assertSee('17:9')->assertSee('Existing findings preserved: 3')
        ->assertSee('Translation key found, but no verified source-language value is available for this scan.');
});

it('opens preserved findings from a scan review and returns to its result category', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('preservedFindingsPage')->with('scan-id', 'pending.blade.php', 1)->andReturn(new LengthAwarePaginator([
        ['id' => 31, 'status' => 'active', 'source_line' => 9, 'source_column' => 4, 'raw_source_code_literal' => 'Save', 'full_name' => 'ui.button.save'],
    ], 1, 25, 1, ['pageName' => 'preservedFindingsPage']));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->set('scanCategory', 'review_files')
        ->call('openPreservedFindings', 'pending.blade.php')->assertSet('scanReviewPath', 'pending.blade.php')
        ->assertSee('ui.button.save')->assertSee('wire:click="open(31)"', false)
        ->call('closePreservedFindings')->assertSet('scanReviewPath', '')->assertSet('scanCategory', 'review_files');
});

it('shows escaped scan captures separately from finding records', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanCapturesPage')->with('scan-id', 'pending.blade.php', 1)->andReturn(new LengthAwarePaginator([
        ['capture_index' => 0, 'source_line' => 7, 'source_column' => 3, 'raw_expression' => "__('</textarea><script>alert(1)</script>')", 'raw_literal' => null,
            'found_translation_key' => null, 'reason' => 'dynamic_or_nonliteral_argument', 'source_sha256' => str_repeat('a', 64)],
    ], 1, 25, 1, ['pageName' => 'scanCapturesPage']));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->set('scanCategory', 'review_files')
        ->call('openScanCaptures', 'pending.blade.php')->assertSet('scanCaptureView', true)
        ->assertSee('Captured source occurrences')->assertSee('Line 7, column 3')
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->call('closePreservedFindings')->assertSet('scanReviewPath', '')->assertSet('scanCategory', 'review_files');
});

it('saves an explicit usage decision for the selected scan capture', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanCapturesPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $context = ['run' => 'scan-id', 'path' => 'pending.php', 'index' => 2,
        'capture' => ['raw_expression' => '__($databaseValue)'], 'decision' => null];
    $editor->shouldReceive('scanUsage')->with('scan-id', 'pending.php', 2)->andReturnUsing(fn () => $context);
    $editor->shouldReceive('decideScanUsage')->once()->with('scan-id', 'pending.php', 2, 'database_value', 'Database output', 7, 0)->andReturn([]);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->call('openScanCaptures', 'pending.php')
        ->call('openScanUsage', 2)->assertSee('Classify source usage')->assertSee('__($databaseValue)')
        ->set('scanUsageChoice', 'database_value')->set('scanUsageReason', 'Database output')->call('saveScanUsage')
        ->assertHasNoErrors()->assertSee('Usage decision saved. The next scan checks the unchanged source before applying it. No translation or source file was changed.');
});

it('keeps a stale scan decision open and shows its error', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanCapturesPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanUsage')->andReturn(['run' => 'scan-id', 'path' => 'pending.php', 'index' => 2,
        'capture' => ['raw_expression' => '__($databaseValue)'], 'decision' => null]);
    $editor->shouldReceive('decideScanUsage')->once()->andThrow(new RuntimeException('Source changed. Run a fresh scan before deciding.'));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->call('openScanCaptures', 'pending.php')
        ->call('openScanUsage', 2)->set('scanUsageChoice', 'translate')->set('scanUsageReason', 'Review usage')->call('saveScanUsage')
        ->assertHasErrors('scanUsage')->assertSet('scanUsageContext.index', 2)->assertSet('scanUsageNotice', '');
});


it('offers saved reasons and presets while preserving custom text when classification changes', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanCapturesPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanUsage')->andReturn(['run' => 'scan-id', 'path' => 'pending.php', 'index' => 2,
        'capture' => ['raw_expression' => '__($databaseValue)'], 'decision' => null]);
    $editor->shouldReceive('scanUsageReasons')->with('database_value')->andReturn(['Saved database reason']);
    $editor->shouldReceive('scanUsageReasons')->with('translate')->andReturn(['Saved translation reason']);
    $test = Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->call('openScanCaptures', 'pending.php')
        ->call('openScanUsage', 2)->set('scanUsageChoice', 'database_value')
        ->assertSee('Saved database reason')->assertSee('Value comes from the database and is displayed unchanged.')
        ->set('scanUsageReason', 'Saved database reason')
        ->assertSet('scanUsageReason', 'Saved database reason')
        ->set('scanUsageReason', 'My adjusted reason')->set('scanUsageChoice', 'translate')
        ->assertSet('scanUsageReason', 'My adjusted reason')
        ->assertSee('Saved translation reason')->assertDontSee('Saved database reason')
        ->assertSet('scanUsageReason', 'My adjusted reason');
});


it('shows saved usage decisions beside their captured source without marking the scan complete', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $base = ['source_line' => 4, 'source_column' => 1, 'raw_expression' => '__($value)',
        'raw_literal' => null, 'found_translation_key' => null, 'reason' => 'dynamic_or_nonliteral_argument',
        'source_sha256' => 'hash', 'can_classify' => true];
    $editor->shouldReceive('scanCapturesPage')->andReturn(new LengthAwarePaginator([
        [...$base, 'capture_index' => 0, 'usage_decision' => ['decision' => 'database_value',
            'reason' => '<script>Stored reason</script>', 'actor_id' => 7, 'occurred_at' => '2026-10-21 10:00:00']],
        [...$base, 'capture_index' => 1, 'usage_decision' => null],
        [...$base, 'capture_index' => 2, 'usage_decision' => ['decision' => 'translate',
            'reason' => 'Still needs translation', 'actor_id' => 8, 'occurred_at' => '2026-10-21 11:00:00']],
    ], 3, 25));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->call('openScanCaptures', 'pending.php')
        ->assertSee('DatabaseValue · decision saved')->assertSee('Translate · work remains open')
        ->assertSee('No usage decision saved')->assertSee('Review usage decision')
        ->assertSee('It does not change this historical scan result.', false)
        ->assertDontSeeHtml('<script>Stored reason</script>');
});


it('opens the source behind a considered decision and returns to the original scan', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanCapturesPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('consideredScanUsage')->once()->with('result-scan', 42)->andReturn([
        'run' => 'origin-scan', 'path' => 'pending.php', 'index' => 28,
        'capture' => ['raw_expression' => '__($value)'],
        'decision' => ['id' => 43, 'decision' => 'translate', 'reason' => 'Updated reason',
            'actor_id' => 7, 'occurred_at' => '2026-10-21 12:00:00'],
    ]);
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'result-scan')->set('scanCategory', 'usage_decisions')
        ->call('openConsideredScanUsage', 42)->assertHasNoErrors()
        ->assertSet('selectedScan', 'origin-scan')->assertSet('scanUsageContext.decision.id', 43)
        ->assertSet('paginators.scanCapturesPage', 2)->assertSet('scanUsageReason', 'Updated reason')
        ->assertSee('You are viewing the original captured source')
        ->call('closePreservedFindings')->assertSet('selectedScan', 'result-scan')
        ->assertSet('scanDecisionReturnRun', '')->assertSet('scanCategory', 'usage_decisions');
});

it('rejects a decision outside the selected scan without navigating away', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('consideredScanUsage')->andThrow(new RuntimeException('This decision was not considered by the selected scan.'));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'result-scan')->call('openConsideredScanUsage', 42)
        ->assertHasErrors('scanReview')->assertSet('selectedScan', 'result-scan')->assertSet('scanReviewPath', '');
});


it('refreshes saved scans and opens considered decisions directly from their count', function () {
    [$editor] = translationWorkbenchFixture();
    $scans = [];
    $editor->shouldReceive('recentScans')->andReturnUsing(function () use (&$scans) { return $scans; });
    $editor->shouldReceive('scanPage')->once()->with('saved-scan', 'usage_decisions', 1)
        ->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanPage')->with('saved-scan', 'usage_decisions', 1)
        ->andReturn(new LengthAwarePaginator([], 0, 25));
    $test = Livewire::test(TranslationWorkbench::class)->assertSee('No saved scans yet.')
        ->assertSee('php artisan translation-workbench:generation-scan --apply');
    $scans = [['id' => 'saved-scan', 'completed_at' => '2026-10-21 12:00:00',
        'confirmed' => 2, 'created' => 0, 'moved' => 0, 'removed' => 0, 'review_files' => 0, 'usage_decisions' => 1]];
    $test->call('$refresh')->assertDontSee('No saved scans yet.')
        ->assertSee('Open considered usage decisions')
        ->call('openScan', 'saved-scan', 'usage_decisions')->assertHasNoErrors()
        ->assertSet('scanCategory', 'usage_decisions')->assertSet('scanOpen', true);
});

it('does not open a scan with an invalid result category', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('saved-scan', 'invalid', 1)
        ->andThrow(new RuntimeException('Invalid scan or result category.'));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'saved-scan', 'invalid')
        ->assertHasErrors('scan')->assertSet('scanOpen', false);
});


it('toggles finding sorts, preserves filters and resets only the findings page', function () {
    translationWorkbenchFixture();
    $component = Livewire::test(TranslationWorkbench::class);
    foreach (['id', 'source', 'expression', 'suggested_key', 'key'] as $column) {
        $html = html_entity_decode($component->html(), ENT_QUOTES | ENT_HTML5);
        expect($html)->toContain('wire:click="sortFindings(\''.$column.'\')"');
    }
    $component->set('search', 'Save')->set('status', 'all')
        ->call('setPage', 3, 'findingsPage')->call('sortFindings', 'source')
        ->assertSet('findingsSort', 'source')->assertSet('findingsDirection', 'asc')
        ->assertSet('paginators.findingsPage', 1)->assertSet('search', 'Save')->assertSet('status', 'all')
        ->call('sortFindings', 'source')->assertSet('findingsDirection', 'desc')
        ->call('sortFindings', 'expression')->assertSet('findingsDirection', 'asc')
        ->call('sortFindings', 'suggested_key')->assertSet('findingsSort', 'suggested_key')->assertSet('findingsDirection', 'asc')
        ->call('sortFindings', 'suggested_key')->assertSet('findingsDirection', 'desc')
        ->call('sortFindings', 'key')->assertSet('findingsSort', 'key')
        ->call('sortFindings', 'id; DROP TABLE')->assertSet('findingsSort', 'key')
        ->call('sortFindings', 'id')->assertSet('findingsDirection', 'asc');
});


it('links findings to their source position in the configured VS Code WSL workspace', function () {
    [$editor, $context] = translationWorkbenchFixture();
    config(['translation-workbench.editor.vscode_wsl_distro' => 'Ubuntu-24.04']);
    $row = [...$context['finding'], 'path' => 'resources/views/example #1.blade.php'];
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$row], 1, 25));
    $path = str_replace('%2F', '/', rawurlencode(str_replace('\\', '/', base_path($row['path']))));
    Livewire::test(TranslationWorkbench::class)
        ->assertSeeHtml('href="vscode://vscode-remote/wsl+Ubuntu-24.04'.$path.':2:3"')
        ->assertSee('Open source in VS Code');
});


it('changes the findings page size without losing filters or sorting', function () {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)->set('search', 'Save')->call('sortFindings', 'key')
        ->call('setPage', 3, 'findingsPage')->set('perPage', 50)
        ->assertSet('paginators.findingsPage', 1)->assertSet('search', 'Save')
        ->assertSet('findingsSort', 'key')->assertSet('perPage', 50)
        ->set('perPage', 0)->assertSet('perPage', 25);
});


it('selects new assignment reasons without assigning a key and reuses saved reasons', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    Livewire::test(TranslationWorkbench::class)
        ->call('open', 11)
        ->assertSee('Previously saved reason')
        ->call('openAssignmentReason', '  Context-specific meaning  ')
        ->assertSet('newAssignmentReason', 'Context-specific meaning')
        ->assertSet('assignmentReasonOpen', true)
        ->assertSet('assignmentReason', '')
        ->call('createAssignmentReason')
        ->assertHasNoErrors()
        ->assertSet('assignmentReason', 'Context-specific meaning')
        ->assertSet('assignmentReasonOpen', false)
        ->assertSee('Context-specific meaning')
        ->call('openAssignmentReason', '  ')
        ->assertSet('newAssignmentReason', '')
        ->assertSet('assignmentReason', 'Context-specific meaning')
        ->call('createAssignmentReason')
        ->assertHasErrors(['newAssignmentReason']);
});


it('resets all edit context when switching datasets and creates fixtures on every activation', function () {
    translationWorkbenchFixture();
    $environment = Mockery::mock(\Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment::class);
    $environment->shouldReceive('currentToken')->andReturn('');
    $environment->shouldReceive('activate');
    $environment->shouldReceive('start')->twice()->andReturn('first-test-run', 'second-test-run');
    $environment->shouldReceive('stop')->once()->with('first-test-run');
    app()->instance(\Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment::class, $environment);
    Livewire::test(TranslationWorkbench::class)
        ->set('value', 'Unsaved working draft')->set('assignmentReason', 'Unsaved working reason')
        ->call('toggleTestcase')->assertSet('testToken', 'first-test-run')
        ->assertSet('value', '')->assertSet('assignmentReason', '')->assertSet('selected', 0)
        ->assertSee('UI test environment')
        ->set('value', 'Unsaved test draft')->call('toggleTestcase')
        ->assertSet('testToken', '')->assertSet('value', '')->assertSet('editorOpen', false)
        ->call('toggleTestcase')->assertSet('testToken', 'second-test-run');
});


it('starts assignment only from the current key and never preselects the immutable suggestion', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturnUsing(function () use (&$context) { return $context; });
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSet('assignmentKey', 'ui.button.save');
    $context['finding']['key_id'] = null;
    $context['finding']['link_id'] = null;
    $context['finding']['full_name'] = null;
    $component->call('open', 11)->assertSet('assignmentKey', '')
        ->set('assignmentReason', 'Keep the source-specific key')->call('confirmAssignment')
        ->assertHasErrors('assignmentKey')->assertSet('confirmOpen', false)
        ->assertSet('context.finding.suggested_key', 'fixture.original.save')
        ->set('assignmentKey', 'ui.button.save')->call('confirmAssignment')
        ->assertSet('assignmentSelection.key', 8)->assertSee('Link this finding to existing key #8.')
        ->assertSet('context.finding.suggested_key', 'fixture.original.save')
        ->set('assignmentKey', 'ui.button.changed')->call('assign')->assertHasErrors('assignment');
});

it('offers segments in the assignment select without extra navigation or writes', function () {
    config(['translation-workbench.allowed_first_translation_key_segments' => ['ui', 'admin', 'reports']]);
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSee('reports.')
        ->assertDontSee('Use this translation key')
        ->assertDontSee('This starting key does not meet the assignment rules.')
        ->assertSet('assignmentKey', 'ui.button.save');
    $keys = [['namespace' => '', 'full_name' => 'ui.button.save']];
    expect($component->instance()->assignmentKeyOptions($keys))->toBe(['ui.', 'admin.', 'reports.']);
    $component->set('assignmentKey', 'ui.')->assertSee('Incomplete translation key')->assertDontSee('New translation key');
    expect($component->instance()->assignmentKeyOptions($keys))->toBe(['ui.button.']);
    $component->set('assignmentKey', 'ui.button.')->assertSee('Incomplete translation key')->assertDontSee('New translation key');
    expect($component->instance()->assignmentKeyOptions($keys))->toBe(['ui.button.save']);
    $component->set('assignmentKeySearch', 'adm');
    expect($component->instance()->assignmentKeyOptions($keys))->toBe(['admin.']);
    $component->set('assignmentKeySearch', 'ui.button.sa');
    expect($component->instance()->assignmentKeyOptions($keys))->toBe(['ui.button.save']);
    $component->set('assignmentKey', 'ui.button.save')->assertSet('assignmentKeySearch', '')
        ->assertSee('New translation key')->assertDontSee('Incomplete translation key')
        ->call('openTranslationKey', 'reports.new.Report')
        ->call('createTranslationKey')->assertHasNoErrors()->assertSet('assignmentKey', 'reports.new.Report')
        ->assertSet('context.finding.suggested_key', 'fixture.original.save');
});

it('stages a new select key without writing and keeps cancelled or invalid proposals out of the assignment', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->call('openTranslationKey')->assertSet('translationKeyOpen', true)
        ->assertSet('newTranslationKey', 'ui.button.save')
        ->assertSee('Valid translation key')
        ->call('openTranslationKey', '  ui.actions.new  ')
        ->assertSet('newTranslationKey', 'ui.actions.new')
        ->assertSet('assignmentKey', 'ui.button.save')
        ->call('openTranslationKey', '   ')
        ->assertSet('newTranslationKey', 'ui.button.save')
        ->set('newTranslationKey', 'ui.cancelled.key')->set('translationKeyOpen', false)
        ->assertSet('assignmentKey', 'ui.button.save')
        ->call('openTranslationKey')->set('newTranslationKey', 'ui.type')
        ->assertSee('Invalid translation key')
        ->call('createTranslationKey')->assertHasErrors('newTranslationKey')
        ->assertSet('assignmentKey', 'ui.button.save')
        ->set('newTranslationKey', '  ui.button.new_key  ')->call('createTranslationKey')
        ->assertHasNoErrors()->assertSet('assignmentKey', 'ui.button.new_key')
        ->assertSet('translationKeyOpen', false)->assertSet('confirmOpen', false)
        ->assertSee('ui.button.new_key')->assertSet('context.finding.suggested_key', 'fixture.original.save')
        ->set('assignmentReason', 'Use a separate key for this context.')->call('confirmAssignment')
        ->assertSet('assignmentSelection.key', null)
        ->call('openTranslationKey')->set('newTranslationKey', 'ui.button.save')->call('createTranslationKey')
        ->assertSet('confirmOpen', false)->call('confirmAssignment')->assertSet('assignmentSelection.key', 8);
});

it('allows uppercase only in the final segment of newly authored translation keys', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11);
    foreach (['UI.button.save', 'ui.Button.save', 'pkg::ui.button.save', 'ui..save', 'ui.button.', 'ui.button.sa ve', 'ui.button.säve', 'ui.save'] as $invalid) {
        $component->call('openTranslationKey')->set('newTranslationKey', $invalid)
            ->call('createTranslationKey')->assertHasErrors('newTranslationKey')
            ->assertSet('assignmentKey', 'ui.button.save');
        $component->set('assignmentKey', $invalid)->set('assignmentReason', 'Test key rules')
            ->call('confirmAssignment')->assertHasErrors('assignmentKey')
            ->set('assignmentKey', 'ui.button.save');
    }
    $component->call('openTranslationKey')->set('newTranslationKey', 'ui.button.Save_2-now')
        ->call('createTranslationKey')->assertHasNoErrors('newTranslationKey')
        ->assertSet('assignmentKey', 'ui.button.Save_2-now')
        ->call('confirmAssignment')->assertHasNoErrors('assignmentKey')
        ->assertSet('context.finding.suggested_key', 'fixture.original.save');
});

it('starts translation in the active target and permits only its configured sublanguages', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    $editor->shouldReceive('inspect')->with(11, 'de-AT')->andReturn($context);
    $editor->shouldNotReceive('save');
    $editor->shouldNotReceive('saveDrafts');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSet('locale', 'de')->assertSet('loadedLocale', 'de')
        ->assertSee('Source language')->assertSee('Active target language')
        ->set('locale', 'de-AT')->assertHasNoErrors()->assertSet('loadedLocale', 'de-AT')
        ->set('locale', 'fr')->assertHasErrors('locale')->assertSet('loadedLocale', 'de-AT')
        ->set('locale', 'en')->assertHasErrors('locale')
        ->set('reason', 'Do not allow a foreign target')->call('saveDraft')->assertHasErrors('locale');
});

it('prefers current English source text and only falls back to captured text when missing', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $context['finding']['raw_literal'] = 'Captured English';
    $context['sourceText'] = 'Current English';
    $editor->shouldReceive('inspect')->andReturnUsing(function () use (&$context) { return $context; });
    $editor->shouldNotReceive('save');
    $editor->shouldNotReceive('saveDrafts');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSet('sourceLanguageValue', 'Current English')
        ->assertSeeHtml('lang="en"')->assertSeeHtml('lang="de"')
        ->assertDontSeeHtml('wire:click="copyCapturedSource"');
    $context['sourceText'] = '';
    $component->call('open', 11)->assertSet('sourceLanguageValue', '');
    $context['sourceText'] = '0';
    $component->call('open', 11)->assertSet('sourceLanguageValue', '0');
    $context['sourceText'] = null;
    $component->call('open', 11)->assertSet('sourceLanguageValue', 'Captured English');
});

it('copies the edited source text into the selected target draft only on request without saving', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('save');
    $editor->shouldNotReceive('saveDrafts');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('value', 'Existing draft')->set('sourceLanguageValue', "Edited :name\nSecond line")
        ->assertSet('value', 'Existing draft')
        ->call('copySourceToTarget')->assertHasNoErrors()->assertDispatched('workbench-select-translation-draft')
        ->assertSet('value', "Edited :name\nSecond line")
        ->assertSet('context.finding.suggested_literal', 'Save')
        ->set('locale', 'de-AT')->set('sourceLanguageValue', '0')
        ->call('copySourceToTarget')->assertSet('value', '0')
        ->set('sourceLanguageValue', '')->call('copySourceToTarget')->assertSet('value', '');
});


it('selects a change reason from search without saving a translation', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('save');
    $editor->shouldNotReceive('saveDrafts');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSee('Previously saved change reason')
        ->call('openChangeReason', '  Improve clarity  ')->assertSet('newChangeReason', 'Improve clarity')
        ->assertSet('reason', '')->call('createChangeReason')->assertHasNoErrors()
        ->assertSet('reason', 'Improve clarity')->assertSet('changeReasonOpen', false)
        ->assertSee('Improve clarity')->call('openChangeReason', ' ')->call('createChangeReason')
        ->assertHasErrors('newChangeReason')->assertSet('reason', 'Improve clarity');
});


it('selects sublanguages independently without replacing or saving the main language draft', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $locales = Mockery::mock(TranslationWorkbenchEditorLocales::class);
    $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => ['de-AT', 'de-CH']]);
    app()->instance(TranslationWorkbenchEditorLocales::class, $locales);
    $editor->shouldReceive('inspect')->with(11, 'de')->twice()->andReturn($context);
    $editor->shouldReceive('inspect')->with(11, 'de-AT')->once()->andReturn(array_replace($context, ['revision' => ['value' => 'Existing Austrian draft']]));
    $editor->shouldReceive('inspect')->with(11, 'de-CH')->once()->andReturn($context);
    $editor->shouldNotReceive('save');
    $editor->shouldNotReceive('saveDrafts');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSet('selectedSubLanguages', [])
        ->assertSeeHtml('wire:model.live="selectedSubLanguages"')
        ->assertDontSeeHtml('wire:model.live="locale"')
        ->set('value', 'Unsaved German draft')->set('sourceLanguageValue', 'Edited English source')
        ->set('selectedSubLanguages', ['de-AT', 'de-CH'])->assertHasNoErrors()
        ->assertSet('subLanguageValues.de-AT', 'Existing Austrian draft')
        ->assertSet('subLanguageValues.de-CH', '')
        ->assertSeeHtml('wire:model="subLanguageValues.de-AT"')->assertSeeHtml('lang="de-CH"')
        ->assertSeeHtml("x-on:click=\"\$wire.\$set('subLanguageValues.de-AT', '', false)\"")
        ->assertDontSeeHtml("@js('subLanguageValues.'")
        ->set('subLanguageValues.de-AT', 'Edited Austrian draft')
        ->set('selectedSubLanguages', ['de-CH'])
        ->assertDontSeeHtml('wire:model="subLanguageValues.de-AT"')
        ->set('selectedSubLanguages', ['de-AT', 'de-CH'])
        ->assertSet('subLanguageValues.de-AT', 'Edited Austrian draft')
        ->assertSet('locale', 'de')->assertSet('loadedLocale', 'de')
        ->assertSet('value', 'Unsaved German draft')->assertSet('sourceLanguageValue', 'Edited English source')
        ->set('selectedSubLanguages', ['de'])->assertHasErrors('selectedSubLanguages.0')
        ->set('selectedSubLanguages', ['fr-CA'])->assertHasErrors('selectedSubLanguages.0')
        ->set('selectedSubLanguages', [])->assertHasNoErrors()
        ->set('selectedSubLanguages', ['de-AT'])
        ->call('open', 11)->assertSet('selectedSubLanguages', [])->assertSet('subLanguageValues', []);
});

it('copies the main language draft only to selected sublanguages without saving', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $locales = Mockery::mock(TranslationWorkbenchEditorLocales::class);
    $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => ['de-AT', 'de-CH', 'de-LI']]);
    app()->instance(TranslationWorkbenchEditorLocales::class, $locales);
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('save');
    $editor->shouldNotReceive('saveDrafts');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('subLanguageValues.de-LI', 'Keep this unselected draft')
        ->set('selectedSubLanguages', ['de-AT', 'de-CH'])
        ->set('value', "Main :name\nSecond line")
        ->call('copyTargetLanguageToAllSelectedSubLanguages')->assertHasNoErrors()
        ->assertSet('subLanguageValues.de-AT', "Main :name\nSecond line")
        ->assertSet('subLanguageValues.de-CH', "Main :name\nSecond line")
        ->assertSet('subLanguageValues.de-LI', 'Keep this unselected draft')
        ->set('value', '0')->call('copyTargetLanguageToAllSelectedSubLanguages')
        ->assertSet('subLanguageValues.de-AT', '0')
        ->set('value', '')->call('copyTargetLanguageToAllSelectedSubLanguages')
        ->assertSet('subLanguageValues.de-CH', '')
        ->set('selectedSubLanguages', [])->call('copyTargetLanguageToAllSelectedSubLanguages')
        ->assertHasErrors('selectedSubLanguages')
        ->set('selectedSubLanguages', ['fr'])->call('copyTargetLanguageToAllSelectedSubLanguages')
        ->assertHasErrors('selectedSubLanguages.0');
    expect($component->get('subLanguageValues'))->not->toHaveKey('fr');
});

it('sorts translations independently while preserving search and page size', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('translationTasks')->andReturn(new LengthAwarePaginator([], 0, 25));
    Livewire::test(TranslationWorkbench::class)->set('tab', 'translations')
        ->set('translationSearch', 'save')->set('translationPerPage', 50)
        ->call('setPage', 2, 'findingsPage')->call('setPage', 3, 'translationsPage')
        ->call('sortTranslations', 'namespace')->assertSet('translationSort', 'namespace')
        ->assertSet('translationDirection', 'asc')->assertSet('paginators.translationsPage', 1)
        ->assertSet('paginators.findingsPage', 2)->assertSet('translationSearch', 'save')
        ->assertSet('translationPerPage', 50)
        ->call('sortTranslations', 'namespace')->assertSet('translationDirection', 'desc')
        ->call('sortTranslations', 'key')->assertSet('translationSort', 'key')->assertSet('translationDirection', 'asc')
        ->call('sortTranslations', 'invalid')->assertSet('translationSort', 'key');
});

it('shows archived finding reasons in Status without stale VS Code links', function ($successor, $kind, $reason, $badge) {
    [$editor, $context] = translationWorkbenchFixture();
    $row = [...$context['finding'], 'status' => 'obsolete', 'successor_id' => $successor,
        'successor_kind' => $kind, 'retired_reason' => $reason];
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$row], 1, 25));
    $component = Livewire::test(TranslationWorkbench::class);
    $component->assertSee('Status')->assertSee('Archived')->assertDontSeeHtml('href="vscode:');
    if ($badge !== null) {
        $component->assertSee($badge);
    } else {
        $dom = new DOMDocument();
        @$dom->loadHTML($component->html());
        $badges = (new DOMXPath($dom))->query('//*[@data-flux-badge]');
        foreach ($badges as $badgeElement) {
            expect(trim($badgeElement->textContent))->not->toBeIn(['Deleted', 'Moved', 'Replaced']);
        }
    }
    if ($successor !== null) {
        $component->assertSee('Successor finding: #'.$successor);
    }
})->with([
    'deleted' => [null, null, 'Absent from completed scan inventory', 'Deleted'],
    'moved' => [42, 'moved', 'Unique same-file movement', 'Moved'],
    'replaced' => [43, 'replaced', 'Published source rewrite', 'Replaced'],
    'unknown reason' => [null, null, 'Old archival reason', null],
]);

it('renders shortened source paths with optional source positions', function ($path, $side, $length, $expected) {
    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-translation-workbench::ui.common.path-preview :path="$path" :side="$side" :length="$length" :line="12" :column="3" />',
        compact('path', 'side', 'length')
    );
    expect($html)->toContain($expected . ':12:3', e($path . ':12:3'))
        ->not->toContain('Line:', 'Column:');
})->with([
    ['abcdefghij', 'left', 4, 'abcd...'],
    ['abcdefghij', 'right', 4, '...ghij'],
    ['abcdefghij', 'center', 5, 'abc...ij'],
    ['äöüabcdef', 'right', 7, '...üabcdef'],
    ['a\\b.php', 'right', 40, 'a/b.php'],
]);

it('omits absent path positions and escapes path text', function () {
    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-translation-workbench::ui.common.path-preview path="&lt;file&gt;.php" />'
    );
    expect($html)->not->toContain('Line:', 'Column:', '<file>');
});

it('sorts review independently while preserving search and page size', function () {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)
        ->set('reviewSearch', 'save')->set('reviewPerPage', 50)
        ->call('setPage', 2, 'findingsPage')->call('setPage', 3, 'reviewPage')
        ->call('sortReview', 'key')->assertSet('reviewSort', 'key')->assertSet('reviewDirection', 'asc')
        ->assertSet('paginators.reviewPage', 1)->assertSet('paginators.findingsPage', 2)
        ->assertSet('reviewSearch', 'save')->assertSet('reviewPerPage', 50)
        ->call('sortReview', 'key')->assertSet('reviewDirection', 'desc')
        ->call('sortReview', 'language')->assertSet('reviewSort', 'language')->assertSet('reviewDirection', 'asc')
        ->call('sortReview', 'submitted')->assertSet('reviewSort', 'submitted')->assertSet('reviewDirection', 'asc')
        ->call('sortReview', 'invalid')->assertSet('reviewSort', 'submitted');
});

it('resets only the corresponding page when separate task filters change', function ($property, $page) {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)
        ->call('setPage', 2, 'findingsPage')->call('setPage', 3, $page)
        ->set($property, '12')
        ->assertSet('paginators.'.$page, 1)->assertSet('paginators.findingsPage', 2)
        ->assertSet($property, '12');
})->with([
    ['translationKeyId', 'translationsPage'],
    ['reviewKeyId', 'reviewPage'],
    ['reviewLanguage', 'reviewPage'],
    ['publicationKeyId', 'publicationPage'],
    ['publicationLanguage', 'publicationPage'],
    ['publishedKeyId', 'publishedPage'],
    ['publishedLanguage', 'publishedPage'],
]);

it('sorts publication worklists without losing their filters', function ($stem, $column) {
    translationWorkbenchFixture();
    $action = 'sort'.ucfirst($stem);
    Livewire::test(TranslationWorkbench::class)
        ->set($stem.'Search', 'save')->set($stem.'Language', 'de')->set($stem.'KeyId', '12')
        ->set($stem.'PerPage', 50)->call('setPage', 2, 'findingsPage')->call('setPage', 3, $stem.'Page')
        ->call($action, $column)->assertSet($stem.'Sort', $column)->assertSet($stem.'Direction', 'asc')
        ->assertSet('paginators.'.$stem.'Page', 1)->assertSet('paginators.findingsPage', 2)
        ->assertSet($stem.'Search', 'save')->assertSet($stem.'Language', 'de')
        ->assertSet($stem.'KeyId', '12')->assertSet($stem.'PerPage', 50)
        ->call($action, $column)->assertSet($stem.'Direction', 'desc')
        ->call($action, 'invalid')->assertSet($stem.'Sort', $column);
})->with([
    ['publication', 'key'], ['publication', 'language'],
    ['published', 'key'], ['published', 'language'], ['published', 'working'], ['published', 'published'],
]);

it('shows the immutable suggested key separately from an assigned translation key', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$context['finding']], 1, 25));
    Livewire::test(TranslationWorkbench::class)
        ->assertSee('Suggested key')->assertSee('fixture.original.save')->assertSee('ui.button.save')
        ->assertDontSee('Missing suggested key');
});

it('shows an unassigned finding suggestion without presenting it as an assignment', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $row = [...$context['finding'], 'key_id' => null, 'full_name' => null];
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$row], 1, 25));
    Livewire::test(TranslationWorkbench::class)->assertSee('fixture.original.save')->assertSee('Not assigned');
});

it('shows candidate status without a badge counter in the individual All view', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$context['finding']], 1, 25));
    $editor->shouldReceive('sharedSummaries')->andReturn([11 => ['candidates' => 6, 'shared' => 0]]);
    $component = Livewire::test(TranslationWorkbench::class)->set('status', 'all')->assertSee('All compact');
    $dom = new DOMDocument();
    @$dom->loadHTML($component->html());
    $badges = (new DOMXPath($dom))->query('//*[@data-flux-badge]');
    $texts = [];
    foreach ($badges as $badge) {
        $texts[] = trim($badge->textContent);
    }
    expect($texts)->toContain('Shared candidate')->not->toContain('Shared candidate · 6');
});

it('keeps the archived preference while Archived selection overrides it and resets pagination', function () {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)
        ->assertSet('includeArchived', false)
        ->assertSee('Active compact')
        ->call('setPage', 3, 'findingsPage')
        ->call('toggleArchived')
        ->assertSet('includeArchived', true)
        ->assertSet('paginators.findingsPage', 1)
        ->call('toggleArchived')
        ->set('status', 'obsolete')
        ->call('toggleArchived')
        ->assertSet('includeArchived', false)
        ->set('status', 'all')
        ->assertSet('includeArchived', false);
});

it('requires a reviewed shared assignment before writing any links', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldNotReceive('assignShared');
    Livewire::test(TranslationWorkbench::class)
        ->call('assignSharedFindings')
        ->assertHasErrors('sharedAssignment');
});

it('returns to the finding editor after confirming shared assignment and preserves unsaved drafts', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    $reference = [...$context['finding'], 'literal' => 'Save', 'namespace' => ''];
    $plan = ['reference' => $reference, 'findings' => [$reference]];
    $editor->shouldReceive('sharedSummaries')->andReturn([11 => $reference]);
    $editor->shouldReceive('sharedFindings')->andReturn(new LengthAwarePaginator([$reference], 1, 25));
    $editor->shouldReceive('previewSharedAssignment')->with(11, ['11'])->once()->andReturn($plan);
    $editor->shouldReceive('assignShared')->with($plan, 7, 'Same meaning')->once()->andReturn(['key' => 8]);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('value', 'Unsaved translation')->set('sourceLanguageValue', 'Unsaved source')
        ->call('openShared', 11)->set('sharedSelection', ['11'])->set('sharedReason', 'Same meaning')
        ->call('reviewSharedAssignment')->assertSet('sharedConfirmOpen', true)
        ->call('assignSharedFindings')->assertHasNoErrors()
        ->assertSet('sharedConfirmOpen', false)->assertSet('sharedOpen', false)
        ->assertSet('editorOpen', true)->assertSet('selected', 11)
        ->assertSet('value', 'Unsaved translation')->assertSet('sourceLanguageValue', 'Unsaved source')
        ->assertSet('sharedSelection', [])->assertSet('sharedAssignmentPlan', [])
        ->assertSee('Selected findings now use the shared key.');
});

it('toggles eligible shared findings on the current page without changing other pages or assignments', function () {
    [$editor] = translationWorkbenchFixture();
    $literal = "\n<Save>\nNext line\n";
    $editor->shouldReceive('sharedSummaries')->andReturn([11 => ['status' => 'active', 'key_id' => 8, 'literal' => $literal]]);
    $row = ['path' => 'example.blade.php', 'source_line' => 1, 'source_column' => 1, 'suggested_key' => 'example.save', 'full_name' => 'ui.button.save', 'literal' => $literal];
    $editor->shouldReceive('sharedFindings')->with(11, 1)->andReturn(new LengthAwarePaginator([
        [...$row, 'id' => 11, 'key_id' => 8],
        [...$row, 'id' => 12, 'key_id' => null],
        [...$row, 'id' => 13, 'key_id' => 9],
        [...$row, 'id' => 14, 'key_id' => 8],
        [...$row, 'id' => 15, 'key_id' => 9, 'literal' => 'Different'],
    ], 5, 25));
    $editor->shouldNotReceive('assignShared');
    $component = Livewire::test(TranslationWorkbench::class)
        ->call('openShared', 11)
        ->assertSeeHtml('wire:click="toggleSharedPageSelection"')
        ->assertSeeHtml('&lt;Save&gt;<br />')
        ->assertDontSeeHtml('whitespace-pre-line')
        ->set('sharedSelection', ['99', '12'])
        ->call('toggleSharedPageSelection')
        ->assertSet('sharedSelection', ['99', '12', '13'])
        ->call('toggleSharedPageSelection')
        ->assertSet('sharedSelection', ['99']);

    $buttons = function () use ($component): array {
        preg_match_all('/<button\b[^>]*wire:click="reviewSharedAssignment"[^>]*>/s', $component->html(), $matches);
        expect($matches[0])->toHaveCount(2);
        return $matches[0];
    };
    foreach ($buttons() as $button) {
        expect($button)->toMatch('/\sdisabled(?:\s|=|>)/');
    }
    $component->set('sharedReason', '   ')->call('reviewSharedAssignment')->assertHasErrors('sharedReason')
        ->set('sharedReason', 'Same meaning')->assertHasNoErrors('sharedReason');
    foreach ($buttons() as $button) {
        expect($button)->not->toMatch('/\sdisabled(?:\s|=|>)/');
    }
    $component->set('sharedReason', '');
    foreach ($buttons() as $button) {
        expect($button)->toMatch('/\sdisabled(?:\s|=|>)/');
    }
});

it('keeps already assigned shared checkboxes visible and disables select all when none remain', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $reference = [...$context['finding'], 'literal' => 'Save'];
    $rows = [$reference, [...$reference, 'id' => 12]];
    $editor->shouldReceive('sharedSummaries')->andReturn([11 => $reference]);
    $editor->shouldReceive('sharedFindings')->andReturn(new LengthAwarePaginator($rows, 2, 25));
    $editor->shouldNotReceive('assignShared');
    $component = Livewire::test(TranslationWorkbench::class)->call('openShared', 11);
    $dom = new DOMDocument();
    @$dom->loadHTML($component->html());
    $xpath = new DOMXPath($dom);
    $checkboxes = $xpath->query('//ui-checkbox[@*[name()="wire:model.live"]="sharedSelection"]');
    expect($checkboxes->length)->toBe(2);
    foreach ($checkboxes as $checkbox) {
        expect($checkbox->hasAttribute('disabled'))->toBeTrue();
    }
    $buttons = $xpath->query('//button[@*[name()="wire:click"]="toggleSharedPageSelection"]');
    expect($buttons->length)->toBe(1)
        ->and($buttons->item(0)->hasAttribute('disabled'))->toBeTrue();
    $component->call('toggleSharedPageSelection')->assertSet('sharedSelection', []);
});

it('uses a language toggle only when source and target differ', function () {
    $render = fn ($target) => \Illuminate\Support\Facades\Blade::render(
        '<x-translation-workbench::ui.common.input-language-selector wire:model.live="inputLocale" :target="$target" :countries="$countries" focus="reason-text" />',
        ['target' => $target, 'countries' => ['en' => 'GB', 'de' => 'DE']]);
    expect($render('de'))->toContain('data-flux-toggle')->not->toContain('data-flux-radio');
    expect($render('en'))->not->toContain('data-flux-toggle')->not->toContain('data-flux-radio');
});

it('keeps the reason input language separate from source and target translation languages', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('saveDrafts')->once()->with(11, 8, 4,
        ['en' => ['value' => 'Save', 'revision' => null], 'de' => ['value' => 'Speichern', 'revision' => null]],
        7, 'Formulierung verbessert', 'de')->andReturn([]);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('value', 'Speichern')->call('openChangeReason', 'Formulierung verbessert')
        ->assertSet('newChangeReasonLocale', 'de')->assertSeeHtml('lang="de"')
        ->assertSeeHtml('wire:key="change-reason-input-de"')
        ->set('newChangeReasonLocale', 'en')->assertSeeHtml('lang="en"')
        ->assertSeeHtml('wire:key="change-reason-input-en"')
        ->assertSet('newChangeReason', 'Formulierung verbessert')->assertSet('value', 'Speichern')
        ->set('newChangeReasonLocale', 'fr')->call('createChangeReason')->assertHasErrors('newChangeReasonLocale')
        ->set('newChangeReasonLocale', 'de')->call('createChangeReason')->assertHasNoErrors('newChangeReasonLocale')
        ->call('saveDraft')->assertHasNoErrors();
});

it('restores a known reason language and does not guess a historical reason language', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('reasonLocales')->with('assignment')->andReturn(['Known reason' => 'en', 'Historical reason' => null]);
    $editor->shouldReceive('assign')->once()->with(11, 8, 4, 7, 'Historical reason', null)->andReturn([]);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->call('openAssignmentReason', 'Known reason')->assertSet('newAssignmentReasonLocale', 'en')
        ->set('assignmentReasonOpen', false)
        ->set('assignmentReason', 'Historical reason')->call('confirmAssignment')->call('assign')->assertHasNoErrors();
});

it('keeps a newly created shared assignment reason separate from the individual reason', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('assignmentReason', 'Individual reason')
        ->call('openAssignmentReason', 'Shared meaning across contexts', true)
        ->call('createAssignmentReason')
        ->assertSet('sharedReason', 'Shared meaning across contexts')
        ->assertSet('assignmentReason', 'Individual reason');
});

it('shows Shared key instead of Shared candidate once the finding shares its assigned key', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$context['finding']], 1, 25));
    $editor->shouldReceive('sharedSummaries')->andReturn([11 => ['candidates' => 6, 'shared' => 3]]);
    Livewire::test(TranslationWorkbench::class)->set('status', 'all')
        ->assertSee('Shared key')
        ->assertDontSee('Shared key in use')
        ->assertDontSee('Shared candidate');
});

it('keeps the assignment filter independent from status and resets the findings page', function () {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)
        ->assertSet('status', 'active')->assertSet('unassignedOnly', false)
        ->call('setPage', 3, 'findingsPage')->call('toggleUnassigned')
        ->assertSet('unassignedOnly', true)->assertSet('paginators.findingsPage', 1)
        ->set('status', 'all')->assertSet('unassignedOnly', true)
        ->call('toggleUnassigned')->assertSet('unassignedOnly', false);
});

it('creates workflow reasons from the search text only in the intended field', function (string $field) {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldNotReceive('assign');
    $editor->shouldNotReceive('saveDrafts');
    Livewire::test(TranslationWorkbench::class)
        ->set('assignmentReason', 'Keep this assignment reason')
        ->call('openWorkflowReason', $field, '  Context-specific reason  ')
        ->assertSet('newWorkflowReason', 'Context-specific reason')
        ->call('createWorkflowReason')->assertHasNoErrors()
        ->assertSet($field, 'Context-specific reason')
        ->assertSet('assignmentReason', 'Keep this assignment reason')
        ->assertSet('workflowReasonOpen', false);
})->with(['reviewReason', 'reviewTaskReason', 'publicationReason', 'closureReason', 'sourceReason', 'sourceCloseReason', 'scanUsageReason']);

it('rejects empty workflow reasons and arbitrary property targets', function () {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)
        ->call('openWorkflowReason', 'publicationReason', '  ')
        ->call('createWorkflowReason')->assertHasErrors('newWorkflowReason')
        ->assertSet('publicationReason', '');
    Livewire::test(TranslationWorkbench::class)
        ->call('openWorkflowReason', 'testToken', 'must not be assigned')
        ->assertStatus(422);
});

it('offers review reasons in both review forms without mixing publication reasons', function () {
    translationWorkbenchFixture();
    $component = Livewire::test(TranslationWorkbench::class)
        ->call('openWorkflowReason', 'reviewReason', 'New review explanation')
        ->call('createWorkflowReason');
    expect($component->instance()->workflowReasonOptions('reviewTaskReason'))->toContain('New review explanation');
    expect($component->instance()->workflowReasonOptions('publicationReason'))->not->toContain('New review explanation');
});

it('renders per-language workflow badges rather than marking the whole finding published', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('page')->andReturn(new LengthAwarePaginator([$context['finding']], 1, 25));
    $editor->shouldReceive('workflowStates')->andReturn([11 => [
        ['locale' => 'en', 'revision' => 1, 'state' => 'draft', 'published_revision' => null],
        ['locale' => 'de', 'revision' => 2, 'state' => 'published', 'published_revision' => 2],
    ]]);
    Livewire::test(TranslationWorkbench::class)->assertSee('Draft · en')->assertSee('Published · de');
});

it('offers English together with the main language for one explicit review', function () {
    [$editor, $context] = generationReviewFixture();
    $context['sourceRevision'] = 18;
    $context['sourceText'] = 'English source';
    $source = $context;
    $source['revision'] = ['id' => 18, 'value' => 'English source'];
    $source['review']['fingerprint'] = str_repeat('b', 64);
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    $editor->shouldReceive('inspect')->with(11, 'en')->andReturn($source);
    $editor->shouldReceive('reviewMany')->once()
        ->with([18 => str_repeat('b', 64), 19 => str_repeat('a', 64)], true, 7, 'Reviewed both languages')->andReturn([]);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->call('confirmReview', true)->assertSet('reviewLocales', ['en', 'de'])
        ->set('reviewReason', 'Reviewed both languages')->call('review')->assertHasNoErrors();
});

it('does not discard unsaved English when approving only the target language', function () {
    [$editor, $context] = generationReviewFixture();
    $context['sourceRevision'] = 18;
    $context['sourceText'] = 'English source';
    $source = $context;
    $source['revision'] = ['id' => 18, 'value' => 'English source'];
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    $editor->shouldReceive('inspect')->with(11, 'en')->andReturn($source);
    $editor->shouldNotReceive('review');
    $editor->shouldNotReceive('reviewMany');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('sourceLanguageValue', 'Unsaved English')->call('confirmReview', true)
        ->set('reviewLocales', ['de'])->set('reviewReason', 'Review target')
        ->call('review')->assertHasErrors('review')->assertSet('sourceLanguageValue', 'Unsaved English');
});

it('groups only variants with the same workflow status and retains the exact languages in the tooltip', function () {
    $states = [];
    foreach (['de' => 'published', 'de-AT' => 'published', 'de-CH' => 'published', 'de-IT' => 'draft'] as $locale => $state) {
        $states[] = ['locale' => $locale, 'state' => $state, 'revision' => count($states) + 1, 'published_revision' => null];
    }
    $html = view('translation-workbench::livewire.translation-workbench.shared.finding-workflow-status', [
        'workflowFinding' => ['id' => 11, 'key_id' => 8],
        'findingWorkflowStates' => [11 => $states],
        'translationFlagCountries' => ['de' => 'DE', 'de-IT' => 'IT'],
    ])->render();
    expect($html)->toContain('aria-label="Published · de (+2)"', 'aria-label="Draft · de-IT"', 'de-AT', 'de-CH')
        ->not->toContain('(+3)');
});


it('renders an executable create-reason action and opens the workflow reason modal', function () {
    [$editor, $context] = generationReviewFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)->call('confirmReview', true);
    $dom = new DOMDocument();
    @$dom->loadHTML($component->html());
    $xpath = new DOMXPath($dom);
    $actions = [];
    foreach ($xpath->query('//*[@*[name()="x-on:click"]]') as $node) {
        $action = $node->getAttribute('x-on:click');
        if (str_contains($action, 'openWorkflowReason')) $actions[] = $action;
    }
    expect($actions)->toContain("\$wire.openWorkflowReason('reviewReason', reasonSearch)");
    foreach ($actions as $action) {
        expect($action)->not->toContain('@js', '$reasonField');
    }
    $component->call('openWorkflowReason', 'reviewReason', 'Check terminology')
        ->assertSet('workflowReasonOpen', true)->assertSet('reviewOpen', true)
        ->assertSee('Create a review reason')->assertSeeHtml('data-flux-toggle')
        ->assertSet('newWorkflowReasonLocale', 'de')
        ->set('newWorkflowReasonLocale', 'en')->assertSeeHtml('lang="en"')
        ->assertSet('newWorkflowReason', 'Check terminology')
        ->call('createWorkflowReason')->assertHasNoErrors()
        ->assertSet('workflowReasonOpen', false)->assertSet('reviewOpen', true)
        ->assertSet('reviewReason', 'Check terminology');
});

it('marks shared review revisions and keeps batch and individual review controls available', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $tasks = new LengthAwarePaginator([
        ['submission' => 1, 'key_id' => 8, 'namespace' => '', 'full_name' => 'ui.button.save', 'locale' => 'de', 'revision' => 19, 'occurred_at' => null, 'actor_id' => 7, 'active_findings_count' => 3],
        ['submission' => 2, 'key_id' => 9, 'namespace' => '', 'full_name' => 'ui.button.cancel', 'locale' => 'en', 'revision' => 20, 'occurred_at' => null, 'actor_id' => 7, 'active_findings_count' => 1],
    ], 2, 25);
    $html = view('translation-workbench::livewire.translation-workbench.tabs.review.table', [
        'reviewTasks' => $tasks, 'reviewSort' => 'key', 'reviewDirection' => 'asc', 'selectedReviewRevisions' => [],
    ])->render();
    expect($html)->toContain('Shared key', '3 active findings', 'toggleVisibleReviewRevisions', 'openReviewBatch', 'openReviewTask(19)', 'selectedReviewRevisions');
    expect(preg_match_all('/>\s*Shared key\s*</', $html))->toBe(1);
});

it('carries the batch review input language into a new reason without relabeling known reasons', function () {
    [$editor] = generationReviewFixture();
    $editor->shouldReceive('reasonLocales')->with('review')->andReturn(['Bekannter Grund' => 'de']);
    Livewire::test(TranslationWorkbench::class)
        ->set('reviewBatchReasonLocale', 'en')
        ->call('openWorkflowReason', 'reviewBatchReason', 'Checked together')
        ->assertSet('newWorkflowReasonLocale', 'en')
        ->call('createWorkflowReason')->assertHasNoErrors()
        ->assertSet('reviewBatchReason', 'Checked together')
        ->call('openWorkflowReason', 'reviewBatchReason', 'Bekannter Grund')
        ->assertSet('newWorkflowReasonLocale', 'de')
        ->set('reviewBatchReasonLocale', 'fr')->assertHasErrors('reviewBatchReasonLocale');
});

it('offers the language selector for publication reasons and retains the chosen language', function () {
    generationReviewFixture();
    $component = Livewire::test(TranslationWorkbench::class)
        ->call('openWorkflowReason', 'publicationReason', 'Veröffentlichung geprüft')
        ->assertSee('Create a publication reason')->assertSeeHtml('data-flux-toggle')
        ->assertSet('newWorkflowReasonLocale', 'de')->assertSeeHtml('lang="de"')
        ->set('newWorkflowReasonLocale', 'en')->assertSeeHtml('lang="en"')
        ->set('newWorkflowReason', 'Checked for publication')->call('createWorkflowReason')
        ->assertHasNoErrors()->assertSet('publicationReason', 'Checked for publication');
    expect($component->get('createdReasonLocales')['publication'][hash('sha256', 'Checked for publication')])->toBe('en');
});
