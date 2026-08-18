<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchPipelineRunStep extends Model
{
    protected $table = 'translation_workbench_pipeline_run_steps';

    protected $guarded = [];

    protected $casts = [
        'arguments' => 'array',
        'summary' => 'array',
        'meta' => 'array',
        'step_number' => 'integer',
        'total_steps' => 'integer',
        'exit_code' => 'integer',
        'duration_ms' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function pipelineRun(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchPipelineRun::class, 'pipeline_run_id');
    }
}
