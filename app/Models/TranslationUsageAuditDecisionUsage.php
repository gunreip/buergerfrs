<?php

// app/Models/TranslationUsageAuditDecisionUsage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationUsageAuditDecisionUsage extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'translation_usage_audit_decision_id',
        'translation_key_id',
        'current_translation_key',
        'target_translation_key',
        'file',
        'line',
        'detected_function',
        'classification',
        'reason',
        'is_stale',
        'raw',
        'original_raw',
        'include_in_change',
        'change_status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'line' => 'integer',
        'is_stale' => 'boolean',
        'include_in_change' => 'boolean',
    ];

    public function decision()
    {
        return $this->belongsTo(TranslationUsageAuditDecision::class, 'translation_usage_audit_decision_id');
    }

    public function translationKey()
    {
        return $this->belongsTo(TranslationKey::class);
    }
}
