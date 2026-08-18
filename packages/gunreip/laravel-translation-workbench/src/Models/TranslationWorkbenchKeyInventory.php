<?php

namespace Gunreip\TranslationWorkbench\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationWorkbenchKeyInventory extends Model
{
    protected $table = 'translation_workbench_key_inventory';

    protected $guarded = [];

    protected $casts = [
        'is_shared' => 'boolean',
        'is_ui' => 'boolean',
        'is_dynamic' => 'boolean',
        'is_dynamic_multi' => 'boolean',
        'has_active_code_usage' => 'boolean',
        'has_only_obsolete_code_usage' => 'boolean',
        'has_lang_values' => 'boolean',
        'is_orphaned_lang_value' => 'boolean',
        'candidate_for_lang_delete' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'scan_count' => 'integer',
        'meta' => 'array',
    ];
}
