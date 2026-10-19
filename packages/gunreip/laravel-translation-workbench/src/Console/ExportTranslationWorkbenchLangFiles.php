<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php

// php artisan translation-workbench:export-lang-files
// php artisan translation-workbench:export-lang-files --locale=de --namespace=ui
// php artisan translation-workbench:export-lang-files --locale=de --key=ui.title.overview
// Guarded writes require --actor, --finding, one --key, --locale and --fingerprint.
// Create a guarded preview with the same scope but without --write/--fingerprint.
// php artisan translation-workbench:export-lang-files --suppress-dry-run-warning

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('translation-workbench:export-lang-files
    {--locale=* : Locale(s) to export. Defaults to all locales with active Workbench lang values.}
    {--namespace=* : Namespace file(s) to export, for example ui or admin. Defaults to all namespaces.}
    {--key=* : Exact translation key(s) to export or prune. Repeat for multiple keys; omitted means all keys.}
    {--dynamic-options : Export reviewed option values for explicit --key owners; does not prune.}
    {--actor= : User ID responsible for guarded publication; must have Admin or Super-Admin role.}
    {--finding= : Finding ID for a guarded single-key publication preview or write.}
    {--fingerprint= : Exact fingerprint of the guarded preview to publish.}
    {--resume= : Resume a journaled publication run as --actor; cannot be combined with export scope options.}
    {--write : Publish a guarded preview. Requires actor, finding, one key, explicit locales and fingerprint.}
    {--suppress-dry-run-warning : Suppress the dry-run warning when the command is used as an orchestrated report refresh step.}')]
#[Description('Export reviewed Translation Workbench lang values into Laravel lang files.')]
class ExportTranslationWorkbenchLangFiles extends Command
{
    public function handle(TranslationWorkbenchLangFileExporter $exporter): int
    {
        $write = (bool) $this->option('write');
        if ($write || $this->option('actor') !== null || $this->option('finding') !== null ||
            $this->option('fingerprint') !== null || $this->option('resume') !== null) {
            return $this->guardedPublication();
        }
        $summary = $exporter->export(
            locales: (array) $this->option('locale'),
            namespaces: (array) $this->option('namespace'),
            write: false,
            keys: (array) $this->option('key'),
            dynamicOptions: (bool) $this->option('dynamic-options'),
        );
        $reportPath = $this->writeReport($summary);

        $hasConflicts = (int) $summary['values_conflicted'] > 0;
        if ($hasConflicts) {
            $this->components->error('Translation Workbench lang file export blocked by conflicts.');
        } else {
            $this->components->info($write
                ? 'Translation Workbench lang file export finished.'
                : 'Translation Workbench lang file export dry-run finished.');
        }
        $this->line('Locales: ' . implode(', ', $summary['locales'] ?: ['-']));
        $this->line('Namespaces: ' . implode(', ', $summary['namespaces'] ?: ['-']));
        $this->line('Mode: ' . ($summary['dynamic_options'] ? 'dynamic options' : 'scalar values'));
        $this->line('Keys: ' . implode(', ', $summary['keys'] ?: ['all']));
        $this->line('Files: ' . number_format((int) $summary['files']));
        $this->line('Exportable values: ' . number_format((int) $summary['values_exportable']));
        $this->line('New values: ' . number_format((int) $summary['values_new']));
        $this->line('Changed values: ' . number_format((int) $summary['values_changed']));
        $this->line('Unchanged values: ' . number_format((int) $summary['values_unchanged']));
        $this->line('Pruned values: ' . number_format((int) $summary['values_pruned']));
        $this->line('Conflicts: ' . number_format((int) $summary['values_conflicted']));
        $this->line('Active scope locales: ' . implode(', ', $summary['active_scope']['locales'] ?: ['-']));
        $this->line('Active scope exportable values: ' . number_format((int) $summary['active_scope']['values_exportable']));
        $this->line('Active scope source values: ' . number_format((int) $summary['active_scope']['source_values']));
        $this->line('Active scope target main values: ' . number_format((int) $summary['active_scope']['target_main_values']));
        $this->line('Active scope target main missing: ' . number_format((int) $summary['active_scope']['target_main_missing']));
        $this->line('Active scope target main extras: ' . number_format((int) $summary['active_scope']['target_main_extra']));
        $this->line('Active scope source/target balanced: ' . ($summary['active_scope']['target_main_balanced'] ? 'yes' : 'no'));
        $this->line('Active scope target sub values: ' . number_format((int) $summary['active_scope']['target_sub_values']));
        $this->line('Files written: ' . number_format((int) $summary['files_written']));
        $this->line('Timeline events created: ' . number_format((int) $summary['timeline_events_created']));
        $this->line('JSON report: ' . $reportPath);

        if (! $hasConflicts && ! $write && ! (bool) $this->option('suppress-dry-run-warning')) {
            $this->warn('Dry run only: no lang files were written. Publication requires a guarded preview with --actor, --finding, one --key and explicit --locale; then --write with its --fingerprint.');
        }

        if ($hasConflicts) {
            $this->warn('No lang files were written. Resolve the conflicts before exporting.');
            foreach ($summary['plans'] as $plan) {
                foreach ($plan['conflicts'] as $conflict) {
                    $this->line($plan['path'].' · '.$conflict['translation_key'].' · '.$conflict['reason']);
                }
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function guardedPublication(): int
    {
        $guard = \Illuminate\Support\Facades\Auth::guard();
        $previous = $guard->user();
        try {
            $actor = (string) $this->option('actor');
            if (! ctype_digit($actor) || (int) $actor < 1) {
                throw new \InvalidArgumentException('Guarded publication requires --actor with an Admin or Super-Admin user ID. No lang files were written.');
            }
            $user = $guard->getProvider()->retrieveById((int) $actor);
            if ($user === null) {
                throw new \InvalidArgumentException('Publication actor does not exist.');
            }
            $guard->setUser($user);
            app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess::class)->authorize();
            $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
            if ($this->option('resume') !== null) {
                if ($this->option('write') || $this->option('finding') !== null || $this->option('fingerprint') !== null ||
                    $this->option('key') || $this->option('locale') || $this->option('namespace') || $this->option('dynamic-options')) {
                    throw new \InvalidArgumentException('--resume accepts only --actor and a run ID, without publication scope options.');
                }
                $result = $runner->resume((string) $this->option('resume'));
                $this->line('Publication run: '.$result['id'].' · '.$result['state']);

                return self::SUCCESS;
            }
            $keys = (array) $this->option('key');
            $locales = (array) $this->option('locale');
            $finding = (string) $this->option('finding');
            if (count($keys) !== 1 || trim($keys[0]) === '' || $locales === [] ||
                count(array_filter($locales, fn ($locale) => trim($locale) === '')) > 0 ||
                ! ctype_digit($finding) || (int) $finding < 1 || $this->option('namespace')) {
                throw new \InvalidArgumentException('Guarded publication requires one --key, --finding and explicit --locale; --namespace is not supported.');
            }
            $key = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey::where('translation_key', $keys[0])->sole();
            $kind = $this->option('dynamic-options') ? 'option' : 'scalar';
            if ($this->option('write')) {
                $fingerprint = (string) $this->option('fingerprint');
                if (! preg_match('/^[a-f0-9]{64}$/D', $fingerprint)) {
                    throw new \InvalidArgumentException('--write requires the --fingerprint from a guarded preview.');
                }
                $result = $runner->publish($kind, (int) $key->id, (int) $finding, $locales, $fingerprint);
                $this->line('Publication run: '.$result['id'].' · '.$result['state']);
            } else {
                if ($this->option('fingerprint') !== null) {
                    throw new \InvalidArgumentException('--fingerprint is only used with --write.');
                }
                $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)
                    ->preview($kind, (int) $key->id, (int) $finding, $locales);
                $path = $this->writeReport($report);
                $this->line('Preview fingerprint: '.$report['fingerprint']);
                $this->line('Changes without current approval: '.$report['changes_without_current_approval']);
                $this->line('Unreviewed deletions: '.$report['pruning_requires_separate_review']);
                $this->line('Conflicts: '.$report['values_conflicted']);
                $this->line('Excluded options: '.$report['values_excluded_by_legacy_review_filter']);
                $this->line('JSON report: '.$path);
                $this->warn('Preview only. No lang files were written.');
            }

            return self::SUCCESS;
        } catch (\Illuminate\Auth\Access\AuthorizationException $exception) {
            $this->components->error('The publication actor requires Admin or Super-Admin permission.');

            return self::FAILURE;
        } catch (\Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            $previous === null ? $guard->forgetUser() : $guard->setUser($previous);
        }
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function writeReport(array $summary): string
    {
        $path = storage_path('translation-workbench/' . Str::of((string) $this->getName())->replace(':', '-') . '.json');
        $directory = dirname($path);

        File::ensureDirectoryExists($directory);
        @chmod($directory, 0777);

        if (File::exists($path) && ! is_writable($path)) {
            @unlink($path);
        }

        File::put($path, json_encode([
            'command' => $this->getName(),
            'generated_at' => now()->toISOString(),
            'write' => (bool) $this->option('write'),
            'dry_run' => ! (bool) $this->option('write'),
            'summary' => collect($summary)->except('plans')->all(),
            'plans' => $summary['plans'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        @chmod($path, 0666);

        return $path;
    }
}
