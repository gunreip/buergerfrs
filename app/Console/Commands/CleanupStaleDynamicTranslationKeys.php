<?php

// app/Console/Commands/CleanupStaleDynamicTranslationKeys.php

// php artisan translations:cleanup-stale-dynamic-keys
// php artisan translations:cleanup-stale-dynamic-keys --apply

namespace App\Console\Commands;

use App\Models\TranslationKey;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('translations:cleanup-stale-dynamic-keys
    {--apply : Persist the cleanup decisions. Without this option the command only reports what would happen.}')]
#[Description('Review legacy stale dynamic translation keys and either reactivate or archive them with audit events.')]
class CleanupStaleDynamicTranslationKeys extends Command
{
    private const STALE_AUDIT_USAGE_REASON = 'stale_audit_usage_not_seen_in_latest_sync';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $now = now();
        $rows = [];
        $currentScanItems = $this->currentScanItems();
        $currentItemsByFingerprint = $currentScanItems->keyBy('fingerprint');
        $currentItemsBySignature = $currentScanItems
            ->groupBy('signature')
            ->map(static fn ($items) => $items->first());
        $summary = [
            'obsolete' => 0,
            'reactivate' => 0,
            'active_without_suggestion' => 0,
            'skip_dynamic_multi' => 0,
        ];

        $candidates = TranslationKey::query()
            ->where('classification', 'dynamic')
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('suggested_key')
                    ->orWhere('suggested_key', '');
            })
            ->whereHas('auditEvents', function (Builder $query): void {
                $query->where('event_type', 'stale_marked');
            })
            ->withCount([
                'usages',
                'usages as active_usages_count' => function (Builder $query): void {
                    $query->where(function (Builder $query): void {
                        $query
                            ->whereNull('reason')
                            ->orWhere('reason', '!=', self::STALE_AUDIT_USAGE_REASON);
                    });
                },
            ])
            ->with([
                'usages' => fn ($query) => $query->orderBy('id'),
            ])
            ->orderBy('id')
            ->get();

        foreach ($candidates as $translationKey) {
            $currentItem = $this->currentScanItemForKey(
                translationKey: $translationKey,
                currentItemsByFingerprint: $currentItemsByFingerprint,
                currentItemsBySignature: $currentItemsBySignature,
            );
            $action = $this->resolveAction($translationKey, $currentItem);
            $summary[$action]++;

            $rows[] = [
                $translationKey->id,
                $translationKey->key ?: '—',
                $translationKey->status ?: '—',
                $translationKey->workflow_status ?: '—',
                $translationKey->usages_count,
                $translationKey->active_usages_count,
                $currentItem !== null ? 'yes' : 'no',
                (string) ($currentItem['suggested_key'] ?? '—'),
                $action,
            ];

            if (! $apply || $action === 'skip_dynamic_multi') {
                continue;
            }

            $this->applyAction($translationKey, $action, $currentItem, $now);
        }

        $this->table(
            ['ID', 'Key', 'Status', 'Workflow', 'Usages', 'Active usages', 'Current code', 'Current suggested key', 'Action'],
            $rows,
        );

        $this->newLine();
        $this->components->info(($apply ? 'Applied' : 'Dry run') . ' stale dynamic key cleanup.');
        $this->table(
            ['Action', 'Count'],
            collect($summary)->map(fn(int $count, string $action): array => [$action, $count])->values()->all(),
        );

        if (! $apply) {
            $this->warn('Dry run only: rerun with --apply to persist these decisions.');
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>|null  $currentItem
     */
    private function resolveAction(TranslationKey $translationKey, ?array $currentItem): string
    {
        if ((bool) ($translationKey->is_dynamic_multi ?? false)) {
            return 'skip_dynamic_multi';
        }

        if ($currentItem !== null) {
            return trim((string) ($currentItem['suggested_key'] ?? '')) !== ''
                ? 'reactivate'
                : 'active_without_suggestion';
        }

        if ((int) ($translationKey->active_usages_count ?? 0) <= 0) {
            return 'obsolete';
        }

        return trim((string) ($translationKey->suggested_key ?? '')) !== ''
            ? 'reactivate'
            : 'active_without_suggestion';
    }

    /**
     * @param  array<string, mixed>|null  $currentItem
     */
    private function applyAction(TranslationKey $translationKey, string $action, ?array $currentItem, mixed $now): void
    {
        $oldStatus = (string) ($translationKey->status ?? '');
        $oldWorkflowStatus = (string) ($translationKey->workflow_status ?? 'open');
        $currentSuggestedKey = trim((string) ($currentItem['suggested_key'] ?? ''));
        $resolvedSuggestedKey = $currentSuggestedKey !== ''
            ? $currentSuggestedKey
            : $translationKey->suggested_key;

        $newStatus = $action === 'obsolete' ? 'obsolete' : 'dynamic';
        $newWorkflowStatus = $action === 'obsolete' ? 'reviewed' : 'open';

        $translationKey->forceFill([
            'status' => $newStatus,
            'workflow_status' => $newWorkflowStatus,
            'reviewed_at' => $action === 'obsolete' ? $now : null,
            'reviewed_by_user_id' => null,
            'review_note' => $action === 'obsolete'
                ? 'legacy_dynamic_stale_obsoleted_by_cleanup'
                : null,
            'source' => 'dynamic_audit',
            'suggested_key' => $resolvedSuggestedKey,
            'namespace' => $this->namespaceFromKey((string) $resolvedSuggestedKey),
            'group' => $this->groupFromKey((string) $resolvedSuggestedKey),
            'obsolete_at' => $action === 'obsolete' ? ($translationKey->obsolete_at ?? $now) : null,
        ])->save();

        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $translationKey->id,
            'translation_usage_id' => null,
            'entity_type' => 'translation_key',
            'event_type' => match ($action) {
                'obsolete' => 'legacy_dynamic_stale_obsoleted',
                'reactivate' => 'legacy_dynamic_stale_reactivated',
                default => 'legacy_dynamic_stale_active_without_suggestion',
            },
            'old_fingerprint' => $translationKey->fingerprint,
            'new_fingerprint' => $translationKey->fingerprint,
            'old_key' => $translationKey->key,
            'new_key' => $translationKey->key,
            'old_value' => $oldWorkflowStatus,
            'new_value' => $newWorkflowStatus,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $action,
            'context' => json_encode([
                'source' => 'translations:cleanup-stale-dynamic-keys',
                'active_usages_count' => (int) ($translationKey->active_usages_count ?? 0),
                'usages_count' => (int) ($translationKey->usages_count ?? 0),
                'suggested_key' => $resolvedSuggestedKey,
                'current_scan_item' => $currentItem,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function currentScanItems(): \Illuminate\Support\Collection
    {
        $path = base_path('storage/audits/translations/dynamic/items.json');

        if (! File::isFile($path)) {
            $this->warn('Current dynamic scan file is missing: ' . $this->relativePath($path));
            $this->warn('Run project:build or translations:sync-dynamic-keys before applying cleanup decisions.');

            return collect();
        }

        $items = json_decode((string) File::get($path), true);

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)
            ->filter(static fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                $item['fingerprint'] = $this->keyFingerprint($item);
                $item['signature'] = $this->itemSignature(
                    (string) ($item['file'] ?? ''),
                    (string) ($item['function'] ?? ''),
                    (string) ($item['raw'] ?? ''),
                );

                return $item;
            })
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<string, array<string, mixed>>  $currentItemsByFingerprint
     * @param  \Illuminate\Support\Collection<string, array<string, mixed>>  $currentItemsBySignature
     * @return array<string, mixed>|null
     */
    private function currentScanItemForKey(
        TranslationKey $translationKey,
        \Illuminate\Support\Collection $currentItemsByFingerprint,
        \Illuminate\Support\Collection $currentItemsBySignature,
    ): ?array {
        $byFingerprint = $currentItemsByFingerprint->get((string) $translationKey->fingerprint);

        if (is_array($byFingerprint)) {
            return $byFingerprint;
        }

        foreach ($translationKey->usages as $usage) {
            $signature = $this->itemSignature(
                (string) ($usage->file ?? ''),
                (string) ($usage->function ?? ''),
                (string) ($usage->raw ?? ''),
            );
            $bySignature = $currentItemsBySignature->get($signature);

            if (is_array($bySignature)) {
                return $bySignature;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function keyFingerprint(array $item): string
    {
        return hash('sha256', implode('|', [
            'dynamic',
            $item['file'] ?? '',
            $item['line'] ?? '',
            $item['function'] ?? '',
            $item['raw'] ?? '',
        ]));
    }

    private function itemSignature(string $file, string $function, string $raw): string
    {
        return hash('sha256', implode('|', [
            $file,
            $function,
            trim($raw),
        ]));
    }

    private function namespaceFromKey(string $key): ?string
    {
        $key = trim($key);

        if ($key === '' || ! str_contains($key, '.')) {
            return null;
        }

        return explode('.', $key)[0] ?: null;
    }

    private function groupFromKey(string $key): ?string
    {
        $key = trim($key);

        if ($key === '' || ! str_contains($key, '.')) {
            return null;
        }

        return explode('.', $key)[1] ?? null;
    }

    private function relativePath(string $path): string
    {
        return str($path)->replace(base_path() . DIRECTORY_SEPARATOR, '')->toString();
    }
}
