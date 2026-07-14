<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationWorkbenchSourceFile extends Model
{
    protected $table = 'translation_workbench_source_files';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'scan_count' => 'integer',
    ];

    public function findings(): HasMany
    {
        return $this->hasMany(TranslationWorkbenchFinding::class, 'source_file_id');
    }
}
