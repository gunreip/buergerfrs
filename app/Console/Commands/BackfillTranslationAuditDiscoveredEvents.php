<?php

// app/Console/Commands/BackfillTranslationAuditDiscoveredEvents.php

namespace App\Console\Commands;

use App\Models\TranslationKey;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('translations:backfill-audit-discovered-events {--dry-run : Report missing baselines without writing them}')]
#[Description('Create missing discovered baseline events for existing translation keys.')]
/**
 * Backfill the missing beginning of legacy translation audit histories.
 *
 * The command is idempotent: translation keys that already have a discovered
 * event are excluded. Backfilled events use first_seen_at as their chronological
 * timestamp, while their context explicitly marks field and usage details as
 * potentially incomplete historical data.
 */
class BackfillTranslationAuditDiscoveredEvents extends Command
{
    /**
     * Create one discovered baseline event for every translation key that lacks one.
     *
     * The dry-run option only reports the number of affected translation keys.
     * Writes are processed in chunks to keep memory use bounded for large datasets.
     */
    public function handle(): int
    {
        $query = TranslationKey::query()
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_audit_events')
                    ->whereColumn('translation_audit_events.translation_key_id', 'translation_keys.id')
                    ->where('translation_audit_events.event_type', 'discovered');
            });

        $missingCount = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->components->info("{$missingCount} translation keys are missing a discovered baseline event.");

            return self::SUCCESS;
        }

        $createdCount = 0;

        $query->orderBy('id')->chunkById(500, function ($translationKeys) use (&$createdCount): void {
            foreach ($translationKeys as $translationKey) {
                $firstSeenAt = $translationKey->first_seen_at ?? $translationKey->created_at ?? now();

                DB::table('translation_audit_events')->insert([
                    'translation_key_id' => $translationKey->id,
                    'translation_usage_id' => null,
                    'entity_type' => 'translation_key',
                    'event_type' => 'discovered',
                    'old_fingerprint' => null,
                    'new_fingerprint' => $translationKey->fingerprint,
                    'old_file' => null,
                    'new_file' => null,
                    'old_line' => null,
                    'new_line' => null,
                    'old_key' => null,
                    'new_key' => $translationKey->key,
                    'old_value' => null,
                    'new_value' => $translationKey->native_text,
                    'old_status' => null,
                    'new_status' => $translationKey->status,
                    'reason' => 'translation_key_discovered_event_backfilled',
                    'context' => json_encode([
                        'classification' => $translationKey->classification,
                        'workflow_status' => $translationKey->workflow_status,
                        'suggested_key' => $translationKey->suggested_key,
                        'source' => $translationKey->source,
                        'first_seen_at' => $firstSeenAt->toDateTimeString(),
                        'backfilled' => true,
                        'affected_usages_snapshot_complete' => false,
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at' => $firstSeenAt,
                    'updated_at' => now(),
                ]);

                $createdCount++;
            }
        });

        $this->components->info("Created {$createdCount} discovered baseline events.");

        return self::SUCCESS;
    }
}
