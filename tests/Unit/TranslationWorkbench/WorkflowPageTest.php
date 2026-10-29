<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders only the selected workflow graph and validates tab changes', function () {
    Livewire::test(TranslationWorkbenchWorkflow::class)
        ->assertSeeHtml('translation-workflow-legacy')
        ->assertDontSeeHtml('translation-workflow-workbench')
        ->set('activeTab', 'workbench')
        ->assertSeeHtml('translation-workflow-workbench')
        ->assertDontSeeHtml('translation-workflow-legacy')
        ->set('activeTab', 'target')
        ->assertSeeHtml('translation-workflow-target')
        ->assertSee('Plan and implementation')
        ->assertSee('workflow.target.reconcile')
        ->assertSee('Decision recorded; production transition pending')
        ->assertSee('Package guard implemented; application setup pending')
        ->assertSee('Empty generation installation verified in isolation')
        ->assertSee('Selected transfer and correction: see recorded rehearsal result')
        ->assertSee('Complete evidenced name inventory verified in isolation')
        ->assertDontSeeHtml('translation-workflow-workbench')
        ->set('activeTab', 'target-ui')
        ->assertSeeHtml('id="workflow-target-ui"')
        ->assertSee('Plan and implementation')
        ->assertSee('workflow.target-ui.recognize')
        ->assertSee('Unlisted graph steps have not been assessed here.')
        ->assertSee('roadmap for the future UI', false)
        ->assertSee('Known and uniquely identified as moved?')
        ->assertSee('Update position; retain finding and edits')
        ->assertSee('Log old → new position in this scan')
        ->assertSee('Previously active occurrence confirmed removed?')
        ->assertSee('Keep key and translations active')
        ->assertSee('Mark unused; await review')
        ->assertSee('No repeated removal events')
        ->assertSee('Store original as immutable raw_data')
        ->assertSee('Generate SuggestedKey')
        ->assertSee('NO · new or additional active occurrence')
        ->assertSee('Additional occurrence → sharedCandidate check')
        ->assertSee('Capture active source occurrences only')
        ->assertSee('Ignore commented-out code')
        ->assertSee('comments must not count towards sharedCandidate')
        ->assertSee('Replaced literal: new capture + removal')
        ->assertSee('Later wording changes retain the key')
        ->assertSee('Original raw_data stays immutable')
        ->assertDontSee('Same occurrence with changed literal text?')
        ->assertDontSee('Append source revision; keep raw_data')
        ->assertSee('Scan process · detail of step 2')
        ->assertSee('Resolve the effective scan scope')
        ->assertSee('Match observations to known findings')
        ->assertSee('Incomplete scan: no removal inference')
        ->assertSee('Status: complete, incomplete or failed')
        ->assertSee('No translation editing or publication')
        ->assertSee('Read the configured scan scope')
        ->assertSee('Make explicit path overrides visible')
        ->assertSee('Capture the source context')
        ->assertSee('Store context beside immutable raw_data')
        ->assertSee('Clarify unknown purpose later in the UI')
        ->assertSee('Classify the argument origin')
        ->assertSee('Describe the text form')
        ->assertSee('Check the translation-key reference')
        ->assertSee('Key reference or unresolved argument?')
        ->assertSee('YES · key reference or unresolved argument')
        ->assertSee('NO · resolved literal')
        ->assertSee('Published key and source occurrence reliably assigned?')
        ->assertSee('No new literal finding or SuggestedKey')
        ->assertSee('Keep occurrence active; no removal')
        ->assertSee('NO · assignment requires clarification')
        ->assertSee('Include observation or clarification in scan result')
        ->assertSee('Placeholders can occur in plural text')
        ->assertSee('Same literal: N ≥ 2 active findings?')
        ->assertSee('Mark all active group members')
        ->assertSee('Remove any existing group markers')
        ->assertSee('Recalculate the affected shared group')
        ->assertSee('Open from scan result or existing work list')
        ->assertSee('Understand the finding in its source context')
        ->assertSee('Does the context still need clarification?')
        ->assertSee('Deferral does not deactivate the finding')
        ->assertSee('Rescan does not clear clarification')
        ->assertSee('Later: clarify and reopen the same finding')
        ->assertSee('Resume deliberately at context review')
        ->assertSee('Select the translation task to work on')
        ->assertSee('Save explanations separately from source')
        ->assertSee('No automatic task or key merging')
        ->assertSee('Opening a task does not change its status')
        ->assertSee('Preview and confirm the publication changes')
        ->assertSee('Verify the written language entries')
        ->assertSee('Did language-file writing or verification fail?')
        ->assertSee('Stop dependent source replacement')
        ->assertSee('Inspect the failed run before planning recovery')
        ->assertSee('Do not automatically repeat successful writes')
        ->assertSee('Verify and show the publication result')
        ->assertSee('Verify key, arguments and resulting file validity')
        ->assertSee('Did source replacement or verification fail?')
        ->assertSee('Stop further source changes')
        ->assertSee('Inspect partial source replacement before recovery')
        ->assertSee('Verified replacements retain their finding link')
        ->assertSee('Mark complete only after verification')
        ->assertSee('Does the review require corrections?')
        ->assertSee('Record review feedback and reopen editing')
        ->assertSee('Other languages retain their own status')
        ->assertSee('Select the saved values for review')
        ->assertSee('Approval belongs to this saved state')
        ->assertSee('Approval does not publish anything')
        ->assertSee('Check approval before starting publication')
        ->assertSee('Changed assignment or value: review again')
        ->assertSee('Shared candidates suitable for one common key?')
        ->assertSee('YES · propose a shared key')
        ->assertSee('NO · keep a separate key')
        ->assertSee('Activation is independent of completeness')
        ->assertSee('Resolve the displayed language value')
        ->assertSee('Parent value missing → source language en')
        ->assertSee('Before key publication: show source literal')
        ->assertSee('Verify source-language fallback before replacement')
        ->assertSee('Select the target languages')
        ->assertSee('Displayed fallback is not a translation')
        ->assertSee('Pause editing and continue later?')
        ->assertSee('Draft is not an effective translation')
        ->assertSee('Later: reopen the draft in the same task')
        ->assertSee('Keep published values and fallback unchanged')
        ->assertSee('Check the translation inputs')
        ->assertSee('Has the edited data or key assignment changed meanwhile?')
        ->assertSee('Compare current values with the proposed edit')
        ->assertSee('Do not reload over unsaved input')
        ->assertSee('Save only if the write-time check still matches')
        ->assertSee('Saving does not approve or publish')
        ->assertSee('Changed values need review again')
        ->assertSee('Accept or edit the SuggestedKey')
        ->assertSee('Check the proposed key assignment')
        ->assertSee('Confirm and save the key assignment')
        ->assertSee('Conflicts remain open for clarification')
        ->assertSee('Keep source text and language files unchanged')
        ->assertSee('Unchanged save creates no duplicate event')
        ->assertDontSeeHtml('id="workflow-target"')
        ->set('activeTab', 'write-contract')
        ->assertSeeHtml('workflow-write-contract')
        ->assertSee('Acceptance criteria — behavior tests still to implement')
        ->assertSeeHtml('contract-w01')
        ->assertSeeHtml('contract-w07')
        ->assertDontSeeHtml('data-tw-graph-preview-tools')
        ->set('activeTab', '../invalid')
        ->assertSet('activeTab', 'legacy');
});

it('keeps the workflow page behind the workbench administration middleware', function () {
    $route = Route::getRoutes()->getByName('admin.translation-workbench.workflow');
    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('admin/translation-workbench/workflow')
        ->and($route->gatherMiddleware())->toContain('auth', 'verified', 'role:Admin|Super-Admin');
});

it('documents every artisan command called by the legacy orchestrator', function () {
    $runner = file_get_contents(app_path('Console/Commands/ProjectTranslations.php'));
    preg_match_all("/'(translations:[a-z-]+(?::[a-z-]+)*)'/", $runner, $matches);
    $documentation = collect(glob(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/legacy/*.blade.php')))
        ->map(fn ($path) => file_get_contents($path))->implode("\n");

    foreach (array_unique($matches[1]) as $command) {
        expect($documentation)->toContain($command);
    }

    expect($documentation)->toContain('git apply --check latest.patch', '--apply-diffs?', 'NOT --skip-export', 'NOT --skip-audits', '--apply-usage-decisions?', 'Previously completed writes remain');
});

it('documents the workbench commands and their distinct write and refresh gates', function () {
    $runner = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php'));
    preg_match_all("/'(translation-workbench:[a-z-]+)'/", $runner, $matches);
    $documentation = collect(glob(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/workbench/*.blade.php')))
        ->map(fn ($path) => file_get_contents($path))->implode("\n");

    foreach (array_unique($matches[1]) as $command) {
        expect($documentation)->toContain($command);
    }

    expect($documentation)->toContain('--complete AND NOT --dry-run', '--write --only-apply',
        'Independent scans, not a migration', 'No --write in this call',
        'Previously completed writes remain', '--skip-lang-file-export',
        'Deferred import after initial export', 'Final source-change preview');
});

it('renders the target lifecycle in one graph with separate stop and publication routes', function () {
    $view = view('translation-workbench::livewire.workflow.target')->render();
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/target.blade.php'));

    $document = new DOMDocument;
    @$document->loadHTML($view);
    $xpath = new DOMXPath($document);

    expect($view)->not->toContain('Both outputs remain separate:')
        ->and($xpath->query('//*[@data-tw-graph-preview-tools]')->length)->toBe(1)
        ->and($source)->toContain('attach-to="workflow.target.approval.false.anchorNode-end"',
            'attach-to="workflow.target.verification.false.anchorNode-end"',
            "'workflow.target.approval.true.anchorNode-end'",
            "'workflow.target.verification.true.anchorNode-end'",
            'No overwrite of editorial decisions', 'Stale / invalid plan → STOP, no writes',
            'TW-Graph renders the supplied history', 'Proposal, not implemented behavior');
});

it('returns the UI workflow to its main axis after decisions', function () {
    view('translation-workbench::livewire.workflow.target-ui')->render();
    $graph = 'translation-workflow-target-ui';
    $coordinate = fn (string $id) => BoundsRegistry::evaluateRemExpression(
        AnchorRegistry::get($graph, 'workflow.target-ui.'.$id)['x'],
    );
    $mainAxis = $coordinate('start.anchorNode-end');

    expect($mainAxis)->not->toBeNull();
    foreach (['scan-scope.anchorNode-end', 'scan-detail.return.anchorNode-end', 'source-context.anchorNode-end', 'argument-origin.anchorNode-end', 'text-form.anchorNode-end', 'key-reference.anchorNode-end', 'literal-return.anchorNode-end', 'scan-result.anchorNode-end', 'shared-log.anchorNode-end', 'capture.anchorNode-end', 'editorial-context.anchorNode-end', 'context-ready-return.anchorNode-end', 'translation-task.anchorNode-end', 'shared-choice-return.anchorNode-end', 'key-check.anchorNode-end', 'key-save.anchorNode-end', 'target-languages.anchorNode-end', 'draft-continue.anchorNode-end', 'translation-check.anchorNode-end', 'concurrent-edit-ready.anchorNode-end', 'review-scope.anchorNode-end', 'review-ready-return.anchorNode-end', 'publication-readiness.anchorNode-end', 'publication-preview.anchorNode-end', 'language-file-check.anchorNode-end', 'language-write-success.anchorNode-end', 'source-write-success.anchorNode-end', 'publication-result.anchorNode-end', 'language-fallback.anchorNode-end', 'display.anchorNode-end'] as $anchor) {
        expect($coordinate($anchor))->toEqualWithDelta($mainAxis, 0.001);
    }
    expect($coordinate('removal-result.anchorNode-end'))->toBeLessThan($mainAxis);
    expect($coordinate('published-key.anchorNode-end'))->toBeLessThan($mainAxis);
    expect($coordinate('publication-recovery.anchorNode-end'))->toBeLessThan($mainAxis);
    expect($coordinate('source-recovery.anchorNode-end'))->toBeLessThan($mainAxis);
    expect($coordinate('input-kind.true.anchorNode-end'))->toBeLessThan($coordinate('input-kind.false.anchorNode-end'));
    $inputTrue = AnchorRegistry::get($graph, 'workflow.target-ui.input-kind.true.anchorNode-end');
    $inputFalse = AnchorRegistry::get($graph, 'workflow.target-ui.input-kind.false.anchorNode-end');
    expect(BoundsRegistry::evaluateRemExpression($inputFalse['y']))
        ->toBeLessThan(BoundsRegistry::evaluateRemExpression($inputTrue['y']));
    expect($coordinate('input-kind.false.anchorNode-end'))->toBeLessThan($mainAxis);
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/target-ui.blade.php'));
    expect($source)->toContain('attach-to="workflow.target-ui.scan-detail.return.anchorNode-end"')
        ->not->toContain('id="workflow.target-ui.scan-detail.end"');
    $detailAxis = $coordinate('scan-detail.start.anchorNode-end');
    expect($detailAxis)->toBeGreaterThan($mainAxis);
    foreach (['scope', 'read', 'classify', 'match', 'reconcile', 'shared', 'finish'] as $step) {
        expect($coordinate('scan-detail.'.$step.'.anchorNode-end'))->toEqualWithDelta($detailAxis, 0.001);
    }
});

it('renders the dashed scan axis with a positive length matching its anchors', function () {
    $html = view('translation-workbench::livewire.workflow.target-ui')->render();
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $lines = $xpath->query('//*[contains(@data-tw-graph-bounds, "workflow.target-ui.scan-detail.main-axis") and contains(@class, "tw-graph-protocol-primitive-line")]');
    expect($lines->length)->toBe(1);
    $line = $lines->item(0);
    expect($line->getAttribute('class'))->toContain('tw-graph-protocol-primitive-line-dashed');
    preg_match('/--tw-graph-protocol-local-length:\s*([^;]+)/', $line->getAttribute('style'), $match);
    $evaluate = fn ($expression) => BoundsRegistry::evaluateRemExpression($expression);
    $length = $evaluate($match[1]);
    $start = AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.scan.anchorNode-end');
    $end = AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.scan-detail.return.anchorNode-end');
    expect($length)->toBeGreaterThan(0)
        ->toEqualWithDelta($evaluate($start['y']) - $evaluate($end['y']), 0.001);
});

it('returns clarification outside the left branch into the context review stem', function () {
    view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $capture = $get('capture');
    $reentry = $get('clarification-loop.reentry');
    $turn = $get('clarification-loop.turn');
    $resume = $get('clarification-resume');
    expect($rem($reentry['x']))->toEqualWithDelta($rem($capture['x']), 0.001);
    expect($rem($reentry['y']))->toEqualWithDelta($rem($capture['y']) - 8, 0.001);
    expect($rem($turn['x']))->toBeLessThan($rem($resume['x']));
    expect($rem($turn['y']))->toBeLessThan($rem($reentry['y']));
    expect($reentry['direction'])->toBe('top-bottom');
});

it('joins both publication recovery paths before rechecking approval', function () {
    view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $review = $get('review');
    $reentry = $get('publication-recovery-loop.reentry');
    $join = $get('publication-recovery-loop.turn');
    $sourceTurn = $get('source-recovery-loop.turn');
    expect($rem($reentry['x']))->toEqualWithDelta($rem($review['x']), 0.001);
    expect($rem($reentry['y']))->toEqualWithDelta($rem($review['y']) - 8, 0.001);
    expect($rem($join['x']))->toEqualWithDelta($rem($sourceTurn['x']), 0.001);
    expect($rem($sourceTurn['y']))->toBeLessThan($rem($join['y']));
    expect($reentry['direction'])->toBe('top-bottom');
});

it('returns conflicting edits to language selection with a fresh baseline', function () {
    view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $key = $get('key-save');
    $reentry = $get('concurrent-edit-loop.reentry');
    expect($rem($reentry['x']))->toEqualWithDelta($rem($key['x']), 0.001);
    expect($rem($reentry['y']))->toEqualWithDelta($rem($key['y']) - 8, 0.001);
    expect($reentry['direction'])->toBe('top-bottom');
});

it('routes draft resumption outside the right branch back to editing', function () {
    view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $key = $get('key-save');
    $reentry = $get('draft-loop.reentry');
    $turn = $get('draft-loop.turn');
    $resume = $get('draft-resume');
    expect($rem($reentry['x']))->toEqualWithDelta($rem($key['x']), 0.001);
    expect($rem($reentry['y']))->toEqualWithDelta($rem($key['y']) - 8, 0.001);
    expect($rem($turn['x']))->toBeGreaterThan($rem($resume['x']));
    expect($reentry['direction'])->toBe('top-bottom');
});

it('joins review corrections to the existing editing return', function () {
    view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $join = $get('concurrent-edit-loop.turn');
    $turn = $get('review-feedback-loop.turn');
    expect($rem($turn['x']))->toEqualWithDelta($rem($join['x']), 0.001);
    expect($rem($turn['y']))->toBeLessThan($rem($join['y']));
});

it('returns published text revisions to editing while keeping completion on the main axis', function () {
    $html = view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $join = $get('review-feedback-loop.turn');
    $turn = $get('published-edit-task-loop.turn');
    $finish = $get('published-edit-complete-return');
    $display = $get('display');
    expect($rem($turn['x']))->toEqualWithDelta($rem($join['x']), 0.001);
    expect($rem($turn['y']))->toBeLessThan($rem($join['y']));
    expect($rem($finish['x']))->toEqualWithDelta($rem($display['x']), 0.001);
    expect($html)->toContain(
        'Revise an already published translation?',
        'Open the existing key for a new revision',
        'Inspect all usages when a key is shared',
        'Publish language values; source key stays unchanged',
    );
});

it('documents explicit maintenance proposals and keeps their decisions on the main axis', function () {
    $html = view('translation-workbench::livewire.workflow.target-ui')->render();
    $get = fn ($id) => AnchorRegistry::get('translation-workflow-target-ui', 'workflow.target-ui.'.$id.'.anchorNode-end');
    $rem = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $previous = $get('published-edit-complete-return');
    foreach (['key-migration-return', 'shared-maintenance-return', 'archive-unused-return', 'maintenance-deferred-return'] as $id) {
        $anchor = $get($id);
        expect($rem($anchor['x']))->toEqualWithDelta($rem($previous['x']), 0.001);
        expect($rem($anchor['y']))->toBeLessThan($rem($previous['y']));
        $previous = $anchor;
    }
    $approved = $get('maintenance-approved.true');
    $outcome = $get('maintenance-outcome');
    expect($rem($outcome['x']))->toEqualWithDelta($rem($approved['x']), 0.001);
    expect($rem($outcome['y']))->toBeLessThan($rem($approved['y']));
    expect($html)->toContain(
        'Change a confirmed or published key?',
        'Change a shared assignment?',
        'Archive an unused translation task?',
        'No approval: no assignment or file changes',
        'Write and verify required target language values',
        'Then update only selected source references',
        'Retire old exports only after usage verification',
        'Partial writes stay incomplete; no assumed rollback',
        'Keep archive separate from permanent deletion',
    );
});

it('compiles target UI components instead of exposing PHP prop arrays to Alpine', function () {
    $html = view('translation-workbench::livewire.workflow.target-ui')->render();
    expect($html)->not->toContain('<x-translation-workbench::', ':step-label=', ':node-labels=', '$fluxDot')
        ->and($html)->toContain('Mark the literal for translation');
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    $xpath = new DOMXPath($document);
    expect($xpath->query('//*[contains(@data-tw-graph-bounds, "workflow.target-ui.author")]')->length)->toBeGreaterThan(0);
});
