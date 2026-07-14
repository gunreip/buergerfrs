<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchFinding extends Model
{
    protected $table = 'translation_workbench_findings';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'scan_count' => 'integer',
    ];

    public function sourceFile(): BelongsTo
    {
        return $this->belongsTo(TranslationWorkbenchSourceFile::class, 'source_file_id');
    }

    public function keys(): BelongsToMany
    {
        return $this->belongsToMany(
            TranslationWorkbenchKey::class,
            'translation_workbench_key_findings',
            'finding_id',
            'key_id',
        )
            ->withPivot(['relation_type', 'status', 'meta'])
            ->withTimestamps();
    }

    public function keyFindings(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchKeyFinding::class, 'finding_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchReview::class, 'finding_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchTimelineEvent::class, 'finding_id');
    }
}
