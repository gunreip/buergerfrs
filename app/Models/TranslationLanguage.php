<?php

// app/Models/TranslationLanguage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationLanguage extends Model
{
    protected $fillable = [
        'locale',
        'name',
        'native_name',
        'is_default',
        'is_enabled_for_translation',
        'is_enabled_for_app',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_enabled_for_translation' => 'boolean',
        'is_enabled_for_app' => 'boolean',
        'sort_order' => 'integer',
    ];
}
