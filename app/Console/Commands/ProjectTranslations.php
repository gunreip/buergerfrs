<?php

// app/Console/Commands/ProjectTranslations.php

// Standard (sicher, ohne Patch-Apply):
// php artisan project:translations

// Mit automatischem Apply von diffs

// php artisan project:translations --apply-diffs

// Optional eingeschränkt:
// php artisan project:translations --locales=de,en
// php artisan project:translations --paths=resources/views,app
// php artisan project:translations --skip-export
// php artisan project:translations --skip-audits
// php artisan project:translations --apply-usage-decisions
//
// Finale Translation-Audits enthalten:
// php artisan translations:audit-lang
// php artisan translations:audit-compare
// php artisan translations:audit-lang-ballast
// php artisan translations:lang-ballast:apply
// php artisan translations:audit-duplicate-usage-literals
// php artisan translations:audit-frequent-usage-literals
// php artisan translations:usage-decisions:preview
//
// Optional mit --apply-usage-decisions:
// php artisan translations:usage-decisions:apply --write

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use App\Support\Console\ProcessProgress;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Executes a dedicated translation workflow pipeline.
 */
class ProjectTranslations extends Command
{
    private int $completedSteps = 0;

    private int $totalSteps = 0;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:translations
        {--paths= : Comma-separated scan paths for translations:generate-literal-diffs}
        {--locales= : Comma-separated locale list for translations:export-lang-files}
        {--apply-diffs : Apply generated latest.patch automatically}
        {--apply-usage-decisions : Apply ready translation usage audit decisions to source files}
        {--skip-export : Skip the read-only Legacy export comparison}
        {--skip-audits : Skip final translations:audit-lang and translations:audit-compare}';

    /**
     * The console command description.
     */
    protected $description = 'Run translation workflow commands in sequence (audit, sync, diff, optional apply, export, final language and usage audits).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $applyDiffs = (bool) $this->option('apply-diffs');
        $applyUsageDecisions = (bool) $this->option('apply-usage-decisions');
        $skipExport = (bool) $this->option('skip-export');
        $skipAudits = (bool) $this->option('skip-audits');
        $pathsOption = trim((string) $this->option('paths'));
        $localesOption = trim((string) $this->option('locales'));

        $this->completedSteps = 0;
        $this->totalSteps = 4 + ($skipExport ? 0 : 2)
            + ($skipAudits ? 0 : 7 + ($applyUsageDecisions ? 6 : 0))
            + ($applyDiffs ? 4 : 0);

        try {
            $this->runArtisanStep('Lang-Locale-Verzeichnisse sicherstellen', 'translations:ensure-lang-directories');

            $this->runArtisanStep('Translation-Code-Audit schreiben', 'translations:audit-code');
            $this->runArtisanStep('Translation-Audits in Datenbank synchronisieren', 'translations:sync-audits');

            $diffOptions = [];
            if ($pathsOption !== '') {
                $diffOptions['--paths'] = $pathsOption;
            }

            $this->runArtisanStep(
                'Literal-vs-Key-DIFFs erzeugen (strict)',
                'translations:generate-literal-diffs',
                $diffOptions,
            );

            $diffApplied = false;

            if ($applyDiffs) {
                $diffApplied = $this->applyLatestDiffPatch();

                if ($diffApplied) {
                    $this->runArtisanStep('Translation-Code-Audit nach DIFF-Apply schreiben', 'translations:audit-code');
                    $this->runArtisanStep('Translation-Audits nach DIFF-Apply synchronisieren', 'translations:sync-audits');
                }
            } else {
                $this->warn('ℹ️ DIFF-Apply übersprungen. Nutze --apply-diffs für automatische Anwendung von latest.patch.');
            }

            if (! $skipExport) {
                $exportOptions = [];
                if ($localesOption !== '') {
                    $exportOptions['--locales'] = $localesOption;
                }

                $this->runArtisanStep('Sub-Language-Redundanz prüfen', 'translations:audit-sub-language-redundancy', $exportOptions);
                $this->runArtisanStep('Legacy-Translation-Export vergleichen (ohne Schreiben)', 'translations:export-lang-files', $exportOptions + ['--dry-run' => true]);
            } else {
                $this->warn('ℹ️ Export übersprungen (--skip-export).');
            }

            if (! $skipAudits) {
                $this->runArtisanStep('Translation-Lang-Audit schreiben', 'translations:audit-lang');
                $this->runArtisanStep('Translation-Compare-Audit schreiben', 'translations:audit-compare');
                $this->runArtisanStep('Translation-Lang-Ballast-Audit schreiben', 'translations:audit-lang-ballast');
                $this->runArtisanStep(
                    'Translation-Lang-Ballast-Apply-Preview schreiben',
                    'translations:lang-ballast:apply',
                );
                $this->runArtisanStep(
                    'Translation-Duplicate-Usage-Literals-Audit schreiben',
                    'translations:audit-duplicate-usage-literals',
                );
                $this->runArtisanStep(
                    'Translation-Frequent-Usage-Literals-Audit schreiben',
                    'translations:audit-frequent-usage-literals',
                );

                $this->runArtisanStep(
                    'Translation-Usage-Decision-Preview schreiben',
                    'translations:usage-decisions:preview',
                );

                if ($applyUsageDecisions) {
                    $this->runArtisanStep(
                        'Translation-Usage-Decisions anwenden',
                        'translations:usage-decisions:apply',
                        ['--write' => true],
                    );

                    $this->runArtisanStep('Translation-Code-Audit nach Usage-Decision-Apply schreiben', 'translations:audit-code');
                    $this->runArtisanStep('Translation-Audits nach Usage-Decision-Apply synchronisieren', 'translations:sync-audits');

                    $this->runArtisanStep(
                        'Translation-Duplicate-Usage-Literals-Audit nach Usage-Decision-Apply schreiben',
                        'translations:audit-duplicate-usage-literals',
                    );
                    $this->runArtisanStep(
                        'Translation-Frequent-Usage-Literals-Audit nach Usage-Decision-Apply schreiben',
                        'translations:audit-frequent-usage-literals',
                    );
                    $this->runArtisanStep(
                        'Translation-Usage-Decision-Preview nach Usage-Decision-Apply schreiben',
                        'translations:usage-decisions:preview',
                    );
                } else {
                    $this->warn('ℹ️ Usage-Decision-Apply übersprungen. Nutze --apply-usage-decisions für automatische Anwendung geprüfter Usage-Decisions.');
                }
            } else {
                $this->warn('ℹ️ Finale Audits übersprungen (--skip-audits).');
            }

            $this->info('✅ Translation-Workflow abgeschlossen!');

            $this->logRunActivity('project.translations.completed', 'Project translations workflow completed.', [
                'options' => [
                    'paths' => $pathsOption,
                    'locales' => $localesOption,
                    'apply_diffs' => $applyDiffs,
                    'apply_usage_decisions' => $applyUsageDecisions,
                    'skip_export' => $skipExport,
                    'skip_audits' => $skipAudits,
                ],
                'summary' => [
                    'diff_applied' => $diffApplied,
                ],
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('❌ Translation-Workflow fehlgeschlagen: '.trim($exception->getMessage()));

            $this->logRunActivity('project.translations.failed', 'Project translations workflow failed.', [
                'error' => trim($exception->getMessage()),
                'options' => [
                    'paths' => $pathsOption,
                    'locales' => $localesOption,
                    'apply_diffs' => $applyDiffs,
                    'apply_usage_decisions' => $applyUsageDecisions,
                    'skip_export' => $skipExport,
                    'skip_audits' => $skipAudits,
                ],
            ]);

            return self::FAILURE;
        }
    }

    /**
     * @param  array<string, string|bool>  $arguments
     */
    private function runArtisanStep(string $description, string $command, array $arguments = []): void
    {
        $tokens = [PHP_BINARY, base_path('artisan'), $command, '--no-interaction'];
        $tokens[] = $this->output->isDecorated() ? '--ansi' : '--no-ansi';
        foreach ($arguments as $name => $value) {
            if ($value === true) {
                $tokens[] = $name;
            } elseif ($value !== false) {
                $tokens[] = $name.'='.$value;
            }
        }

        $this->runProcess($tokens, $description.' ('.$command.')');
    }

    private function applyLatestDiffPatch(): bool
    {
        $patchPath = base_path('storage/audits/translations/diffs/latest.patch');

        if (! File::exists($patchPath) || trim((string) File::get($patchPath)) === '') {
            $this->totalSteps -= 4;
            $this->warn('ℹ️ Kein latest.patch gefunden oder Patch ist leer. DIFF-Apply übersprungen.');

            return false;
        }

        $this->runProcess(['git', 'apply', '--check', $patchPath], 'DIFF patch prüfen (git apply --check)');

        $this->runProcess(['git', 'apply', $patchPath], 'DIFF patch anwenden (git apply)');

        return true;
    }

    /**
     * @param  array<int, string>  $command
     */
    private function runProcess(array $command, string $description): void
    {
        $process = $this->createProcess($command);
        $process->setTimeout(null);

        try {
            (new ProcessProgress($this->output, unit: 'steps'))->run(
                $process, $description, $this->completedSteps, $this->totalSteps,
            );
        } finally {
            // Keep command diagnostics, including failure output, below the spinner.
            $this->output->write($process->getOutput());
            $this->output->write($process->getErrorOutput());
        }

        if (! $process->isSuccessful()) {
            throw new RuntimeException('Step failed: '.$description.' (exit code '.$process->getExitCode().')');
        }

        $this->completedSteps++;
    }

    /** @param list<string> $command */
    protected function createProcess(array $command): Process
    {
        return new Process($command, base_path());
    }

    protected function logRunActivity(string $event, string $description, array $properties = []): void
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
