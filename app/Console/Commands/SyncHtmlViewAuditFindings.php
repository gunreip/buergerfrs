<?php

// app/Console/Commands/SyncHtmlViewAuditFindings.php

namespace App\Console\Commands;

use App\Models\HtmlViewAuditFinding;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

#[Signature('html:sync-view-audit')]
#[Description('Sync the current HTML view audit JSON snapshot into the database history.')]
/**
 * Synchronizes HTML view audit snapshots from storage into database history.
 */
class SyncHtmlViewAuditFindings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = storage_path('audits/html/view-html-check.json');

        if (! File::exists($path)) {
            $this->error('HTML view audit file missing: storage/audits/html/view-html-check.json');
            $this->line('Run php artisan html:check first.');

            $this->logRunActivity('html.view_audit_sync.failed', 'HTML view audit sync failed because source file is missing.', [
                'path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
            ]);

            return self::FAILURE;
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            $this->error('HTML view audit file is not valid JSON: storage/audits/html/view-html-check.json');

            $this->logRunActivity('html.view_audit_sync.failed', 'HTML view audit sync failed because source JSON is invalid.', [
                'path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
            ]);

            return self::FAILURE;
        }

        $problems = $this->extractProblems($payload);
        $now = now();

        $currentFingerprints = [];
        $currentSourceFingerprints = [];

        $created = 0;
        $reopened = 0;
        $updated = 0;
        $changed = 0;
        $resolved = 0;
        $ignoredSeen = 0;

        DB::transaction(function () use (
            $problems,
            $now,
            &$currentFingerprints,
            &$currentSourceFingerprints,
            &$created,
            &$reopened,
            &$updated,
            &$changed,
            &$resolved,
            &$ignoredSeen,
        ): void {
            foreach ($problems as $problem) {
                $fingerprint = $this->fingerprint($problem);
                $sourceFingerprint = $this->sourceFingerprint($problem);

                $currentFingerprints[] = $fingerprint;
                $currentSourceFingerprints[] = $sourceFingerprint;

                $existing = HtmlViewAuditFinding::query()
                    ->where('fingerprint', $fingerprint)
                    ->first();

                if ($existing) {
                    $wasResolved = $existing->status === 'resolved';

                    $existing->fill([
                        ...$this->findingAttributes($problem, $fingerprint, $sourceFingerprint),
                        'status' => $existing->status === 'ignored' ? 'ignored' : 'open',
                        'last_seen_at' => $now,
                        'resolved_at' => null,
                        'resolved_source' => null,
                    ])->save();

                    if ($existing->status === 'ignored') {
                        $ignoredSeen++;
                    } elseif ($wasResolved) {
                        $reopened++;
                    } else {
                        $updated++;
                    }

                    continue;
                }

                $previousFinding = HtmlViewAuditFinding::query()
                    ->where('source_fingerprint', $sourceFingerprint)
                    ->whereIn('status', ['open', 'changed'])
                    ->latest('last_seen_at')
                    ->first();

                if ($previousFinding) {
                    $previousFinding->fill([
                        'status' => 'changed',
                        'last_seen_at' => $now,
                        'resolved_at' => null,
                        'resolved_source' => null,
                    ])->save();

                    $changed++;
                }

                HtmlViewAuditFinding::query()->create([
                    ...$this->findingAttributes($problem, $fingerprint, $sourceFingerprint),
                    'previous_finding_id' => $previousFinding?->id,
                    'status' => 'open',
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                ]);

                $created++;
            }

            $currentFingerprints = array_values(array_unique($currentFingerprints));
            $currentSourceFingerprints = array_values(array_unique($currentSourceFingerprints));

            $changed += HtmlViewAuditFinding::query()
                ->where('status', 'open')
                ->whereNotIn('fingerprint', $currentFingerprints)
                ->whereIn('source_fingerprint', $currentSourceFingerprints)
                ->update([
                    'status' => 'changed',
                    'resolved_at' => null,
                    'resolved_source' => null,
                    'updated_at' => $now,
                ]);

            $resolved = HtmlViewAuditFinding::query()
                ->whereIn('status', ['open', 'changed'])
                ->whereNotIn('fingerprint', $currentFingerprints)
                ->whereNotIn('source_fingerprint', $currentSourceFingerprints)
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => $now,
                    'resolved_source' => 'audit_sync',
                    'updated_at' => $now,
                ]);
        });

        $this->info('HTML view audit findings synced.');
        $this->line('Current problems: '.count($problems));
        $this->line('Created: '.$created);
        $this->line('Updated: '.$updated);
        $this->line('Changed / moved: '.$changed);
        $this->line('Reopened: '.$reopened);
        $this->line('Resolved: '.$resolved);
        $this->line('Ignored but still seen: '.$ignoredSeen);

        $this->logRunActivity('html.view_audit_sync.completed', 'HTML view audit findings sync completed.', [
            'current_problems' => count($problems),
            'created' => $created,
            'updated' => $updated,
            'changed' => $changed,
            'reopened' => $reopened,
            'resolved' => $resolved,
            'ignored_seen' => $ignoredSeen,
        ]);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function extractProblems(array $payload): array
    {
        $sections = is_array($payload['sections'] ?? null) ? $payload['sections'] : [];
        $problems = [];

        foreach ($sections as $sectionKey => $section) {
            if (! is_array($section)) {
                continue;
            }

            foreach (($section['problems'] ?? []) as $problem) {
                if (! is_array($problem)) {
                    continue;
                }

                $problem['section'] = (string) ($problem['section'] ?? $sectionKey);
                $problems[] = $problem;
            }
        }

        return $problems;
    }

    /**
     * @param  array<string, mixed>  $problem
     * @return array<string, mixed>
     */
    private function findingAttributes(array $problem, string $fingerprint, string $sourceFingerprint): array
    {
        return [
            'fingerprint' => $fingerprint,
            'source_fingerprint' => $sourceFingerprint,
            'section' => $this->stringValue($problem['section'] ?? null),
            'type' => $this->stringValue($problem['type'] ?? null),
            'file' => $this->stringValue($problem['file'] ?? null),
            'tag' => $this->nullableStringValue($problem['tag'] ?? null),
            'closing_tag' => $this->nullableStringValue($problem['closing_tag'] ?? null),
            'opened_line' => $this->nullableIntegerValue($problem['opened_line'] ?? null),
            'closing_line' => $this->nullableIntegerValue($problem['closing_line'] ?? null),
            'expected_closing' => $this->nullableStringValue($problem['expected_closing'] ?? null),
            'actual_closing' => $this->nullableStringValue($problem['actual_closing'] ?? null),
            'snapshot_payload' => $problem,
        ];
    }

    /**
     * Precise fingerprint. Includes line numbers.
     *
     * @param  array<string, mixed>  $problem
     */
    private function fingerprint(array $problem): string
    {
        return hash('sha256', json_encode([
            'section' => $this->stringValue($problem['section'] ?? null),
            'type' => $this->stringValue($problem['type'] ?? null),
            'file' => $this->stringValue($problem['file'] ?? null),
            'tag' => $this->nullableStringValue($problem['tag'] ?? null),
            'closing_tag' => $this->nullableStringValue($problem['closing_tag'] ?? null),
            'opened_line' => $this->nullableIntegerValue($problem['opened_line'] ?? null),
            'closing_line' => $this->nullableIntegerValue($problem['closing_line'] ?? null),
            'expected_closing' => $this->nullableStringValue($problem['expected_closing'] ?? null),
            'actual_closing' => $this->nullableStringValue($problem['actual_closing'] ?? null),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Softer fingerprint. Ignores line numbers to detect probable moved findings.
     *
     * @param  array<string, mixed>  $problem
     */
    private function sourceFingerprint(array $problem): string
    {
        return hash('sha256', json_encode([
            'section' => $this->stringValue($problem['section'] ?? null),
            'type' => $this->stringValue($problem['type'] ?? null),
            'file' => $this->stringValue($problem['file'] ?? null),
            'tag' => $this->nullableStringValue($problem['tag'] ?? null),
            'closing_tag' => $this->nullableStringValue($problem['closing_tag'] ?? null),
            'expected_closing' => $this->nullableStringValue($problem['expected_closing'] ?? null),
            'actual_closing' => $this->nullableStringValue($problem['actual_closing'] ?? null),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    private function stringValue(mixed $value): string
    {
        return Str::squish((string) $value);
    }

    private function nullableStringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = Str::squish((string) $value);

        return $value !== '' && $value !== '—' ? $value : null;
    }

    private function nullableIntegerValue(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === '—') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            activity('html')
                ->event($event)
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
