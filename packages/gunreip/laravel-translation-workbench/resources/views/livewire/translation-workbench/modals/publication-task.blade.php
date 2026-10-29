<flux:modal name="workbench-publication-task" wire:model="publicationTaskOpen" class="w-full max-w-2xl">
    @if ($publicationTask)
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Prepare publication') }}</flux:heading>
            <flux:text class="break-all"><code>{{ $publicationTask['context']['full_name'] }}</code>
                · {{ $publicationTask['context']['locale'] }} · #{{ $publicationTask['context']['revision_id'] }}</flux:text>
            <flux:field>
                <flux:label>{{ __('Approved saved text') }}</flux:label>
                <flux:textarea readonly rows="5">{{ $publicationTask['context']['value'] }}</flux:textarea>
            </flux:field>
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'publicationReason', 'reasonLabel' => __('Reason for publication')])
            <flux:text>{{ __('Preparation shows the planned language file changes. Files are written only after confirmation in the preview.') }}</flux:text>
            <flux:error name="publicationTask" />
            <div class="flex justify-end gap-3">
                <flux:button variant="outline" wire:click="$set('publicationTaskOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" wire:click="preparePublicationTask">{{ __('Prepare file preview') }}</flux:button>
            </div>
        </div>
    @endif
</flux:modal>
