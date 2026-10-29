<?php

use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Tests\Support\TranslationWorkbench\BaselinePrototype;

function approvalBaseline(): array
{
    $db = new BaselinePrototype;
    $key = 'ui.button.cancel';
    $item = new DiscoveredTranslation(kind: 'key', sourceType: 'blade', sourcePath: 'example.blade.php', sourceLine: 1,
        functionName: '__', rawExpression: "__('ui.button.cancel')", literalText: null, literalTextSuggested: null,
        translationKey: $key, translationKeySource: 'code', existingKey: $key, suggestedKey: $key,
        namespace: 'ui', group: 'button', sourceSignature: 'fixture', fingerprint: 'fixture', sourceFingerprint: 'fixture',
        expressionFingerprint: 'fixture', semanticFingerprint: 'fixture', sourceColumn: 1);
    $db->scan('capture', [$item], ['example.blade.php' => 'fixture-hash']);
    $saved = $db->save('draft', 1, 'en', 'Cancel', 7, 'Initial translation');

    return [$db, $saved['revision_id']];
}

it('requires explicit revision approval and keeps previews out of the work history', function () {
    [$db, $revision] = approvalBaseline();
    $before = $db->rows('events');
    expect(fn () => $db->publicationPreview([$revision]))->toThrow(RuntimeException::class, 'not approved');
    $review = $db->reviewSnapshot($revision);
    expect($db->rows('events'))->toBe($before);
    $approved = $db->decideRevision('approve', $revision, $review['fingerprint'], true, 8, 'Checked English wording');
    $plan = $db->publicationPreview([$revision]);
    expect($plan['items'][0]['value'])->toBe('Cancel')->and($plan['items'][0]['approval_id'])->toBe($approved['approval_id'])
        ->and($db->verifyPublicationPreview([$revision], $plan['fingerprint']))->toBe($plan)
        ->and(array_column($db->rows('events'), 'event_type'))->toBe(['finding_captured', 'translation_saved', 'revision_approved'])
        ->and($db->rows('events')[2]['actor_id'])->toBe(8)
        ->and($db->rows('events')[2]['reason'])->toBe('Checked English wording');
});

it('does not transfer an old approval to a newer draft even if the text returns to an earlier value', function () {
    [$db, $revision] = approvalBaseline();
    $fingerprint = $db->reviewSnapshot($revision)['fingerprint'];
    $db->decideRevision('approve', $revision, $fingerprint, true, 8, 'Checked');
    $db->save('edit', 1, 'en', 'Abort', 7, 'Different context');
    $returned = $db->save('edit-again', 1, 'en', 'Cancel', 7, 'Restore wording')['revision_id'];
    expect(fn () => $db->publicationPreview([$revision]))->toThrow(RuntimeException::class, 'newer draft');
    expect(fn () => $db->publicationPreview([$returned]))->toThrow(RuntimeException::class, 'not approved');
    expect(fn () => $db->decideRevision('stale-dialog', $revision, $fingerprint, true, 9, 'Old dialog'))->toThrow(RuntimeException::class, 'newer draft');
    expect($db->rows('approvals'))->toHaveCount(1)->and($db->rows('revisions'))->toHaveCount(3);
});

it('invalidates a stale review when the finding changes its key assignment', function () {
    [$db, $revision] = approvalBaseline();
    $review = $db->reviewSnapshot($revision);
    $db->assign('reassign', 1, 'ui.button.abort', 7, 'Separate context');
    expect(fn () => $db->decideRevision('approve', $revision, $review['fingerprint'], true, 8, 'Checked'))->toThrow(RuntimeException::class, 'context changed');
    expect($db->rows('approvals'))->toBe([]);
});

it('invalidates prepared eligibility after revocation and does not revive it by reapproval', function () {
    [$db, $revision] = approvalBaseline();
    $fingerprint = $db->reviewSnapshot($revision)['fingerprint'];
    $db->decideRevision('approve', $revision, $fingerprint, true, 8, 'Checked');
    $plan = $db->publicationPreview([$revision]);
    $db->decideRevision('revoke', $revision, $fingerprint, false, 9, 'Needs another review');
    expect(fn () => $db->verifyPublicationPreview([$revision], $plan['fingerprint']))->toThrow(RuntimeException::class, 'not approved');
    $db->decideRevision('reapprove', $revision, $fingerprint, true, 9, 'Reviewed again');
    expect(fn () => $db->verifyPublicationPreview([$revision], $plan['fingerprint']))->toThrow(RuntimeException::class, 'preview changed');
    expect(array_column($db->rows('approvals'), 'decision'))->toBe(['approved', 'revoked', 'approved']);
});

it('records one approval on retry and no event for an unchanged approval', function () {
    [$db, $revision] = approvalBaseline();
    $hash = $db->reviewSnapshot($revision)['fingerprint'];
    $first = $db->decideRevision('approve', $revision, $hash, true, 8, 'Checked');
    expect($db->decideRevision('approve', $revision, $hash, true, 8, 'Checked'))->toBe($first);
    expect($db->decideRevision('same-choice', $revision, $hash, true, 8, 'Checked')['changed'])->toBeFalse()
        ->and($db->rows('approvals'))->toHaveCount(1)->and($db->rows('events'))->toHaveCount(3);
});

it('rolls back approval if its work event cannot be recorded', function () {
    [$db, $revision] = approvalBaseline();
    $db->db->exec("CREATE TRIGGER fail_approval_event BEFORE INSERT ON events WHEN NEW.event_type='revision_approved' BEGIN SELECT RAISE(ABORT,'History unavailable'); END");
    expect(fn () => $db->decideRevision('approve', $revision, $db->reviewSnapshot($revision)['fingerprint'], true, 8, 'Checked'))->toThrow(PDOException::class);
    expect($db->rows('approvals'))->toBe([])->and($db->rows('events'))->toHaveCount(2)
        ->and(array_column($db->rows('operations'), 'id'))->not->toContain('approve');
});

it('does not treat baseline values as approved or published', function () {
    $db = new BaselinePrototype;
    $db->captureValues('baseline', [['key' => 'ui.button.cancel', 'locale' => 'en', 'value' => 'Cancel', 'source' => 'lang_file', 'reference' => 'baseline#hash']], 7, 'Observed existing file');
    expect($db->rows('approvals'))->toBe([]);
    expect(fn () => $db->publicationPreview([1]))->toThrow(RuntimeException::class, 'not approved');
    expect(array_column($db->rows('events'), 'event_type'))->toBe(['baseline_values_captured']);
});

it('does not silently include another locale or fabricate a missing translation', function () {
    [$db, $en] = approvalBaseline();
    $de = $db->save('german', 1, 'de', 'Abbrechen', 7, 'German draft')['revision_id'];
    $db->decideRevision('english-only', $en, $db->reviewSnapshot($en)['fingerprint'], true, 8, 'English checked');
    expect(array_column($db->publicationPreview([$en])['items'], 'locale'))->toBe(['en']);
    expect(fn () => $db->publicationPreview([$en, $de]))->toThrow(RuntimeException::class, 'not approved');
    expect($db->rows('revisions'))->toHaveCount(2);
});

it('requires fresh approval after a key is detached and assigned again', function () {
    [$db, $revision] = approvalBaseline();
    $fingerprint = $db->reviewSnapshot($revision)['fingerprint'];
    $db->decideRevision('approve', $revision, $fingerprint, true, 8, 'Checked');
    $plan = $db->publicationPreview([$revision]);
    $db->assign('away', 1, 'ui.button.abort', 7, 'Different context');
    $db->assign('back', 1, 'ui.button.cancel', 7, 'Reconsidered');
    expect(fn () => $db->verifyPublicationPreview([$revision], $plan['fingerprint']))->toThrow(RuntimeException::class, 'not approved');
    expect($db->rows('approvals'))->toHaveCount(1);
    $db->decideRevision('new-review', $revision, $db->reviewSnapshot($revision)['fingerprint'], true, 8, 'New usage checked');
    expect($db->publicationPreview([$revision])['fingerprint'])->not->toBe($plan['fingerprint']);
});

it('refuses publication of a root scalar key without correcting it automatically', function () {
    [$db, $revision] = approvalBaseline();
    // Simulate an imported invalid name; the prototype does not implement renaming yet.
    $db->db->exec("UPDATE keys SET name='ui.type' WHERE id=1");
    $db->decideRevision('approve', $revision, $db->reviewSnapshot($revision)['fingerprint'], true, 8, 'Wording checked');
    expect(fn () => $db->publicationPreview([$revision]))->toThrow(RuntimeException::class, 'Nested scalar key required');
    expect($db->rows('keys')[0]['name'])->toBe('ui.type');
});

it('rejects empty or duplicate publication selections', function () {
    [$db, $revision] = approvalBaseline();
    expect(fn () => $db->publicationPreview([]))->toThrow(RuntimeException::class, 'distinct');
    expect(fn () => $db->publicationPreview([$revision, $revision]))->toThrow(RuntimeException::class, 'distinct');
    expect($db->rows('approvals'))->toBe([])->and($db->rows('events'))->toHaveCount(2);
});
