<?php

namespace App\Support\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use Symfony\Component\Process\Process;

final class ProcessProgress
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly float $heartbeatSeconds = 10,
        private readonly string $unit = 'checks',
    ) {}

    public function run(Process $process, string $name, int $completed, int $total): void
    {
        $startedAt = microtime(true);
        $lastHeartbeat = $startedAt;
        $indicator = $this->output->isDecorated()
            ? new ProgressIndicator($this->output, 'verbose')
            : null;
        $running = sprintf('[%d/%d %s completed] running: %s', $completed, $total, $this->unit, $name);
        $terminal = new Terminal;
        // ProgressIndicator clears one physical line, not wrapped terminal rows.
        // Reserve room for its spinner, elapsed time and the terminal's final column.
        $fitMessage = static fn (): string => OutputFormatter::escape(mb_strimwidth(
            str_replace(["\r", "\n", "\t"], ' ', $running),
            0, max(1, $terminal->getWidth() - 24), '…', 'UTF-8',
        ));
        $displayMessage = $fitMessage();
        $name = OutputFormatter::escape($name);
        $escapedRunning = OutputFormatter::escape($running);

        if ($indicator) {
            if ($displayMessage !== $escapedRunning) {
                $this->output->writeln($name);
            }
            $indicator->start($displayMessage);
        } else {
            $this->output->writeln($escapedRunning.' · 0 s');
        }

        $finished = false;
        try {
            $process->start();
            while ($process->isRunning()) {
                $process->checkTimeout();
                if ($indicator && ($resizedMessage = $fitMessage()) !== $displayMessage) {
                    $displayMessage = $resizedMessage;
                    $indicator->setMessage($displayMessage);
                }
                $indicator?->advance();
                $now = microtime(true);
                if (! $indicator && $now - $lastHeartbeat >= $this->heartbeatSeconds) {
                    $this->output->writeln($escapedRunning.sprintf(' · %.1f s', $now - $startedAt));
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
                '[%d/%d %s completed] %s: %s · %.1f s',
                $completed + 1,
                $total,
                $this->unit,
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
