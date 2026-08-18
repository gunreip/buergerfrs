<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchPipelineRun;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchPipelineRunStep;
use Illuminate\Support\Facades\Schema;

class TranslationWorkbenchPipelineRunTracker
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function create(string $command, array $options = []): ?TranslationWorkbenchPipelineRun
    {
        if (! $this->tablesExist()) {
            return null;
        }

        return TranslationWorkbenchPipelineRun::query()->create([
            'command' => $command,
            'options' => $options,
            'status' => 'pending',
            'summary' => [],
            'meta' => [],
        ]);
    }

    public function find(?int $runId): ?TranslationWorkbenchPipelineRun
    {
        if (! $runId || ! $this->tablesExist()) {
            return null;
        }

        return TranslationWorkbenchPipelineRun::query()->find($runId);
    }

    public function latest(): ?TranslationWorkbenchPipelineRun
    {
        if (! $this->tablesExist()) {
            return null;
        }

        return TranslationWorkbenchPipelineRun::query()
            ->with('steps')
            ->latest('id')
            ->first();
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @param  array<string, mixed>  $summary
     */
    public function start(TranslationWorkbenchPipelineRun $run, array $steps, array $summary = []): void
    {
        $run->forceFill([
            'status' => 'running',
            'current_step' => 0,
            'total_steps' => count($steps),
            'current_step_label' => null,
            'current_step_command' => null,
            'exit_code' => null,
            'error_message' => null,
            'summary' => $summary,
            'started_at' => now(),
            'finished_at' => null,
        ])->save();

        foreach ($steps as $index => $step) {
            TranslationWorkbenchPipelineRunStep::query()->updateOrCreate(
                [
                    'pipeline_run_id' => $run->id,
                    'step_number' => $index + 1,
                ],
                [
                    'total_steps' => count($steps),
                    'label' => (string) ($step['label'] ?? ''),
                    'command' => (string) ($step['command'] ?? ''),
                    'arguments' => (array) ($step['arguments'] ?? []),
                    'status' => 'pending',
                    'exit_code' => null,
                    'duration_ms' => null,
                    'error_message' => null,
                    'summary' => [],
                    'started_at' => null,
                    'finished_at' => null,
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $step
     */
    public function startStep(TranslationWorkbenchPipelineRun $run, int $stepNumber, int $totalSteps, array $step): void
    {
        $run->forceFill([
            'status' => 'running',
            'current_step' => $stepNumber,
            'total_steps' => $totalSteps,
            'current_step_label' => (string) ($step['label'] ?? ''),
            'current_step_command' => (string) ($step['command'] ?? ''),
        ])->save();

        TranslationWorkbenchPipelineRunStep::query()->updateOrCreate(
            [
                'pipeline_run_id' => $run->id,
                'step_number' => $stepNumber,
            ],
            [
                'total_steps' => $totalSteps,
                'label' => (string) ($step['label'] ?? ''),
                'command' => (string) ($step['command'] ?? ''),
                'arguments' => (array) ($step['arguments'] ?? []),
                'status' => 'running',
                'started_at' => now(),
                'finished_at' => null,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $meta
     */
    public function finishStep(
        TranslationWorkbenchPipelineRun $run,
        int $stepNumber,
        int $exitCode,
        int $durationMs,
        array $summary = [],
        array $meta = [],
    ): void {
        TranslationWorkbenchPipelineRunStep::query()
            ->where('pipeline_run_id', $run->id)
            ->where('step_number', $stepNumber)
            ->update([
                'status' => $exitCode === 0 ? 'finished' : 'failed',
                'exit_code' => $exitCode,
                'duration_ms' => $durationMs,
                'summary' => $summary,
                'meta' => $meta,
                'finished_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    public function failStep(
        TranslationWorkbenchPipelineRun $run,
        int $stepNumber,
        string $errorMessage,
        int $durationMs,
        array $summary = [],
    ): void {
        TranslationWorkbenchPipelineRunStep::query()
            ->where('pipeline_run_id', $run->id)
            ->where('step_number', $stepNumber)
            ->update([
                'status' => 'failed',
                'exit_code' => 1,
                'duration_ms' => $durationMs,
                'error_message' => $errorMessage,
                'summary' => $summary,
                'finished_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    public function finish(TranslationWorkbenchPipelineRun $run, int $exitCode, array $summary = []): void
    {
        $run->forceFill([
            'status' => $exitCode === 0 ? 'finished' : 'failed',
            'exit_code' => $exitCode,
            'summary' => $summary,
            'finished_at' => now(),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    public function fail(TranslationWorkbenchPipelineRun $run, string $errorMessage, array $summary = []): void
    {
        $run->forceFill([
            'status' => 'failed',
            'exit_code' => 1,
            'error_message' => $errorMessage,
            'summary' => $summary,
            'finished_at' => now(),
        ])->save();
    }

    public function tablesExist(): bool
    {
        return Schema::hasTable('translation_workbench_pipeline_runs')
            && Schema::hasTable('translation_workbench_pipeline_run_steps');
    }
}
