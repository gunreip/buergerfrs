<?php

// app/Console/Commands/ProjectTestSafe.php

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Runs tests with guaranteed database snapshot backup and restore safeguards.
 */
class ProjectTestSafe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:test-safe
        {--with-lint : Führt vor dem Testlauf composer lint:check aus}
        {--filter= : Übergibt --filter an php artisan test}
        {--testsuite= : Übergibt --testsuite an php artisan test}
        {--parallel : Führt Tests mit --parallel aus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sichert die DB vor Tests, führt Tests aus und stellt die DB danach garantiert wieder her.';

    private ?string $snapshotFile = null;

    private bool $restoreFinished = false;

    private bool $restoreStarted = false;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = config('database.connections.'.config('database.default'));
        $driver = (string) ($connection['driver'] ?? '');

        if ($driver !== 'pgsql') {
            $this->error('❌ project:test-safe unterstützt aktuell nur PostgreSQL als aktive DB-Verbindung.');
            $this->warn('ℹ️ Aktiver Driver: '.$driver);

            $this->logRunActivity('project.test_safe.failed', 'Test-safe run failed due to unsupported database driver.', [
                'driver' => $driver,
            ]);

            return self::FAILURE;
        }

        $this->registerRestoreGuards($connection);

        if ($this->option('with-lint')) {
            $this->info('➤ Lint-Check ausführen (composer lint:check)');

            $lintExit = $this->runProcess(['composer', 'lint:check']);

            if ($lintExit !== self::SUCCESS) {
                $this->error('❌ Lint-Check fehlgeschlagen. Testlauf wird nicht gestartet.');

                $this->logRunActivity('project.test_safe.failed', 'Test-safe run aborted because lint check failed.', [
                    'with_lint' => true,
                    'lint_exit_code' => $lintExit,
                ]);

                return self::FAILURE;
            }
        }

        $this->info('➤ Datenbank-Snapshot vor Testlauf erstellen');
        $this->snapshotFile = $this->createDatabaseSnapshot($connection);

        $testExit = self::FAILURE;

        try {
            $this->info('➤ Tests ausführen');

            $testExit = $this->runProcess($this->buildTestCommand());
        } catch (Throwable $exception) {
            $this->error('❌ Unerwarteter Fehler während des Testlaufs: '.trim($exception->getMessage()));

            $testExit = self::FAILURE;
        } finally {
            $this->restoreDatabaseSnapshot($connection, 'finally');
        }

        if ($testExit === self::SUCCESS) {
            if ($this->restoreFinished) {
                $this->info('✅ Testlauf abgeschlossen und Datenbank wurde wiederhergestellt.');
            } else {
                $this->error('❌ Testlauf abgeschlossen, aber DB-Restore war nicht erfolgreich.');
            }
        } else {
            $this->warn('⚠️ Testlauf fehlgeschlagen, Datenbank wurde trotzdem wiederhergestellt.');
        }

        if (! $this->restoreFinished) {
            $this->logRunActivity('project.test_safe.failed', 'Test-safe run completed with failed restore.', [
                'test_exit_code' => $testExit,
                'restore_finished' => $this->restoreFinished,
                'restore_started' => $this->restoreStarted,
            ]);

            return self::FAILURE;
        }

        $this->logRunActivity('project.test_safe.completed', 'Test-safe run completed.', [
            'test_exit_code' => $testExit,
            'restore_finished' => $this->restoreFinished,
            'restore_started' => $this->restoreStarted,
            'options' => [
                'with_lint' => (bool) $this->option('with-lint'),
                'filter' => (string) ($this->option('filter') ?? ''),
                'testsuite' => (string) ($this->option('testsuite') ?? ''),
                'parallel' => (bool) $this->option('parallel'),
            ],
        ]);

        return $testExit;
    }

    /**
     * Register shutdown/signal handlers so restore also runs on abort paths.
     *
     * @param  array<string, mixed>  $connection
     */
    private function registerRestoreGuards(array $connection): void
    {
        register_shutdown_function(function () use ($connection): void {
            $this->restoreDatabaseSnapshot($connection, 'shutdown');
        });

        if (! function_exists('pcntl_async_signals') || ! function_exists('pcntl_signal')) {
            return;
        }

        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () use ($connection): void {
            $this->warn('⚠️ SIGINT empfangen. Starte DB-Restore ...');
            $this->restoreDatabaseSnapshot($connection, 'signal:SIGINT');
            exit(130);
        });

        pcntl_signal(SIGTERM, function () use ($connection): void {
            $this->warn('⚠️ SIGTERM empfangen. Starte DB-Restore ...');
            $this->restoreDatabaseSnapshot($connection, 'signal:SIGTERM');
            exit(143);
        });
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function createDatabaseSnapshot(array $connection): string
    {
        $this->assertCommandExists('pg_dump');
        $this->assertCommandExists('psql');
        $this->assertCommandExists('pg_restore');

        $snapshotDir = storage_path('app/private/dev/test-db-snapshots');
        File::ensureDirectoryExists($snapshotDir);

        $fileName = sprintf(
            '%s_%s.dump',
            preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) ($connection['database'] ?? 'database')),
            now()->format('Ymd_His')
        );

        $snapshotFile = $snapshotDir.DIRECTORY_SEPARATOR.$fileName;

        $exitCode = $this->runProcess(
            [
                'pg_dump',
                '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
                '--port='.(string) ($connection['port'] ?? '5432'),
                '--username='.(string) ($connection['username'] ?? ''),
                '--dbname='.(string) ($connection['database'] ?? ''),
                '--format=custom',
                '--no-owner',
                '--no-privileges',
                '--file='.$snapshotFile,
            ],
            [
                'PGPASSWORD' => (string) ($connection['password'] ?? ''),
            ]
        );

        if ($exitCode !== self::SUCCESS || ! is_file($snapshotFile)) {
            throw new RuntimeException('Snapshot-Erstellung fehlgeschlagen.');
        }

        $this->line('Snapshot: '.$snapshotFile);

        return $snapshotFile;
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function restoreDatabaseSnapshot(array $connection, string $source): void
    {
        if ($this->restoreFinished || $this->restoreStarted) {
            return;
        }

        if (! $this->snapshotFile || ! is_file($this->snapshotFile)) {
            return;
        }

        $this->restoreStarted = true;
        $this->line('➤ DB-Restore starten ('.$source.')');

        try {
            $resetExitCode = $this->runProcess(
                [
                    'psql',
                    '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
                    '--port='.(string) ($connection['port'] ?? '5432'),
                    '--username='.(string) ($connection['username'] ?? ''),
                    '--dbname='.(string) ($connection['database'] ?? ''),
                    '--set=ON_ERROR_STOP=1',
                    '--command=DROP SCHEMA IF EXISTS public CASCADE; CREATE SCHEMA public;',
                ],
                [
                    'PGPASSWORD' => (string) ($connection['password'] ?? ''),
                ]
            );

            if ($resetExitCode !== self::SUCCESS) {
                throw new RuntimeException('Schema-Reset vor Restore fehlgeschlagen (Exit-Code: '.$resetExitCode.').');
            }

            $exitCode = $this->runProcess(
                [
                    'pg_restore',
                    '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
                    '--port='.(string) ($connection['port'] ?? '5432'),
                    '--username='.(string) ($connection['username'] ?? ''),
                    '--dbname='.(string) ($connection['database'] ?? ''),
                    '--no-owner',
                    '--no-privileges',
                    '--exit-on-error',
                    $this->snapshotFile,
                ],
                [
                    'PGPASSWORD' => (string) ($connection['password'] ?? ''),
                ]
            );

            if ($exitCode !== self::SUCCESS) {
                throw new RuntimeException('Restore fehlgeschlagen (Exit-Code: '.$exitCode.').');
            }

            $this->restoreFinished = true;
            $this->info('✅ DB-Restore erfolgreich.');
        } catch (Throwable $exception) {
            $this->error('❌ DB-Restore fehlgeschlagen: '.trim($exception->getMessage()));
        }
    }

    /**
     * @return array<int, string>
     */
    private function buildTestCommand(): array
    {
        $command = ['php', 'artisan', 'test'];

        if ($this->option('parallel')) {
            $command[] = '--parallel';
        }

        $filter = (string) ($this->option('filter') ?? '');

        if ($filter !== '') {
            $command[] = '--filter='.$filter;
        }

        $testsuite = (string) ($this->option('testsuite') ?? '');

        if ($testsuite !== '') {
            $command[] = '--testsuite='.$testsuite;
        }

        return $command;
    }

    private function assertCommandExists(string $command): void
    {
        $exitCode = $this->runProcess(['which', $command], [], false);

        if ($exitCode !== self::SUCCESS) {
            throw new RuntimeException('Benötigtes Kommando nicht gefunden: '.$command);
        }
    }

    /**
     * @param  array<int, string>  $command
     * @param  array<string, string>  $extraEnv
     */
    private function runProcess(array $command, array $extraEnv = [], bool $streamOutput = true): int
    {
        $process = new Process($command, base_path(), array_merge($_ENV, $extraEnv));
        $process->setTimeout(null);

        if ($streamOutput) {
            $process->run(function (string $type, string $buffer): void {
                $this->output->write($buffer);
            });

            return $process->getExitCode() ?? self::FAILURE;
        }

        $process->run();

        return $process->getExitCode() ?? self::FAILURE;
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            activity('project')
                ->event($event)
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
