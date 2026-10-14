<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchPipelineRun;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class TranslationWorkbenchPipelineAccess
{
    public function validateOptions(string $runId, array $actual): void
    {
        $run = TranslationWorkbenchPipelineRun::query()->findOrFail((int) $runId);
        $expected = collect($run->options ?? [])->except(['started_from', 'user_id'])->all();
        ksort($actual);
        ksort($expected);
        if ($actual !== $expected) {
            throw new AuthorizationException('Pipeline options differ from the stored UI request. Start a new run from the Workbench.');
        }
    }

    public function authorize(?string $runId): void
    {
        // Standalone CLI execution retains its existing OS-level access model.
        if ($runId === null) {
            return;
        }

        if (! ctype_digit($runId) || (int) $runId < 1) {
            throw new AuthorizationException('Invalid pipeline run ID; no standalone fallback is allowed.');
        }

        $run = TranslationWorkbenchPipelineRun::query()->find((int) $runId);
        if (! $run || $run->command !== 'translation:workbench'
            || ! in_array($run->status, ['pending', 'running'], true)
            || ($run->options['started_from'] ?? null) !== 'ui') {
            throw new AuthorizationException('The pipeline run is missing, inactive or has no supported UI actor.');
        }

        $userId = $run->options['user_id'] ?? null;
        $user = $userId ? Auth::createUserProvider()->retrieveById($userId) : null;
        if (! $user || ! method_exists($user, 'hasAnyRole') || ! $user->hasAnyRole(['Admin', 'Super-Admin'])) {
            throw new AuthorizationException('The user who started this pipeline no longer has the required Admin or Super-Admin role.');
        }
    }
}
