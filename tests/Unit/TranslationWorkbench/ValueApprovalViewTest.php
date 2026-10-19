<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('renders the stored revision approval preview with escaped values and explicit status', function (string $state) {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-value-approval")', [
        'valueApprovalRows' => [[
            'snapshot' => ['kind' => 'option', 'value_id' => 1, 'locale' => 'de', 'value_revision' => 3,
                'translation_key' => 'ui.status', 'value_key' => 'draft', 'value' => '<script>alert(1)</script>'],
            'state' => $state, 'legacy_reviewed_at' => '2026-10-15 12:00:00',
        ]],
    ]);
    expect($html)->toContain('ui.status', 'Revision 3', '&lt;script&gt;alert(1)&lt;/script&gt;', 'Unsaved editor input is not approved.', 'Historical reviewed-at timestamp:')
        ->not->toContain('<script>alert(1)</script>');
    expect($html)->toContain(match ($state) {
        'approved' => 'Current revision approved', 'changed' => 'Changed since approval', default => 'No revision approval',
    });
})->with(['unapproved', 'approved', 'changed']);

it('renders publication comparisons with old and new values without executable markup', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-value-approval")', [
        'valueApprovalRows' => [],
        'valuePublicationPreview' => [
            'unchanged_values_retained' => 2, 'changes_without_current_approval' => 1,
            'pruning_requires_separate_review' => 1, 'values_conflicted' => 1, 'values_excluded_by_legacy_review_filter' => 1,
            'plans' => [['path' => 'lang/de/ui.php',
                'changes' => [['translation_key' => 'ui.title', 'approval_state' => 'unapproved', 'old_value' => 'Old title', 'new_value' => '<script>new</script>']],
                'pruned' => [['translation_key' => 'ui.removed']], 'conflicts' => [['translation_key' => 'ui.conflict']],
            ]],
        ],
    ]);
    expect($html)->toContain('Old title', '&lt;script&gt;new&lt;/script&gt;', 'ui.removed', 'ui.conflict', 'Published translations would be removed', 'Clearing a stored value does not remove its published text.', 'No files are written.', 'Current revision not approved')
        ->not->toContain('<script>new</script>');
});

it('renders publication recovery status and escapes errors and paths', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-value-approval")', [
        'valueApprovalRows' => [],
        'valuePublicationRuns' => [[
            'id' => 'run-1', 'created_at' => '2026-10-16', 'state' => 'interrupted', 'recorded' => false,
            'error' => '<script>failure</script>',
            'files' => [['path' => 'lang/de/<bad>.php', 'current_state' => 'changed']],
        ]],
    ]);
    expect($html)->toContain('Unfinished publication', 'Database completion pending', 'Changed outside this publication',
        'Recheck and resume publication', 'resumeValuePublication(0)', '&lt;script&gt;failure&lt;/script&gt;', 'lang/de/&lt;bad&gt;.php')
        ->not->toContain('<script>failure</script>');
});

it('shows explicit publication closure consequences and escapes its reason', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-value-approval")', [
        'valueApprovalRows' => [],
        'valuePublicationRuns' => [[
            'id' => 'run-closed', 'created_at' => '2026-10-16', 'state' => 'closed', 'recorded' => false,
            'error' => null, 'closure_reason' => '<script>reason</script>', 'files' => [],
        ]],
        'valuePublicationClosure' => ['id' => 'run-other', 'files' => [['path' => 'lang/de/ui.php', 'current_state' => 'result']]],
    ]);
    expect($html)->toContain('Closed without further writing', 'Closure recorded; publication was not completed',
        '&lt;script&gt;reason&lt;/script&gt;', 'Files stay exactly as they are', 'Keep files and close this attempt')
        ->not->toContain('<script>reason</script>', 'resumeValuePublication(0)');
});


it('shows a root scalar key error and hides it for nested leaves', function () {
    $html = view('translation-workbench::livewire.entries.review.root-scalar-warning', ['structureKey' => 'ui.type'])->render();
    expect($html)->toContain('Translation key structure error', 'ui.type', 'nothing is renamed automatically');
    expect(trim(view('translation-workbench::livewire.entries.review.root-scalar-warning', ['structureKey' => 'ui.type.type'])->render()))->toBe('');
});

it('renders duplicate key ownership independently of scalar structure and lists record IDs', function () {
    $html = view('translation-workbench::livewire.entries.review.duplicate-key-warning', ['ownerCount' => 2, 'ownerIds' => [656, 1212]])->render();
    expect($html)->toContain('Multiple active key records', '656, 1212', 'no record is selected automatically');
    expect(trim(view('translation-workbench::livewire.entries.review.duplicate-key-warning', ['ownerCount' => 1, 'ownerIds' => [656]])->render()))->toBe('');
});

it('renders the read-only publication comparison with escaped values and no approval action', function () {
    $html = view('translation-workbench::livewire.entries.stored-publication-comparison', [
        'storedPublicationComparison' => ['keys' => ['ui.type'], 'values_unchanged' => 0, 'plans' => [[
            'path' => 'lang/de/ui.php', 'changes' => [['translation_key' => 'ui.type', 'old_value' => '<script>old</script>', 'new_value' => 'Typ']],
            'conflicts' => [], 'pruned' => [],
        ]]],
    ])->render();
    expect($html)->toContain('Stored and published values', '&lt;script&gt;old&lt;/script&gt;', 'Typ', 'Nothing is approved or published.')
        ->not->toContain('<script>old</script>', 'wire:click');
});

it('renders key conflict assignments and escapes stored texts and paths', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.key-conflict-inspection")', [
        'keyConflictInspection' => ['source' => 'ui.old.leaf', 'target' => 'ui.new.leaf', 'groups' => [[
            'key' => 'ui.old.leaf', 'owners' => [
                ['id' => 1, 'status' => 'open', 'suggested_key' => 'ui.old.leaf', 'review_status' => 'reviewed', 'reviewed_at' => '2026-07-30 10:01:03'],
                ['id' => 2, 'status' => 'open', 'suggested_key' => '<script>candidate</script>', 'review_status' => 'pending', 'reviewed_at' => null],
            ],
            'findings' => [['key_id' => 1, 'id' => 3, 'path' => '<script>file</script>', 'source_line' => 7,
                'status' => 'active', 'link_status' => 'active', 'relation_type' => 'literal']],
            'values' => [['id' => 4, 'locale' => 'de', 'status' => 'obsolete', 'value' => '<script>value</script>']],
        ]]],
    ]);
    expect($html)->toContain('ui.new.leaf', '&lt;script&gt;file&lt;/script&gt;', '&lt;script&gt;value&lt;/script&gt;', 'obsolete', 'Read-only snapshot.',
        'Review status', 'Reviewed at', '2026-07-30 10:01:03', 'pending', '&lt;script&gt;candidate&lt;/script&gt;', 'Identical literal text can belong to different translation tasks.',
        'Multiple active key records for the same translation key', 'Several findings may use one key record.')
        ->not->toContain('<script>file</script>', '<script>value</script>', '<script>candidate</script>');
});

it('renders source assignments with key and finding identifiers and escaped paths', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.key-sources")', [
        'translationKeyFindingId' => 12,
        'translationKeySources' => [
            ['key_id' => 7, 'findings' => [['id' => 12, 'path' => 'views/<script>.blade.php', 'source_line' => 42, 'status' => 'active']]],
            ['key_id' => 8, 'findings' => []],
        ],
    ]);
    expect($html)->toContain('Key #7', 'Key #8', '#12', 'views/&lt;script&gt;.blade.php:42', 'Opened finding', 'No actively linked findings.', 'vscode://')
        ->not->toContain('views/<script>.blade.php');
});


it('renders executable link visibility conditions for the inactive links toggle', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.key-conflict-inspection")', [
        'keyConflictInspection' => ['source' => 'ui.example.title', 'target' => 'ui.example.title', 'groups' => [[
            'key' => 'ui.example.title', 'owners' => [], 'values' => [],
            'findings' => [
                ['key_id' => 1, 'id' => 2, 'path' => 'active.php', 'source_line' => 1, 'status' => 'obsolete', 'link_status' => 'active', 'relation_type' => 'candidate'],
                ['key_id' => 1, 'id' => 3, 'path' => 'inactive.php', 'source_line' => 2, 'status' => 'active', 'link_status' => 'obsolete', 'relation_type' => 'candidate'],
            ],
        ]]],
    ]);
    expect($html)->toContain('x-show="showInactiveLinks || true"', 'x-show="showInactiveLinks || false"')
        ->not->toContain('@js(');
});

it('renders the explicit finding choice separately from the read-only key tables', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.key-finding-assignment")', [
        'keyConflictInspection' => ['source' => 'ui.shared.title', 'target' => 'ui.shared.title', 'groups' => [],
            'assignment' => ['finding_id' => 42, 'literal_text' => '<script>Original literal</script>', 'proof' => 'server-proof', 'keys' => [
                ['id' => 7, 'suggested_key' => '<script>candidate</script>', 'review_evidence' => [
                    ['review_ids' => [11], 'direct' => true, 'bulk' => false, 'decision' => 'translation_key_updated',
                        'translation_key' => '<script>key</script>', 'reviewed_at' => '2026-10-18 10:00:00'],
                    ['review_ids' => [12, 13], 'direct' => false, 'bulk' => true, 'decision' => 'translation_key_bulk_equalized',
                        'translation_key' => 'ui.shared.title', 'reviewed_at' => '2026-10-18 10:01:00'],
                ]],
                ['id' => 8, 'suggested_key' => 'another.context.title'],
            ]]],
    ]);
    expect($html)->toContain('Choose the key record for finding #42', 'wire:click="openKeyAssignmentConfirmation"', 'wire:model.live="selectedAssignmentKeyId"', 'readonly', '&lt;script&gt;Original literal&lt;/script&gt;',
        '&lt;script&gt;candidate&lt;/script&gt;', 'No key records are merged', 'Review evidence (2 operations)',
        'This finding', 'Included in bulk selection', 'Bulk review', '#12, #13', '2026-10-18 10:01:00',
        'No recorded review for this finding on this key.', '&lt;script&gt;key&lt;/script&gt;')
        ->not->toContain('<script>candidate</script>', '<script>key</script>');
});

it('shows a saved finding assignment after the choice controls disappear', function (bool $active) {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.key-conflict-inspection")', [
        'keyConflictInspection' => ['source' => 'ui.shared.title', 'target' => 'ui.shared.title', 'groups' => [], 'assignment' => null,
            'assignment_decision' => ['finding_id' => 42, 'key_id' => 8, 'review_id' => 12, 'reviewed_at' => '2026-10-18 11:00:00', 'active' => $active]],
    ]);
    expect($html)->toContain('Saved finding assignment', 'Finding #42', 'Key #8', 'Review #12', '2026-10-18 11:00:00')
        ->not->toContain('chooseFindingKeyAssignment(');
    expect($html)->toContain($active ? 'This selection is active.' : 'The stored selection no longer matches the active assignment.');
})->with([true, false]);

it('shows explicit unused key retirement with affected finding ids but no proof in html', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.key-conflict-inspection")', [
        'keyConflictInspection' => ['source' => 'ui.shared.title', 'target' => 'ui.shared.title', 'groups' => [],
            'retirements' => [['key_id' => 7, 'target_id' => 8, 'finding_ids' => [42, 43], 'proof' => 'secret-proof']]],
    ]);
    expect($html)->toContain('Key #7 has no remaining active assignments', 'key #8', '#42, #43', 'retireUnassignedKey(7)', 'wire:confirm', 'Archive key #7')
        ->not->toContain('secret-proof');
});

it('renders blocked task separation without an action and with escaped sources', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = view('translation-workbench::livewire.entries.review.key-separation-preview', ['separation' => [
        'key_id' => 7, 'source' => 'ui.shared.title', 'target' => 'ui.separate.title', 'remaining_owner_ids' => [8, 9],
        'findings' => [['id' => 42, 'path' => '<script>source</script>', 'source_line' => 12, 'status' => 'active']],
        'shared_values' => [['locale' => 'de', 'value' => 'Titel']], 'issues' => ['Review this assignment.'],
    ]])->render();
    expect($html)->toContain('Separate translation task', 'Key #7', '#8, #9', '#42',
        '&lt;script&gt;source&lt;/script&gt;:12', 'Stored values on the saved key: 1.', 'Review this assignment.', 'no values are copied, no files are changed and nothing is published.')
        ->not->toContain('<script>source</script>', 'wire:click');
});

it('offers an explicit task separation confirmation without exposing its proof', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = view('translation-workbench::livewire.entries.review.key-separation-preview', ['separation' => [
        'key_id' => 7, 'source' => 'ui.shared.title', 'target' => 'ui.separate.title', 'remaining_owner_ids' => [8],
        'findings' => [], 'shared_values' => [], 'issues' => [], 'proof' => 'secret-proof',
    ]])->render();
    expect($html)->toContain('separateTranslationTask', 'wire:confirm', 'Separate without copying values', 'no values are copied')
        ->not->toContain('secret-proof');
});


it('explains and disables publication when the preview has no changes', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.modal-value-approval")', [
        'valueApprovalRows' => [],
        'valuePublicationPreview' => [
            'values_new' => 0, 'values_changed' => 0,
            'unchanged_values_retained' => 1, 'changes_without_current_approval' => 0,
            'pruning_requires_separate_review' => 0, 'values_conflicted' => 0,
            'values_excluded_by_legacy_review_filter' => 0, 'plans' => [],
        ],
    ]);
    expect($html)->toContain('No new or changed translations to publish.');
    $dom = new \DOMDocument;
    @$dom->loadHTML($html);
    $buttons = (new \DOMXPath($dom))->query('//button[@*[name()="wire:click"]="publishValuePublication"]');
    expect($buttons->length)->toBe(1)
        ->and($buttons->item(0)->hasAttribute('disabled'))->toBeTrue();
});


it('renders finding assignment confirmation in a Flux modal without a native confirm dialog', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.modal-key-assignment-confirm")', [
        'pendingKeyAssignment' => ['finding_id' => 42, 'key_id' => 7, 'suggested_key' => '<script>key</script>'],
    ]);
    expect($html)->toContain('data-flux-modal', 'Finding #42', 'Key #7', '&lt;script&gt;key&lt;/script&gt;', 'wire:click="confirmFindingKeyAssignment"', 'Cancel')
        ->not->toContain('wire:confirm', '<script>key</script>');
});

it('keeps the confirmation and timeline modal identities separate when rendered together', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = Blade::render('@include("translation-workbench::livewire.entries.review.modal-key-assignment-confirm") @include("translation-workbench::livewire.entries.modal-timeline")', [
        'pendingKeyAssignment' => ['finding_id' => 42, 'key_id' => 7, 'suggested_key' => 'ui.example.key'],
        'timelineFinding' => null, 'timelineEvents' => collect(),
    ]);
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
    $xpath = new DOMXPath($dom);
    foreach (['translation-workbench-key-assignment-confirm' => 'keyAssignmentConfirmOpen', 'translation-workbench-finding-timeline' => 'timelineModalOpen'] as $name => $property) {
        $dialog = $xpath->query('//dialog[@data-modal="'.$name.'"]');
        expect($dialog->length)->toBe(1);
        $host = $dialog->item(0)->parentNode;
        expect($host->getAttribute('wire:model.self'))->toBe($property)
            ->and($host->getAttribute('wire:key'))->toBe($name.'-modal');
    }
});

it('keeps key table warnings compact with full explanations in Flux tooltips', function () {
    $html = view('translation-workbench::livewire.entries.review.duplicate-key-warning', ['ownerCount' => 2, 'ownerIds' => [], 'compactWarning' => true])->render();
    expect($html)->toContain('Key conflict (2)', 'data-flux-tooltip', 'no record is selected automatically.')->not->toContain('Multiple active key records');
    $html = view('translation-workbench::livewire.entries.review.root-scalar-warning', ['structureKey' => 'ui.type', 'compactWarning' => true])->render();
    expect($html)->toContain('Invalid key structure', 'data-flux-tooltip', 'Publication is blocked');
});

it('renders a distinct Flux ownership modal with explicit choices and escaped original literals', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = view('translation-workbench::livewire.entries.review.modal-key-ownership', [
        'keyOwnershipPlan' => ['text' => 'ui.shared.title', 'same_literals' => false,
            'owners' => [['id' => 10, 'suggested_key' => 'ui.first.title', 'review_status' => 'reviewed'], ['id' => 11, 'suggested_key' => 'ui.second.title', 'review_status' => 'pending']],
            'findings' => [['id' => 20, 'key_ids' => [10], 'literal_text' => '<script>literal</script>', 'path' => 'resources/views/example.blade.php', 'source_line' => 3]],
            'values' => [['locale' => 'en', 'status' => 'active', 'value' => '<b>Translation</b>']],
            'reviews' => [], 'own_values' => [], 'issues' => [], 'proof' => 'opaque'],
        'ownershipTargetKeyId' => '10', 'ownershipMergeConfirmed' => false, 'ownershipSeparationPlan' => null,
    ])->render();
    expect($html)->toContain('wire:model.self="keyOwnershipModalOpen"', 'data-modal="translation-workbench-key-ownership"',
        'Original literal', '&lt;script&gt;literal&lt;/script&gt;', '&lt;b&gt;Translation&lt;/b&gt;',
        'Key records to archive', 'Confirm consolidation', 'Preview separation', 'wire:click="confirmKeyOwnershipMerge"')
        ->not->toContain('<script>literal</script>', 'wire:confirm=');
});

it('renders read only finding lineage with distinct finding and key ids and escaped historical paths', function () {
    $html = view('translation-workbench::livewire.entries.review.finding-lineage', ['findingLineage' => [
        ['role' => 'selected', 'id' => 12, 'status' => 'obsolete', 'path' => 'views/<old>.blade.php', 'line' => 2, 'column' => 4, 'keys' => [['key_id' => 90, 'status' => 'obsolete']]],
        ['role' => 'successor', 'id' => 13, 'status' => 'active', 'path' => 'views/new.blade.php', 'line' => 6, 'column' => 4, 'keys' => [['key_id' => 90, 'status' => 'active']]],
    ]])->render();
    expect($html)->toContain('F#12', 'F#13', 'K#90', 'views/&lt;old&gt;.blade.php:2:4', 'Archived finding',
        'openFindingVersion(12)', 'openFindingVersion(13)', 'Successor')
        ->not->toContain('openTranslationKeyModal', 'views/<old>');
    expect(trim(view('translation-workbench::livewire.entries.review.finding-lineage', ['findingLineage' => []])->render()))->toBe('');
});
