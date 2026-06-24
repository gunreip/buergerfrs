<?php

// app/Console/Commands/ActivityLogAudit.php

// php artisan activity-log:audit

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('activity-log:audit')]
#[Description('Audit activity_log runtime entries and source code usages.')]
class ActivityLogAudit extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $auditPath = storage_path('audits');

        File::ensureDirectoryExists($auditPath);

        $runtimeMissingAudit = $this->runtimeMissingAudit();
        $sourceUsageAudit = $this->sourceUsageAudit();

        $this->writeJson($auditPath.'/activity-log-runtime-missing.json', $runtimeMissingAudit);
        $this->writeJson($auditPath.'/activity-log-runtime-missing-preview.json', $this->previewAudit($runtimeMissingAudit));

        $this->writeJson($auditPath.'/activity-log-source-usage.json', $sourceUsageAudit);
        $this->writeJson($auditPath.'/activity-log-source-usage-preview.json', $this->previewAudit($sourceUsageAudit));

        $this->info('Activity log audit written.');
        $this->line('Runtime: storage/audits/activity-log-runtime-missing.json');
        $this->line('Source:  storage/audits/activity-log-source-usage.json');

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function runtimeMissingAudit(): array
    {
        $groups = [];

        foreach (DB::table('activity_log')
            ->select(['log_name', 'event', 'subject_type', 'subject_id', 'causer_type', 'causer_id', 'properties'])
            ->orderBy('id')
            ->cursor() as $row) {
            $logName = trim((string) ($row->log_name ?? ''));
            $event = trim((string) ($row->event ?? ''));
            $groupKey = $logName."\0".$event;
            $subjectRequired = $this->eventRequiresSubject($event);
            $properties = $this->decodeProperties($row->properties ?? null);
            $hasActorContext = data_get($properties, 'actor.type') !== null
                || data_get($properties, 'actor.terminal_user') !== null;

            $groups[$groupKey] ??= [
                'log_name' => $logName,
                'event' => $event,
                'total' => 0,
                'subject_required' => $subjectRequired,
                'missing_required_subject' => 0,
                'missing_causer_or_actor' => 0,
            ];

            $groups[$groupKey]['total']++;

            if ($subjectRequired && ($row->subject_type === null || $row->subject_id === null)) {
                $groups[$groupKey]['missing_required_subject']++;
            }

            if (($row->causer_type === null || $row->causer_id === null) && ! $hasActorContext) {
                $groups[$groupKey]['missing_causer_or_actor']++;
            }
        }

        $items = collect($groups)
            ->map(static fn (array $group): array => [
                ...$group,
                'has_missing_required_subject' => $group['missing_required_subject'] > 0,
                'has_missing_causer_or_actor' => $group['missing_causer_or_actor'] > 0,
            ])
            ->sortByDesc(fn (array $group): array => [
                $group['missing_required_subject'],
                $group['missing_causer_or_actor'],
                $group['total'],
            ])
            ->values()
            ->all();

        return [
            'generated_at' => now()->toIso8601String(),
            'type' => 'runtime_missing',
            'total_groups' => count($items),
            'problem_groups' => collect($items)
                ->filter(fn (array $item): bool => $item['has_missing_required_subject'] || $item['has_missing_causer_or_actor'])
                ->count(),
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sourceUsageAudit(): array
    {
        $items = [];
        $unloggedMutationCandidates = [];

        foreach (File::allFiles(app_path()) as $file) {
            $path = $file->getPathname();

            if ($file->getExtension() !== 'php' || $this->isExcludedPath($path)) {
                continue;
            }

            $contents = (string) File::get($path);
            $lines = preg_split('/\R/', $contents) ?: [];
            $relativePath = $this->relativePath($path);
            $hasActivityIntegration = preg_match(
                '/\bactivity\s*\(|AdminActivity|ManagementActivity|TranslationActivity|ConsoleActivityContext/',
                $contents,
            ) === 1;
            $hasMutationCandidate = preg_match(
                '/->\s*(?:save|delete|update|create|sync|forceFill|markReviewed|markUnreviewed|setSetting)\s*\(/',
                $contents,
            ) === 1;

            if (
                $hasMutationCandidate
                && ! $hasActivityIntegration
                && $this->isMutationAuditScope($relativePath)
                && ! $this->isExpectedUnloggedMutation($relativePath)
            ) {
                $unloggedMutationCandidates[] = [
                    'file' => $relativePath,
                    'recommendation' => 'Review mutating code for a meaningful activity-log summary.',
                ];
            }

            foreach ($lines as $lineIndex => $line) {
                if (! preg_match('/\bactivity\s*\(/', $line)) {
                    continue;
                }

                $block = $this->activityBlock($lines, $lineIndex);
                $hasCausedBy = preg_match('/->\s*causedBy\s*\(/', $block) === 1;
                $hasPerformedOn = preg_match('/->\s*performedOn\s*\(/', $block) === 1;
                $hasWithProperties = preg_match('/->\s*withProperties\s*\(/', $block) === 1;
                $hasActorMetadata = preg_match(
                    '/[\'"]actor[\'"]|consoleActor\s*\(|ConsoleActivityContext::(?:merge|forCommand|actor)\s*\(/',
                    $block."\n".$contents,
                ) === 1;
                $hasEvent = preg_match('/->\s*event\s*\(/', $block) === 1;
                $hasLog = preg_match('/->\s*log\s*\(/', $block) === 1;

                $recommendations = [];

                if (! $hasPerformedOn) {
                    $recommendations[] = 'Add performedOn(...) when a concrete subject/model exists.';
                }

                if (! $hasCausedBy && ! $hasActorMetadata) {
                    $recommendations[] = 'Add causedBy(...) for app users or properties.actor for console/terminal context.';
                }

                $items[] = [
                    'file' => $relativePath,
                    'line' => $lineIndex + 1,
                    'has_event' => $hasEvent,
                    'has_log' => $hasLog,
                    'has_with_properties' => $hasWithProperties,
                    'has_caused_by' => $hasCausedBy,
                    'has_performed_on' => $hasPerformedOn,
                    'has_actor_metadata' => $hasActorMetadata,
                    'needs_subject_review' => false,
                    'subject_review_recommended' => ! $hasPerformedOn,
                    'needs_causer_or_actor_review' => ! $hasCausedBy && ! $hasActorMetadata,
                    'recommendations' => $recommendations,
                    'snippet' => trim($line),
                ];
            }
        }

        return [
            'generated_at' => now()->toIso8601String(),
            'type' => 'source_usage',
            'total_usages' => count($items),
            'problem_usages' => collect($items)
                ->filter(fn (array $item): bool => ! $item['has_event']
                    || ! $item['has_log']
                    || ! $item['has_with_properties']
                    || $item['needs_causer_or_actor_review'])
                ->count(),
            'unlogged_mutation_candidate_count' => count($unloggedMutationCandidates),
            'unlogged_mutation_candidates' => $unloggedMutationCandidates,
            'items' => $items,
        ];
    }

    private function eventRequiresSubject(string $event): bool
    {
        foreach ([
            'admin.user.',
            'admin.role.',
            'admin.permission.',
            'admin.fallback_report.',
            'admin.flag_reference.',
            'management.person.',
            'translations.admin.',
        ] as $prefix) {
            if (str_starts_with($event, $prefix)) {
                return true;
            }
        }

        return in_array($event, ['login', 'logout', 'registered', 'password_reset'], true);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeProperties(mixed $properties): array
    {
        if (is_array($properties)) {
            return $properties;
        }

        if (! is_string($properties) || trim($properties) === '') {
            return [];
        }

        $decoded = json_decode($properties, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function isMutationAuditScope(string $relativePath): bool
    {
        return str_starts_with($relativePath, 'app/Livewire/')
            || str_starts_with($relativePath, 'app/Actions/')
            || str_starts_with($relativePath, 'app/Console/Commands/');
    }

    private function isExpectedUnloggedMutation(string $relativePath): bool
    {
        return in_array($relativePath, [
            // Fortify dispatches PasswordReset after this action; the event listener owns logging.
            'app/Actions/Fortify/ResetUserPassword.php',
            // Personal list/filter preferences are intentionally excluded from the audit trail.
            'app/Livewire/Account/Preferences.php',
            'app/Livewire/Concerns/InteractsWithUserSettings.php',
        ], true);
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function activityBlock(array $lines, int $startLineIndex): string
    {
        $blockLines = [];

        for ($index = $startLineIndex; $index < min(count($lines), $startLineIndex + 40); $index++) {
            $blockLines[] = $lines[$index];

            if (str_contains($lines[$index], '->log(')) {
                break;
            }
        }

        return implode("\n", $blockLines);
    }

    private function isExcludedPath(string $path): bool
    {
        $basename = basename($path);

        return str_contains($basename, 'xxx')
            || str_contains($basename, 'yyy')
            || str_contains($basename, 'zzz');
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }

    /**
     * @param  array<string, mixed>  $audit
     * @return array<string, mixed>
     */
    private function previewAudit(array $audit): array
    {
        return [
            ...$audit,
            'items' => collect($audit['items'] ?? [])
                ->take(20)
                ->values()
                ->all(),
            'unlogged_mutation_candidates' => collect($audit['unlogged_mutation_candidates'] ?? [])
                ->take(20)
                ->values()
                ->all(),
            'preview_limit' => 20,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function writeJson(string $path, array $data): void
    {
        File::put(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }
}
