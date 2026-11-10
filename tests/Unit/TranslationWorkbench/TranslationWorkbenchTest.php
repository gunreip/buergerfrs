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
    $editor->shouldReceive('savedLanguageVariants')->andReturn([])->byDefault();
    $editor->shouldReceive('translationFiles')->andReturn([])->byDefault();
    $editor->shouldReceive('assignmentReasons')->andReturn(['Previously saved reason'])->byDefault();
    $editor->shouldReceive('recentScans')->andReturn([])->byDefault();
    $editor->shouldReceive('recentWork')->andReturn([])->byDefault();
    $editor->shouldReceive('workTasks')->andReturn(new LengthAwarePaginator([], 0, 25))->byDefault();
    $editor->shouldReceive('scanCategoryCounts')->andReturn([])->byDefault();
    $editor->shouldReceive('scanRequirementSummary')->andReturn(['required' => false, 'findings' => 0, 'files' => 0, 'examples' => []])->byDefault();
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
        ->assertHasNoErrors()->assertSet('context.revision.id', 19)
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'success'
            && $params['slots']['heading'] === 'Translation drafts saved'
            && str_contains($params['slots']['text'], 'Key record #8 · Languages: en, de'));
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
        ->call('assign')->assertHasErrors('assignment')->assertSee('The key assignment changed.')
        ->assertNotDispatched('toast-show', fn ($event, $params) => ($params['dataset']['variant'] ?? '') === 'success');
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
        ->assertSet('reviewOpen', false)
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'success'
            && $params['slots']['heading'] === 'Revisions approved'
            && $params['slots']['text'] === 'Key record #8 · Revision IDs: #19');
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

it('offers the reserving publication in a toast before opening it for inspection', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $publisher->shouldReceive('prepare')->once()->andThrow(
        new \Gunreip\TranslationWorkbench\Generation\PublicationFileReserved($plan['run'], 'en/ui.php')
    );
    $publisher->shouldReceive('inspect')->once()->with($plan['run'])->andReturn($plan);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('publicationReason', 'Publish reviewed English')->call('preparePublication')
        ->assertHasNoErrors()->assertSet('publicationOpen', false)
        ->assertDispatched('toast-show', fn ($event, $notice) =>
            $notice['dataset']['variant'] === 'danger' && str_contains($notice['slots']['text'], 'lang/en/ui.php')
            && $notice['action']['params']['run'] === $plan['run'])
        ->call('openPublication', $plan['run'])->assertSet('publicationOpen', true)
        ->assertSee('Review closure without further writes');
});

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
    $component->call('publish')->assertHasNoErrors()->assertSet('publication.state', 'complete')->assertSee('File matches the publication result.')
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'success'
            && $params['slots']['heading'] === 'Publication completed');
});

it('reports a failed publication through a contextual error toast', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $publisher->shouldReceive('inspect')->with($plan['run'])->andReturn($plan);
    $publisher->shouldReceive('publish')->once()->andThrow(new RuntimeException('Language file is not writable.'));

    Livewire::test(TranslationWorkbench::class)->call('openPublication', $plan['run'])
        ->call('publish')->assertHasErrors('publication')->assertSet('publicationOpen', true)
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'danger'
            && $params['slots']['heading'] === 'Publication failed'
            && $params['slots']['text'] === 'Language file is not writable.');
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
    $editor->shouldReceive('translationFiles')->once()->with(8, 'ui.button.save', ['en', 'de', 'de-AT'])->andReturn([
        ['locale' => 'en', 'path' => 'en/ui.php', 'actual' => true, 'state' => 'published', 'detail' => 'Matches the published revision.'],
        ['locale' => 'de-AT', 'path' => 'de-at/ui.php', 'actual' => false, 'state' => 'unavailable', 'detail' => 'Missing file.'],
    ]);
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('sourcePreview')->once()->with(11, 8, 4, 'en')->andReturn([
        'finding' => 11, 'key_id' => 8, 'key' => 'ui.button.save', 'path' => 'example.blade.php', 'line' => 2, 'column' => 3,
        'before' => "__('Save')", 'after' => "__('ui.button.save')", 'already_set' => false, 'blocker' => null,
        'language_path' => 'en/ui.php', 'source_locale' => 'en', 'published_value' => 'Save', 'ready' => true,
    ]);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->set('locale', 'de')->call('previewSource')
        ->assertHasNoErrors()->assertSet('sourcePreviewOpen', true)->assertSee('Source replacement preview')
        ->assertSee('Translation files')->assertSee('lang/de-at/ui.php')->assertSee('Expected')
        ->assertDontSee('Published source-language value')->assertSee('Exact source preview')->assertSee('Preparation records a plan without writing the file.')
        ->assertSeeHtml('wire:click="openWorkflowStatus(11)"');
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
    $plan = ['run' => 'plan-id', 'actor' => 7, 'reason' => 'Use approved key', 'preview' => ['finding' => 11, 'key_id' => 8, 'path' => 'example.blade.php', 'key' => 'ui.button.save', 'before' => "__('Save')", 'after' => "__('ui.button.save')"], 'state' => 'pending', 'file_state' => 'written', 'result' => null];
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


it('loads current shared candidate status for findings on the scan result page', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([
        ['id' => 999, 'predecessor' => null, 'path' => 'new.blade.php', 'source_line' => 2],
    ], 1, 25, 1, ['pageName' => 'scanResultsPage']));
    $editor->shouldReceive('sharedSummaries')->withArgs(fn ($ids) => in_array(999, $ids, true))
        ->andReturn([999 => ['candidates' => 3, 'shared' => 0]]);

    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')
        ->assertSee('Current shared status')->assertSee('Shared candidate')
        ->assertSeeHtml('wire:click="open(999)"');
});

it('changes the scan page size independently and resets its page', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([], 60, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 2)->andReturn(new LengthAwarePaginator([], 60, 25, 2));
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1, '', 10)->andReturn(new LengthAwarePaginator([], 60, 10));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')
        ->call('setPage', 2, 'scanResultsPage')->set('scanPerPage', 10)
        ->assertSet('paginators.scanResultsPage', 1)->assertSet('scanPerPage', 10)
        ->set('scanPerPage', 99999)->assertSet('scanPerPage', 25);
});

it('filters scan results and resets pagination while retaining category context', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([], 60, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 2)->andReturn(new LengthAwarePaginator([], 60, 25, 2));
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1, '#42')->andReturn(new LengthAwarePaginator([
        ['id' => 42, 'predecessor' => null, 'path' => 'target.php', 'source_line' => 3],
    ], 1, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'review_files', 1, '#42')->andReturn(new LengthAwarePaginator([], 0, 25));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')
        ->assertSee('Files and usage decisions')->assertSee('Finding ID, source path or translation key')
        ->call('setPage', 2, 'scanResultsPage')
        ->set('scanSearch', '#42')->assertSet('paginators.scanResultsPage', 1)->assertSee('target.php:3')
        ->set('scanCategory', 'review_files')->assertSet('paginators.scanResultsPage', 1)
        ->assertSee('No results match this search in the selected category.')
        ->call('openScan', 'scan-id')->assertSet('scanSearch', '');
});

it('offers missing immutable scan information without conflating ordinary scan results', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'missing_immutable', 1)->andReturn(new LengthAwarePaginator([
        ['id' => 11, 'predecessor' => null, 'path' => 'legacy.php', 'source_line' => 7, 'captured_key' => 'ui.states.all'],
    ], 1, 25, 1, ['pageName' => 'scanResultsPage']));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')
        ->set('scanCategory', 'missing_immutable')->assertSet('scanCategory', 'missing_immutable')
        ->assertSee('Missing immutable information')->assertSee('Reconstructed source text missing')
        ->assertSee('legacy.php:7')->assertSee('ui.states.all')
        ->set('scanCategory', 'created')->assertDontSee('Reconstructed source text missing');
});

it('explains why a scan file was preserved without inventing a finding', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'review_files', 1)->andReturn(new LengthAwarePaginator([
        ['id' => null, 'predecessor' => null, 'path' => 'pending.blade.php', 'source_line' => null, 'preserved_count' => 3,
            'issues' => [['line' => 17, 'column' => 9, 'reason' => 'key_like_argument_without_lang_evidence']]],
    ], 1, 25, 1, ['pageName' => 'scanResultsPage']));
    $component = Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->set('scanCategory', 'review_files')
        ->assertSee('pending.blade.php')->assertSee('17:9')
        ->assertSee('Translation key found, but no verified source-language value is available for this scan.');
    expect(preg_replace('/\s+/u', ' ', strip_tags($component->html())))->toContain('Existing findings preserved: 3');
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
        'capture' => ['raw_expression' => '__($databaseValue)', 'source_line' => 87, 'source_column' => 38], 'decision' => null];
    $editor->shouldReceive('scanUsage')->with('scan-id', 'pending.php', 2)->andReturnUsing(fn () => $context);
    $editor->shouldReceive('decideScanUsage')->once()->with('scan-id', 'pending.php', 2, 'database_value', 'Database output', 7, 0)->andReturn([]);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->call('openScanCaptures', 'pending.php')
        ->call('openScanUsage', 2)->assertSet('scanUsageOpen', true)->assertSet('scanOpen', false)
        ->assertSee('Line 87, column 38')
        ->set('scanUsageOpen', false)->assertSet('scanUsageContext', [])->assertSet('scanOpen', true)
        ->assertSet('scanReviewPath', 'pending.php')
        ->call('openScanUsage', 2)->assertSee('Classify source usage')->assertSee('__($databaseValue)')
        ->set('scanUsageChoice', 'database_value')->set('scanUsageReason', 'Database output')->call('saveScanUsage')
        ->assertHasNoErrors()->assertSet('scanUsageOpen', false)->assertSet('scanOpen', true)->assertSee('Usage decision saved. The next scan checks the unchanged source before applying it. No translation or source file was changed.');
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
        ->assertHasErrors('scanUsage')->assertSet('scanUsageOpen', true)->assertSet('scanUsageContext.index', 2)->assertSet('scanUsageNotice', '');
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
    app(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class)->shouldReceive('find')->once()->with(7)
        ->andReturn(['name' => 'Alex Weber', 'avatar_url' => '/storage/avatars/test.png']);
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
        ->assertSee('Alex Weber')->assertSeeHtml('/storage/avatars/test.png')->assertSee('#8')
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
        ->assertSet('scanUsageOpen', true)->assertSet('scanOpen', false)
        ->set('scanUsageOpen', false)->assertSet('scanOpen', true)
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
        ->assertSet('assignmentSelection.key', 8)
        ->assertSee('Translation key assignment')->assertSee('Key record ID')
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
        ->assertSee('Assigned key')->assertDontSee('Incomplete translation key')
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
        ->assertHasNoErrors()
        ->assertSet('subLanguageValues.de-AT', '0')
        ->assertSet('subLanguageValues.de-CH', '0')
        ->set('value', '')->call('copyTargetLanguageToAllSelectedSubLanguages')
        ->assertHasErrors(['value' => 'required'])
        ->assertSet('subLanguageValues.de-AT', '0')
        ->assertSet('subLanguageValues.de-CH', '0')
        ->assertSet('subLanguageValues.de-LI', 'Keep this unselected draft')
        ->set('value', 'Main language draft')
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
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'success'
            && $params['slots']['text'] === 'Findings linked: 1 · Key record #8.');
});

it('groups shared checkboxes without replacing selections from other pages or changing assignments', function () {
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
        ->assertSeeHtml('data-flux-checkbox-group')
        ->assertDontSeeHtml('wire:click="toggleSharedPageSelection"')
        ->assertSeeHtml('&lt;Save&gt;<br />')
        ->assertDontSeeHtml('whitespace-pre-line')
        ->set('sharedSelection', ['99', '12'])
        ->set('sharedConfirmOpen', true)
        ->set('sharedSelection', ['99', '12', '13'])
        ->assertSet('sharedConfirmOpen', false)
        ->assertSet('sharedSelection', ['99', '12', '13'])
        ->set('sharedSelection', ['99'])
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
    $reference = [...$context['finding'], 'literal' => 'Save', 'candidates' => 2, 'shared' => 2, 'assignment_reason' => 'Use the same meaning.'];
    $rows = [$reference, [...$reference, 'id' => 12]];
    $editor->shouldReceive('sharedSummaries')->andReturn([11 => $reference]);
    $editor->shouldReceive('sharedFindings')->andReturn(new LengthAwarePaginator($rows, 2, 25));
    $editor->shouldNotReceive('assignShared');
    $component = Livewire::test(TranslationWorkbench::class)->call('openShared', 11);
    $dom = new DOMDocument();
    @$dom->loadHTML($component->html());
    $xpath = new DOMXPath($dom);
    $checkboxes = $xpath->query('//ui-checkbox[@disabled and @checked]');
    expect($checkboxes->length)->toBe(2);
    foreach ($checkboxes as $checkbox) {
        expect($checkbox->hasAttribute('disabled'))->toBeTrue()
            ->and($checkbox->hasAttribute('checked'))->toBeTrue()
            ->and($checkbox->hasAttribute('wire:model.live'))->toBeFalse();
    }
    $buttons = $xpath->query('//ui-checkbox[@all]');
    expect($buttons->length)->toBe(1)
        ->and($buttons->item(0)->hasAttribute('disabled'))->toBeTrue();
    $component->assertSet('sharedSelection', [])->assertSee('Use the same meaning.')->assertSee('Already assigned');
    $reason = $xpath->query('//ui-select[@*[name()="wire:model.live"]="sharedReason"]')->item(0);
    expect($reason->hasAttribute('disabled'))->toBeTrue();
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
    $editor->shouldReceive('assign')->once()->with(11, 8, 4, 7, 'Historical reason', null)->andReturn(['key' => 8, 'link' => 4]);
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
})->with(['reviewReason', 'reviewBatchReason', 'publicationReason', 'closureReason', 'sourceReason', 'sourceCloseReason', 'scanUsageReason']);

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
    expect($component->instance()->workflowReasonOptions('reviewBatchReason'))->toContain('New review explanation');
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
        ->assertSet('reviewReason', 'Check terminology')
        ->assertSeeHtml('reviewReasonLocale')
        ->set('reviewReasonLocale', 'en')
        ->call('openWorkflowReason', 'reviewReason', 'Checked wording')
        ->assertSet('newWorkflowReasonLocale', 'en')
        ->set('reviewReasonLocale', 'fr')->assertHasErrors('reviewReasonLocale');
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
    expect($html)->toContain('Shared key', '3 active findings', 'ui-checkbox-group', 'openReviewBatch', 'openReviewBatch(19)', 'selectedReviewRevisions');
    expect(preg_match_all('/>\s*Shared key\s*</', $html))->toBe(1);
    expect($html)->toMatch('/<ui-checkbox\b[^>]*\ball(?:\s|>)/')
        ->not->toContain('toggleVisibleReviewRevisions');
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
        ->set('publicationReasonLocale', 'de')
        ->call('openWorkflowReason', 'publicationReason', 'Veröffentlichung geprüft')
        ->assertSee('Create a publication reason')->assertSeeHtml('data-flux-toggle')
        ->assertSet('newWorkflowReasonLocale', 'de')->assertSeeHtml('lang="de"')
        ->set('newWorkflowReasonLocale', 'en')->assertSeeHtml('lang="en"')
        ->set('newWorkflowReason', 'Checked for publication')->call('createWorkflowReason')
        ->assertHasNoErrors()->assertSet('publicationReason', 'Checked for publication');
    expect($component->get('createdReasonLocales')['publication'][hash('sha256', 'Checked for publication')])->toBe('en');
    $component->set('publicationReasonLocale', 'en')
        ->call('openWorkflowReason', 'publicationReason', 'New publication reason')
        ->assertSet('newWorkflowReasonLocale', 'en')
        ->set('publicationReasonLocale', 'fr')->assertHasErrors('publicationReasonLocale');
});

it('summarizes finding editor validation errors in a persistent toast with a field action', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('assignmentKey', '')->set('assignmentReason', '')
        ->call('confirmAssignment')
        ->assertHasErrors(['assignmentKey', 'assignmentReason'])
        ->assertSet('confirmOpen', false)
        ->assertDispatched('toast-show', function ($event, $params) {
            return $params['dataset']['variant'] === 'danger'
                && $params['duration'] === 0
                && count(explode("\n", $params['slots']['text'])) === 2
                && $params['action']['event'] === 'workbench-editor-error-focus'
                && $params['action']['params']['target'] === 'assignmentKey'
                && $params['action']['params']['finding'] === 11
                && $params['dataset']['noticeScope'] === $params['dataset']['replaceNoticeScope'];
        });
    foreach ($component->instance()->getErrorBag()->all() as $message) {
        $component->assertDontSee($message);
    }
});

it('reports a draft conflict without moving the editor layout or losing entered text', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('saveDrafts')->once()->andThrow(new RuntimeException('The saved revision changed.'));
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('value', 'Keep my translation')->set('reason', 'Reviewing the text')
        ->call('saveDraft')->assertHasErrors('value')
        ->assertSet('value', 'Keep my translation')->assertSet('editorOpen', true)
        ->assertDontSee('The saved revision changed.')
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['slots']['text'] === 'The saved revision changed.'
            && $params['action']['params']['target'] === 'value');
});

it('keeps reason-modal validation in its own modal rather than directing to the finding editor', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->call('openChangeReason')->set('newChangeReason', '')
        ->call('createChangeReason')->assertHasErrors('newChangeReason')
        ->assertNotDispatched('toast-show');
});


it('distinguishes a missing change reason from an invalid reason and marks the select', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('saveDrafts');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('reason', '')->call('saveDraft')
        ->assertHasErrors(['reason' => 'required'])
        ->assertSet('editorOpen', true)
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['slots']['heading'] === 'Required reason is missing'
            && $params['slots']['text'] === 'Select a reason for translation change.'
            && $params['action']['params']['target'] === 'reason');
    $document = new DOMDocument;
    @$document->loadHTML($component->html());
    $xpath = new DOMXPath($document);
    expect($xpath->query('//*[@data-editor-error-target="reason"]//*[@data-invalid]')->length)->toBeGreaterThan(0);
    $component->set('reason', 'x')->call('saveDraft')
        ->assertHasErrors(['reason' => 'min'])
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['slots']['heading'] === 'Check the field value'
            && $params['action']['params']['target'] === 'reason');
});

it('reports unsaved drafts as a workflow prerequisite without marking the reason field', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('reviewWorklist');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)->set('value', 'Speichern')
        ->call('submitForReview')->assertHasErrors('submission')->assertHasNoErrors('reason')
        ->assertSet('editorOpen', true)
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['slots']['heading'] === 'Submission for review blocked'
            && $params['action']['label'] === 'Go to translation drafts'
            && $params['action']['params']['target'] === 'translationValues');
});


it('shows a persisted assignment and only enables assignment actions for a changed selection', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSee('Assigned key')->assertDontSee('New translation key');
    $disabledActions = function () use ($component) {
        $document = new DOMDocument;
        @$document->loadHTML($component->html());
        $xpath = new DOMXPath($document);
        return $xpath->query('//button[@*[name()="wire:click"]="confirmAssignment" and @disabled]')->length;
    };
    expect($disabledActions())->toBe(2);
    $component->set('assignmentKey', 'ui.button.changed')->assertSee('New translation key');
    expect($disabledActions())->toBe(0);
    $component->set('assignmentKey', 'ui.button.save')->assertSee('Assigned key');
    expect($disabledActions())->toBe(2);
    $component->call('open', 11)->assertSee('Assigned key');
    expect($disabledActions())->toBe(2);
});


it('keeps source-only drafts saveable but blocks empty target text from review', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('saveDrafts')->once()->withArgs(fn ($finding, $key, $link, $drafts) =>
        $drafts['en']['value'] === 'Save' && $drafts['de']['value'] === '')->andReturn([]);
    $editor->shouldNotReceive('reviewWorklist');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('reason', 'Continue this draft later')->call('saveDraft')->assertHasNoErrors();
    $component->set('value', '   ')->call('submitForReview')->assertHasErrors(['value' => 'required'])
        ->assertSet('editorOpen', true);
});

it('requires selected variant text for review without deleting other variant drafts', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $locales = Mockery::mock(TranslationWorkbenchEditorLocales::class);
    $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => ['de-AT', 'de-CH']]);
    app()->instance(TranslationWorkbenchEditorLocales::class, $locales);
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('reviewWorklist');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('value', 'Speichern')->set('selectedSubLanguages', ['de-AT', 'de-CH'])
        ->set('subLanguageValues.de-AT', 'Speichern in Österreich')->set('subLanguageValues.de-CH', ' ')
        ->call('submitForReview')->assertHasErrors(['subLanguageValues.de-CH' => 'required'])
        ->assertSet('subLanguageValues.de-AT', 'Speichern in Österreich')
        ->set('value', '')->call('submitForReview')->assertHasErrors(['value' => 'required'])
        ->assertSet('subLanguageValues.de-AT', 'Speichern in Österreich');
});

it('rejects an empty publication selection without opening a preview', function () {
    translationWorkbenchFixture();
    Livewire::test(TranslationWorkbench::class)
        ->set('selectedPublicationRevisions', [])->call('openPublicationTask')
        ->assertHasErrors('selectedPublicationRevisions')->assertSet('publicationTaskOpen', false)
        ->assertSet('publicationBatch', []);
});

it('shows source replacement inspection with disabled actions and separate findings', function () {
    [$editor] = translationWorkbenchFixture();
    $rows = [];
    foreach ([101 => 'ready', 102 => 'blocked', 103 => 'already_set'] as $id => $state) {
        $rows[] = ['id' => $id, 'key_id' => 8, 'path' => 'example.blade.php', 'source_line' => 2,
            'source_column' => 3, 'full_name' => 'ui.button.save', 'raw_expression' => "__('Save')",
            'check_state' => $state, 'check_detail' => 'Inspection detail'];
    }
    $editor->shouldReceive('latestWorkingEventId')->andReturn(42);
    $editor->shouldReceive('sourceReplacementPage')->once()->with('', 1, 25, 'finding', 'asc')
        ->andReturn(new LengthAwarePaginator($rows, 3, 25, 1, ['pageName' => 'sourceReplacementPage']));
    $service = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class);
    $service->shouldReceive('target')->once()->andReturn(['database' => 'fixture', 'schema' => 'fixture', 'paths' => []]);
    app()->instance(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class, $service);
    $panel = Livewire::test(TranslationWorkbench::class)->set('tab', 'source-replacement')
        ->assertSee('Ready at check time')->assertSee('Needs attention')
        ->assertSee('Assigned key already in source')->assertSee('#101')->assertSee('#102')
        ->assertDontSeeHtml('wire:click="previewSource"')->assertSeeHtml('wire:click="$parent.previewSelectedSource(101)"');
    // The browser bundles reactive children with the parent, unlike a simple test call().
    $dom = \Dom\HTMLDocument::createFromString($panel->effects['html'], LIBXML_NOERROR);
    $table = $dom->querySelector('[wire\\:name="translation-workbench.source-replacement-table"]');
    $response = $this->withoutMiddleware()->postJson(Livewire::getUpdateUri(), ['components' => [
        ['snapshot' => json_encode($panel->snapshot), 'updates' => [], 'calls' => [['method' => 'openScanWorkflow', 'params' => []]]],
        ['snapshot' => $table->getAttribute('wire:snapshot'), 'updates' => [], 'calls' => []],
    ]], ['X-Livewire' => 'true']);
    $response->assertOk()->assertJsonPath('components.1.skip', true);
    expect($response->json('components.0.effects.html'))->toContain('Scan scope');

});

it('keeps one complete source replacement key group selected across filters until released', function () {
    [$editor] = translationWorkbenchFixture();
    $group = ['key_id' => 8, 'full_name' => 'ui.button.save', 'finding_ids' => [11, 12, 99]];
    $editor->shouldReceive('sourceReplacementGroup')->once()->with(11)->andReturn($group);
    $editor->shouldReceive('sourceReplacementGroup')->once()->with(25)->andReturn([
        'key_id' => 9, 'full_name' => 'ui.button.close', 'finding_ids' => [25],
    ]);
    Livewire::test(TranslationWorkbench::class)
        ->call('selectSourceReplacementGroup', 11)->assertSet('sourceReplacementGroup', $group)
        ->set('sourceReplacementSearch', 'different')->set('sourceReplacementPerPage', 10)
        ->call('selectSourceReplacementGroup', 25)->assertSet('sourceReplacementGroup', $group)
        ->call('releaseSourceReplacementGroup')->assertSet('sourceReplacementGroup', [])
        ->call('selectSourceReplacementGroup', 25)->assertSet('sourceReplacementGroup.key_id', 9);
});

it('leaves source replacement selection empty when the finding is no longer eligible', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('sourceReplacementGroup')->with(11)->andThrow(new RuntimeException('Finding changed.'));
    Livewire::test(TranslationWorkbench::class)->call('selectSourceReplacementGroup', 11)
        ->assertSet('sourceReplacementGroup', [])->assertHasErrors('sourceReplacementGroup');
});

it('sorts source replacement findings while retaining search and selection', function ($column) {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('sourceReplacementPage')->with('ui.button', 1, 25, 'finding', 'asc')
        ->andReturn(new LengthAwarePaginator([], 0, 25));
    $first = $column === 'finding' ? 'desc' : 'asc';
    $second = $first === 'asc' ? 'desc' : 'asc';
    foreach ([$first, $second] as $direction) {
        $editor->shouldReceive('sourceReplacementPage')->with('ui.button', 1, 25, $column, $direction)
            ->andReturn(new LengthAwarePaginator([], 0, 25));
    }
    $component = Livewire::test(\Gunreip\TranslationWorkbench\Livewire\SourceReplacementTable::class,
        ['search' => 'ui.button', 'selectedIds' => '11,12']);
    $component->instance()->setPage(3, 'sourceReplacementPage');
    $component->call('sortBy', $column)->assertSet('sortColumn', $column)->assertSet('sortDirection', $first)
        ->assertSet('paginators.sourceReplacementPage', 1)
        ->assertSet('search', 'ui.button')->assertSet('selectedIds', '11,12')
        ->call('sortBy', $column)->assertSet('sortDirection', $second)
        ->call('sortBy', 'invalid')->assertSet('sortColumn', $column)->assertSet('sortDirection', $second);
})->with(['finding', 'key_id', 'key', 'source', 'expression']);

it('opens a read-only source comparison only for findings in the selected key group', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('sourceReplacementGroup')->with(609)->andReturn([
        'key_id' => 1538, 'full_name' => 'ui.table.header.reason', 'finding_ids' => [609],
    ]);
    $editor->shouldReceive('sourceReplacementComparison')->once()->with(609, 1538)->andReturn([
        'finding' => ['id' => 609, 'key_id' => 1538, 'full_name' => 'ui.table.header.reason', 'path' => 'example.php',
            'source_line' => 683, 'source_column' => 130, 'raw_expression' => "__('Reason')"],
        'check' => ['state' => 'possible_move', 'detail' => 'Possible move.', 'positions' => [['line' => 705, 'column' => 130]],
            'contexts' => [['line' => 705, 'column' => 130, 'text' => '<script>not executable</script>',
                'lines' => [['number' => 705, 'focus' => true, 'before' => '', 'match' => '<script>not executable</script>', 'after' => '']]]]],
    ]);
    Livewire::test(TranslationWorkbench::class)->call('openSourceComparison', 609)
        ->assertHasErrors('sourceReplacementGroup')->assertSet('sourceComparisonOpen', false)
        ->call('selectSourceReplacementGroup', 609)->call('openSourceComparison', 609)
        ->assertSet('sourceComparisonOpen', true)->assertSee('Proposed handling — not applied')
        ->assertSee('Position comparison')->assertSee('Lines')->assertSee('+22')->assertSee('Columns')->assertSee('+0')
        ->assertDontSeeHtml('<script>not executable</script>')
        ->call('releaseSourceReplacementGroup')->assertSet('sourceComparisonOpen', false)->assertSet('sourceComparison', []);
});

it('leaves the source comparison when continuing the source workflow step', function () {
    [$editor] = translationWorkbenchFixture();
    $overview = ['finding' => ['id' => 609, 'key_id' => 1538],
        'steps' => [['id' => 'source', 'status' => 'current']]];
    $editor->shouldReceive('workflowOverview')->once()->with(609, 'de')->andReturn($overview);
    $component = Livewire::test(TranslationWorkbench::class);
    $instance = $component->instance();
    $instance->workflowStatusOpen = true;
    $instance->workflowStatus = $overview;
    $instance->sourceComparisonOpen = true;
    $instance->sourcePreviewOpen = true;
    $instance->continueWorkflow();

    expect($instance->sourceComparisonOpen)->toBeFalse()
        ->and($instance->sourcePreviewOpen)->toBeFalse()
        ->and($instance->workflowStatusOpen)->toBeFalse()
        ->and($instance->tab)->toBe('source-replacement')
        ->and($instance->sourceReplacementSearch)->toBe('609');
});

it('checks the complete source replacement selection and clears stale results', function () {
    [$editor] = translationWorkbenchFixture();
    $group = ['key_id' => 8, 'full_name' => 'ui.button.save', 'finding_ids' => [11, 12, 99]];
    $editor->shouldReceive('sourceReplacementGroup')->with(11)->andReturn($group);
    $report = ['checked_at' => '2026-11-02T10:00:00+00:00', 'total' => 3, 'confirmed' => 2,
        'attention' => [['id' => 99, 'detail' => 'Source file cannot be read.']]];
    $editor->shouldReceive('checkSourceReplacementGroup')->once()->with(8, [11, 12, 99])->andReturn($report);
    $editor->shouldReceive('checkSourceReplacementGroup')->once()->with(8, [11, 12, 99])
        ->andThrow(new RuntimeException('Assignments changed.'));
    Livewire::test(TranslationWorkbench::class)
        ->call('checkSelectedSourceFindings')->assertHasErrors('sourceReplacementGroup')
        ->call('selectSourceReplacementGroup', 11)->set('sourceReplacementSearch', 'something else')
        ->call('checkSelectedSourceFindings')->assertSet('sourceGroupCheck', $report)
        ->call('checkSelectedSourceFindings')->assertHasErrors('sourceReplacementGroup')->assertSet('sourceGroupCheck', [])
        ->call('releaseSourceReplacementGroup')->assertSet('sourceGroupCheck', []);
    $html = view('translation-workbench::livewire.translation-workbench.tabs.source-replacement.group-check',
        ['sourceGroupCheck' => $report])->render();
    expect($html)->toContain('3 findings checked', 'openSourceComparison(99)');
});

it('requires an explicit confirmed source position decision and releases the group after applying it', function () {
    [$editor] = translationWorkbenchFixture();
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    $editor->shouldReceive('sourceReplacementGroup')->with(609)->andReturn([
        'key_id' => 1538, 'full_name' => 'ui.table.header.reason', 'finding_ids' => [609, 20483],
    ]);
    $editor->shouldReceive('sourceReplacementComparison')->with(609, 1538)->andReturn([
        'finding' => ['id' => 609, 'key_id' => 1538, 'full_name' => 'ui.table.header.reason', 'path' => 'example.php',
            'source_line' => 683, 'source_column' => 130, 'raw_expression' => "__('Reason')"],
        'check' => ['state' => 'possible_move', 'detail' => 'Possible move.', 'positions' => [['line' => 705, 'column' => 130]]],
        'decision' => ['fingerprint' => 'reviewed-fingerprint', 'can_move' => true, 'can_remove' => false],
    ]);
    $editor->shouldReceive('applySourcePositionDecision')->once()
        ->withArgs(fn ($operation, $finding, $key, $expected, $decision, $actor, $reason) =>
            \Illuminate\Support\Str::isUuid($operation) && $finding === 609 && $key === 1538
            && $expected === 'reviewed-fingerprint' && $decision === 'moved' && $actor > 0 && $reason === 'Confirmed the moved call.')
        ->andReturn(['key' => 1538, 'successors' => [['predecessor' => 609, 'successor' => 22000]]]);
    Livewire::test(TranslationWorkbench::class)
        ->call('selectSourceReplacementGroup', 609)->call('openSourceComparison', 609)
        ->assertSee('Reason for source finding decision')->assertSee('Confirmed move')
        ->call('applySourcePositionDecision')->assertHasErrors(['sourcePositionChoice', 'sourcePositionReason', 'sourcePositionConfirmed'])
        ->set('sourcePositionChoice', 'moved')->set('sourcePositionReason', 'Confirmed the moved call.')
        ->set('sourcePositionConfirmed', true)->call('applySourcePositionDecision')
        ->assertHasNoErrors()->assertSet('sourceComparisonOpen', false)->assertSet('sourceReplacementGroup', [])
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'success'
            && $params['slots']['text'] === 'Finding #609 archived; successor #22000 created with the same suggested key and translation key assignment.');
});

it('opens a source replacement preview directly for a selected group and clears it on release', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('sourceReplacementGroup')->with(11)->andReturn([
        'key_id' => 8, 'full_name' => 'ui.button.save', 'finding_ids' => [11, 12, 99],
    ]);
    $preview = ['finding' => 99, 'key_id' => 8, 'key' => 'ui.button.save', 'path' => 'example.blade.php', 'line' => 2, 'column' => 3,
        'before' => "__('Save')", 'after' => "__('ui.button.save')", 'already_set' => false, 'blocker' => null,
        'language_path' => 'en/ui.php', 'source_locale' => 'en', 'published_value' => 'Save', 'ready' => true, 'fingerprint' => 'source-hash'];
    $editor->shouldReceive('sourceReplacementPreviewForGroup')->once()->with(99, 8, [11, 12, 99])->andReturn($preview);
    app(SourceReplacementWriter::class)->shouldNotReceive('prepare');
    app(SourceReplacementWriter::class)->shouldNotReceive('apply');
    Livewire::test(TranslationWorkbench::class)
        ->call('previewSelectedSource', 99)->assertHasErrors('sourcePreview')->assertSet('sourcePreviewOpen', false)
        ->call('selectSourceReplacementGroup', 11)->call('previewSelectedSource', 25)->assertHasErrors('sourcePreview')
        ->set('sourceReason', 'Previous reason')->call('previewSelectedSource', 99)->assertHasNoErrors()
        ->assertSet('sourcePreview.finding', 99)->assertSet('sourceReason', '')->assertSet('editorOpen', false)
        ->assertSet('sourcePreviewOpen', true)->assertSee('Prepare source replacement')
        ->call('releaseSourceReplacementGroup')->assertSet('sourcePreviewOpen', false)->assertSet('sourcePreview', []);
});

it('rejects a stale selected source preview without keeping its old content', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('sourceReplacementGroup')->with(11)->andReturn([
        'key_id' => 8, 'full_name' => 'ui.button.save', 'finding_ids' => [11],
    ]);
    $editor->shouldReceive('sourceReplacementPreviewForGroup')->with(11, 8, [11])
        ->andThrow(new RuntimeException('The selected findings changed.'));
    Livewire::test(TranslationWorkbench::class)->call('selectSourceReplacementGroup', 11)
        ->call('previewSelectedSource', 11)->assertHasErrors('sourcePreview')
        ->assertSet('sourcePreview', [])->assertSet('sourcePreviewOpen', false);
});

it('restores saved active sublanguage drafts when opening a finding without saving or leaking another key values', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $locales = Mockery::mock(TranslationWorkbenchEditorLocales::class);
    $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => ['de-AT', 'de-CH', 'de-LI']]);
    app()->instance(TranslationWorkbenchEditorLocales::class, $locales);
    $editor->shouldReceive('inspect')->with(11, 'de')->andReturn($context);
    $editor->shouldReceive('savedLanguageVariants')->with(8, ['de-AT', 'de-CH', 'de-LI'])->andReturn([
        'de-AT' => ['id' => 31, 'value' => 'Gespeicherter Text'],
        'de-CH' => ['id' => 32, 'value' => '0'],
    ]);
    $other = $context;
    $other['finding']['id'] = 12;
    $other['finding']['key_id'] = 9;
    $editor->shouldReceive('inspect')->with(12, 'de')->andReturn($other);
    $editor->shouldReceive('savedLanguageVariants')->with(9, ['de-AT', 'de-CH', 'de-LI'])->andReturn([]);
    $editor->shouldNotReceive('saveDrafts');
    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->assertSet('selectedSubLanguages', ['de-AT', 'de-CH'])
        ->assertSet('subLanguageValues.de-AT', 'Gespeicherter Text')->assertSet('subLanguageValues.de-CH', '0')
        ->assertSet('subLanguageRevisions.de-AT', 31)->assertSet('subLanguageSavedValues.de-CH', '0')
        ->assertSeeHtml('wire:model="subLanguageValues.de-AT"')->assertDontSeeHtml('wire:model="subLanguageValues.de-LI"')
        ->set('selectedSubLanguages', [])->call('open', 11)->assertSet('selectedSubLanguages', ['de-AT', 'de-CH'])
        ->call('open', 12)->assertSet('selectedSubLanguages', [])->assertSet('subLanguageValues', []);
});

it('previews and prepares the complete selected source group without applying files', function () {
    [$editor] = translationWorkbenchFixture();
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    $editor->shouldReceive('sourceReplacementGroup')->with(11)->andReturn([
        'key_id' => 8, 'full_name' => 'ui.button.save', 'finding_ids' => [11, 99],
    ]);
    $preview = ['batch' => true, 'key_id' => 8, 'key' => 'ui.button.save', 'finding' => 11, 'finding_ids' => [11, 99], 'source_locale' => 'en',
        'fingerprint' => 'batch-hash', 'ready' => true, 'blocked' => 0, 'items' => [
            ['finding' => 11, 'path' => 'a.php', 'before' => "__('Save')", 'after' => "__('ui.button.save')", 'already_set' => false, 'ready' => true, 'blocker' => null],
            ['finding' => 99, 'path' => 'b.php', 'before' => "__('Save')", 'after' => "__('ui.button.save')", 'already_set' => false, 'ready' => true, 'blocker' => null],
        ]];
    $writer = app(SourceReplacementWriter::class);
    $writer->shouldReceive('previewBatch')->once()->with(8, [11, 99])->andReturn($preview);
    $writer->shouldReceive('prepareBatch')->once()->with(8, [11, 99], 'batch-hash', 7, 'Shared reason')->andReturn('batch-run');
    $writer->shouldReceive('inspect')->with('batch-run')->andReturn([
        'batch' => true, 'run' => 'batch-run', 'actor' => 7, 'reason' => 'Shared reason', 'preview' => $preview,
        'state' => 'pending', 'files' => [['run' => 'file-run', 'path' => 'a.php', 'state' => 'pending', 'file_state' => 'original', 'impacts' => []]],
    ]);
    $writer->shouldNotReceive('apply');
    Livewire::test(TranslationWorkbench::class)->call('previewSelectedSourceGroup')->assertHasErrors('sourcePreview')
        ->call('selectSourceReplacementGroup', 11)->call('previewSelectedSourceGroup')
        ->assertHasNoErrors()->assertSet('sourcePreviewOpen', true)->assertSee('Key #8 · 2 findings')->assertSee('b.php')
        ->set('sourceReason', 'Shared reason')->call('prepareSource')
        ->assertHasNoErrors()->assertSet('sourcePreviewOpen', false)->assertSet('sourceRunOpen', true)
        ->assertSee('Confirm selected replacements / resume');
});

it('shows the regular scan workflow for a stale finding elsewhere in the source file', function () {
    $item = ['finding' => 20486, 'path' => 'example.blade.php', 'before' => "__('Reason')", 'after' => "__('ui.table.reason')", 'ready' => false, 'already_set' => false,
        'blocker' => 'Stored source position is stale', 'scan_required' => ['finding' => 522, 'path' => 'example.blade.php', 'line' => 58, 'column' => 24]];
    $html = view('translation-workbench::livewire.translation-workbench.shared.source-batch-preview', ['sourcePreview' => [
        'key_id' => 1538, 'key' => 'ui.table.reason', 'items' => [$item, array_replace($item, ['finding' => 20488])], 'ready' => false, 'blocked' => 2,
    ]])->render();
    expect($html)->toContain('New scan required', '#522', '58:24', 'php artisan translation-workbench:generation-scan --apply')
        ->not->toContain('Open prepared operation');
    expect(substr_count($html, 'Finding to check'))->toBe(1);
});

it('shows scan requirements in the overview before opening a source preview', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanRequirementSummary')->andReturn([
        'required' => true, 'findings' => 1, 'files' => 1,
        'examples' => [['finding' => 522, 'path' => 'example.php', 'line' => 58, 'column' => 24]],
    ]);
    Livewire::test(TranslationWorkbench::class)->assertSee('New scan required')
        ->assertSee('Stored positions for findings in source files')
        ->assertSee('Refresh source check')->assertSet('sourcePreviewOpen', false)
        ->call('refreshScanRequirement')->assertHasNoErrors();
});


it('opens the scan workflow and confirms only a completed server preview', function () {
    translationWorkbenchFixture();
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    session()->forget('workbench.scan_workflow');
    $target = ['database' => 'fixture', 'schema' => 'current_generation_v2', 'paths' => ['resources/views']];
    $service = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class);
    $service->shouldReceive('target')->once()->andReturn($target);
    app()->instance(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class, $service);
    $coordinator = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class);
    $id = (string) \Illuminate\Support\Str::uuid();
    $coordinator->shouldReceive('start')->once()->with(7)->andReturn($id);
    $coordinator->shouldReceive('confirm')->once()->with($id, 7);
    $counts = array_fill_keys(['confirmed', 'created', 'moved', 'removed', 'review_files', 'outside_scope_files', 'suggested_keys_initialized', 'usage_decisions'], 0);
    $coordinator->shouldReceive('status')->with($id, 7)->andReturn(
        ['state' => 'preview_ready', 'target' => $target, 'preview' => ['counts' => $counts]],
        ['state' => 'queued_apply', 'target' => $target, 'preview' => ['counts' => $counts]],
    );
    app()->instance(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class, $coordinator);
    Livewire::test(TranslationWorkbench::class)->call('openScanWorkflow')->assertHasNoErrors()
        ->assertSee('Scan scope')->assertDontSeeHtml('wire:click="confirmScan"')
        ->call('startScanPreview')->assertHasNoErrors()->assertSee('Scan and save results')
        ->call('confirmScan')->assertHasNoErrors()->assertSee('Checking and saving scan results');
});

it('polls scan progress independently until the workbench needs the phase result', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldNotReceive('recentScans');
    $id = (string) \Illuminate\Support\Str::uuid();
    $progress = ['state' => 'scanning', 'progress' => ['steps' => [
        ['phase' => 'scanning', 'status' => 'running', 'percent' => 40],
    ]]];
    $coordinator = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class);
    $coordinator->shouldReceive('status')->with($id, 7)->andReturn($progress, $progress, ['state' => 'preview_ready']);
    app()->instance(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class, $coordinator);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);

    $panel = Livewire::test(\Gunreip\TranslationWorkbench\Livewire\ScanProgress::class, [
        'scanWorkflowId' => $id, 'scanWorkflow' => ['state' => 'queued_preview', 'progress' => ['steps' => []]],
    ])->call('refreshProgress')->assertSee('40%')->assertNotDispatched('scan-phase-ended');
    $panel->call('refreshProgress');
    expect($panel->effects)->not->toHaveKey('html');
    $panel->call('refreshProgress')->assertDispatched('scan-phase-ended')->assertDontSeeHtml('wire:poll.500ms');
});

it('opens unchanged recent scan results without starting a scan', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->with('recent-run', 'created', 1)->andReturn(new LengthAwarePaginator([], 0, 25));
    $freshness = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\ScanResultFreshness::class);
    $freshness->shouldReceive('inspect')->once()->andReturn(['reusable' => true, 'run' => 'recent-run', 'expires' => time() + 60]);
    app()->instance(\Gunreip\TranslationWorkbench\Generation\ScanResultFreshness::class, $freshness);
    Livewire::test(TranslationWorkbench::class)->call('openCurrentScan')->assertHasNoErrors()
        ->assertSet('selectedScan', 'recent-run')->assertSet('scanOpen', true)->assertSet('scanWorkflowOpen', false)->assertSee('Open last scan');
});

it('rechecks scan freshness on click instead of trusting the previous button state', function () {
    translationWorkbenchFixture();
    session()->forget('workbench.scan_workflow');
    $freshness = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\ScanResultFreshness::class);
    $freshness->shouldReceive('inspect')->twice()->andReturn(
        ['reusable' => true, 'run' => 'recent-run', 'expires' => time() + 60],
        ['reusable' => false, 'reason' => 'Source files changed.'],
    );
    app()->instance(\Gunreip\TranslationWorkbench\Generation\ScanResultFreshness::class, $freshness);
    $service = Mockery::mock(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class);
    $service->shouldReceive('target')->once()->andReturn(['database' => 'fixture', 'schema' => 'fixture', 'paths' => []]);
    app()->instance(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class, $service);
    Livewire::test(TranslationWorkbench::class)->call('refreshScanFreshness')->assertSee('Open last scan')
        ->call('openCurrentScan')->assertHasNoErrors()->assertSet('scanOpen', false)->assertSet('scanWorkflowOpen', true)
        ->assertSee('Run new scan')->assertSee('Source files changed.');
});

it('lists confirmed scan findings and displays counts for every result category', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanCategoryCounts')->with('scan-id')->andReturn([
        'created' => 0, 'confirmed' => 9004, 'moved' => 0, 'removed' => 0,
        'review_files' => 367, 'usage_decisions' => 0, 'missing_immutable' => 0,
    ]);
    $editor->shouldReceive('scanPage')->with('scan-id', 'created', 1)->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanPage')->with('scan-id', 'confirmed', 1)->andReturn(new LengthAwarePaginator([
        ['id' => 11, 'path' => 'confirmed.php', 'source_line' => 12, 'predecessor' => null],
    ], 9004, 25));
    Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')
        ->assertSee('New findings')->assertSee('Unchanged confirmed findings')->assertSee('367')
        ->set('scanCategory', 'confirmed')->assertHasNoErrors()->assertSee('confirmed.php:12');
});

it('keeps a native close control in captured scan results', function () {
    [$editor] = translationWorkbenchFixture();
    $editor->shouldReceive('scanPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $editor->shouldReceive('scanCapturesPage')->andReturn(new LengthAwarePaginator([], 0, 25));
    $component = Livewire::test(TranslationWorkbench::class)->call('openScan', 'scan-id')->call('openScanCaptures', 'example.php');
    $html = $component->html();
    $dom = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
    $modal = $dom->querySelector('dialog[data-modal="workbench-scan-results"]');
    expect($modal)->not->toBeNull();
    expect($modal->querySelectorAll('[data-flux-modal-close]')->length)->toBe(1);
    expect($modal->hasAttribute('data-flux-modal-overflow'))->toBeFalse();
    expect($modal->querySelector('[data-flux-modal-content]'))->toBeNull();
    expect($modal->getAttribute('class'))->toContain('relative');
    expect($modal->querySelector('[data-flux-modal-close]')->parentElement->parentElement->isSameNode($modal))->toBeTrue();
    expect(strpos($modal->textContent, 'Back to scan results'))->toBeLessThan(strpos($modal->textContent, 'Scan context'));
});

it('keeps targeted loading overlays inside the workbench dialogs', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11);
    $document = new DOMDocument;
    @$document->loadHTML($component->html());
    $xpath = new DOMXPath($document);
    $loaders = $xpath->query('//*[@data-workbench-modal-loading]');
    expect($loaders->length)->toBeGreaterThanOrEqual(20);
    foreach ($loaders as $loader) {
        expect($xpath->query('ancestor::dialog', $loader)->length)->toBe(1);
        $targets = $loader->getAttribute('wire:target');
        expect($targets)->not->toBe('')->not->toContain('refreshScanFreshness')->not->toContain('pollScanWorkflow');
    }
});


it('confirms the persisted key id in a toast for new and existing assignments', function ($name, $key, $newName) {
    [$editor, $context] = translationWorkbenchFixture();
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldReceive('assign')->once()
        ->with(11, $newName === null ? 8 : 0, 4, 7, 'Reviewed assignment', $newName)
        ->andReturn(['key' => $key, 'link' => 99]);
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);

    Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('assignmentKey', $name)->set('assignmentReason', 'Reviewed assignment')
        ->call('confirmAssignment')->call('assign')->assertHasNoErrors()
        ->assertSet('confirmOpen', false)
        ->assertDispatched('toast-show', fn ($event, $params) =>
            $params['dataset']['variant'] === 'success'
            && $params['slots']['heading'] === 'Translation key assignment confirmed'
            && $params['slots']['text'] === "Finding #11 → Key record #{$key}. Translation key: {$name}");
})->with([
    'existing key' => ['ui.button.save', 8, null],
    'new key' => ['ui.button.new_key', 123, 'ui.button.new_key'],
]);

it('disables assignment confirmation only for the already linked key', function ($assigned, $selectedKey, $disabled, $badge) {
    [$editor, $context] = translationWorkbenchFixture();
    if (! $assigned) {
        $context['finding']['key_id'] = null;
        $context['finding']['link_id'] = null;
        $context['finding']['full_name'] = null;
    }
    $editor->shouldReceive('inspect')->andReturn($context);
    $editor->shouldNotReceive('assign');
    $component = Livewire::test(TranslationWorkbench::class)->call('open', 11)
        ->set('assignmentKey', $selectedKey)
        ->set('assignmentReason', 'Review this assignment')
        ->call('confirmAssignment')->assertSet('confirmOpen', true)->assertHasNoErrors();

    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$component->html());
    $xpath = new DOMXPath($document);
    $buttons = $xpath->query('//button[@*[name()="wire:click" and .="assign"]]');
    expect($buttons->length)->toBe(2);
    foreach ($buttons as $button) {
        expect($button->hasAttribute('disabled'))->toBe($disabled)
            ->and($button->getAttribute('wire:loading.attr'))->toBe('disabled');
    }
    if ($badge !== null) {
        $component->assertSee($badge);
    }
    if ($disabled) {
        $component->assertSee('Finding #11 already uses this translation key. No further confirmation is needed.');
    }
})->with([
    'unchanged assignment' => [true, 'ui.button.save', true, 'Already assigned'],
    'different key selected' => [true, 'ui.button.new_key', false, 'Different key selected'],
    'first assignment' => [false, 'ui.button.save', false, null],
]);

it('watches only existing Livewire properties when initializing the finding editor', function () {
    $source = file_get_contents(resource_path('../packages/gunreip/laravel-translation-workbench/resources/views/livewire/translation-workbench/modals/finding-editor.blade.php'));
    preg_match('/x-init="([^"]+)"/', $source, $init);
    preg_match_all('/\$watch\(\x27\$wire\.([a-zA-Z_]+)\x27/', $init[1], $direct);
    preg_match_all('/\[([^\]]+)\]\s*\.forEach/', $init[1], $lists);
    $properties = $direct[1];
    foreach ($lists[1] as $list) {
        preg_match_all('/\x27([a-zA-Z_]+)\x27/', $list, $names);
        array_push($properties, ...$names[1]);
    }
    expect($properties)->not->toBeEmpty();
    $component = new ReflectionClass(TranslationWorkbench::class);
    foreach ($properties as $property) {
        expect($component->hasProperty($property), 'Unknown watched property: '.$property)->toBeTrue()
            ->and($component->getProperty($property)->isPublic())->toBeTrue();
    }
});

it('lists manually started shared work and resets its filters independently', function () {
    [$editor] = translationWorkbenchFixture();
    $user = new User;
    $user->id = 7;
    $this->actingAs($user);
    $row = ['key_id' => 8, 'namespace' => '', 'full_name' => 'ui.button.save', 'finding_count' => 22,
        'finding_id' => 505, 'locales' => 'de, de-AT, en', 'step' => 'translations',
        'source_pending' => 20, 'occurred_at' => '2026-11-08 10:00:00+00', 'actor_id' => 7];
    $editor->shouldReceive('recentWork')->with('de', 7)->andReturn([$row]);
    $editor->shouldReceive('workTasks')->with('de', 7, '', 1, 25)
        ->andReturn(new LengthAwarePaginator([$row], 1, 25));
    $component = Livewire::test(TranslationWorkbench::class)->set('tab', 'in-progress')
        ->assertSee('In progress')->assertSee('Recently edited')->assertSee('Shared key')
        ->assertSee('22 active')->assertSee('Continue with finding #505')
        ->assertSeeHtml('wire:click="resumeWork(8)"');
    $editor->shouldReceive('workTasks')->with('de', null, 'old/path.php', 1, 25)
        ->andReturn(new LengthAwarePaginator([], 0, 25));
    $component->set('paginators.workPage', 3)->set('workSearch', 'old/path.php')
        ->assertSet('paginators.workPage', 1)->set('workOnlyMine', false)
        ->set('workPerPage', 999)->assertSet('workPerPage', 25);
});

it('resumes through the current active finding instead of a previously edited predecessor', function () {
    [$editor, $context] = translationWorkbenchFixture();
    $context['finding']['id'] = 22000;
    $editor->shouldReceive('workItem')->with(8, 'de')->once()->andReturn([
        'finding_id' => 22000, 'step' => 'translations', 'full_name' => 'ui.button.save', 'open_publication' => null,
    ]);
    $editor->shouldReceive('workflowOverview')->with(22000, 'de')->andReturn([
        'finding' => $context['finding'], 'languages' => [], 'shared' => [], 'successor' => null,
        'steps' => (new \Gunreip\TranslationWorkbench\Generation\WorkflowProgress)->steps(
            $context['finding'], [['locale' => 'de', 'state' => 'missing', 'revision' => null]], false, false
        ),
    ]);
    $editor->shouldReceive('inspect')->with(22000, 'de')->andReturn($context);
    Livewire::test(TranslationWorkbench::class)->call('resumeWork', 8)
        ->assertHasNoErrors()->assertSet('selected', 22000)->assertSet('editorOpen', true)
        ->assertSet('workflowStatusOpen', false)
        ->assertDispatched('workflow-focus', target: 'workbench-translation-values');
});

it('resumes an open publication without preparing a second run', function () {
    [$publisher, $plan] = generationPublicationFixture();
    $editor = app(GenerationEditor::class);
    $editor->shouldReceive('workItem')->with(8, 'de')->once()->andReturn([
        'finding_id' => 11, 'step' => 'publication', 'full_name' => 'ui.button.save', 'open_publication' => $plan['run'],
    ]);
    $publisher->shouldReceive('inspect')->with($plan['run'])->andReturn($plan);
    $publisher->shouldNotReceive('prepare');
    Livewire::test(TranslationWorkbench::class)->call('resumeWork', 8)
        ->assertHasNoErrors()->assertSet('publicationOpen', true)->assertSet('publication.run', $plan['run']);
});
