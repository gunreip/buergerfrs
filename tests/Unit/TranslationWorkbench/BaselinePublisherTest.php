<?php

use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Tests\Support\TranslationWorkbench\BaselinePrototype;
use Tests\Support\TranslationWorkbench\BaselinePublisher;

beforeEach(function () {
    $this->baseline = new BaselinePrototype;
    $item = new DiscoveredTranslation(kind: 'key', sourceType: 'blade', sourcePath: 'example.blade.php', sourceLine: 1,
        functionName: '__', rawExpression: "__('ui.button.cancel')", literalText: null, literalTextSuggested: null,
        translationKey: 'ui.button.cancel', translationKeySource: 'code', existingKey: 'ui.button.cancel', suggestedKey: 'ui.button.cancel',
        namespace: 'ui', group: 'button', sourceSignature: 'fixture', fingerprint: 'fixture', sourceFingerprint: 'fixture',
        expressionFingerprint: 'fixture', semanticFingerprint: 'fixture', sourceColumn: 1);
    $this->baseline->scan('capture', [$item], ['example.blade.php' => 'fixture']);
    $this->revisions = [];
    foreach (['de' => 'Abbrechen', 'en' => 'Cancel'] as $locale => $value) {
        $id = $this->baseline->save('draft-'.$locale, 1, $locale, $value, 7, 'Initial draft')['revision_id'];
        $this->baseline->decideRevision('approve-'.$locale, $id, $this->baseline->reviewSnapshot($id)['fingerprint'], true, 8, 'Wording checked');
        $this->revisions[] = $id;
    }
    $this->publisher = new BaselinePublisher($this->baseline);
});

afterEach(function () {
    $root = $this->publisher->directory;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $file) {
        if ($file->isDir() && ! $file->isLink()) {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($root);
});

it('writes approved revisions while preserving unrelated values and empty containers', function () {
    mkdir($this->publisher->directory.'/en');
    $file = $this->publisher->directory.'/en/ui.php';
    file_put_contents($file, "<?php return ['other' => ['label' => 'Keep me'], 'empty' => []];");
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish checked wording');
    expect(is_file($this->publisher->directory.'/de/ui.php'))->toBeFalse();
    $result = $this->publisher->publish('publish', $plan['fingerprint']);
    expect($result['state'])->toBe('complete');
    $tree = (new BaselineValueReader)->treeFromBytes(file_get_contents($file));
    expect($tree)->toBe(['other' => ['label' => 'Keep me'], 'empty' => [], 'button' => ['cancel' => 'Cancel']]);
    $events = $this->baseline->rows('events');
    $event = end($events);
    expect($event['event_type'])->toBe('revisions_published')->and($event['actor_id'])->toBe(9)
        ->and($event['reason'])->toBe('Publish checked wording')
        ->and(json_decode($event['details'], true)['files']['en/ui.php']['after'])->toBe(hash_file('sha256', $file));
    $this->publisher->publish('publish', $plan['fingerprint']);
    expect($this->baseline->rows('events'))->toBe($events);
});

it('checks every file before writing and refuses a changed target', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    mkdir($this->publisher->directory.'/en');
    $file = $this->publisher->directory.'/en/ui.php';
    $external = "<?php return ['someone' => 'Else'];";
    file_put_contents($file, $external);
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint']))->toThrow(RuntimeException::class, 'File changed outside');
    expect(file_get_contents($file))->toBe($external)
        ->and(is_file($this->publisher->directory.'/de/ui.php'))->toBeFalse()
        ->and($this->publisher->readJournal('publish')['state'])->toBe('interrupted')
        ->and(array_column($this->baseline->rows('events'), 'event_type'))->not->toContain('revisions_published');
});

it('resumes after rename without rewriting the finished file or duplicating history', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint'], function ($stage) {
        if ($stage === 'after_write') {
            throw new RuntimeException('Simulated interruption');
        }
    }))->toThrow(RuntimeException::class, 'Simulated');
    $done = $this->publisher->directory.'/de/ui.php';
    expect(is_file($done))->toBeTrue()->and(is_file($this->publisher->directory.'/en/ui.php'))->toBeFalse();
    touch($done, 1000000000);
    $writes = [];
    $result = $this->publisher->publish('publish', $plan['fingerprint'], function ($stage, $file) use (&$writes) {
        if ($stage === 'after_write') {
            $writes[] = $file;
        }
    });
    clearstatcache();
    expect($result['state'])->toBe('complete')->and($writes)->toBe(['en/ui.php'])
        ->and(filemtime($done))->toBe(1000000000)
        ->and(array_count_values(array_column($this->baseline->rows('events'), 'event_type'))['revisions_published'])->toBe(1);
});

it('recovers a committed receipt even after a newer draft without republishing', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint'], function ($stage) {
        if ($stage === 'after_receipt') {
            throw new RuntimeException('Journal completion interrupted');
        }
    }))->toThrow(RuntimeException::class, 'Journal completion');
    $this->baseline->save('new-draft', 1, 'en', 'Abort', 7, 'Next wording');
    $events = $this->baseline->rows('events');
    $result = $this->publisher->publish('publish', $plan['fingerprint'], fn () => throw new RuntimeException('Must not write again'));
    expect($result['state'])->toBe('complete')->and($this->baseline->rows('events'))->toBe($events);
});

it('preserves written files and resumes after a receipt failure', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    $this->baseline->db->exec("CREATE TRIGGER fail_publication BEFORE INSERT ON events WHEN NEW.event_type='revisions_published' BEGIN SELECT RAISE(ABORT,'Recorder failed'); END");
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint']))->toThrow(PDOException::class);
    expect(is_file($this->publisher->directory.'/en/ui.php'))->toBeTrue()
        ->and(array_column($this->baseline->rows('events'), 'event_type'))->not->toContain('revisions_published');
    $this->baseline->db->exec('DROP TRIGGER fail_publication');
    expect($this->publisher->publish('publish', $plan['fingerprint'], function ($stage) {
        if ($stage === 'after_write') {
            throw new RuntimeException('No rewrite');
        }
    })['state'])->toBe('complete');
});

it('stops a partial run if approval has been revoked', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint'], fn () => throw new RuntimeException('Stop')))->toThrow(RuntimeException::class);
    $id = $this->revisions[1];
    $this->baseline->decideRevision('revoke', $id, $this->baseline->reviewSnapshot($id)['fingerprint'], false, 8, 'Needs clarification');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint']))->toThrow(RuntimeException::class, 'not approved');
    expect(is_file($this->publisher->directory.'/de/ui.php'))->toBeTrue()
        ->and(is_file($this->publisher->directory.'/en/ui.php'))->toBeFalse()
        ->and(array_column($this->baseline->rows('events'), 'event_type'))->not->toContain('revisions_published');
});

it('refuses collisions against existing unselected file nodes', function (string $source) {
    mkdir($this->publisher->directory.'/en');
    $file = $this->publisher->directory.'/en/ui.php';
    file_put_contents($file, $source);
    expect(fn () => $this->publisher->prepare('publish', $this->revisions, 9, 'Publish'))->toThrow(RuntimeException::class, 'Cannot replace');
    expect(file_get_contents($file))->toBe($source)->and(is_file($this->publisher->directory.'/de/ui.php'))->toBeFalse();
})->with(["<?php return ['button' => 'scalar'];", "<?php return ['button'=>['cancel'=>['child'=>'Nested']]];"]);

it('refuses an overlapping unfinished run and altered plan fingerprint', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->prepare('another', $this->revisions, 9, 'Other run'))->toThrow(RuntimeException::class, 'overlaps');
    expect(fn () => $this->publisher->publish('publish', str_repeat('0', 64)))->toThrow(RuntimeException::class, 'plan changed');
    expect(is_file($this->publisher->directory.'/de/ui.php'))->toBeFalse();
});

it('does not record another publication when all files already match', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    $this->publisher->publish('publish', $plan['fingerprint']);
    $events = $this->baseline->rows('events');
    expect(fn () => $this->publisher->prepare('unchanged', $this->revisions, 9, 'Repeat'))->toThrow(RuntimeException::class, 'No file changes');
    expect($this->baseline->rows('events'))->toBe($events);
});

it('rejects a symlink destination without changing the linked file', function () {
    $outside = $this->publisher->directory.'/outside.php';
    file_put_contents($outside, '<?php return [];');
    mkdir($this->publisher->directory.'/en');
    symlink($outside, $this->publisher->directory.'/en/ui.php');
    expect(fn () => $this->publisher->prepare('publish', $this->revisions, 9, 'Publish'))->toThrow(RuntimeException::class, 'Symlink');
    expect(file_get_contents($outside))->toBe('<?php return [];');
});

it('refuses changed files on resume after a committed receipt', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint'], function ($stage) {
        if ($stage === 'after_receipt') {
            throw new RuntimeException('Journal not completed');
        }
    }))->toThrow(RuntimeException::class);
    $file = $this->publisher->directory.'/en/ui.php';
    file_put_contents($file, '<?php return [];');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint']))->toThrow(RuntimeException::class, 'Receipt exists but file state differs');
    expect(file_get_contents($file))->toBe('<?php return [];');
});

it('closes a revoked partial publication without changing its files', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint'], fn () => throw new RuntimeException('Stop')))->toThrow(RuntimeException::class);
    $file = $this->publisher->directory.'/de/ui.php';
    $bytes = file_get_contents($file);
    $id = $this->revisions[1];
    $this->baseline->decideRevision('revoke', $id, $this->baseline->reviewSnapshot($id)['fingerprint'], false, 8, 'Review');
    $events = $this->baseline->rows('events');
    $review = $this->publisher->closurePreview('publish');
    expect($this->baseline->rows('events'))->toBe($events)
        ->and($review['snapshot']['files']['de/ui.php']['status'])->toBe('matches_result')
        ->and($review['snapshot']['files']['en/ui.php']['status'])->toBe('before')
        ->and($review['snapshot']['approval_error'])->not->toBeNull();
    $closed = $this->publisher->close('publish', $review['fingerprint'], 12, 'Stop after review');
    expect($closed['state'])->toBe('closed')->and($closed['closure']['actor'])->toBe(12)
        ->and(file_get_contents($file))->toBe($bytes)
        ->and(is_file($this->publisher->directory.'/en/ui.php'))->toBeFalse();
    $events = $this->baseline->rows('events');
    $this->publisher->close('publish', $review['fingerprint'], 12, 'Stop after review');
    expect($this->baseline->rows('events'))->toBe($events)
        ->and(array_column($events, 'event_type'))->toContain('publication_closed_without_writing')->not->toContain('revisions_published');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint']))->toThrow(RuntimeException::class, 'closed');
});

it('requires fresh file review and preserves external changes on closure', function () {
    $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    $review = $this->publisher->closurePreview('publish');
    mkdir($this->publisher->directory.'/de');
    $file = $this->publisher->directory.'/de/ui.php';
    file_put_contents($file, '<?php return [];');
    expect(fn () => $this->publisher->close('publish', $review['fingerprint'], 12, 'Close'))->toThrow(RuntimeException::class, 'preview changed');
    $review = $this->publisher->closurePreview('publish');
    expect($review['snapshot']['files']['de/ui.php']['status'])->toBe('diverged');
    expect(fn () => $this->publisher->close('publish', $review['fingerprint'], 12, ''))->toThrow(RuntimeException::class);
    $this->publisher->close('publish', $review['fingerprint'], 12, 'Keep external version');
    expect(file_get_contents($file))->toBe('<?php return [];');
    expect($this->publisher->prepare('replacement', $this->revisions, 9, 'New review')['run'])->toBe('replacement');
});

it('blocks publication after a closure receipt even if its journal was not updated', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    $review = $this->publisher->closurePreview('publish');
    expect(fn () => $this->publisher->close('publish', $review['fingerprint'], 12, 'Close', fn () => throw new RuntimeException('Crash')))->toThrow(RuntimeException::class, 'Crash');
    expect($this->publisher->readJournal('publish')['state'])->toBe('prepared');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint']))->toThrow(RuntimeException::class, 'closed');
    expect(fn () => $this->publisher->close('publish', $review['fingerprint'], 13, 'Close'))->toThrow(RuntimeException::class, 'retry differs');
    expect($this->publisher->prepare('replacement', $this->revisions, 9, 'New review')['run'])->toBe('replacement');
    $events = $this->baseline->rows('events');
    expect($this->publisher->close('publish', $review['fingerprint'], 12, 'Close')['state'])->toBe('closed')
        ->and($this->baseline->rows('events'))->toBe($events)
        ->and(is_file($this->publisher->directory.'/de/ui.php'))->toBeFalse();
});

it('keeps a run resumable when recording closure fails', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    $review = $this->publisher->closurePreview('publish');
    $this->baseline->db->exec("CREATE TRIGGER fail_closure BEFORE INSERT ON events WHEN NEW.event_type='publication_closed_without_writing' BEGIN SELECT RAISE(ABORT, 'Closure failed'); END");
    expect(fn () => $this->publisher->close('publish', $review['fingerprint'], 12, 'Close'))->toThrow(PDOException::class);
    expect($this->publisher->readJournal('publish')['state'])->toBe('prepared');
    $this->baseline->db->exec('DROP TRIGGER fail_closure');
    expect($this->publisher->publish('publish', $plan['fingerprint'])['state'])->toBe('complete');
});

it('does not close a successful receipt as an unfinished publication', function () {
    $plan = $this->publisher->prepare('publish', $this->revisions, 9, 'Publish');
    expect(fn () => $this->publisher->publish('publish', $plan['fingerprint'], function ($stage) {
        if ($stage === 'after_receipt') {
            throw new RuntimeException('Crash');
        }
    }))->toThrow(RuntimeException::class);
    expect(fn () => $this->publisher->closurePreview('publish'))->toThrow(RuntimeException::class, 'Successful receipt');
    expect(fn () => $this->publisher->close('publish', 'invalid', 12, 'Close'))->toThrow(RuntimeException::class, 'Successful receipt');
});
