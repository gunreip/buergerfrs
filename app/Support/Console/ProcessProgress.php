<?php

namespace App\Support\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class ProcessProgress
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly float $heartbeatSeconds = 10,
    ) {}

    public function run(Process $process, string $name, int $completed, int $total): void
    {
        $startedAt = microtime(true);
        $lastHeartbeat = $startedAt;
        $indicator = $this->output->isDecorated()
            ? new ProgressIndicator($this->output, 'verbose')
            : null;
        $name = OutputFormatter::escape($name);
        $running = sprintf('[%d/%d checks completed] running: %s', $completed, $total, $name);

        if ($indicator) {
            $indicator->start($running);
        } else {
            $this->output->writeln($running.' · 0 s');
        }

        $finished = false;
        try {
            $process->start();
            while ($process->isRunning()) {
                $process->checkTimeout();
                $indicator?->advance();
                $now = microtime(true);
                if (! $indicator && $now - $lastHeartbeat >= $this->heartbeatSeconds) {
                    $this->output->writeln($running.sprintf(' · %.1f s', $now - $startedAt));
                    $lastHeartbeat = $now;
                }
                usleep(100_000);
            }
            // Drain remaining output and retain Symfony's signal/error handling.
            $process->wait();
            $finished = true;
        } finally {
            $passed = $finished && $process->isSuccessful();
            $message = sprintf(
                '[%d/%d checks completed] %s: %s · %.1f s',
                $completed + 1,
                $total,
                $passed ? 'passed' : 'failed',
                $name,
                microtime(true) - $startedAt,
            );
            if ($indicator) {
                $indicator->finish($message, $passed ? '✔' : '✘');
            } else {
                $this->output->writeln($message);
            }
        }
    }
}
