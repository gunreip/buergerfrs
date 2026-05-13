<?php

// app/Models/FallbackReport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FallbackReport extends Model
{
    protected $fillable = [
        'type',
        'key',
        'fallback',
        'fingerprint',
        'context',
        'count',
        'first_seen_at',
        'last_seen_at',
        'reviewed',
        'reviewed_at',
        'reviewed_by_user_id',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'count' => 'integer',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'reviewed' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function scopeOpen($query)
    {
        return $query->where('reviewed', false);
    }

    public function scopeReviewed($query)
    {
        return $query->where('reviewed', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function markReviewed(?int $userId = null, ?string $note = null): void
    {
        $this->forceFill([
            'reviewed' => true,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => $userId,
            'review_note' => $note,
        ])->save();
    }

    public function markUnreviewed(): void
    {
        $this->forceFill([
            'reviewed' => false,
            'reviewed_at' => null,
            'reviewed_by_user_id' => null,
            'review_note' => null,
        ])->save();
    }
}
