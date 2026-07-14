<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEventType;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineEvent;
use Illuminate\Support\Facades\Auth;

class TranslationWorkbenchTimelineRecorder
{
    /**
     * Central write path for new Translation Workbench foundation timeline events.
     *
     * All foundation scanner, review, edit and export workflows should write
     * timeline events through this class. Do not create timeline rows ad hoc in
     * commands or Livewire components; extend this recorder instead.
     *
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $context
     */
    public function record(
        string $eventType,
        ?TranslationWorkbenchKey $key = null,
        ?TranslationWorkbenchFinding $finding = null,
        ?TranslationWorkbenchReview $review = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $context = null,
        ?int $createdByUserId = null,
    ): TranslationWorkbenchTimelineEvent {
        $eventTypeModel = $this->eventType($eventType);

        return TranslationWorkbenchTimelineEvent::query()->create([
            'key_id' => $key?->id,
            'finding_id' => $finding?->id,
            'review_id' => $review?->id,
            'event_type_id' => $eventTypeModel->id,
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'context' => $context,
            'created_by_user_id' => $createdByUserId ?? Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $context
     */
    public function recordFindingEvent(
        TranslationWorkbenchFinding $finding,
        string $eventType,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $context = null,
    ): TranslationWorkbenchTimelineEvent {
        return $this->record(
            eventType: $eventType,
            finding: $finding,
            oldValues: $oldValues,
            newValues: $newValues,
            context: $context,
        );
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $context
     */
    public function recordKeyEvent(
        TranslationWorkbenchKey $key,
        string $eventType,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $context = null,
    ): TranslationWorkbenchTimelineEvent {
        return $this->record(
            eventType: $eventType,
            key: $key,
            oldValues: $oldValues,
            newValues: $newValues,
            context: $context,
        );
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $context
     */
    public function recordKeyFindingEvent(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        string $eventType,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $context = null,
    ): TranslationWorkbenchTimelineEvent {
        return $this->record(
            eventType: $eventType,
            key: $key,
            finding: $finding,
            oldValues: $oldValues,
            newValues: $newValues,
            context: $context,
        );
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $context
     */
    public function recordReviewEvent(
        TranslationWorkbenchReview $review,
        string $eventType,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $context = null,
    ): TranslationWorkbenchTimelineEvent {
        return $this->record(
            eventType: $eventType,
            key: $review->key,
            finding: $review->finding,
            review: $review,
            oldValues: $oldValues,
            newValues: $newValues,
            context: $context,
        );
    }

    private function eventType(string $eventType): TranslationWorkbenchEventType
    {
        $eventTypeModel = TranslationWorkbenchEventType::query()->firstOrNew(['key' => $eventType]);
        $eventTypeModel->fill([
            'label' => $eventTypeModel->label ?: str($eventType)->replace('_', ' ')->title()->toString(),
            'category' => $eventTypeModel->category ?: $this->eventCategory($eventType),
            'severity' => $eventTypeModel->severity ?: $this->eventSeverity($eventType),
            'icon' => $eventTypeModel->icon ?: $this->eventIcon($eventType),
            'color' => $eventTypeModel->color ?: $this->eventColor($eventType),
            'is_active' => $eventTypeModel->exists ? $eventTypeModel->is_active : true,
            'meta' => $eventTypeModel->meta ?: [
                'source' => 'translation_workbench_timeline_recorder',
                'auto_created' => true,
            ],
        ]);
        $eventTypeModel->save();

        return $eventTypeModel;
    }

    private function eventCategory(string $eventType): string
    {
        return match (true) {
            str_contains($eventType, 'review') => 'review',
            str_contains($eventType, 'value') => 'translation',
            str_contains($eventType, 'relation') => 'relation',
            str_contains($eventType, 'source_file'),
            str_contains($eventType, 'finding'),
            str_contains($eventType, 'key_candidate') => 'scanner',
            default => 'system',
        };
    }

    private function eventSeverity(string $eventType): string
    {
        return match (true) {
            str_contains($eventType, 'deleted'),
            str_contains($eventType, 'obsolete'),
            str_contains($eventType, 'removed') => 'warning',
            str_contains($eventType, 'failed'),
            str_contains($eventType, 'error') => 'danger',
            default => 'info',
        };
    }

    private function eventIcon(string $eventType): string
    {
        return match (true) {
            str_contains($eventType, 'source_file') => 'file-code',
            str_contains($eventType, 'finding') => 'scan-search',
            str_contains($eventType, 'key') => 'key-round',
            str_contains($eventType, 'relation') => 'git-branch',
            str_contains($eventType, 'review') => 'badge-check',
            str_contains($eventType, 'value') => 'languages',
            default => 'activity',
        };
    }

    private function eventColor(string $eventType): string
    {
        return match ($this->eventSeverity($eventType)) {
            'warning' => 'amber',
            'danger' => 'red',
            default => 'sky',
        };
    }
}
