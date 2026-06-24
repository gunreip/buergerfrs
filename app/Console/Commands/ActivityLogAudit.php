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
        $items = DB::table('activity_log')
            ->selectRaw('
                log_name,
                event,
                COUNT(*) as total,
                SUM(CASE WHEN subject_type IS NULL OR subject_id IS NULL THEN 1 ELSE 0 END) as missing_subject,
                SUM(CASE WHEN causer_type IS NULL OR causer_id IS NULL THEN 1 ELSE 0 END) as missing_causer
            ')
            ->groupBy('log_name', 'event')
            ->orderByDesc('missing_subject')
            ->orderByDesc('missing_causer')
            ->orderByDesc('total')
            ->get()
            ->map(fn (object $row): array => [
                'log_name' => $row->log_name,
                'event' => $row->event,
                'total' => (int) $row->total,
                'missing_subject' => (int) $row->missing_subject,
                'missing_causer' => (int) $row->missing_causer,
                'has_missing_subject' => (int) $row->missing_subject > 0,
                'has_missing_causer' => (int) $row->missing_causer > 0,
            ])
            ->values()
            ->all();

        return [
            'generated_at' => now()->toIso8601String(),
            'type' => 'runtime_missing',
            'total_groups' => count($items),
            'problem_groups' => collect($items)
                ->filter(fn (array $item): bool => $item['has_missing_subject'] || $item['has_missing_causer'])
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

        foreach (File::allFiles(app_path()) as $file) {
            $path = $file->getPathname();

            if ($file->getExtension() !== 'php' || $this->isExcludedPath($path)) {
                continue;
            }

            $contents = (string) File::get($path);
            $lines = preg_split('/\R/', $contents) ?: [];

            foreach ($lines as $lineIndex => $line) {
                if (! preg_match('/\bactivity\s*\(/', $line)) {
                    continue;
                }

                $block = $this->activityBlock($lines, $lineIndex);
                $relativePath = $this->relativePath($path);

                $hasCausedBy = preg_match('/->\s*causedBy\s*\(/', $block) === 1;
                $hasPerformedOn = preg_match('/->\s*performedOn\s*\(/', $block) === 1;
                $hasWithProperties = preg_match('/->\s*withProperties\s*\(/', $block) === 1;
                $hasActorMetadata = preg_match(
                    '/[\'"]actor[\'"]|consoleActor\s*\(|ConsoleActivityContext::(?:merge|forCommand|actor)\s*\(/',
                    $block,
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
                    'needs_subject_review' => ! $hasPerformedOn,
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
                ->filter(fn (array $item): bool => $item['needs_subject_review'] || $item['needs_causer_or_actor_review'])
                ->count(),
            'items' => $items,
        ];
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
