<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchPipelineRun extends Model
{
    protected $table = 'translation_workbench_pipeline_runs';

    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'summary' => 'array',
        'meta' => 'array',
        'current_step' => 'integer',
        'total_steps' => 'integer',
        'exit_code' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchPipelineRunStep::class, 'pipeline_run_id')
            ->orderBy('step_number');
    }
}
