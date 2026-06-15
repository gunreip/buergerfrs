<?php

// app/Models/TranslationLangBallastDecision.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationLangBallastDecision extends Model
{
    public const ACTION_REMOVE_FROM_LANG = 'remove_from_lang';

    public const ACTION_ADD_TO_LANG = 'add_to_lang';

    public const ACTION_REVIEW = 'review';

    public const STATUS_OPEN = 'open';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_IGNORED = 'ignored';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'locale',
        'namespace',
        'group',
        'key',
        'file',
        'file_key',
        'value_hash',
        'candidate_hash',
        'translation_key_id',
        'translation_value_id',
        'action_candidate',
        'decision_status',
        'decision_note',
        'reason_detail',
        'lang_file_action_reason',
        'reviewed_at',
        'reviewed_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function translationValue(): BelongsTo
    {
        return $this->belongsTo(TranslationValue::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
