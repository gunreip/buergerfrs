<?php

use Gunreip\TranslationWorkbench\Generation\WorkflowProgress;
use Tests\TestCase;

uses(TestCase::class);

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
