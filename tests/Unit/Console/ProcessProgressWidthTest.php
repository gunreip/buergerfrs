<?php

use App\Support\Console\ProcessProgress;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;

it('keeps every animated update on one terminal row for long step names', function (): void {
    $previousColumns = getenv('COLUMNS');
    putenv('COLUMNS=60');
    try {
        $name = str_repeat('Längerer Translation-Schritt ', 5);
        $output = new BufferedOutput(decorated: true);
        $process = new Process([PHP_BINARY, '-r', 'usleep(350000);']);
        (new ProcessProgress($output, unit: 'steps'))->run($process, $name, 15, 19);
        $frames = explode("\r\033[2K", $output->fetch());

        expect(array_shift($frames))->toContain($name);
        $finished = array_pop($frames);
        expect($finished)->toContain('[16/19 steps completed] passed:', $name);
        expect(count($frames))->toBeGreaterThan(1);
        foreach ($frames as $frame) {
            expect($frame)->not->toContain("\n", "\r");
            expect(mb_strwidth($frame, 'UTF-8'))->toBeLessThan(60);
        }
    } finally {
        putenv($previousColumns === false ? 'COLUMNS' : 'COLUMNS='.$previousColumns);
    }
});
