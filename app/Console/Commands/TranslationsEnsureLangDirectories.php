<?php

// app/Console/Commands/TranslationsEnsureLangDirectories.php

namespace App\Console\Commands;

use App\Models\TranslationLanguage;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Ensures target lang/{locale} directories exist for translation workflows.
 */
class TranslationsEnsureLangDirectories extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:ensure-lang-directories
        {--locales= : Comma-separated locale list override (e.g. de,en,fr)}
        {--dry-run : Show what would be created without writing to disk}';

    /**
     * The console command description.
     */
    protected $description = 'Ensure lang/{locale} directories exist for translation-enabled languages.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $locales = $this->targetLocales();
        $dryRun = (bool) $this->option('dry-run');

        if ($locales === []) {
            $this->warn('No target locales found. Nothing to do.');
            $this->logNoTargetLocalesActivity($dryRun);

            return self::SUCCESS;
        }

        if (! $dryRun) {
            File::ensureDirectoryExists(lang_path());
        }

        $rows = [];
        $created = 0;
        $existing = 0;

        foreach ($locales as $locale) {
            $path = lang_path($locale);
            $exists = File::isDirectory($path);

            if ($exists) {
                $existing++;
                $rows[] = [$locale, 'existing', $this->relativePath($path)];

                continue;
            }

            if (! $dryRun) {
                File::ensureDirectoryExists($path);
                $this->logDirectoryCreatedActivity($locale, $path);
            }

            $created++;
            $rows[] = [$locale, $dryRun ? 'would_create' : 'created', $this->relativePath($path)];
        }

        $this->components->info('Language directories check finished.');

        $this->table(['Locale', 'Status', 'Directory'], $rows);

        $this->line('');
        $this->line('Created: '.$created);
        $this->line('Existing: '.$existing);

        if ($dryRun) {
            $this->warn('Dry run only: no directories were created.');
        }

        $this->logRunCompletedActivity(
            locales: $locales,
            created: $created,
            existing: $existing,
            dryRun: $dryRun,
        );

        return self::SUCCESS;
    }

    /**
     * Resolve target locales either from --locales or translation language settings.
     *
     * @return array<int, string>
     */
    private function targetLocales(): array
    {
        $localesOption = trim((string) $this->option('locales'));

        if ($localesOption !== '') {
            return collect(explode(',', $localesOption))
                ->map(static fn (string $locale): string => self::normalizeLocale($locale))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        $fromSettings = TranslationLanguage::query()
            ->where('is_enabled_for_translation', true)
            ->pluck('locale')
            ->map(static fn (string $locale): string => self::normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $variantLocales = DB::table('translation_values')
            ->whereNotNull('locale')
            ->where('locale', '<>', '')
            ->whereRaw('locale like ?', ['%-%'])
            ->distinct()
            ->pluck('locale')
            ->map(static fn (string $locale): string => self::normalizeLocale($locale))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $locales = array_values(array_unique(array_merge($fromSettings, $variantLocales)));

        if ($locales !== []) {
            return $locales;
        }

        return ['de', 'en'];
    }

    private static function normalizeLocale(string $locale): string
    {
        $normalized = strtolower(trim($locale));

        if ($normalized === '') {
            return '';
        }

        return str_replace('_', '-', $normalized);
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }

    private function logDirectoryCreatedActivity(string $locale, string $path): void
    {
        try {
            activity('translations')
                ->event('translations.lang.directory_created')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'locale' => $locale,
                    'path' => $this->relativePath($path),
                    'absolute_path' => $path,
                    'options' => [
                        'locales' => (string) $this->option('locales'),
                        'dry_run' => (bool) $this->option('dry-run'),
                    ],
                ]))
                ->log('Translation language directory created');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for locale "'.$locale.'": '.$exception->getMessage());
        }
    }

    private function logNoTargetLocalesActivity(bool $dryRun): void
    {
        try {
            activity('translations')
                ->event('translations.lang.directories.no_target_locales')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'options' => [
                        'locales' => (string) $this->option('locales'),
                        'dry_run' => $dryRun,
                    ],
                ]))
                ->log('No target locales found for translation directory ensure run');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for no-target-locales event: '.$exception->getMessage());
        }
    }

    /**
     * @param  array<int, string>  $locales
     */
    private function logRunCompletedActivity(array $locales, int $created, int $existing, bool $dryRun): void
    {
        try {
            activity('translations')
                ->event('translations.lang.directories.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => [
                        'locales' => $locales,
                        'created' => $created,
                        'existing' => $existing,
                        'dry_run' => $dryRun,
                    ],
                ]))
                ->log('Translation language directories ensure run completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: '.$exception->getMessage());
        }
    }
}
