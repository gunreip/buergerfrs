<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationWorkbenchTimelineEvent extends Model
{
    protected $table = 'translation_workbench_timeline_events';

    protected $guarded = [];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'context' => 'array',
    ];

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchEventType::class, 'event_type_id');
    }

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchKey::class, 'key_id');
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchFinding::class, 'finding_id');
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchReview::class, 'review_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by_user_id');
    }
}
