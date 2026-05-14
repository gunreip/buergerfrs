<?php

// app/Models/AddressFormat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'key',
    'country_id',
    'country_code',
    'format',
    'local_format',
    'required_fields',
    'uppercase_fields',
    'postal_code_pattern',
    'administrative_area_type',
    'locality_type',
    'dependent_locality_type',
    'postal_code_type',
    'source',
])]

class AddressFormat extends Model
{
    protected function casts(): array
    {
        return [
            'required_fields' => 'array',
            'uppercase_fields' => 'array',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
