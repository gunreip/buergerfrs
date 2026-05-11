<?php

// app/Models/Language.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'iso639_1',
    'iso639_3',
    'name',
    'native_name',
    'is_active',
    'sort_order',
])]
class Language extends Model
{
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Get the language relationship rows using this language.
     */
    public function personLanguageRows(): HasMany
    {
        return $this->hasMany(PersonLanguage::class);
    }

    /**
     * Get people assigned to this language.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'person_languages')
            ->using(PersonLanguage::class)
            ->withPivot([
                'id',
                'proficiency',
                'is_native',
                'is_primary',
                'preferred_for_communication',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }
}
