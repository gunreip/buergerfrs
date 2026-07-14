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
    'iso639_2_b',
    'iso639_2_t',
    'iso639_3',
    'name',
    'native_name',
    'scope',
    'type',
    'macrolanguage_code',
    'default_script',
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
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->orderBy('iso639_1')
            ->orderBy('iso639_3');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get localized names for this language.
     */
    public function names(): HasMany
    {
        return $this->hasMany(LanguageName::class);
    }

    /**
     * Get locales assigned to this language.
     */
    public function locales(): HasMany
    {
        return $this->hasMany(Locale::class);
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
                'can_speak',
                'can_read',
                'can_write',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }
}
