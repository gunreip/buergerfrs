<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use RuntimeException;
use Throwable;

/** Test-only writer. Creates its own temp root; cannot be pointed at /lang. */
final class BaselinePublisher
{
    public readonly string $directory;

    public function __construct(private readonly BaselinePrototype $baseline)
    {
        $this->directory = sys_get_temp_dir().'/tw-publication-'.bin2hex(random_bytes(12));
        if (! mkdir($this->directory, 0700)) {
            throw new RuntimeException('Cannot create test directory');
        }
        mkdir($this->directory.'/journals', 0700);
    }

    public function prepare(string $run, array $revisionIds, int $actor, string $reason): array
    {
        return $this->locked(function () use ($run, $revisionIds, $actor, $reason) {
            $this->validateRun($run);
            if ($actor < 1 || trim($reason) === '') {
                throw new RuntimeException('Actor and reason required');
            }
            if (file_exists($this->journalPath($run))) {
                throw new RuntimeException('Run already prepared');
            }
            $manifest = $this->baseline->publicationPreview($revisionIds);
            $grouped = [];
            foreach ($manifest['items'] as $item) {
                if (! preg_match('/^[A-Za-z0-9_-]+$/D', $item['locale'])) {
                    throw new RuntimeException('Unsupported locale');
                }
                [$group, $key] = explode('.', $item['key_name'], 2);
                $grouped[$item['locale'].'/'.$group.'.php'][] = ['path' => explode('.', $key), 'value' => $item['value']];
            }
            ksort($grouped);
            $files = [];
            foreach ($grouped as $relative => $changes) {
                $before = $this->bytes($relative);
                $tree = $before === null ? [] : (new BaselineValueReader)->treeFromBytes($before);
                foreach ($changes as $change) {
                    $this->putValue($tree, $change['path'], $change['value']);
                }
                $after = "<?php\n\nreturn ".var_export($tree, true).";\n";
                // Unchanged content is not needlessly reformatted.
                if ($before !== null && (new BaselineValueReader)->treeFromBytes($before) === $tree) {
                    $after = $before;
                }
                $files[$relative] = ['before' => $before === null ? null : hash('sha256', $before), 'after' => hash('sha256', $after), 'bytes' => $after];
            }
            if (array_filter($files, fn ($file) => $file['before'] !== $file['after']) === []) {
                throw new RuntimeException('No file changes to publish');
            }
            foreach (glob($this->directory.'/journals/*.json') as $existing) {
                $open = json_decode(file_get_contents($existing), true, flags: JSON_THROW_ON_ERROR);
                if (! in_array($open['state'], ['complete', 'closed'], true) && $this->closureReceipt($open['plan']['id']) === null && array_intersect(array_keys($files), array_keys($open['plan']['files'])) !== []) {
                    throw new RuntimeException('Unfinished publication overlaps these files');
                }
            }
            $plan = ['id' => $run, 'revision_ids' => $revisionIds, 'manifest' => $manifest['fingerprint'], 'actor' => $actor, 'reason' => $reason, 'files' => $files];
            $fingerprint = hash('sha256', json_encode($plan, JSON_THROW_ON_ERROR));
            $this->store(['plan' => $plan, 'fingerprint' => $fingerprint, 'state' => 'prepared', 'error' => null]);

            return ['run' => $run, 'fingerprint' => $fingerprint, 'files' => array_keys($files)];
        });
    }

    /** fault callback simulates crashes, including after rename but before receipt. */
    public function publish(string $run, string $expectedFingerprint, ?callable $fault = null): array
    {
        return $this->locked(function () use ($run, $expectedFingerprint, $fault) {
            $journal = $this->readJournal($run);
            $plan = $journal['plan'];
            if (! hash_equals($journal['fingerprint'], $expectedFingerprint)
                || ! hash_equals($journal['fingerprint'], hash('sha256', json_encode($plan, JSON_THROW_ON_ERROR)))) {
                throw new RuntimeException('Publication plan changed');
            }
            if ($journal['state'] === 'closed' || $this->closureReceipt($run) !== null) {
                throw new RuntimeException('Publication was closed without further writing');
            }
            if ($journal['state'] === 'complete') {
                return $journal;
            }
            // A committed receipt is historical fact, even if a newer draft now exists.
            $receiptExists = array_filter($this->baseline->rows('operations'), fn ($row) => $row['id'] === 'publication:'.$run) !== [];
            if ($receiptExists) {
                foreach ($plan['files'] as $relative => $file) {
                    if ($this->fileHash($relative) !== $file['after']) {
                        throw new RuntimeException('Receipt exists but file state differs');
                    }
                }
                $journal['state'] = 'complete';
                $journal['error'] = null;
                $this->store($journal);

                return $journal;
            }
            try {
                $this->baseline->verifyPublicationPreview($plan['revision_ids'], $plan['manifest']);
                // Preflight all files before the first write, allowing our own completed writes on resume.
                foreach ($plan['files'] as $relative => $file) {
                    $this->assertExpectedFile($relative, $file);
                }
                $journal['state'] = 'writing';
                $this->store($journal);
                foreach ($plan['files'] as $relative => $file) {
                    $this->baseline->verifyPublicationPreview($plan['revision_ids'], $plan['manifest']);
                    $hash = $this->assertExpectedFile($relative, $file);
                    if ($hash !== $file['after']) {
                        $this->atomicWrite($this->target($relative), $file['bytes']);
                        if ($this->fileHash($relative) !== $file['after']) {
                            throw new RuntimeException('Written hash mismatch');
                        }
                        if ($fault) {
                            $fault('after_write', $relative);
                        }
                    }
                }
                $receipts = [];
                foreach ($plan['files'] as $relative => $file) {
                    if ($this->fileHash($relative) !== $file['after']) {
                        throw new RuntimeException('Final file verification failed');
                    }
                    $receipts[$relative] = ['before' => $file['before'], 'after' => $file['after']];
                }
                $this->baseline->recordTestPublication('publication:'.$run, $plan['revision_ids'], $plan['manifest'], $receipts, $plan['actor'], $plan['reason']);
                if ($fault) {
                    $fault('after_receipt', null);
                }
                $journal['state'] = 'complete';
                $journal['error'] = null;
                $this->store($journal);
            } catch (Throwable $error) {
                $journal['state'] = 'interrupted';
                $journal['error'] = $error->getMessage();
                $this->store($journal);
                throw $error;
            }

            return $journal;
        });
    }

    /** Read-only review; a matching result is not proof of who wrote the file. */
    public function closurePreview(string $run): array
    {
        return $this->locked(fn () => $this->closureReview($run));
    }

    private function closureReview(string $run): array
    {
        $journal = $this->readJournal($run);
        $plan = $journal['plan'];
        if (! hash_equals($journal['fingerprint'], hash('sha256', json_encode($plan, JSON_THROW_ON_ERROR)))) {
            throw new RuntimeException('Publication plan changed');
        }
        if ($journal['state'] === 'complete' || array_filter($this->baseline->rows('operations'), fn ($row) => $row['id'] === 'publication:'.$run) !== []) {
            throw new RuntimeException('Successful receipt exists; cannot close as unfinished');
        }
        if ($this->closureReceipt($run) !== null || $journal['state'] === 'closed') {
            throw new RuntimeException('Publication already closed');
        }
        $files = [];
        foreach ($plan['files'] as $relative => $file) {
            $current = $this->fileHash($relative);
            $status = $current === $file['before']
                ? ($file['before'] === $file['after'] ? 'unchanged' : 'before')
                : ($current === $file['after'] ? 'matches_result' : 'diverged');
            $files[$relative] = ['before' => $file['before'], 'after' => $file['after'], 'current' => $current, 'status' => $status];
        }
        $approvalError = null;
        try {
            $this->baseline->verifyPublicationPreview($plan['revision_ids'], $plan['manifest']);
        } catch (RuntimeException $error) {
            $approvalError = $error->getMessage();
        }
        $snapshot = ['run' => $run, 'plan' => $journal['fingerprint'], 'state' => $journal['state'],
            'revision_ids' => $plan['revision_ids'], 'files' => $files, 'approval_error' => $approvalError];

        return ['snapshot' => $snapshot, 'fingerprint' => hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR))];
    }

    /** Only the journal and work history change; no destination file is touched. */
    public function close(string $run, string $expectedFingerprint, int $actor, string $reason, ?callable $fault = null): array
    {
        return $this->locked(function () use ($run, $expectedFingerprint, $actor, $reason, $fault) {
            $receipt = $this->closureReceipt($run);
            if ($receipt !== null) {
                if ($receipt['review']['fingerprint'] !== $expectedFingerprint || $receipt['actor'] !== $actor || $receipt['reason'] !== $reason) {
                    throw new RuntimeException('Closure retry differs from recorded decision');
                }
            } else {
                $review = $this->closureReview($run);
                if (! hash_equals($review['fingerprint'], $expectedFingerprint)) {
                    throw new RuntimeException('Closure preview changed; inspect again');
                }
                $receipt = $this->baseline->recordTestClosure($run, $review, $actor, $reason);
                if ($fault) {
                    $fault('after_closure_receipt');
                }
            }
            $journal = $this->readJournal($run);
            $journal['state'] = 'closed';
            $journal['closure'] = $receipt;
            $this->store($journal);

            return $journal;
        });
    }

    private function closureReceipt(string $run): ?array
    {
        foreach ($this->baseline->rows('operations') as $operation) {
            if ($operation['id'] === 'publication-closure:'.$run) {
                return json_decode($operation['result'], true, flags: JSON_THROW_ON_ERROR);
            }
        }

        return null;
    }

    public function readJournal(string $run): array
    {
        $this->validateRun($run);

        return json_decode(file_get_contents($this->journalPath($run)), true, flags: JSON_THROW_ON_ERROR);
    }

    private function putValue(array &$tree, array $segments, string $value): void
    {
        $key = array_shift($segments);
        if ($segments === []) {
            if (isset($tree[$key]) && is_array($tree[$key])) {
                throw new RuntimeException('Cannot replace a container with a scalar');
            }
            $tree[$key] = $value;

            return;
        }
        if (isset($tree[$key]) && ! is_array($tree[$key])) {
            throw new RuntimeException('Cannot replace a scalar with a container');
        }
        $tree[$key] ??= [];
        $this->putValue($tree[$key], $segments, $value);
    }

    private function target(string $relative): string
    {
        if (! preg_match('~^[A-Za-z0-9_-]+/[A-Za-z0-9_-]+\.php$~D', $relative)) {
            throw new RuntimeException('Unsafe test path');
        }
        $path = $this->directory.'/'.$relative;
        if (is_link($this->directory) || is_link(dirname($path)) || is_link($path)) {
            throw new RuntimeException('Symlink target rejected');
        }

        return $path;
    }

    private function bytes(string $relative): ?string
    {
        $path = $this->target($relative);
        if (! file_exists($path)) {
            return null;
        }
        $bytes = file_get_contents($path);
        if ($bytes === false) {
            throw new RuntimeException('Cannot read test file');
        }

        return $bytes;
    }

    private function fileHash(string $relative): ?string
    {
        $bytes = $this->bytes($relative);

        return $bytes === null ? null : hash('sha256', $bytes);
    }

    private function assertExpectedFile(string $relative, array $file): ?string
    {
        $hash = $this->fileHash($relative);
        if ($hash !== $file['before'] && $hash !== $file['after']) {
            throw new RuntimeException('File changed outside this publication: '.$relative);
        }

        return $hash;
    }

    private function atomicWrite(string $path, string $bytes): void
    {
        if (! is_dir(dirname($path)) && ! mkdir(dirname($path), 0700, true)) {
            throw new RuntimeException('Cannot create directory');
        }
        $temp = tempnam(dirname($path), '.publication-');
        try {
            if (file_put_contents($temp, $bytes) !== strlen($bytes) || ! rename($temp, $path)) {
                throw new RuntimeException('Atomic write failed');
            }
        } finally {
            if (is_file($temp)) {
                unlink($temp);
            }
        }
    }

    private function journalPath(string $run): string
    {
        return $this->directory.'/journals/'.$run.'.json';
    }

    private function store(array $journal): void
    {
        $this->atomicWrite($this->journalPath($journal['plan']['id']), json_encode($journal, JSON_THROW_ON_ERROR));
    }

    private function validateRun(string $run): void
    {
        if (! preg_match('/^[A-Za-z0-9_-]+$/D', $run)) {
            throw new RuntimeException('Invalid run ID');
        }
    }

    private function locked(callable $action): mixed
    {
        $lock = fopen($this->directory.'/publication.lock', 'c');
        if ($lock === false || ! flock($lock, LOCK_EX)) {
            throw new RuntimeException('Cannot lock publication');
        }
        try {
            return $action();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
