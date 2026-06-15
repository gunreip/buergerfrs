<?php

// app/Console/Commands/ProjectDbBackup.php

// php artisan project:db-backup
// php artisan schedule:run
// php artisan schedule:list

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;
use Throwable;

/**
 * Creates database backups and applies project-specific retention rules.
 */
class ProjectDbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:db-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erstellt ein DB-Backup und bereinigt alte Backups nach Projektregeln.';

    private const MIN_BACKUPS_TO_KEEP = 10;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $beforePathsByDisk = $this->collectBackupPathsByDisk();

        $this->info('➤ DB-Backup starten (Spatie backup:run --only-db)');

        $result = $this->call('backup:run', [
            '--only-db' => true,
        ]);

        if ($result !== self::SUCCESS) {
            $this->error('❌ DB-Backup fehlgeschlagen. Bereinigung wird übersprungen.');

            $this->logActivity(
                event: 'backup.db_backup_failed',
                description: 'Database backup run failed',
                properties: [
                    'result_code' => $result,
                ],
            );

            return self::FAILURE;
        }

        $createdCount = $this->logCreatedBackups($beforePathsByDisk);

        $this->info('➤ Backup-Bereinigung nach Aufbewahrungslogik starten');

        $deletedCount = 0;
        $keptCount = 0;

        $destinations = BackupDestinationFactory::createFromArray(Config::fromArray(config('backup')));

        foreach ($destinations as $destination) {
            if (! $destination->isReachable()) {
                $this->warn('⚠️ Backup-Disk nicht erreichbar: ' . $destination->diskName());

                continue;
            }

            /** @var Collection<int, Backup> $backups */
            $backups = $destination->fresh()->backups()
                ->filter(fn(Backup $backup): bool => $backup->exists())
                ->sortByDesc(fn(Backup $backup): int => $backup->date()->timestamp)
                ->values();

            if ($backups->isEmpty()) {
                continue;
            }

            ['keep' => $keep, 'delete' => $delete] = $this->splitBackupsByPolicy($backups);

            $keptCount += $keep->count();

            foreach ($delete as $backup) {
                $this->logActivity(
                    event: 'backup.db_backup_deleted',
                    description: 'Database backup deleted by retention policy',
                    properties: [
                        'disk' => $destination->diskName(),
                        'path' => $backup->path(),
                        'backup_date' => $backup->date()->toIso8601String(),
                    ],
                );

                $backup->delete();
                $deletedCount++;
            }
        }

        $this->logActivity(
            event: 'backup.db_backup_run_completed',
            description: 'Database backup run completed',
            properties: [
                'created_count' => $createdCount,
                'kept_count' => $keptCount,
                'deleted_count' => $deletedCount,
            ],
        );

        $this->info('✅ Backup-Policy abgeschlossen. Behalten: ' . $keptCount . ', gelöscht: ' . $deletedCount);

        return self::SUCCESS;
    }

    /**
     * Ermittelt den aktuellen Backup-Bestand pro Disk vor dem neuen Lauf.
     *
     * @return array<string, array<int, string>>
     */
    private function collectBackupPathsByDisk(): array
    {
        $pathsByDisk = [];

        $destinations = BackupDestinationFactory::createFromArray(Config::fromArray(config('backup')));

        foreach ($destinations as $destination) {
            if (! $destination->isReachable()) {
                continue;
            }

            $pathsByDisk[$destination->diskName()] = $destination->fresh()->backups()
                ->filter(fn(Backup $backup): bool => $backup->exists())
                ->map(fn(Backup $backup): string => $backup->path())
                ->values()
                ->all();
        }

        return $pathsByDisk;
    }

    /**
     * Schreibt Activity-Logs für neu erzeugte Backup-Dateien.
     *
     * @param  array<string, array<int, string>>  $beforePathsByDisk
     */
    private function logCreatedBackups(array $beforePathsByDisk): int
    {
        $createdCount = 0;

        $destinations = BackupDestinationFactory::createFromArray(Config::fromArray(config('backup')));

        foreach ($destinations as $destination) {
            if (! $destination->isReachable()) {
                continue;
            }

            $before = collect($beforePathsByDisk[$destination->diskName()] ?? []);
            $after = $destination->fresh()->backups()
                ->filter(fn(Backup $backup): bool => $backup->exists())
                ->values();

            $created = $after->filter(fn(Backup $backup): bool => ! $before->contains($backup->path()));

            foreach ($created as $backup) {
                $this->logActivity(
                    event: 'backup.db_backup_created',
                    description: 'Database backup created',
                    properties: [
                        'disk' => $destination->diskName(),
                        'path' => $backup->path(),
                        'backup_date' => $backup->date()->toIso8601String(),
                        'size_bytes' => $backup->sizeInBytes(),
                    ],
                );

                $createdCount++;
            }
        }

        return $createdCount;
    }

    /**
     * Schreibt einen robusten Activity-Log-Eintrag, auch im Scheduler-Kontext ohne User.
     */
    private function logActivity(string $event, string $description, array $properties = []): void
    {
        try {
            $logger = activity('backup')
                ->event($event)
                ->withProperties(array_merge($properties, [
                    'source' => [
                        'command' => $this->getName(),
                        'class' => static::class,
                    ],
                ]));

            if (Auth::check()) {
                $logger->causedBy(Auth::user());
            }

            $logger->log($description);
        } catch (Throwable) {
            // Activity-Logging darf den Backup-Lauf nicht blockieren.
        }
    }

    /**
     * Policy:
     * - Immer mindestens 10 neueste Backups behalten.
     * - Backups, die älter als 2 Stunden sind: auf 1 Backup pro Stunde ausdünnen.
     * - Backups, die älter als 2 Tage sind: löschen (außer sie gehören zu den 10 neuesten).
     *
     * @param  Collection<int, Backup>  $backups
     * @return array{keep: Collection<int, Backup>, delete: Collection<int, Backup>}
     */
    private function splitBackupsByPolicy(Collection $backups): array
    {
        $now = CarbonImmutable::now();
        $twoHoursAgo = $now->subHours(2);
        $twoDaysAgo = $now->subDays(2);

        // Die 10 neuesten Backups sind immer geschützt.
        $keep = $backups->take(self::MIN_BACKUPS_TO_KEEP)->values();
        $candidates = $backups->slice(self::MIN_BACKUPS_TO_KEEP)->values();
        $delete = collect();

        // Für Backups zwischen 2 Stunden und 2 Tagen behalten wir das neueste je Stunde.
        $hourBucketsKept = [];

        foreach ($candidates as $backup) {
            $backupDate = CarbonImmutable::instance($backup->date());

            if ($backupDate->lessThan($twoDaysAgo)) {
                $delete->push($backup);

                continue;
            }

            if ($backupDate->lessThan($twoHoursAgo)) {
                $hourBucket = $backupDate->format('Y-m-d-H');

                if (! array_key_exists($hourBucket, $hourBucketsKept)) {
                    $hourBucketsKept[$hourBucket] = true;
                    $keep->push($backup);

                    continue;
                }

                $delete->push($backup);

                continue;
            }

            $keep->push($backup);
        }

        return [
            'keep' => $keep->values(),
            'delete' => $delete->values(),
        ];
    }
}
