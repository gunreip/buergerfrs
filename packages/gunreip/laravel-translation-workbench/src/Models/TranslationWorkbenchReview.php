<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchReview extends Model
{
    protected $table = 'translation_workbench_reviews';

    protected $guarded = [];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'meta' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchKey::class, 'key_id');
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchFinding::class, 'finding_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'reviewed_by_user_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchTimelineEvent::class, 'review_id');
    }
}
