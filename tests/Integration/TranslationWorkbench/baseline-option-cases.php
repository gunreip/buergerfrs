<?php

// Isolated persistence cases; no runtime provider or production data is called.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption;
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Illuminate\Support\Arr;
use Tests\Support\TranslationWorkbench\PostgresPublisher;

if (! isset($db, $assert, $directory, $uuid, $key, $race)) {
    throw new RuntimeException('Run baseline-persistence.php');
}
$optionKey = $key('options.example.choice');
$finding = $db->capture($uuid(), 'options.blade.php', 1, 'Choice')['finding'];
$db->assign($uuid(), $finding, $optionKey, null, 7, 'Options context');
$scalarRevision = $db->save($uuid(), $optionKey, 'en', 'Choice', null, 7, 'Scalar label')['revision'];
$options = ['yes', 'no', '0', '01', '1', '1.0', 'a.b', 'a%2Eb', 'ä / label'];
$optionRevisions = [];
foreach ($options as $identity) {
    $optionRevisions[$identity] = $db->save($uuid(), $optionKey, 'en', 'Label '.$identity, null, 7, 'Option draft', $identity)['revision'];
}
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_value_slots WHERE key_id=?', [$optionKey]) === count($options) + 1, 'Options collapsed or replaced scalar');
foreach ($options as $identity) {
    $snapshot = $db->reviewSnapshot($optionRevisions[$identity]);
    $assert($snapshot['context']['option_identity'] === $identity && $snapshot['context']['kind'] === 'option', 'Option identity changed');
}
$assert($db->reviewSnapshot($scalarRevision)['context']['option_identity'] === null, 'Scalar acquired option identity');
$reject(fn () => $db->save($uuid(), $optionKey, 'en', 'Empty', null, 7, 'Invalid identity', ''), 'Option identity must not be empty');
$reject(fn () => $db->query("INSERT INTO translation_workbench_value_slots(key_id,locale,kind,option_identity) VALUES (?,'en','option','yes')", [$optionKey]), 'option_slot_identity');
$reject(fn () => $db->query("INSERT INTO translation_workbench_value_slots(key_id,locale,kind,option_identity) VALUES (?,'en','option',NULL)", [$optionKey]), 'check');
$reject(fn () => $db->query("INSERT INTO translation_workbench_value_slots(key_id,locale,kind,option_identity) VALUES (?,'en','scalar','yes')", [$optionKey]), 'check');
$reject(fn () => $db->query("UPDATE translation_workbench_value_slots SET option_identity='other' WHERE key_id=? AND option_identity='yes'", [$optionKey]), 'Immutable slot identity');
$reject(fn () => $db->query("UPDATE translation_workbench_value_slots SET current_revision_id=? WHERE key_id=? AND option_identity='no'", [$optionRevisions['yes'], $optionKey]), 'revision_same_slot');

$yes = $optionRevisions['yes'];
$db->decide($uuid(), $yes, $db->reviewSnapshot($yes)['fingerprint'], true, 7, 'Yes checked');
$assert($db->approvalIsCurrent($yes), 'Option approval failed');
$assert(! $db->approvalIsCurrent($optionRevisions['no']) && ! $db->approvalIsCurrent($scalarRevision), 'Approval leaked across slots');
$german = $db->save($uuid(), $optionKey, 'de', 'Ja', null, 7, 'German option', 'yes')['revision'];
$assert(! $db->approvalIsCurrent($german), 'Approval leaked across locales');
$unchanged = $db->save($uuid(), $optionKey, 'en', 'Label yes', $yes, 7, 'Unchanged option', 'yes')['revision'];
$assert($unchanged === $yes && $db->approvalIsCurrent($yes), 'Unchanged option changed revision');
$newNo = $db->save($uuid(), $optionKey, 'en', 'No changed', $optionRevisions['no'], 7, 'Change no', 'no')['revision'];
$assert($db->approvalIsCurrent($yes), 'Another option invalidated approved text');
$assert(! $db->approvalIsCurrent($newNo), 'New option revision inherited approval');

$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$optionKey], [
    ['method' => 'save', 'args' => [$uuid(), $optionKey, 'en', 'Yes A', $yes, 7, 'Edit A', 'yes']],
    ['method' => 'save', 'args' => [$uuid(), $optionKey, 'en', 'Yes B', $yes, 8, 'Edit B', 'yes']],
]);
$assert(count(array_filter($results, fn ($r) => isset($r['result']))) === 1, 'Two stale option edits succeeded');
$assert(count(array_filter($results, fn ($r) => ($r['error'] ?? '') === 'Revision context changed')) === 1, 'Stale option edit not rejected');
$assert(! $db->approvalIsCurrent($yes), 'Option edit kept old approval valid');
$currentYes = (int) $scalar("SELECT current_revision_id FROM translation_workbench_value_slots WHERE key_id=? AND locale='en' AND option_identity='yes'", [$optionKey]);
$db->decide($uuid(), $currentYes, $db->reviewSnapshot($currentYes)['fingerprint'], true, 7, 'New yes checked');
$publisher = new PostgresPublisher($directory);
// Same operation must keep the exact option identity on retry.
$optionRetryArgs = [$uuid(), $optionKey, 'en', '', null, 7, 'Explicit empty option text', 'empty-text'];
$optionRetry = $db->save(...$optionRetryArgs);
$assert($db->save(...$optionRetryArgs) === $optionRetry, 'Option retry changed revision');
$alteredRetry = $optionRetryArgs;
$alteredRetry[7] = 'different-option';
$reject(fn () => $db->save(...$alteredRetry), 'Operation request changed');
$assert($db->reviewSnapshot($optionRetry['revision'])['context']['value'] === '', 'Empty text converted to missing');
file_put_contents($directory.'/option-restart.json', json_encode([
    'payload' => ['method' => 'save', 'args' => $optionRetryArgs],
    'result' => ['result' => $optionRetry],
], JSON_THROW_ON_ERROR));

// Publish using the same option-key mapping used by the runtime resolver.
$optionRevisions['yes'] = $currentYes;
$optionRevisions['no'] = $newNo;
$selectedOptions = [...array_values($optionRevisions), $german, $optionRetry['revision']];
foreach ($selectedOptions as $revision) {
    if (! $db->approvalIsCurrent($revision)) {
        $db->decide($uuid(), $revision, $db->reviewSnapshot($revision)['fingerprint'], true, 7, 'Option export checked');
    }
}
$db->decide($uuid(), $scalarRevision, $db->reviewSnapshot($scalarRevision)['fingerprint'], true, 7, 'Scalar checked');
$beforePlans = (int) $scalar('SELECT count(*) FROM translation_workbench_publication_runs');
$reject(fn () => $publisher->prepare($uuid(), [$scalarRevision, $currentYes], 7, 'Conflicting shapes'), 'Publication target conflict');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_publication_runs') === $beforePlans, 'Conflict left plan');
// Fixture resolution only: retire the conflicting scalar's current pointer. No product workflow implied.
$db->query('UPDATE translation_workbench_value_slots SET current_revision_id=NULL WHERE current_revision_id=?', [$scalarRevision]);
$optionPlan = $publisher->prepare($uuid(), $selectedOptions, 7, 'Publish options');
$publisher->publish($optionPlan['run'], $optionPlan['fingerprint']);
$reader = new BaselineValueReader;
foreach ($selectedOptions as $revision) {
    $context = $db->reviewSnapshot($revision)['context'];
    $tree = $reader->treeFromBytes(file_get_contents($publisher->directory.'/'.$context['locale'].'/options.php'), allowOptionKeys: true);
    $runtimeKey = TranslationWorkbenchOption::key($context['full_name'], $context['option_identity']);
    [, $nested] = explode('.', $runtimeKey, 2);
    $assert(Arr::get($tree, $nested) === $context['value'], 'Runtime option lookup changed identity or value');
}
// Re-read generated integer PHP keys, update one option, and preserve its siblings.
$changed = $db->save($uuid(), $optionKey, 'en', 'Numeric updated', $optionRevisions['1'], 7, 'Edit numeric option', '1')['revision'];
$db->decide($uuid(), $changed, $db->reviewSnapshot($changed)['fingerprint'], true, 7, 'Numeric checked');
$nextPlan = $publisher->prepare($uuid(), [$changed], 7, 'Update one option');
$publisher->publish($nextPlan['run'], $nextPlan['fingerprint']);
$tree = $reader->treeFromBytes(file_get_contents($publisher->directory.'/en/options.php'), allowOptionKeys: true);
$assert($tree['example']['choice']['options'][1] === 'Numeric updated', 'Numeric option update failed');
$assert($tree['example']['choice']['options']['01'] === 'Label 01' && $tree['example']['choice']['options']['a%2Eb'] === 'Label a.b' && $tree['example']['choice']['options']['a%252Eb'] === 'Label a%2Eb', 'Sibling identities overwritten');
$reject(fn () => $publisher->prepare($uuid(), [$scalarRevision], 7, 'Overwrite options'), 'Revision not currently approved');
$reject(fn () => $reader->treeFromBytes("<?php return ['regular' => [1 => 'bad']];", allowOptionKeys: true), 'explicit non-dotted string keys');
// A distinct scalar key may not silently target a selected option's physical path.
$aliasKey = $key('options.example.choice.options.yes');
$aliasFinding = $db->capture($uuid(), 'option-alias.blade.php', 1, 'Alias')['finding'];
$db->assign($uuid(), $aliasFinding, $aliasKey, null, 7, 'Alias usage');
$aliasRevision = $db->save($uuid(), $aliasKey, 'en', 'Alias', null, 7, 'Alias draft')['revision'];
$db->decide($uuid(), $aliasRevision, $db->reviewSnapshot($aliasRevision)['fingerprint'], true, 7, 'Alias checked');
$reject(fn () => $publisher->prepare($uuid(), [$currentYes, $aliasRevision], 7, 'Ambiguous targets'), 'Publication target conflict');

$reject(fn () => $publisher->prepare($uuid(), [$currentYes], 7, 'Unselected alias conflict'), 'Publication target conflict');
$reject(fn () => $publisher->prepare($uuid(), [$aliasRevision], 7, 'Unselected option conflict'), 'Publication target conflict');
