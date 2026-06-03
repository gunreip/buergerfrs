<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time normalization for manually keyed native entries.
 */
class TranslationsNormalizeKeyedNativeStatuses extends Command
{
    protected $signature = 'translations:normalize-keyed-native-statuses
        {--limit=0 : Maximum number of rows to normalize (0 = no limit)}
        {--dry-run : Preview affected rows without writing changes}';

    protected $description = 'Normalize legacy translation_keys rows with status native and a concrete key to status missing + classification key.';

    public function handle(): int
    {
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        $query = DB::table('translation_keys')
            ->select(['id', 'key', 'status', 'classification', 'updated_at'])
            ->where('status', 'native')
            ->whereNotNull('key')
            ->where('key', '<>', '')
            ->orderBy('id', 'asc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->get();

        if ($rows->isEmpty()) {
            $this->warn('No legacy keyed native rows found.');

            return self::SUCCESS;
        }

        $updated = 0;

        if (! $dryRun) {
            foreach ($rows as $row) {
                $changed = DB::table('translation_keys')
                    ->where('id', (int) $row->id)
                    ->update([
                        'status' => 'missing',
                        'classification' => 'key',
                        'updated_at' => now(),
                    ]);

                $updated += $changed;
            }
        }

        $this->components->info('Keyed native status normalization finished.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Matched rows', $rows->count()],
                ['Rows updated', $dryRun ? 0 : $updated],
                ['Dry run', $dryRun ? 'yes' : 'no'],
            ],
        );

        $preview = $rows->take(20)->map(static fn(object $row): array => [
            'id' => (int) $row->id,
            'key' => (string) $row->key,
            'old_status' => (string) $row->status,
            'old_classification' => (string) ($row->classification ?? ''),
        ])->values()->all();

        if ($preview !== []) {
            $this->line('');
            $this->table(
                ['ID', 'Key', 'Old status', 'Old classification'],
                $preview,
            );
        }

        if ($dryRun) {
            $this->warn('Dry run only: no rows were updated.');
        }

        return self::SUCCESS;
    }
}
