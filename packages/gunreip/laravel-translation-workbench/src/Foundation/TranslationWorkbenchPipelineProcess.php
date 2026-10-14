<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchPipelineRun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

/** Local process liveness only. Lock files must never be removed while a run can exist. */
class TranslationWorkbenchPipelineProcess
{
    public function acquire(int $id, bool $create = true): mixed
    {
        $path = storage_path('translation-workbench/pipeline-locks/'.$id.'.lock');
        if ($create) {
            File::ensureDirectoryExists(dirname($path));
        }
        $handle = @fopen($path, $create && ! is_file($path) ? 'c+' : 'r');
        if ($handle === false) {
            throw new \RuntimeException('The pipeline process lock cannot be inspected.');
        }
        if (! flock($handle, LOCK_EX | LOCK_NB, $blocked)) {
            fclose($handle);
            if ($blocked) {
                return null;
            }
            throw new \RuntimeException('The pipeline process lock is unavailable.');
        }

        return $handle;
    }

    public function state(TranslationWorkbenchPipelineRun $run): string
    {
        if ($run->status !== 'running' || ($run->meta['process_host'] ?? null) !== gethostname()) {
            return 'unknown';
        }
        try {
            $handle = $this->acquire($run->id, false);
        } catch (\RuntimeException) {
            return 'unknown';
        }
        if ($handle === null) {
            return 'active';
        }
        fclose($handle);

        return 'interrupted';
    }

    public function markInterrupted(int $id): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $run = TranslationWorkbenchPipelineRun::query()->find($id);
        if (! $run || $this->state($run) !== 'interrupted') {
            throw ValidationException::withMessages(['pipelineRun' => __('The process is active or its state cannot be established. No status was changed.')]);
        }
        $handle = $this->acquire($id, false);
        if ($handle === null) {
            throw ValidationException::withMessages(['pipelineRun' => __('The process is active. No status was changed.')]);
        }
        try {
            DB::transaction(function () use ($id): void {
                $run = TranslationWorkbenchPipelineRun::query()->lockForUpdate()->findOrFail($id);
                if ($run->status !== 'running' || ($run->meta['process_host'] ?? null) !== gethostname()) {
                    throw ValidationException::withMessages(['pipelineRun' => __('The run changed. Refresh its status and try again.')]);
                }
                $message = __('The background process ended without completing the run. Marked as interrupted; completed work was not rolled back.');
                $run->steps()->where('status', 'running')->update([
                    'status' => 'failed', 'exit_code' => 1, 'error_message' => $message, 'finished_at' => now(),
                ]);
                $run->forceFill(['meta' => [
                    ...($run->meta ?? []),
                    'interruption_confirmed_by' => Auth::id(),
                    'interruption_confirmed_at' => now()->toISOString(),
                ]])->save();
                app(TranslationWorkbenchPipelineRunTracker::class)->fail($run, $message, [
                    ...($run->summary ?? []), 'exit_code' => 1, 'interrupted' => true,
                ]);
            });
        } finally {
            fclose($handle);
        }
    }
}
