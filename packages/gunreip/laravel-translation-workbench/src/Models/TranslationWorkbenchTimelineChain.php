<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationWorkbenchTimelineChain extends Model
{
    protected $table = 'translation_workbench_timeline_chains';

    protected $guarded = [];

    protected $casts = [
        'key_ids' => 'array',
        'finding_ids' => 'array',
        'review_ids' => 'array',
        'timeline_event_ids' => 'array',
        'lang_value_ids' => 'array',
        'related_translation_keys' => 'array',
        'relation_summary' => 'array',
        'lang_value_summary' => 'array',
        'timeline_event_summary' => 'array',
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];
}
