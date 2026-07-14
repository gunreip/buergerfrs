<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchEventType extends Model
{
    protected $table = 'translation_workbench_event_types';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchTimelineEvent::class, 'event_type_id');
    }
}
