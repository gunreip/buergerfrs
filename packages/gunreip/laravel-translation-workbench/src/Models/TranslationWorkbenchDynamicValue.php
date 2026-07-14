<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchDynamicValue extends Model
{
    protected $table = 'translation_workbench_dynamic_values';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEntry::class, 'entry_id');
    }
}
