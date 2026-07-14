<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchEntry extends Model
{
    protected $table = 'translation_workbench_entries';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'deleted_segments' => 'array',
        'is_ui_key' => 'boolean',
        'is_ui_candidate_rejected' => 'boolean',
        'is_dynamic_key' => 'boolean',
        'is_dynamic_candidate_rejected' => 'boolean',
        'is_dynamic_multi' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'scan_count' => 'integer',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchEvent::class, 'entry_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchValue::class, 'entry_id');
    }

    public function dynamicValues(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchDynamicValue::class, 'entry_id');
    }

    public function duplicateCandidates(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchDuplicateCandidate::class, 'entry_id');
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchOccurrence::class, 'entry_id');
    }

    public function previousEntry(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_entry_id');
    }

    public function replacedByEntry(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaced_by_entry_id');
    }

    public function replacementEntries(): HasMany
    {
        return $this->hasMany(self::class, 'previous_entry_id');
    }
}
