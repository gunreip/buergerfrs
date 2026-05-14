<?php

// app/Models/LanguageName.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'language_id',
    'locale',
    'name',
    'native_name',
    'source',
    'is_default',
])]
class LanguageName extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
