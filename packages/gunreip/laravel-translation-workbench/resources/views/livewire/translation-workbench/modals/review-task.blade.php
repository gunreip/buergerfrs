<flux:modal name="workbench-review-task" wire:model="reviewTaskOpen" class="w-full max-w-xl">
    @if ($reviewTask)
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Review saved translation') }}</flux:heading>
            <flux:text><code>{{ $reviewTask['context']['full_name'] }}</code> · {{ $reviewTask['context']['locale'] }} · #{{ $reviewTask['context']['revision_id'] }}</flux:text>
            <flux:field>
                <flux:label>{{ __('Saved revision value') }}</flux:label>
                <flux:textarea readonly rows="5" :lang="$reviewTask['context']['locale']">{{ $reviewTask['context']['value'] }}</flux:textarea>
            </flux:field>
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'reviewTaskReason', 'reasonLabel' => __('Reason for review decision')])
            <flux:text>{{ __('This decision is recorded with your user and reason. It does not write language files or source code.') }}</flux:text>
            <flux:error name="reviewTask" />
            <div class="flex flex-wrap justify-end gap-3">
                <flux:button variant="ghost" wire:click="$set('reviewTaskOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="outline" wire:click="decideReviewTask(false)">{{ __('Return for correction') }}</flux:button>
                <flux:button variant="primary" wire:click="decideReviewTask(true)">{{ __('Approve revision') }}</flux:button>
            </div>
        </div>
    @endif
</flux:modal>
