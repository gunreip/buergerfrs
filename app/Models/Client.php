<?php

// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_number',
    'name',
    'legal_name',
    'type',
    'status',
    'description',
])]
class Client extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the client/person relationship rows.
     */
    public function clientPeople(): HasMany
    {
        return $this->hasMany(ClientPerson::class);
    }

    /**
     * Get the people assigned to this client.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'client_person')
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
     * Get the client's display name.
     */
    public function displayName(): string
    {
        return trim($this->name);
    }

    /**
     * Get the client's legal or display name.
     */
    public function legalOrDisplayName(): string
    {
        return trim((string) ($this->legal_name ?: $this->name));
    }

    /**
     * Check whether the client is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
