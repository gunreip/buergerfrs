<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchKeyValue extends Model
{
    protected $table = 'translation_workbench_key_values';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchKey::class, 'key_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'reviewed_by_user_id');
    }
}
