<?php

use Gunreip\TranslationWorkbench\Generation\WorkflowProgress;
use Tests\TestCase;

uses(TestCase::class);

it('groups only shared key translation steps while preserving statuses and navigation', function () {
    $steps = (new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 8], [
        ['locale' => 'en', 'state' => 'draft', 'revision' => 1],
        ['locale' => 'de', 'state' => 'missing', 'revision' => null],
    ], true, true);
    $html = Illuminate\Support\Facades\Blade::render(
        '<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" :navigable="true" :shared-key="[\'id\' => 8, \'name\' => \'ui.button.save\']" />',
        compact('steps'),
    );
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $blocks = $xpath->query('//*[@data-flux-timeline-block]');
    expect($blocks->length)->toBe(1);
    $block = $blocks->item(0);
    expect($block->textContent)->toContain('Shared key #8', 'ui.button.save', 'Translation saved', 'Translation reviewed', 'Translation published')
        ->not->toContain('Finding captured', 'Assigned key used in source', 'Source confirmed by scan');
    $indicators = $xpath->query('.//*[@data-flux-timeline-indicator]', $block);
    expect($indicators->length)->toBe(3);
    // Flux status selectors override explicit indicator colors. Use its color API alone here.
    expect($xpath->query('.//*[@data-flux-timeline-status]', $block)->length)->toBe(0);
    $expected = array_values(array_filter($steps, fn ($step) => in_array($step['id'], ['draft', 'review', 'publication'], true)));
    foreach ($indicators as $i => $indicator) {
        expect($indicator->getAttribute('class'))->toContain('bg-'.$expected[$i]['color'].'-'.($expected[$i]['color'] === 'zinc' ? '100' : '500'));
    }
    expect($html)->toContain('wire:click="continueWorkflow"');
    $browserDom = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
    $outer = $browserDom->querySelector('[data-flux-timeline]');
    $outerItems = 0;
    foreach ($outer->children as $child) {
        $outerItems += $child->hasAttribute('data-flux-timeline-item') ? 1 : 0;
    }
    expect($outerItems)->toBe(5);
    expect($outer->lastElementChild->textContent)->toContain('Source confirmed by scan');
    $plain = Illuminate\Support\Facades\Blade::render('<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" />', compact('steps'));
    expect($plain)->not->toContain('data-flux-timeline-block');
});

it('marks assignment as the next step for an unassigned finding', function () {
    $steps = array_column((new WorkflowProgress)->steps(['status' => 'active', 'key_id' => null], [
        ['locale' => 'en', 'state' => 'missing', 'revision' => null],
        ['locale' => 'de', 'state' => 'missing', 'revision' => null],
    ], false, false), null, 'id');
    expect($steps['capture']['status'])->toBe('complete')
        ->and($steps['assignment']['status'])->toBe('current')
        ->and($steps['draft']['status'])->toBe('incomplete');
});

it('keeps partial language progress visible without claiming the key is fully published', function () {
    $steps = array_column((new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 8], [
        ['locale' => 'en', 'state' => 'published', 'revision' => 1],
        ['locale' => 'de', 'state' => 'published', 'revision' => 2],
        ['locale' => 'de-AT', 'state' => 'review', 'revision' => 3],
    ], true, false), null, 'id');
    expect($steps['draft']['done'])->toBeTrue()
        ->and($steps['review']['status'])->toBe('current')
        ->and($steps['publication']['done'])->toBeFalse()
        ->and(array_column($steps['publication']['languages'], 'done'))->toBe([true, true, false])
        ->and($steps['source']['done'])->toBeTrue()
        ->and($steps['scan']['done'])->toBeFalse();
});

it('renders the flux timeline with language and blocker details', function () {
    $steps = (new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 8], [
        ['locale' => 'en', 'state' => 'draft', 'revision' => 1],
    ], false, false, 'Source position changed.');
    $html = Illuminate\Support\Facades\Blade::render('<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" :countries="[\'en\' => \'gb\']" />', compact('steps'));
    expect($html)->toContain('data-flux-timeline', 'Source position changed.', 'Translation reviewed', 'en');
});

it('does not suggest continuing an archived finding and completes only a verified workflow', function () {
    $language = [['locale' => 'en', 'state' => 'published', 'revision' => 1]];
    $archived = (new WorkflowProgress)->steps(['status' => 'obsolete', 'key_id' => null], [], false, false);
    expect(array_column($archived, 'status'))->not->toContain('current');
    $complete = (new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 8], $language, true, true);
    expect(array_unique(array_column($complete, 'status')))->toBe(['complete']);
});

it('explains the scan commands and does not suggest running them on UI test cases', function () {
    $steps = (new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 8], [], true, false);
    $scan = array_column($steps, null, 'id')['scan'];
    expect($scan['icon'])->toBe('x-mark')->and($scan['color'])->toBe('red');
    $html = Illuminate\Support\Facades\Blade::render('<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" />', compact('steps'));
    expect(preg_replace('/\s+/', ' ', $html))->toContain('php artisan translation-workbench:generation-scan --apply', 'Preview scan (read-only)');
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $complete = $xpath->query('//*[@data-flux-timeline-status="complete"]//*[@data-flux-timeline-indicator]')->item(0);
    $indicators = $xpath->query('//*[@data-flux-timeline-item]//*[@data-flux-timeline-indicator]');
    expect($complete->getAttribute('style'))->toContain('background-color: var(--color-green-500)', 'color: var(--color-white)');
    expect($indicators->item(6)->getAttribute('style'))->toContain('background-color: var(--color-red-500)', 'border-color: var(--color-red-500)');
    $testHtml = Illuminate\Support\Facades\Blade::render('<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" :test-mode="true" />', compact('steps'));
    expect($testHtml)->not->toContain('php artisan')->and($testHtml)->toContain('not on these UI test cases');
});

it('recognizes existing publications without inventing reviews or requesting another translation', function () {
    $steps = array_column((new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 770], [
        ['locale' => 'en', 'state' => 'existing_publication', 'revision' => null],
        ['locale' => 'de', 'state' => 'existing_publication', 'revision' => null],
    ], true, true), null, 'id');
    expect($steps['draft']['done'])->toBeTrue()
        ->and($steps['publication']['done'])->toBeTrue()
        ->and($steps['review']['done'])->toBeFalse()
        ->and($steps['review']['summary'])->toBe('Review not recorded')
        ->and($steps['review']['color'])->toBe('amber')
        ->and(array_column($steps, 'status'))->not->toContain('current');
    foreach ([null, ['id' => 770, 'name' => 'ui.states.all']] as $sharedKey) {
        $html = Illuminate\Support\Facades\Blade::render('<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" :shared-key="$sharedKey" :navigable="true" />', compact('steps', 'sharedKey'));
        expect($html)->toContain('Review not recorded', 'Existing publication verified')
            ->not->toContain('wire:click="continueWorkflow"');
    }
});

it('still requests review of a working revision alongside an imported publication', function () {
    $steps = array_column((new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 770], [
        ['locale' => 'en', 'state' => 'existing_publication', 'revision' => null],
        ['locale' => 'de', 'state' => 'draft', 'revision' => 9],
    ], true, true), null, 'id');
    expect($steps['review']['status'])->toBe('current')
        ->and($steps['publication']['done'])->toBeFalse();
});

it('verifies the actual imported value without executing PHP or trusting a stale file snapshot', function () {
    $root = sys_get_temp_dir().'/workflow-evidence-'.bin2hex(random_bytes(6));
    mkdir($root.'/en', 0700, true);
    $path = $root.'/en/ui.php';
    $evidence = [['locale' => 'en', 'path' => 'lang/en/ui.php', 'full_name' => 'ui.states.all', 'value' => 'All']];
    $reader = new Gunreip\TranslationWorkbench\Generation\WorkflowFileEvidence;
    try {
        file_put_contents($path, "<?php return ['states' => ['all' => 'All'], 'unrelated' => 'Changed'];");
        expect($reader->languages($evidence, $root)['en']['state'])->toBe('existing_publication');
        file_put_contents($path, "<?php return ['states' => ['all' => 'Different']];");
        expect($reader->languages($evidence, $root))->toBe([]);
        file_put_contents($path, "<?php return ['states' => ['all' => strtoupper('all')]];");
        expect($reader->languages($evidence, $root))->toBe([]);
        unlink($path);
        expect($reader->languages($evidence, $root))->toBe([]);
    } finally {
        if (is_file($path)) unlink($path);
        rmdir($root.'/en');
        rmdir($root);
    }
});

it('keeps publication file details inside language tooltips for individual and shared workflows', function ($shared) {
    $steps = (new WorkflowProgress)->steps(['status' => 'active', 'key_id' => 8], [
        ['locale' => 'de-AT', 'state' => 'published', 'revision' => 19,
            'file' => ['actual' => true, 'path' => 'de-AT/ui.php', 'detail' => 'The file value matches the publication record for the current saved revision.']],
    ], true, true);
    $html = Illuminate\Support\Facades\Blade::render(
        '<x-translation-workbench::ui.common.workflow-timeline :steps="$steps" :shared-key="$sharedKey" />',
        ['steps' => $steps, 'sharedKey' => $shared ? ['id' => 8, 'name' => 'ui.button.save'] : null],
    );
    $document = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
    $matches = 0;
    foreach ($document->querySelectorAll('[data-flux-tooltip-content]') as $tooltip) {
        if (str_contains($tooltip->textContent, 'lang/de-AT/ui.php')) {
            $matches++;
            expect($tooltip->textContent)->toContain('matches the publication record');
        }
    }
    expect($matches)->toBe(1)
        ->and(substr_count($html, 'lang/de-AT/ui.php'))->toBe(1);
})->with([false, true]);
