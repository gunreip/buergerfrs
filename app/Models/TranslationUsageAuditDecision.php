<?php

// app/Models/TranslationUsageAuditDecision.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationUsageAuditDecision extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'audit_type',
        'normalized_value_hash',
        'normalized_value',
        'source_locale',
        'source_value',
        'suggested_translation_key',
        'target_translation_key',
        'decision_action',
        'decision_status',
        'review_note',
        'snapshot',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'snapshot' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function usages()
    {
        return $this->hasMany(TranslationUsageAuditDecisionUsage::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
