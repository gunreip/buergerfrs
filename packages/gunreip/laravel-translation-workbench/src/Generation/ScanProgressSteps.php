<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Progress belongs to a particular pass, even when a later pass repeats a phase. */
final class ScanProgressSteps
{
    private array $steps = [];

    public function advance(string $phase, int $done, int $total): array
    {
        // The planner reports a combined read/compare scale. Each UI row has its own scale.
        if (in_array($phase, ['scanning', 'comparing'], true)) {
            $files = intdiv(max(1, $total) - 1, 2);
            $done = $phase === 'comparing' ? $done - $files : $done;
            $total = $files;
        } elseif ($phase === 'preview_complete') {
            $done = $total = 1;
        }
        $index = array_key_last($this->steps);
        if ($index === null || $this->steps[$index]['phase'] !== $phase) {
            if ($index !== null) {
                $this->steps[$index]['status'] = 'completed';
                $this->steps[$index]['percent'] = 100;
            }
            $this->steps[] = ['phase' => $phase, 'status' => 'running', 'percent' => 0];
            $index = array_key_last($this->steps);
        }
        $percent = $total > 0 ? (int) floor(100 * $done / $total) : 100;
        $this->steps[$index]['percent'] = max($this->steps[$index]['percent'], min(100, max(0, $percent)));
        return $this->steps;
    }

    public function finish(bool $successful): array
    {
        $index = array_key_last($this->steps);
        if ($index !== null) {
            $this->steps[$index]['status'] = $successful ? 'completed' : 'failed';
            if ($successful) $this->steps[$index]['percent'] = 100;
        }
        return $this->steps;
    }
}
