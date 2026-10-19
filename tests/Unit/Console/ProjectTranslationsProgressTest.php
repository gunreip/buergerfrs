<?php

use App\Console\Commands\ProjectTranslations;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

uses(Tests\TestCase::class);

function translationProgressCommand(?int $failAt = null): ProjectTranslations
{
    $command = new class($failAt) extends ProjectTranslations
    {
        public array $commands = [];

        public function __construct(private readonly ?int $failAt)
        {
            parent::__construct();
        }

        protected function createProcess(array $command): Process
        {
            $this->commands[] = $command;

            return new Process([PHP_BINARY, '-r', count($this->commands) === $this->failAt
                ? 'fwrite(STDERR, "test failure details\n"); exit(2);'
                : 'echo "step output\n";']);
        }

        protected function logRunActivity(string $event, string $description, array $properties = []): void {}
    };
    $command->setLaravel(app());

    return $command;
}

it('counts selected translation steps and forwards options as separate process arguments', function (array $options, int $total): void {
    $command = translationProgressCommand();
    $tester = new CommandTester($command);
    $tester->execute($options + ['--paths' => 'resources/views,a path', '--locales' => 'de,en']);

    expect($tester->getStatusCode())->toBe(0)
        ->and($command->commands)->toHaveCount($total)
        ->and($tester->getDisplay())->toContain("[$total/$total steps completed] passed:", 'step output', 'Translation-Workflow abgeschlossen');
    expect($command->commands[3])->toContain('--paths=resources/views,a path', '--no-interaction', '--no-ansi');
    foreach ($command->commands as $tokens) {
        if (in_array('translations:export-lang-files', $tokens, true)) {
            expect($tokens)->toContain('--locales=de,en', '--dry-run');
        }
        if (in_array('translations:usage-decisions:apply', $tokens, true)) {
            expect($tokens)->toContain('--write');
        }
    }
})->with([
    'minimal' => [['--skip-export' => true, '--skip-audits' => true], 4],
    'normal' => [[], 13],
    'usage decisions' => [['--apply-usage-decisions' => true], 19],
]);

it('shows diagnostics and stops after a failed translation step', function (): void {
    $command = translationProgressCommand(failAt: 2);
    $tester = new CommandTester($command);
    $tester->execute(['--skip-export' => true, '--skip-audits' => true]);

    expect($tester->getStatusCode())->toBe(1)
        ->and($command->commands)->toHaveCount(2)
        ->and($tester->getDisplay())->toContain('[2/4 steps completed] failed:', 'test failure details', 'exit code 2')
        ->not->toContain('Translation-Workflow abgeschlossen');
});

it('includes patch checks and follow-up audits only when a patch exists', function (bool $hasPatch, int $total): void {
    \Illuminate\Support\Facades\File::shouldReceive('exists')
        ->with(base_path('storage/audits/translations/diffs/latest.patch'))->once()->andReturn($hasPatch);
    if ($hasPatch) {
        \Illuminate\Support\Facades\File::shouldReceive('get')
            ->with(base_path('storage/audits/translations/diffs/latest.patch'))->once()->andReturn('test patch');
    }
    $command = translationProgressCommand();
    $tester = new CommandTester($command);
    $tester->execute(['--apply-diffs' => true, '--skip-export' => true]);

    expect($tester->getStatusCode())->toBe(0)
        ->and($command->commands)->toHaveCount($total)
        ->and($tester->getDisplay())->toContain("[$total/$total steps completed] passed:");
    if ($hasPatch) {
        expect(array_slice($command->commands[4], 0, 3))->toBe(['git', 'apply', '--check'])
            ->and(array_slice($command->commands[5], 0, 2))->toBe(['git', 'apply']);
    }
})->with([[true, 15], [false, 11]]);
