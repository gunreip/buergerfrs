<?php

use App\Models\User;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementWriter;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbench;
use Gunreip\TranslationWorkbench\Support\GenerationCaseDecisions;
use Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$bin = '/usr/lib/postgresql/18/bin';
$dir = sys_get_temp_dir().'/tw-ui-test-'.bin2hex(random_bytes(8));
mkdir($dir, 0700);
mkdir($dir.'/socket', 0700);
$run = static fn ($cmd) => (new Process($cmd))->mustRun();
$checks = 0;
$started = false;
$check = static function ($ok, string $message) use (&$checks) {
    if (! $ok) {
        throw new RuntimeException($message);
    } $checks++;
};
$reject = static function (callable $action, string $message) use ($check) {
    try {
        $action();
    } catch (RuntimeException $e) {
        $check(str_contains($e->getMessage(), $message), 'Unexpected rejection: '.$e->getMessage());

        return;
    }
    throw new RuntimeException('Expected rejection: '.$message);
};
try {
    $run([$bin.'/initdb', '-D', $dir.'/data', '-U', 'tester', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($dir.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$dir."/socket'\n");
    $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-l', $dir.'/log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $dir.'/socket', '-U', 'tester', 'editor']);
    $db = new PDO('pgsql:host='.$dir.'/socket;dbname=editor;user=tester', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    config(['database.default' => 'ui_test_admin', 'database.connections.ui_test_admin' => [
        'driver' => 'pgsql', 'host' => $dir.'/socket', 'port' => 5432, 'database' => 'editor',
        'username' => 'tester', 'password' => '', 'charset' => 'utf8', 'prefix' => '', 'search_path' => 'public', 'sslmode' => 'prefer',
    ], 'session.driver' => 'array']);
    $app->instance('env', 'testing');
    $app->useStoragePath($dir.'/storage');
    $app->useLangPath($dir.'/real-lang');
    mkdir($dir.'/real-lang', 0770, true);
    file_put_contents($dir.'/real-lang/sentinel.php', 'UNCHANGED');
    $db->exec("CREATE TABLE public.sentinel(value text); INSERT INTO public.sentinel VALUES ('UNCHANGED')");
    $user = new User;
    $user->id = 7;
    auth()->setUser($user);
    $access = Mockery::mock(TranslationWorkbenchEditorAccess::class);
    $access->shouldReceive('authorize');
    $app->instance(TranslationWorkbenchEditorAccess::class, $access);
    $environment = app(WorkbenchTestEnvironment::class);
    $token = $environment->start();
    $firstRoot = $environment->root();
    $editor = app(GenerationEditor::class);
    $check($editor->totals()['database'] === 'tw_ui_test_'.$token, 'Wrong test database');
    $check($editor->page('', '', 'all', 1)->total() === 24, 'Expected multiple cases per state');
    $check((int) $editor->totals()['revisions'] === 0, 'Initial test data must contain no translation revisions');
    $check(app(GenerationPublisher::class)->recent() === [], 'Initial test data must contain no publication runs');
    $check(count(\Illuminate\Support\Facades\File::allFiles($firstRoot.'/lang')) === 0, 'Initial language directory must contain no published translations');
    $catalog = json_decode(file_get_contents($firstRoot.'/cases.json'), true);
    foreach (array_count_values(array_column($catalog, 'state')) as $count) {
        $check($count === 3, 'Every scenario needs three variants');
    }
    $caseId = static fn ($state) => array_values(array_filter($catalog, fn ($c) => $c['state'] === $state))[0]['id'];
    $shared = $caseId('shared');
    $archived = $caseId('archived');
    $summaries = $editor->sharedSummaries([$shared, $archived]);
    $check((int) $summaries[$shared]['shared'] === 3, 'Shared key should count three active fixtures');
    $check((int) $summaries[$shared]['candidates'] > 3, 'Matching text with different assignments should remain a candidate');
    $check((int) $summaries[$archived]['candidates'] === 0 && (int) $summaries[$archived]['shared'] === 0, 'Archived findings must not be candidates');
    $matches = $editor->sharedFindings($shared, 1);
    $check((int) $matches->items()[0]['id'] === $shared && ! in_array($archived, array_column($matches->items(), 'id')), 'Matching findings must start with the current finding and exclude archived records');
    $check($matches->total() === (int) $summaries[$shared]['candidates'], 'All exact text matches should be available');

    $unassigned = $caseId('unassigned');
    $reject(fn () => $editor->assign($unassigned, 0, null, 7, 'Invalid root test', 'unconfigured.test.key'), 'configured first key segment');
    $check($editor->inspect($unassigned, 'en')['finding']['key_id'] === null, 'Rejected root must not assign a key');
    $initialWorkflow = $editor->workflowOverview($unassigned, 'de');
    $check(array_column($initialWorkflow['languages'], 'locale') === ['en', 'de'], 'Unused sublanguages must not appear as pending workflow tasks');
    $check(array_column($initialWorkflow['steps'], null, 'id')['assignment']['status'] === 'current', 'Unassigned finding must start at key assignment');
    $assigned = $editor->assign($unassigned, 0, null, 7, 'UI test assignment', 'ui.test.new_key');
    $check($editor->inspect($unassigned, 'en')['finding']['key_id'] == $assigned['key'], 'Assignment not persisted to test database');
    foreach (['en' => 'Save changes', 'de' => 'Änderungen speichern', 'de-AT' => 'Änderungen übernehmen'] as $locale => $text) {
        $check($editor->inspect($unassigned, $locale)['revision'] === null, 'Initial locale must be untranslated');
        $revision = $editor->save($unassigned, $assigned['key'], $assigned['link'], $locale, $text, null, 7, 'UI test: translate an empty locale')['revision'];
        $check($editor->inspect($unassigned, $locale)['revision']['value'] === $text, 'Translation must persist in its locale');
        if ($locale === 'en') {
            $editor->review($revision, $editor->inspect($unassigned, 'en')['review']['fingerprint'], true, 7, 'UI test approval');
            $publisher = app(GenerationPublisher::class);
            $allowedSegments = config('translation-workbench.allowed_first_translation_key_segments');
            config(['translation-workbench.allowed_first_translation_key_segments' => ['admin']]);
            $reject(fn () => $publisher->prepare((string) Str::uuid(), [$revision], 7, 'Reject unconfigured root'), 'configured first key segment');
            config(['translation-workbench.allowed_first_translation_key_segments' => $allowedSegments]);
            $plan = $publisher->prepare((string) Str::uuid(), [$revision], 7, 'UI test publication');
            config(['translation-workbench.allowed_first_translation_key_segments' => ['admin']]);
            $reject(fn () => $publisher->publish($plan['run'], $plan['fingerprint']), 'configured first key segment');
            $check(!is_file($firstRoot.'/lang/en/ui.php'), 'Removed config root must block writing a prepared publication');
            config(['translation-workbench.allowed_first_translation_key_segments' => $allowedSegments]);
            $publisher->publish($plan['run'], $plan['fingerprint']);
            $check($publisher->inspect($plan['run'])['state'] === 'complete', 'Publication must complete in the fixture language directory');
        }
    }
    // Existing application folders use lowercase even though the editor locale is de-CH.
    mkdir($firstRoot.'/lang/de-ch');
    file_put_contents($firstRoot.'/lang/de-ch/ui.php', "<?php return ['existing' => ['label' => 'Keep me']];");
    $swissRevision = $editor->save($unassigned, $assigned['key'], $assigned['link'], 'de-CH', 'Schweizer Text', null, 7, 'Regional publication regression')['revision'];
    $editor->review($swissRevision, $editor->inspect($unassigned, 'de-CH')['review']['fingerprint'], true, 7, 'Approve Swiss text');
    $swissPlan = $publisher->prepare((string) Str::uuid(), [$swissRevision], 7, 'Use existing locale directory');
    $publisher->publish($swissPlan['run'], $swissPlan['fingerprint']);
    $swissValues = require $firstRoot.'/lang/de-ch/ui.php';
    $check($swissValues['test']['new_key'] === 'Schweizer Text' && $swissValues['existing']['label'] === 'Keep me', 'Regional publication must update existing lowercase files and preserve other values');
    $check(!is_dir($firstRoot.'/lang/de-CH') && $editor->inspect($unassigned, 'de-CH')['revision']['value'] === 'Schweizer Text', 'Publication must preserve the database locale without creating another directory');
    mkdir($firstRoot.'/lang/de-CH');
    $reject(fn () => $publisher->prepare((string) Str::uuid(), [$swissRevision], 7, 'Duplicate folder'), 'Ambiguous locale directory');
    rmdir($firstRoot.'/lang/de-CH');
    $aliasRevision = $editor->save($unassigned, $assigned['key'], $assigned['link'], 'de_ch', 'Conflicting alias', null, 7, 'Ambiguous locale regression')['revision'];
    $reject(fn () => $publisher->prepare((string) Str::uuid(), [$swissRevision], 7, 'Duplicate locale slot'), 'Ambiguous locale spelling');
    $published = $editor->inspect($unassigned, 'en');
    $preview = $editor->sourcePreview($published['finding']['id'], $published['finding']['key_id'], $published['finding']['link_id'], 'en');
    $check($preview['ready'], 'Published fixture must allow source replacement');
    $writer = app(SourceReplacementWriter::class);
    $sourceRun = $writer->prepare($published['finding']['id'], 'en', $preview['fingerprint'], 7, 'UI fixture replacement');
    $check($sourceRun !== '', 'Source replacement could not be prepared');
    $writer->apply($sourceRun, 7);
    $check(str_contains(file_get_contents($firstRoot.'/'.$published['finding']['path']), $published['finding']['full_name']), 'Replacement must write only the fixture source');
    $oldWorkflow = $editor->workflowOverview($unassigned, 'de');
    $successorWorkflow = $editor->workflowOverview((int) $oldWorkflow['successor'], 'de');
    $sourceSteps = array_column($successorWorkflow['steps'], null, 'id');
    $check($sourceSteps['source']['done'] && !$sourceSteps['scan']['done'], 'Source rewrite must not pretend a later full scan has occurred');
    $check((int) $oldWorkflow['successor'] !== $unassigned, 'Archived finding must retain its successor navigation');
    $service = app(GenerationCaseDecisions::class);
    $snapshot = $service->snapshot();
    $check(count($snapshot['cases']) === 9 && count($service->decisions($service->connection($snapshot))) === 6, 'Source usage cases missing');
    $service->save($service->connection($snapshot), $snapshot['cases']['source-case-1'], 'database_value', 'UI test decision', 7, 0);
    $check(count($service->decisions($service->connection($snapshot))) === 7, 'Source decision not persisted');
    $newToken = $environment->start();
    $check($newToken !== $token, 'Activation must create a fresh test run');
    $check(! is_dir($firstRoot), 'Previous test files must be removed');
    $check(! $db->query("SELECT 1 FROM pg_database WHERE datname='tw_ui_test_".$token."'")->fetchColumn(), 'Previous test database must be removed');
    $check(app(GenerationEditor::class)->inspect($unassigned, 'en')['finding']['key_id'] === null, 'Test edits leaked into the reset');
    $check((int) app(GenerationEditor::class)->totals()['revisions'] === 0, 'User-created translations must be removed on reset');
    try {
        $environment->activate($token);
        throw new RuntimeException('Stale token accepted');
    } catch (HttpException $e) {
        $check($e->getStatusCode() === 409, 'Stale run must fail closed');
    }
    try {
        $environment->activate('');
        throw new RuntimeException('Old real-mode page accepted');
    } catch (HttpException $e) {
        $check($e->getStatusCode() === 409, 'Old real-mode page must require reload');
    }
    $locales = Mockery::mock(TranslationWorkbenchEditorLocales::class);
    $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => ['de-AT']]);
    $app->instance(TranslationWorkbenchEditorLocales::class, $locales);
    $component = Livewire::test(TranslationWorkbench::class);
    $component->assertSet('testToken', $newToken)->assertSee('UI test environment')->call('open', $unassigned)->assertSee('Translation key assignment');
    $component->call('openWorkflowStatus', $unassigned)->assertSet('workflowStatusOpen', true)->assertSeeHtml('data-flux-timeline')->assertSee('Finding captured')->assertHasNoErrors()->set('workflowStatusOpen', false);

    $component->set('assignmentReason', 'Keep this unsaved reason')
        ->call('openWorkflowStatus', $unassigned)
        ->assertSeeHtml('wire:click="continueWorkflow"')
        ->call('continueWorkflow')->assertSet('workflowStatusOpen', false)
        ->assertSet('editorOpen', true)->assertSet('selected', $unassigned)
        ->assertSet('assignmentReason', 'Keep this unsaved reason')
        ->assertDispatched('workflow-focus', target: 'workbench-key-assignment')
        ->assertHasNoErrors()->set('assignmentReason', '');

    $statesFor = static fn (int $id) => array_column(app(GenerationEditor::class)->workflowStates([$id])[$id] ?? [], 'state', 'locale');
    $openBefore = app(GenerationEditor::class)->page('', '', 'active', 1, 'id', 'desc', 25, true)->total();
    $tasksBefore = app(GenerationEditor::class)->translationTasks('', 1)->total();
    foreach (['key', 'namespace', 'findings'] as $sort) {
        $ascending = app(GenerationEditor::class)->translationTasks('', 1, 100, $sort, 'asc')->items();
        $descending = app(GenerationEditor::class)->translationTasks('', 1, 100, $sort, 'desc')->items();
        $check(array_column($ascending, 'key_id') === array_reverse(array_column($descending, 'key_id')), 'Translation sorting must reverse deterministically');
        $paged = app(GenerationEditor::class)->translationTasks('', 2, 10, $sort, 'asc');
        $check(array_column($paged->items(), 'key_id') === array_column(array_slice($ascending, ($paged->currentPage() - 1) * 10, 10), 'key_id'), 'Translation pagination must follow the sorted result');
    }
    $reject(fn () => app(GenerationEditor::class)->translationTasks('', 1, 25, 'invalid'), 'Invalid translations sort.');

    $component->set('tab', 'translations')->assertSee('Saved language revisions')->set('tab', 'findings');

    $component->call('openShared', $shared)->assertSet('sharedOpen', true)->assertSee('Same assigned key')->assertSee('Different assigned key');
    $component->set('sharedOpen', false);
    $component->set('assignmentKey', 'ui.test.editor_workflow')
        ->call('openAssignmentReason', 'Exercise the complete edit workflow')
        ->assertSet('newAssignmentReasonLocale', 'de')->set('newAssignmentReasonLocale', 'en')
        ->call('createAssignmentReason')
        ->call('confirmAssignment')->assertSet('confirmOpen', true)
        ->call('assign')->assertHasNoErrors()->assertSet('confirmOpen', false)
        ->set('sourceLanguageValue', 'Edited English source')
        ->call('copySourceToTarget')->assertSet('value', 'Edited English source')
        ->set('value', 'Deutscher Workflow-Test')
        ->set('selectedSubLanguages', ['de-AT'])
        ->call('copyTargetLanguageToAllSelectedSubLanguages')
        ->assertSet('subLanguageValues.de-AT', 'Deutscher Workflow-Test')
        ->set('subLanguageValues.de-AT', 'Österreichischer Workflow-Test')
        ->call('openChangeReason', 'Save main-language draft through the editor')
        ->assertSet('newChangeReasonLocale', 'de')->call('createChangeReason')
        ->call('saveDraft');
    $check($component->errors()->isEmpty(), 'Editor save failed: '.json_encode($component->errors()->toArray()));
    $check(app(GenerationEditor::class)->reasonLocales('assignment')['Exercise the complete edit workflow'] === 'en', 'Assignment reason language must persist independently of the target language');
    $check(app(GenerationEditor::class)->reasonLocales('change')['Save main-language draft through the editor'] === 'de', 'German reason language must be stored even for English source revisions');
    $component->call('openChangeReason', 'Save main-language draft through the editor')->assertSet('newChangeReasonLocale', 'de')->set('changeReasonOpen', false);
    $component->call('openAssignmentReason', 'Exercise the complete edit workflow')->assertSet('newAssignmentReasonLocale', 'en')->set('assignmentReasonOpen', false);

    $check(app(GenerationEditor::class)->inspect($unassigned, 'de')['revision']['value'] === 'Deutscher Workflow-Test', 'Editor draft must persist to the isolated database');
    $check(app(GenerationEditor::class)->inspect($unassigned, 'en')['revision']['value'] === 'Edited English source', 'English source draft must persist');
    $check(app(GenerationEditor::class)->inspect($unassigned, 'de-AT')['revision']['value'] === 'Österreichischer Workflow-Test', 'Selected variant draft must persist');
    $component->assertSet('sourceLanguageValue', 'Edited English source')
        ->assertSet('subLanguageValues.de-AT', 'Österreichischer Workflow-Test');
    $batchEditor = app(GenerationEditor::class);
    $snapshot = $batchEditor->inspect($unassigned, 'de');
    $sourceSnapshot = $batchEditor->inspect($unassigned, 'en');
    $variantSnapshot = $batchEditor->inspect($unassigned, 'de-AT');
    $batchEditor->save($unassigned, (int) $snapshot['finding']['key_id'], (int) $snapshot['finding']['link_id'],
        'de-AT', 'Concurrent Austrian edit', (int) $variantSnapshot['revision']['id'], 7, 'Simulate a concurrent editor');
    $reject(fn () => $batchEditor->saveDrafts($unassigned, (int) $snapshot['finding']['key_id'], (int) $snapshot['finding']['link_id'], [
        'en' => ['value' => 'Must roll back', 'revision' => (int) $sourceSnapshot['revision']['id']],
        'de-AT' => ['value' => 'Stale edit', 'revision' => (int) $variantSnapshot['revision']['id']],
    ], 7, 'Check atomic conflict handling'), 'Revision context changed');
    $check($batchEditor->inspect($unassigned, 'en')['revision']['value'] === 'Edited English source', 'Conflicting variant must roll back the entire batch');

    $check(app(GenerationEditor::class)->page('', '', 'active', 1, 'id', 'desc', 25, true)->total() === $openBefore - 1, 'Assigned finding must leave the open queue');
    $tasks = app(GenerationEditor::class)->translationTasks('ui.test.editor_workflow', 1);
    $check($tasks->total() === 1 && count($tasks->items()[0]['locales']) === 3, 'Translations must show the key once with all saved languages');
    $filterKeyId = (string) $tasks->items()[0]['key_id'];
    $check(app(GenerationEditor::class)->translationTasks('ui.test.editor_workflow', 1, keyId: $filterKeyId)->total() === 1, 'Translation ID and key filters must combine');
    $check(app(GenerationEditor::class)->translationTasks('missing', 1, keyId: $filterKeyId)->total() === 0, 'Matching ID must not bypass mismatching key filter');
    $check(app(GenerationEditor::class)->translationTasks('', 1, keyId: 'invalid')->total() === 0, 'Invalid key ID must return no tasks');

    $check(app(GenerationEditor::class)->translationTasks('', 1)->total() === $tasksBefore + 1, 'New key must enter the translation queue');
    $check($statesFor($unassigned)['de'] === 'draft', 'Saved draft must have draft workflow status');
    $component->set('tab', 'translations')->set('translationSearch', 'ui.test.editor_workflow')
        ->assertSee('ui.test.editor_workflow')->assertSee('de-AT');
    $component->call('open', $unassigned)->assertSet('value', 'Deutscher Workflow-Test')
        ->call('confirmReview', true)->assertSet('reviewOpen', true)->set('reviewLocales', ['de'])
        ->set('reviewReason', 'Approve the tested German draft')->call('review')->assertHasNoErrors()
        ->assertSet('context.review.approved', true)
        ->call('openWorkflowReason', 'publicationReason', 'Publish only to isolated test files')
        ->assertSet('newWorkflowReasonLocale', 'de')->set('newWorkflowReasonLocale', 'en')
        ->call('createWorkflowReason')->assertHasNoErrors()
        ->call('preparePublication')->assertHasNoErrors()->assertSet('publicationOpen', true)
        ->call('publish')->assertHasNoErrors();
    $component->assertSet('publication.state', 'complete');
    $check(app(GenerationEditor::class)->reasonLocales('publication')['Publish only to isolated test files'] === 'en', 'Publication reason language must survive preparation and publication');
    $check($statesFor($unassigned)['de'] === 'published' && $statesFor($unassigned)['en'] === 'draft', 'Publication status must be locale-specific');
    $change = $component->get('publication.changes')[0];
    $languageFile = $environment->root().'/lang/'.$change['path'];
    $translations = require $languageFile;
    $check(data_get($translations, 'test.editor_workflow') === 'Deutscher Workflow-Test', 'Editor publication must be readable from the isolated language file');

    $component->call('open', $unassigned)->assertSet('sourceLanguageValue', 'Edited English source')
        ->assertSet('value', 'Deutscher Workflow-Test')->set('selectedSubLanguages', ['de-AT'])
        ->assertSet('subLanguageValues.de-AT', 'Concurrent Austrian edit');
    $component->set('value', 'Unsaved change')->call('submitForReview')->assertHasErrors('submission');
    $component->set('value', 'Deutscher Workflow-Test')->call('submitForReview')->assertHasNoErrors()
        ->assertSet('tab', 'review')->assertSet('editorOpen', false);
    $worklist = app(GenerationEditor::class)->reviewWorklist();
    $pending = $worklist->page('', 1)->items();
    $check($statesFor($unassigned)['en'] === 'review', 'Submitted revision must be in review');
    $check($worklist->page('ui.test.editor_workflow', 1, keyId: $filterKeyId, locale: 'en')->total() === 1, 'Review filters must combine key, ID and locale');
    $check($worklist->page('', 1, keyId: $filterKeyId, locale: 'fr')->total() === 0, 'Review locale must restrict matching key IDs');
    $component->set('reviewKeyId', $filterKeyId)->set('reviewLanguage', 'en')->assertSee('Language')
        ->set('reviewLanguage', '')->set('reviewKeyId', '');

    $check(array_column($worklist->page('', 1, 25, 'language', 'asc')->items(), 'locale') === ['de-AT', 'en'], 'Review language sort must be ascending');
    $check(array_column($worklist->page('', 1, 25, 'language', 'desc')->items(), 'locale') === ['en', 'de-AT'], 'Review language sort must be descending');
    foreach (['key', 'submitted'] as $sort) {
        $ascending = array_column($worklist->page('', 1, 25, $sort, 'asc')->items(), 'revision');
        $descending = array_column($worklist->page('', 1, 25, $sort, 'desc')->items(), 'revision');
        $check($ascending === array_reverse($descending), 'Review '.$sort.' sort must have deterministic reversed ties');
    }
    $reject(fn () => $worklist->page('', 1, 25, 'invalid'), 'Invalid review sort');
    $reject(fn () => $worklist->page('', 1, 25, 'key', 'invalid'), 'Invalid review sort');
    $component->call('sortReview', 'language')->assertSet('reviewDirection', 'asc')->assertSeeHtml('sortReview');
    $component->call('sortReview', 'language')->assertSet('reviewDirection', 'desc');

    $check(count($pending) === 2, 'Unapproved source and variant revisions must enter review; approved German revision is skipped');
    $duplicate = [];
    foreach ($pending as $row) {
        $duplicate[(int) $row['revision']] = $worklist->inspect((int) $row['revision'])['fingerprint'];
    }
    $check($worklist->submit($duplicate, 7)['submitted'] === [], 'Repeated submission must not create duplicate review events');

    $revisionIds = array_map('strval', array_column($pending, 'revision'));
    $component->set('selectedReviewRevisions', [end($revisionIds)])
        ->set('reviewLanguage', $pending[0]['locale'])->call('toggleVisibleReviewRevisions');
    $check(count($component->get('selectedReviewRevisions')) === 2, 'Select page preserves selections outside the filtered page');
    $component->call('toggleVisibleReviewRevisions');
    $check($component->get('selectedReviewRevisions') === [end($revisionIds)], 'Deselect page preserves selections outside the filtered page');
    $component->set('reviewLanguage', '')->set('selectedReviewRevisions', $revisionIds)
        ->call('openReviewBatch')->assertSet('reviewBatchOpen', true)->assertSee('Review selected revisions')
        ->assertSet('reviewBatchReasonLocale', 'de')->assertSeeHtml('reviewBatchReasonLocale')
        ->set('reviewBatchReasonLocale', 'en');
    $reviewDom = new DOMDocument();
    @$reviewDom->loadHTML($component->html());
    $reviewXpath = new DOMXPath($reviewDom);
    $reasonInput = $reviewXpath->query('//input[starts-with(@id, "review-batch-reason-search-")]')->item(0);
    $check($reasonInput !== null && $reasonInput->getAttribute('lang') === 'en' && $reasonInput->getAttribute('spellcheck') === 'true', 'Batch review language must reach the actual reason search input');
    $component->call('decideReviewBatch', true)->assertHasErrors('reviewBatchReason');
    $stale = $duplicate;
    $stale[array_key_last($stale)] = str_repeat('0', 64);
    $reject(fn () => $worklist->decideMany($stale, true, 7, 'Stale batch'), 'Review context changed');
    $check($worklist->page('', 1)->total() === 2, 'A stale batch must not approve any revision');
    $component->set('reviewBatchOpen', false)->set('selectedReviewRevisions', []);

    $check(app(GenerationEditor::class)->translationTasks('ui.test.editor_workflow', 1)->total() === 0, 'Fully submitted or approved key must leave Translations');
    $sourceReview = array_values(array_filter($pending, fn ($row) => $row['locale'] === 'en'))[0];
    $component->call('openReviewTask', (int) $sourceReview['revision'])->assertSet('reviewTaskOpen', true)
        ->set('reviewTaskReason', 'Source needs a correction')->call('decideReviewTask', false)->assertHasNoErrors();
    $check($statesFor($unassigned)['en'] === 'draft', 'Returned revision must be a draft again');
    $check($worklist->page('', 1)->total() === 1, 'Returned source must leave Review');
    $check(app(GenerationEditor::class)->translationTasks('ui.test.editor_workflow', 1)->total() === 1, 'Returned revision must remain editable in Translations');
    $component->call('open', $unassigned)->set('selectedSubLanguages', ['de-AT'])->call('submitForReview')->assertHasNoErrors();
    $check($worklist->page('', 1)->total() === 2, 'Returned revision can be explicitly resubmitted');
    $component->call('openReviewTask', (int) $sourceReview['revision'])
        ->call('openWorkflowReason', 'reviewTaskReason', 'Quelle geprüft')
        ->assertSee('Create a review reason')->assertSeeHtml('data-flux-toggle')
        ->assertSet('newWorkflowReasonLocale', 'de')->call('createWorkflowReason')
        ->assertSet('reviewTaskReason', 'Quelle geprüft')->call('decideReviewTask', true)->assertHasNoErrors();
    $check(app(GenerationEditor::class)->reasonLocales('review')['Quelle geprüft'] === 'de', 'Review reason language must persist independently of the reviewed English revision');
    $check($statesFor($unassigned)['en'] === 'approved', 'Approved revision must await publication');
    $check($worklist->publicationPage('', 1)->total() === 1, 'Only approved unpublished source must enter Publication');
    foreach (['key', 'language'] as $sort) {
        $check($worklist->publicationPage('', 1, sort: $sort, direction: 'asc')->total() === 1, 'Publication sort must retain approved rows');
        $check($worklist->publicationPage('', 1, sort: $sort, direction: 'desc')->total() === 1, 'Publication reverse sort must retain approved rows');
    }
    $reject(fn () => $worklist->publicationPage('', 1, sort: 'invalid'), 'Invalid publication sort');

    $check($worklist->publicationPage('ui.test.editor_workflow', 1, keyId: $filterKeyId, locale: 'en')->total() === 1, 'Publication filters must combine');
    $check($worklist->publicationPage('missing', 1, keyId: $filterKeyId, locale: 'en')->total() === 0, 'Publication key text must not be bypassed');

    $check($worklist->publicationPage('', 1, 10, locale: 'en')->total() === 1, 'Publication search must find locale');
    $component->set('tab', 'publication')->assertSee('ui.test.editor_workflow')
        ->call('openPublicationTask', (int) $sourceReview['revision'])->assertSet('publicationTaskOpen', true)
        ->set('publicationReason', 'Publish reviewed English source')->call('preparePublicationTask')
        ->assertHasNoErrors()->assertSet('publicationTaskOpen', false)->assertSet('publication.state', 'prepared');
    $check($statesFor($unassigned)['en'] === 'approved', 'Prepared publication must not show Published');
    $check($worklist->publicationPage('', 1)->total() === 1, 'Preparation must not mark a revision published');
    $check(app(GenerationEditor::class)->publishedWorklist()->page('', 1)->total() === 1, 'Prepared English revision must not appear in Published');
    $component->call('publish')->assertHasNoErrors()->assertSet('publication.state', 'complete');
    $check($worklist->publicationPage('', 1)->total() === 0, 'Completed revision must leave Publication');
    $publishedSource = require $environment->root().'/lang/en/ui.php';
    $check(data_get($publishedSource, 'test.editor_workflow') === 'Edited English source', 'Publication tab writes the approved English text');
    $reject(fn () => $worklist->inspectPublication((int) $sourceReview['revision']), 'no longer waiting for publication');
    $variantCurrent = app(GenerationEditor::class)->inspect($unassigned, 'de-AT');
    app(GenerationEditor::class)->save($unassigned, (int) $variantCurrent['finding']['key_id'], (int) $variantCurrent['finding']['link_id'],
        'de-AT', 'Changed after submission', (int) $variantCurrent['revision']['id'], 7, 'Invalidate pending submission');
    $check($worklist->page('', 1)->total() === 0, 'Changed submitted revision must disappear from Review');
    $check(app(GenerationEditor::class)->translationTasks('ui.test.editor_workflow', 1)->total() === 1, 'Changed language must return to Translations');
    $variantSnapshot = app(GenerationEditor::class)->inspect($unassigned, 'de-AT');
    $worklist->submit([(int) $variantSnapshot['revision']['id'] => $variantSnapshot['review']['fingerprint']], 7);
    $worklist->decide((int) $variantSnapshot['revision']['id'], $worklist->inspect((int) $variantSnapshot['revision']['id'])['fingerprint'], true, 7, 'Review before changed source linkage');
    $check($worklist->publicationPage('', 1)->total() === 1, 'Approved variant must enter Publication');
    $component->call('openPublicationTask', (int) $variantSnapshot['revision']['id']);
    $otherFinding = $caseId('assigned');
    $other = app(GenerationEditor::class)->inspect($otherFinding, 'de');
    app(GenerationEditor::class)->assign($otherFinding, (int) $variantSnapshot['finding']['key_id'],
        (int) $other['finding']['link_id'], 7, 'Test changed review source context');
    $check($worklist->page('', 1)->total() === 0, 'New source assignment must invalidate the pending review context');
    $check($statesFor($unassigned)['de'] === 'draft', 'Changed linkage must not present stale approval as current Published status');
    $check($worklist->publicationPage('', 1)->total() === 0, 'Changed source linkage must invalidate publication approval');
    $component->set('publicationReason', 'Attempt stale publication')->call('preparePublicationTask')->assertHasErrors('publicationTask');


    $archivedRows = app(GenerationEditor::class)->page('', '', 'obsolete', 1)->items();
    $movedRows = array_values(array_filter($archivedRows, fn ($row) => $row['successor_id'] !== null));
    $deletedRows = array_values(array_filter($archivedRows, fn ($row) => $row['retired_reason'] === 'Absent from completed scan inventory'));
    $check(count($movedRows) === 3 && count($deletedRows) === 3, 'Archived fixtures must distinguish removed findings and linked successors');
    $check(count(array_filter($movedRows, fn ($row) => $row['successor_kind'] === 'moved')) === 3, 'Moved findings must expose their successor classification');
    $publishedList = app(GenerationEditor::class)->publishedWorklist();
    $check($publishedList->page('', 1)->total() === 2, 'Published must contain completed English and German revisions only');
    foreach (['key', 'language', 'working', 'published'] as $sort) {
        $ascending = array_column($publishedList->page('', 1, sort: $sort, direction: 'asc')->items(), 'revision');
        $descending = array_column($publishedList->page('', 1, sort: $sort, direction: 'desc')->items(), 'revision');
        $check($ascending === array_reverse($descending), 'Published '.$sort.' sort must reverse deterministically');
    }
    $reject(fn () => $publishedList->page('', 1, sort: 'invalid'), 'Invalid published sort');

    $check($publishedList->page('ui.test.editor_workflow', 1, keyId: $filterKeyId, locale: 'de')->total() === 1, 'Published filters must combine');
    $check($publishedList->page('', 1, keyId: 'invalid', locale: 'de')->total() === 0, 'Published must reject invalid key IDs');

    $check($publishedList->page('', 1, locale: 'de-AT')->total() === 0, 'Unpublished variant must not appear in Published');
    $check($publishedList->page('', 99, 10, locale: 'en')->currentPage() === 1, 'Published pagination must clamp to the available page');
    $component->set('tab', 'published')->assertSee('Matches published revision')->assertSee('ui.test.editor_workflow');
    $dePublished = $publishedList->page('', 1, locale: 'de')->items()[0];
    $component->call('open', (int) $dePublished['finding_id'])->assertSet('editorOpen', true)->assertSet('value', 'Deutscher Workflow-Test');
    $currentGerman = app(GenerationEditor::class)->inspect($unassigned, 'de');
    app(GenerationEditor::class)->save($unassigned, (int) $currentGerman['finding']['key_id'], (int) $currentGerman['finding']['link_id'],
        'de', 'Revised German publication', (int) $currentGerman['revision']['id'], 7, 'Edit after publication');
    $check($publishedList->page('', 1, locale: 'de')->items()[0]['value'] === 'Deutscher Workflow-Test', 'Draft must not overwrite the published snapshot');
    $component->set('editorOpen', false)->assertSee('Different working revision');
    $check(array_column($publishedList->page('', 1, sort: 'working', direction: 'asc')->items(), 'working_state') === [0, 1], 'Working revision sort must group the displayed states');

    $currentGerman = app(GenerationEditor::class)->inspect($unassigned, 'de');
    app(GenerationEditor::class)->review((int) $currentGerman['revision']['id'], $currentGerman['review']['fingerprint'], true, 7, 'Approve revised German text');
    $publisher = app(GenerationPublisher::class);
    $newRun = $publisher->prepare((string) Str::uuid(), [(int) $currentGerman['revision']['id']], 7, 'Republish revised German text');
    $publisher->publish($newRun['run'], $publisher->inspect($newRun['run'])['fingerprint']);
    $check($publishedList->page('', 1, locale: 'de')->total() === 1, 'Repeated publication must replace the previous destination in the list');
    $check($publishedList->page('', 1, locale: 'de')->items()[0]['value'] === 'Revised German publication', 'Latest completed value must be shown');
    $check($statesFor($unassigned)['de'] === 'published', 'Republished current revision must show Published');
    $check($statesFor($archived) === [], 'Archived findings must not present current key workflow states');
    $check($publishedList->page('missing-key', 1)->total() === 0, 'Published search must exclude unrelated keys');
    $component->set('publishedSearch', 'missing-key')->assertSee('No completed publications found.')
        ->set('publishedPerPage', 999)->assertSet('publishedPerPage', 25);
    $allFindings = app(GenerationEditor::class)->page('', '', 'all', 1, perPage: 100);
    $compactFindings = app(GenerationEditor::class)->page('', '', 'all_compact', 1, perPage: 100);
    $check($allFindings->total() > $compactFindings->total(), 'Compact view must group repeated active literals');
    $check(array_sum(array_column($compactFindings->items(), 'compact_count')) === $allFindings->total(), 'Compact groups must account for every filtered finding');
    $archivedCompact = array_filter($compactFindings->items(), fn ($row) => $row['status'] !== 'active');
    $check(count($archivedCompact) === count(array_filter($allFindings->items(), fn ($row) => $row['status'] !== 'active')), 'Archived findings must never be hidden by candidate grouping');
    $active = app(GenerationEditor::class)->page('', '', 'active', 1, perPage: 100, unassignedOnly: true);
    $activeCompact = app(GenerationEditor::class)->page('', '', 'active_compact', 1, perPage: 100, unassignedOnly: true);
    $check(array_sum(array_column($activeCompact->items(), 'compact_count')) === $active->total(), 'Active compact must preserve the open active queue');
    $allActive = app(GenerationEditor::class)->page('', '', 'active', 1, perPage: 100);
    $allActiveCompact = app(GenerationEditor::class)->page('', '', 'active_compact', 1, perPage: 100);
    $check($allActiveCompact->total() < $allActive->total(), 'Active compact must group matching active findings');
    $check(array_sum(array_column($allActiveCompact->items(), 'compact_count')) === $allActive->total(), 'Active grouping must retain every active finding');
    foreach (['all', 'all_compact'] as $view) {
        $visible = app(GenerationEditor::class)->page('', '', $view, 1, perPage: 100, includeArchived: false);
        $check(count(array_filter($visible->items(), fn ($row) => $row['status'] !== 'active')) === 0, 'Archived toggle must filter before grouping');
    }
    $archivedOnly = app(GenerationEditor::class)->page('', '', 'obsolete', 1, includeArchived: false);
    $check($archivedOnly->total() > 0 && count(array_filter($archivedOnly->items(), fn ($row) => $row['status'] !== 'obsolete')) === 0, 'Archived selection must override the toggle');
    foreach (['active', 'active_compact', 'obsolete', 'all', 'all_compact'] as $view) {
        $unassignedPage = app(GenerationEditor::class)->page('', '', $view, 1, perPage: 100, unassignedOnly: true);
        $check(count(array_filter($unassignedPage->items(), fn ($row) => $row['key_id'] !== null)) === 0, 'Not assigned must apply independently of status: '.$view);
    }
    $assignedExample = array_values(array_filter($allActive->items(), fn ($row) => $row['key_id'] !== null))[0];
    $component->set('tab', 'findings')->set('status', 'active')
        ->set('findingId', (string) $assignedExample['id']);
    $check($component->viewData('rows')->total() === 1, 'Active must show assigned findings by default');
    $component->call('toggleUnassigned');
    $check($component->viewData('rows')->total() === 0, 'Not assigned must exclude the assigned finding');
    $component->call('toggleUnassigned')->set('findingId', '');
    $specificFinding = $allFindings->items()[0];
    $specificCompact = app(GenerationEditor::class)->page('', (string) $specificFinding['id'], 'all_compact', 1);
    $check($specificCompact->total() === 1 && (int) $specificCompact->items()[0]['id'] === (int) $specificFinding['id'], 'Exact ID lookup must happen before compact grouping');
    $filteredCompact = app(GenerationEditor::class)->page($specificFinding['path'], '', 'all_compact', 1);
    $check($filteredCompact->total() === 1 && (int) $filteredCompact->items()[0]['id'] === (int) $specificFinding['id'], 'Source search must happen before compact grouping');
    $component->set('tab', 'findings')->set('status', 'all_compact')->assertSee('representative finding')
        ->set('status', 'all')->assertDontSee('representative finding');
    $component->set('tab', 'source-usage')->assertSee('Synthetic UI examples');
    $check(true, 'Workbench and source-usage views render with isolated fixtures');
    // Approve and publish English, main language and saved variants in one explicit selection.
    $component->call('open', $unassigned)->call('confirmReview', true)->assertHasNoErrors();
    $choices = $component->get('reviewChoices');
    $check(isset($choices['en'], $choices['de'], $choices['de-AT']), 'Saved variants must remain available after reopening without selecting textareas');
    $expected = [];
    foreach ($choices as $choice) {
        $expected[$choice['revision']] = $choice['fingerprint'];
    }
    $beforeBatchStates = $statesFor($unassigned);
    $invalid = $expected;
    $invalid[array_key_last($invalid)] = str_repeat('0', 64);
    $reject(fn () => app(GenerationEditor::class)->reviewMany($invalid, true, 7, 'Test atomic language review'), 'Review context changed');
    $check($statesFor($unassigned) === $beforeBatchStates, 'A stale language must roll back the entire review batch');
    $component->set('reviewLocales', ['en', 'de', 'de-AT'])
        ->set('reviewReason', 'Review source and targets together')->call('review');
    $check(! $component->instance()->getErrorBag()->any(), 'Batch review failed: '.json_encode($component->instance()->getErrorBag()->toArray()));
    $component->call('openPublicationSelection')->assertSet('publicationSelectionOpen', true);
    $check(count($component->get('publicationLocales')) === 3, 'All approved saved languages must be offered for publication');
    $component->set('publicationReason', 'Publish source and targets together')->call('preparePublication')
        ->assertHasNoErrors()->assertSet('publicationSelectionOpen', false)->assertSet('publication.state', 'prepared');
    $check(count($component->get('publication.changes')) === 3, 'Preview must cover each selected language');
    $component->call('publish')->assertHasNoErrors()->assertSet('publication.state', 'complete');
    $check(count(array_filter($statesFor($unassigned), fn ($state) => $state === 'published')) === 3, 'Source and targets must all be published after confirmation');
    // Shared assignment is explicit, atomic and preserves the captured source and values.
    $sharedEditor = app(GenerationEditor::class);
    $reference = $sharedEditor->inspect($shared, 'en');
    $target = (int) $reference['finding']['key_id'];
    $candidates = array_values(array_filter($sharedEditor->sharedFindings($shared, 1)->items(),
        fn ($row) => (int) $row['key_id'] !== $target && $row['literal'] === $reference['finding']['raw_literal']));
    $check(count($candidates) >= 3, 'Need different assignments for the shared assignment regression');
    $ids = array_map('intval', array_column(array_slice($candidates, 0, 2), 'id'));
    $plan = $sharedEditor->previewSharedAssignment($shared, $ids);
    $before = $sharedEditor->inspect($ids[0], 'en');
    $other = $sharedEditor->inspect($ids[1], 'en');
    $sharedEditor->assign($ids[1], $target, isset($other['finding']['link_id']) ? (int) $other['finding']['link_id'] : null, 7, 'Concurrent shared assignment');
    $reject(fn () => $sharedEditor->assignShared($plan, 7, 'Share matching meaning'), 'assignment changed');
    $check($sharedEditor->inspect($ids[0], 'en')['finding']['link_id'] === $before['finding']['link_id'], 'Stale batch must not partially assign earlier findings');
    $reject(fn () => $sharedEditor->previewSharedAssignment($shared, [$archived]), 'active findings');
    $existingBefore = $sharedEditor->inspect($shared, 'en');
    $existingPlan = $sharedEditor->previewSharedAssignment($shared, [$shared, $ids[1]]);
    $sharedEditor->assignShared($existingPlan, 7, 'Confirm existing shared assignments');
    $existingAfter = $sharedEditor->inspect($shared, 'en');
    $check($existingBefore['finding']['link_id'] === $existingAfter['finding']['link_id'], 'Selecting the current finding must preserve its existing link');
    $check($existingBefore['history'] === $existingAfter['history'], 'Unchanged assignments must not create duplicate finding history');
    $third = (int) $candidates[2]['id'];
    $revisionCount = $sharedEditor->totals()['revisions'];
    $component->call('open', $shared)->call('openShared', $shared)
        ->assertSee('Shared target key')->assertSee('Suggested key')
        ->set('sharedSelection', [(string) $shared, (string) $ids[0], (string) $third])
        ->set('sharedReason', 'Reuse the reviewed shared key.')
        ->call('reviewSharedAssignment')->assertHasNoErrors()->assertSet('sharedConfirmOpen', true)
        ->assertSee('Confirm shared key assignment')
        ->call('assignSharedFindings');
    $check(! $component->instance()->getErrorBag()->any(), 'Shared assignment failed: '.json_encode($component->instance()->getErrorBag()->toArray()));
    $component->assertSet('sharedConfirmOpen', false);
    $check((int) $sharedEditor->inspect($third, 'en')['finding']['key_id'] === $target, 'All selected findings must be assigned in the batch');
    $check($sharedEditor->totals()['revisions'] === $revisionCount, 'Shared assignment must not change translation revisions');
    $after = $sharedEditor->inspect($ids[0], 'en');
    $check((int) $after['finding']['key_id'] === $target, 'Confirmed selection must use the reference key');
    foreach (['suggested_key', 'raw_expression', 'raw_literal', 'path', 'source_line', 'source_column'] as $immutable) {
        $check($after['finding'][$immutable] === $before['finding'][$immutable], 'Shared assignment changed immutable '.$immutable);
    }
    $check((int) $sharedEditor->sharedSummaries([$shared])[$shared]['shared'] >= 5, 'Shared status must derive from active links');
    $check(count(array_filter($after['history'], fn ($event) => $event['event_type'] === 'key_assigned')) > 0, 'Shared assignment must record working history');
    $check(in_array('Publish only to isolated test files', $sharedEditor->workflowReasons('publication'), true), 'Publication reasons must be reusable from saved plans');
    $check(in_array('Approve revised German text', $sharedEditor->workflowReasons('review'), true), 'Review reasons must be reusable from review history');
    foreach (['source', 'source_closure', 'closure'] as $kind) {
        $check(is_array($sharedEditor->workflowReasons($kind)), 'Reason history query must support '.$kind);
    }
    $reject(fn () => $sharedEditor->workflowReasons('invalid'), 'Unknown reason category');
    $component->call('open', $unassigned)->set('sourceLanguageValue', 'Batch review source')
        ->set('value', 'Sammelprüfung')->set('selectedSubLanguages', [])
        ->set('reason', 'Prepare two revisions for batch review')->call('saveDraft')->assertHasNoErrors()
        ->call('submitForReview')->assertHasNoErrors();
    $remainingReview = app(GenerationEditor::class)->reviewWorklist()->page('', 1)->items();
    $check(count($remainingReview) >= 2, 'Batch fixture must contain pending revisions');
    $component->set('selectedReviewRevisions', array_map('strval', array_column($remainingReview, 'revision')))
        ->call('openReviewBatch')->call('openWorkflowReason', 'reviewBatchReason', 'Gemeinsam geprüft')
        ->call('createWorkflowReason')->call('decideReviewBatch', true)->assertHasNoErrors()
        ->assertSet('reviewBatchOpen', false)->assertSet('selectedReviewRevisions', []);
    $check(app(GenerationEditor::class)->reviewWorklist()->page('', 1)->total() === 0, 'Batch approval removes all selected revisions from Review');
    $check(app(GenerationEditor::class)->reasonLocales('review')['Gemeinsam geprüft'] === 'de', 'Batch reason language persists');
    $environment->stop($newToken);

    $check($environment->currentToken() === '', 'Test mode not cleared');
    $check($db->query('SELECT value FROM public.sentinel')->fetchColumn() === 'UNCHANGED', 'Working database changed');
    $check(file_get_contents($dir.'/real-lang/sentinel.php') === 'UNCHANGED', 'Working language files changed');
    echo json_encode(['checks' => $checks, 'state' => 'passed', 'production_connected' => false]).PHP_EOL;
} catch (Throwable $error) {
    fwrite(STDERR, $error->__toString().PHP_EOL);
    $failed = true;
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-m', 'immediate', '-w', 'stop']);
    }
    (new Process(['rm', '-rf', $dir]))->mustRun();
}

if ($failed ?? false) {
    exit(1);
}
