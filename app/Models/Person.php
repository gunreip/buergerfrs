<?php

// app/Models/Person.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'person_number',
    'first_name',
    'last_name',
    'date_of_birth',
])]
class Person extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the person/client relationship rows.
     */
    public function clientPeople(): HasMany
    {
        return $this->hasMany(ClientPerson::class);
    }

    /**
     * Get the clients assigned to this person.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_person')
            ->using(ClientPerson::class)
            ->withPivot([
                'id',
                'relationship_type',
                'status',
                'is_primary',
                'starts_at',
                'ends_at',
                'verified_at',
                'verified_by_user_id',
                'created_by_user_id',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get the login user assigned to this person.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the person's display name.
     */
    public function displayName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
