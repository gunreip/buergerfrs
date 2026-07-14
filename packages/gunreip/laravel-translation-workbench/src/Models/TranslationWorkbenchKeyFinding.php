<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchKeyFinding extends Model
{
    protected $table = 'translation_workbench_key_findings';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchKey::class, 'key_id');
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchFinding::class, 'finding_id');
    }
}
