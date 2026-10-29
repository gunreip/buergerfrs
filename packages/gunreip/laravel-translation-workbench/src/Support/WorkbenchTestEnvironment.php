<?php

namespace Gunreip\TranslationWorkbench\Support;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementWriter;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;

/** Request-scoped selection. Never changes Laravel's default database or application paths. */
class WorkbenchTestEnvironment
{
    private const SESSION = 'translation-workbench.ui-test';

    private ?array $active = null;

    public function currentToken(): string
    {
        return (string) session(self::SESSION.'.token', '');
    }

    public function start(): string
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('UI test datasets are only available in local and testing environments.');
        }
        $previous = session(self::SESSION);
        $token = bin2hex(random_bytes(12));
        $database = 'tw_ui_test_'.$token;
        $owner = (int) auth()->id();
        if ($owner < 1) {
            throw new RuntimeException('An authenticated editor is required.');
        }
        $root = storage_path('app/private/workbench-ui-tests/'.$token);
        $admin = DB::connection()->getPdo();
        if ($admin->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
            throw new RuntimeException('UI test datasets require PostgreSQL.');
        }
        $manifest = compact('token', 'database', 'owner', 'root');
        $admin->exec('CREATE DATABASE "'.$database.'"');
        $admin->exec('COMMENT ON DATABASE "'.$database.'" IS '.$admin->quote($this->marker($manifest)));
        try {
            File::ensureDirectoryExists($root.'/lang', 0770, true);
            $db = $this->connect($manifest);
            (new WorkingGenerationSchema)->install($db, $database, 'current_generation_v2', (string) Str::uuid());
            $db->exec('SET search_path TO current_generation_v2,pg_catalog');
            (new WorkbenchTestFixtures)->seed($db, $root, $owner);
            $db = null;
            session([self::SESSION => $manifest]);
            $this->activate($token);
        } catch (\Throwable $error) {
            session([self::SESSION => $previous]);
            $this->forgetServices();
            $this->remove($manifest);
            throw $error;
        }
        if ($previous !== null) {
            $this->remove($previous);
        }

        return $token;
    }

    public function activate(string $token): void
    {
        $manifest = session(self::SESSION);
        if ($token === '') {
            abort_if($manifest !== null, 409, 'The workbench mode changed. Reload this page.');

            return;
        }
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        abort_unless(is_array($manifest) && hash_equals($manifest['token'], $token)
            && $manifest['owner'] === (int) auth()->id(), 409, 'This UI test run has expired. Reload the workbench.');
        $this->validate($manifest);
        $db = $this->connect($manifest);
        $generation = $db->query('SELECT generation FROM current_generation_v2.workbench_generation')->fetchColumn();
        (new WorkingGenerationSchema)->verify($db, $manifest['database'], 'current_generation_v2', $generation);
        $this->active = $manifest;
        $root = $manifest['root'];
        app()->instance(GenerationEditor::class, new GenerationEditor($db, $root, $root.'/lang'));
        app()->instance(GenerationPublisher::class, new GenerationPublisher($db, $root.'/lang'));
        app()->instance(SourceReplacementWriter::class, new SourceReplacementWriter($db, $root, $root.'/lang', app(TranslationWorkbenchScanner::class)));
        app()->instance(GenerationCaseDecisions::class, new GenerationCaseDecisions($root, $root.'/source-cases.json', $db));
    }

    public function stop(string $token): void
    {
        $this->activate($token);
        $previous = session(self::SESSION);
        if ($previous !== null) {
            $this->remove($previous);
        }
        session()->forget(self::SESSION);
        $this->forgetServices();
    }

    public function root(): string
    {
        return $this->active['root'] ?? base_path();
    }

    public function runtimeOptions(): ?array
    {
        return $this->active === null ? null : json_decode(file_get_contents($this->active['root'].'/runtime-options.json'), true, flags: JSON_THROW_ON_ERROR);
    }

    private function connect(array $manifest): PDO
    {
        $this->validate($manifest);
        $config = DB::connection()->getConfig();
        unset($config['url']);
        $config['database'] = $manifest['database'];
        $config['search_path'] = 'current_generation_v2,pg_catalog';
        $db = (new PostgresConnector)->connect($config);
        if ($db->query('SELECT current_database()')->fetchColumn() !== $manifest['database']) {
            throw new RuntimeException('UI test database isolation failed.');
        }

        return $db;
    }

    private function validate(array $manifest): void
    {
        if (! preg_match('/^[a-f0-9]{24}$/D', $manifest['token'] ?? '')
            || $manifest['database'] !== 'tw_ui_test_'.$manifest['token']
            || $manifest['root'] !== storage_path('app/private/workbench-ui-tests/'.$manifest['token'])
            || is_link($manifest['root'])) {
            throw new RuntimeException('Invalid UI test environment identity.');
        }
    }

    private function marker(array $manifest): string
    {
        return 'translation-workbench-ui-test:'.$manifest['owner'].':'.$manifest['token'];
    }

    private function remove(array $manifest): void
    {
        $this->validate($manifest);
        $db = DB::connection()->getPdo();
        $q = $db->prepare("SELECT shobj_description(oid, 'pg_database') FROM pg_database WHERE datname=?");
        $q->execute([$manifest['database']]);
        if ($q->fetchColumn() !== $this->marker($manifest)) {
            throw new RuntimeException('Refusing to remove an unrecognized UI test database.');
        }
        $db->exec('DROP DATABASE "'.$manifest['database'].'" WITH (FORCE)');
        File::deleteDirectory($manifest['root']);
    }

    private function forgetServices(): void
    {
        $this->active = null;
        foreach ([GenerationEditor::class, GenerationPublisher::class, SourceReplacementWriter::class, GenerationCaseDecisions::class] as $service) {
            app()->forgetInstance($service);
        }
    }
}
