<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationWorkbenchLangValue extends Model
{
    protected $table = 'translation_workbench_lang_values';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'scan_count' => 'integer',
    ];
}
