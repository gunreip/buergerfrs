<?php

// Isolated export destination contracts; no application paths or database are used.
use Tests\Support\TranslationWorkbench\PostgresPublisher;

if (! isset($db, $assert, $reject, $directory, $uuid, $key, $scalar)) {
    throw new RuntimeException('Run baseline-persistence.php');
}
$publisher = new PostgresPublisher($directory);
$destinationFixture = function (string $name, string $locale = 'en', string $namespace = '') use ($db, $uuid, $key): array {
    $id = $key($name);
    if ($namespace !== '') {
        $db->query('UPDATE translation_workbench_key_names SET namespace=? WHERE key_id=?', [$namespace, $id]);
    }
    $finding = $db->capture($uuid(), 'destination-'.$id.'.blade.php', 1, 'Destination')['finding'];
    $db->assign($uuid(), $finding, $id, null, 7, 'Destination fixture');
    $revision = $db->save($uuid(), $id, $locale, 'Destination', null, 7, 'Draft')['revision'];
    $db->decide($uuid(), $revision, $db->reviewSnapshot($revision)['fingerprint'], true, 7, 'Checked');

    return [$id, $revision];
};

// A new conflicting slot after planning must be detected again before writing.
[$parentKey, $parentRevision] = $destinationFixture('destinations.example.parent');
$plan = $publisher->prepare($uuid(), [$parentRevision], 7, 'Before conflict');
[$childKey, $childRevision] = $destinationFixture('destinations.example.parent.child');
$db->decide($uuid(), $childRevision, $db->reviewSnapshot($childRevision)['fingerprint'], false, 7, 'Unapproved still has identity');
$reject(fn () => $publisher->publish($plan['run'], $plan['fingerprint']), 'Publication target conflict');
$assert(! is_file($publisher->directory.'/en/destinations.php'), 'Late conflict wrote file');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_publication_outcomes WHERE run_id=?', [$plan['run']]) === 0, 'Late conflict claimed success');
$preview = $publisher->closurePreview($plan['run']);
$publisher->close($plan['run'], $preview['fingerprint'], 7, 'Resolve identities before retry');
$reject(fn () => $publisher->prepare($uuid(), [$parentRevision], 7, 'Unapproved child collision'), 'Publication target conflict');

// Locale metadata stays authored; publication uses lowercase hyphenated directories.
[$localeKey, $localeRevision] = $destinationFixture('regional.example.label', 'de-AT');
$localePlan = $publisher->prepare($uuid(), [$localeRevision], 7, 'Exact locale');
$publisher->publish($localePlan['run'], $localePlan['fingerprint']);
$assert(is_file($publisher->directory.'/de-at/regional.php') && ! is_dir($publisher->directory.'/de-AT'), 'Publication must use lowercase locale directories');
$assert($db->reviewSnapshot($localeRevision)['context']['locale'] === 'de-AT', 'Publication changed revision locale');
mkdir($publisher->directory.'/de-AT');
$reject(fn () => $publisher->prepare($uuid(), [$localeRevision], 7, 'Duplicate directory'), 'Ambiguous locale directory');
rmdir($publisher->directory.'/de-AT');
mkdir($publisher->directory.'/de-ch');
file_put_contents($publisher->directory.'/de-ch/regional.php', "<?php return ['existing' => ['value' => 'Keep me']];");
[, $swissRevision] = $destinationFixture('regional.swiss.label', 'de-CH');
$swissPlan = $publisher->prepare($uuid(), [$swissRevision], 7, 'Existing lowercase directory');
$publisher->publish($swissPlan['run'], $swissPlan['fingerprint']);
$swissValues = require $publisher->directory.'/de-ch/regional.php';
$assert($swissValues['swiss']['label'] === 'Destination' && $swissValues['existing']['value'] === 'Keep me', 'Existing regional file must be updated without losing other translations');
$assert(! is_dir($publisher->directory.'/de-CH'), 'Publication created a competing locale directory');
$alternate = $db->save($uuid(), $localeKey, 'de_at', 'Alternative', null, 7, 'Ambiguous spelling')['revision'];
$db->decide($uuid(), $alternate, $db->reviewSnapshot($alternate)['fingerprint'], true, 7, 'Checked');
$reject(fn () => $publisher->prepare($uuid(), [$alternate], 7, 'Alternate locale'), 'Ambiguous locale spelling');
$changed = $db->save($uuid(), $localeKey, 'de-AT', 'Changed', $localeRevision, 7, 'Edit')['revision'];
$db->decide($uuid(), $changed, $db->reviewSnapshot($changed)['fingerprint'], true, 7, 'Checked');
$reject(fn () => $publisher->prepare($uuid(), [$changed], 7, 'Conflicting slot spelling'), 'Ambiguous locale spelling');
$assert($db->reviewSnapshot($alternate)['context']['locale'] === 'de_at', 'Locale evidence changed');

// Unmapped namespaces cannot fall back into the application's lang directory.
[, $packageRevision] = $destinationFixture('package.example.label', 'en', 'vendor-package');
$reject(fn () => $publisher->prepare($uuid(), [$packageRevision], 7, 'Unknown loader root'), 'explicit loader-root mapping');
$assert(! is_file($publisher->directory.'/en/package.php'), 'Namespace leaked into app lang files');
[, $unsafeLocaleRevision] = $destinationFixture('unsafe.example.label', '../en');
$reject(fn () => $publisher->prepare($uuid(), [$unsafeLocaleRevision], 7, 'Unsafe locale'), 'Unsupported publication key or locale');
// These unrelated blocked entries must not block a valid application export.
[, $safeRevision] = $destinationFixture('independent.example.label');
$safePlan = $publisher->prepare($uuid(), [$safeRevision], 7, 'Unrelated valid export');
$publisher->publish($safePlan['run'], $safePlan['fingerprint']);
$assert(is_file($publisher->directory.'/en/independent.php'), 'Unrelated entries blocked export');
