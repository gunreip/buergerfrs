<?php

use Gunreip\TranslationWorkbench\Generation\ReplacementFileAccess;

it('preserves shared directory write permissions when replacing a read-only-for-group file', function () {
    $directory = sys_get_temp_dir().'/tw-access-'.bin2hex(random_bytes(8));
    mkdir($directory, 0770);
    chmod($directory, 0770);
    $target = $directory.'/source.php';
    file_put_contents($target, 'before');
    chmod($target, 0644);
    $temp = ReplacementFileAccess::temporary($target, '.test-');
    try {
        file_put_contents($temp, 'after');
        ReplacementFileAccess::permissions($temp, $target);
        rename($temp, $target);
        clearstatcache(true, $target);
        expect(file_get_contents($target))->toBe('after')
            ->and(fileperms($target) & 0777)->toBe(0664);
    } finally {
        if (is_file($temp)) unlink($temp);
        unlink($target);
        rmdir($directory);
    }
});

it('does not add group write access in a private publication directory', function () {
    $directory = sys_get_temp_dir().'/tw-access-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    chmod($directory, 0700);
    $target = $directory.'/new.php';
    $temp = ReplacementFileAccess::temporary($target, '.test-');
    try {
        ReplacementFileAccess::permissions($temp, $target);
        clearstatcache(true, $temp);
        expect(fileperms($temp) & 0777)->toBe(0644);
    } finally {
        unlink($temp);
        rmdir($directory);
    }
});

it('keeps newly published and republished language files writable by the shared group', function () {
    $directory = sys_get_temp_dir().'/tw-publish-access-'.bin2hex(random_bytes(8));
    mkdir($directory, 0770);
    chmod($directory, 0770);
    mkdir($directory.'/en', 0770);
    chmod($directory.'/en', 0770);
    $publisher = new \Gunreip\TranslationWorkbench\Generation\GenerationPublisher(Mockery::mock(PDO::class), $directory);
    $write = new ReflectionMethod($publisher, 'write');
    $target = $directory.'/en/ui.php';
    try {
        $write->invoke($publisher, 'en/ui.php', '<?php return [];');
        clearstatcache(true, $target);
        expect(fileperms($target) & 0777)->toBe(0664);
        chmod($target, 0644);
        $write->invoke($publisher, 'en/ui.php', '<?php return ["saved" => true];');
        clearstatcache(true, $target);
        expect(fileperms($target) & 0777)->toBe(0664)
            ->and(file_get_contents($target))->toBe('<?php return ["saved" => true];');
    } finally {
        if (is_file($target)) unlink($target);
        rmdir($directory.'/en');
        rmdir($directory);
    }
});
