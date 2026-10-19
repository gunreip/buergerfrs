<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translation-workbench:reconcile-sources
    {--paths= : Comma-separated scan paths; defaults to configured paths.}
    {--save-plan : Save a signed repair plan and print its ID; no database writes.}
    {--apply= : Apply a previously saved plan ID after fresh validation.}
    {--json : Print the complete machine-readable comparison.}')]
#[Description('Compare current source occurrences with stored findings and assignments without changing data.')]
class ReconcileTranslationWorkbenchSources extends Command
{
    public function handle(TranslationWorkbenchSourceReconciliation $reconciliation): int
    {
        $repair = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class);
        $directory = storage_path('app/private/translation-workbench/source-repairs');
        $apply = (string) $this->option('apply');
        if ($apply !== '') {
            if ($this->option('paths') || $this->option('save-plan') || ! preg_match('/^[a-f0-9]{32}$/', $apply)) {
                $this->error('Use --apply with a saved plan ID only; paths are stored in the plan.');
                return self::FAILURE;
            }
            $file = $directory.'/'.$apply.'.json';
            if (! is_file($file)) {
                $this->error('Repair plan not found.');
                return self::FAILURE;
            }
            $envelope = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            $payload = $envelope['payload'] ?? '';
            if (! is_string($payload) || ! hash_equals(hash_hmac('sha256', $payload, (string) config('app.key')), (string) ($envelope['signature'] ?? ''))) {
                $this->error('Repair plan signature is invalid.');
                return self::FAILURE;
            }
            $results = $repair->apply(json_decode($payload, true, 512, JSON_THROW_ON_ERROR));
            $this->line(json_encode(['plan_id' => $apply, 'results' => $results], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            return collect($results)->contains(fn ($r) => $r['state'] === 'skipped') ? self::FAILURE : self::SUCCESS;
        }
        $paths = array_values(array_filter(array_map('trim', explode(',', (string) $this->option('paths')))));
        if ($this->option('save-plan')) {
            $plan = $repair->plan($paths === [] ? null : $paths);
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Cannot create private plan directory.');
            }
            $id = bin2hex(random_bytes(16));
            $payload = json_encode($plan, JSON_THROW_ON_ERROR);
            $file = $directory.'/'.$id.'.json';
            $handle = fopen($file, 'x');
            if (! $handle) {
                throw new \RuntimeException('Cannot create repair plan.');
            }
            try {
                chmod($file, 0600);
                $encoded = json_encode(['payload' => $payload, 'signature' => hash_hmac('sha256', $payload, (string) config('app.key'))], JSON_THROW_ON_ERROR);
                if (fwrite($handle, $encoded) !== strlen($encoded)) {
                    throw new \RuntimeException('Cannot write complete repair plan.');
                }
            } finally {
                fclose($handle);
            }
            $this->line(json_encode(['plan_id' => $id, 'plan' => $plan], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            return self::SUCCESS;
        }
        $report = $reconciliation->plan($paths === [] ? null : $paths);
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        } else {
            $this->components->info('Read-only source reconciliation; no data or files changed.');
            $this->table(['State', 'Count'], collect($report['summary'])->map(fn ($count, $state) => [$state, $count])->all());
            $this->table(['State', 'Finding IDs', 'Source', 'Line old → current', 'Assignment issue', 'Reviewed key differs'], array_map(fn ($r) => [
                $r['state'], implode(', ', $r['finding_ids']), $r['path'], ($r['old_line'] ?? '—').' → '.($r['line'] ?? '—'),
                $r['assignment_issue'] ?? '—', $r['reviewed_key_differs_from_code'] ? 'yes (preserved)' : '—',
            ], array_values(array_filter($report['rows'], fn ($r) => $r['state'] !== 'unchanged' || $r['assignment_issue'] !== null || $r['reviewed_key_differs_from_code']))));
            $this->line('Use --json for all occurrences and their key assignments. Cross-file moves and ambiguous occurrences require review.');
        }

        return self::SUCCESS;
    }
}
