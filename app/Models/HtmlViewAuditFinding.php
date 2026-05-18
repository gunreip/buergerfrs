<?php

// app/Models/HtmlViewAuditFinding.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HtmlViewAuditFinding extends Model
{
    protected $fillable = [
        'fingerprint',
        'source_fingerprint',
        'previous_finding_id',
        'status',
        'section',
        'type',
        'file',
        'tag',
        'closing_tag',
        'opened_line',
        'closing_line',
        'expected_closing',
        'actual_closing',
        'first_seen_at',
        'last_seen_at',
        'resolved_at',
        'resolved_source',
        'ignored_at',
        'ignored_by',
        'comment',
        'snapshot_payload',
    ];

    protected function casts(): array
    {
        return [
            'opened_line' => 'integer',
            'closing_line' => 'integer',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'resolved_at' => 'datetime',
            'ignored_at' => 'datetime',
            'snapshot_payload' => 'array',
        ];
    }

    public function previousFinding(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_finding_id');
    }

    public function ignoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ignored_by');
    }
}
