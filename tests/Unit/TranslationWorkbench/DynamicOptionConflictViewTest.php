<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('shows source conflict provenance safely in the existing Flux editor', function (bool $conflicting) {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $row = ['field_key' => 'v_test', 'value_key' => 'test', 'source' => 'Reviewed source', 'target' => 'Reviewed target', 'native_label' => null, 'status' => 'active'];
    if ($conflicting) {
        $row['source_conflicts'] = [
            ['source_id' => 11, 'reference' => 'Provider A', 'value' => '<script>alert(1)</script>'],
            ['source_id' => 12, 'reference' => 'Provider B', 'value' => 'Different value'],
        ];
    }
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-edit-dynamic-multi")', [
        'dynamicMultiEditFinding' => (object) ['id' => 1, 'review_status' => 'reviewed', 'translation_key' => 'ui.test', 'dynamic_scope' => 'test'],
        'dynamicMultiRows' => [$row], 'editLocales' => ['source' => 'en', 'active' => 'de'],
        'dynamicMultiSourceValues' => [], 'dynamicMultiTargetValues' => [], 'dynamicMultiSourceEqualsTargetOverrides' => [],
        'dynamicMultiEditableTargetFields' => [], 'dynamicMultiEditableSourceFields' => [],
    ]);
    if ($conflicting) {
        expect($html)->toContain('Conflicting option sources', 'Provider A', 'Provider B', 'Source 11', 'Source 12', '&lt;script&gt;alert(1)&lt;/script&gt;')
            ->not->toContain('<script>alert(1)</script>');
    } else {
        expect($html)->not->toContain('Conflicting option sources');
    }
})->with([false, true]);

it('shows a stale source assignment error in the source link confirmation dialog', function () {
    view()->share('errors', (new \Illuminate\Support\ViewErrorBag)->put('default', new \Illuminate\Support\MessageBag([
        'dynamicSourceLinkRelatedSourceId' => 'The source assignment has changed.',
    ])));
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-dynamic-source-link-confirm")', ['dynamicSourceLinkPreview' => null]);
    expect($html)->toContain('The source assignment has changed.');
});
