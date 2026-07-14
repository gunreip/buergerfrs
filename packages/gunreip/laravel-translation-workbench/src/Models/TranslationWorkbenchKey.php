<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchKey extends Model
{
    protected $table = 'translation_workbench_keys';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'is_ui_key' => 'boolean',
        'is_dynamic_key' => 'boolean',
        'is_dynamic_multi' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(
            TranslationWorkbenchFinding::class,
            'translation_workbench_key_findings',
            'key_id',
            'finding_id',
        )
            ->withPivot(['relation_type', 'status', 'meta'])
            ->withTimestamps();
    }

    public function keyFindings(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchKeyFinding::class, 'key_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchKeyValue::class, 'key_id');
    }

    public function dynamicValues(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchDynamicKeyValue::class, 'key_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchReview::class, 'key_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchTimelineEvent::class, 'key_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'reviewed_by_user_id');
    }
}
