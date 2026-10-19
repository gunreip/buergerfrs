<flux:modal name="translation-workbench-value-approval" wire:model="valueApprovalModalOpen" class="w-full max-w-4xl">
    <div class="space-y-4">
        <flux:heading size="lg">{{ __('Review stored values') }}</flux:heading>
        <flux:text>{{ __('This preview shows saved database values only. Unsaved editor input is not approved. Approval does not publish or change the existing export rules.') }}</flux:text>
        <flux:text>{{ __('A previous key review or an options reviewed-at timestamp is not an approval of the current value revision. Existing translations are not automatically approved or removed.') }}</flux:text>
        <flux:error name="valueApproval" />
        @forelse ($valueApprovalRows as $index => $row)
            <flux:card class="space-y-3" wire:key="value-approval-{{ $row['snapshot']['kind'] }}-{{ $row['snapshot']['value_id'] }}">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge>{{ $row['snapshot']['locale'] }}</flux:badge>
                    <flux:badge>{{ __('Revision :revision', ['revision' => $row['snapshot']['value_revision']]) }}</flux:badge>
                    <flux:badge :color="$row['state'] === 'approved' ? 'green' : 'amber'">
                        {{ match ($row['state']) { 'approved' => __('Current revision approved'), 'changed' => __('Changed since approval'), default => __('No revision approval') } }}
                    </flux:badge>
                </div>
                <flux:text class="break-all font-mono">{{ $row['snapshot']['translation_key'] }}{{ $row['snapshot']['value_key'] !== null ? ' · '.$row['snapshot']['value_key'] : '' }}</flux:text>
                <flux:text class="whitespace-pre-wrap break-words">{{ $row['snapshot']['value'] }}</flux:text>
                @if ($row['legacy_reviewed_at'])
                    <flux:text>{{ __('Historical reviewed-at timestamp: :date', ['date' => $row['legacy_reviewed_at']]) }}</flux:text>
                @endif
                <flux:button size="sm" variant="outline" icon="check" wire:click="approveStoredValue({{ $index }})" :disabled="$row['state'] === 'approved'" wire:loading.attr="disabled">
                    {{ __('Approve this stored revision') }}
                </flux:button>
            </flux:card>
        @empty
            <flux:text>{{ __('No stored values available for approval in the editor languages.') }}</flux:text>
        @endforelse
        <flux:button variant="outline" icon="document-magnifying-glass" wire:click="previewValuePublication" wire:loading.attr="disabled">
            {{ __('Preview language files') }}
        </flux:button>
        @if ($valuePublicationPreview ?? null)
            @if ($valuePublicationPreview['pruning_requires_separate_review'] > 0)
                <flux:callout color="amber">
                    <flux:callout.heading>{{ __('Published translations would be removed') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('Clearing a stored value does not remove its published text. This preview includes removals and cannot be published here. Review those removals separately.') }}</flux:callout.text>
                </flux:callout>
            @endif
            <flux:callout>
                <flux:callout.heading>{{ __('Language file preview') }}</flux:callout.heading>
                @if (($valuePublicationPreview['values_new'] ?? 0) + ($valuePublicationPreview['values_changed'] ?? 0) === 0 && $valuePublicationPreview['pruning_requires_separate_review'] === 0 && $valuePublicationPreview['values_conflicted'] === 0)
                    <flux:callout.text>{{ __('No new or changed translations to publish.') }}</flux:callout.text>
                @endif
                <flux:button size="sm" variant="outline" icon="arrow-path" wire:click="verifyValuePublication" wire:loading.attr="disabled">
                    {{ __('Check preview again') }}
                </flux:button>
                <flux:button size="sm" variant="primary" wire:click="publishValuePublication" wire:loading.attr="disabled"
                    :disabled="(($valuePublicationPreview['values_new'] ?? 0) + ($valuePublicationPreview['values_changed'] ?? 0) === 0) || $valuePublicationPreview['changes_without_current_approval'] > 0 || $valuePublicationPreview['pruning_requires_separate_review'] > 0 || $valuePublicationPreview['values_conflicted'] > 0 || $valuePublicationPreview['values_excluded_by_legacy_review_filter'] > 0">
                    {{ __('Publish approved values to language files') }}
                </flux:button>
                <flux:callout.text>{{ __('Publishing rechecks this preview, writes language files and records the result. Source code remains unchanged.') }}</flux:callout.text>
                @if ($valuePublicationVerified ?? false)
                    <flux:callout.text>{{ __('The preview matched the data and files at the last check. This does not authorize publication.') }}</flux:callout.text>
                @endif
                <flux:callout.text>{{ __('Read-only comparison. No files are written. This is not a publication authorization; concurrent changes must be checked again before publishing.') }}</flux:callout.text>
                <flux:callout.text>{{ __('Unchanged values retained: :count', ['count' => $valuePublicationPreview['unchanged_values_retained']]) }}</flux:callout.text>
                <flux:callout.text>{{ __('Changes without current approval: :count', ['count' => $valuePublicationPreview['changes_without_current_approval']]) }}</flux:callout.text>
                <flux:callout.text>{{ __('Deletions requiring separate review: :count', ['count' => $valuePublicationPreview['pruning_requires_separate_review']]) }}</flux:callout.text>
                <flux:callout.text>{{ __('Structure conflicts: :count', ['count' => $valuePublicationPreview['values_conflicted']]) }}</flux:callout.text>
                <flux:callout.text>{{ __('Options excluded by the existing reviewed-at export filter: :count', ['count' => $valuePublicationPreview['values_excluded_by_legacy_review_filter']]) }}</flux:callout.text>
                @foreach ($valuePublicationPreview['plans'] as $plan)
                    <flux:heading class="mt-3 break-all font-mono" size="sm">{{ $plan['path'] }}</flux:heading>
                    @foreach ($plan['changes'] as $change)
                        <flux:text class="break-all">{{ $change['translation_key'] }} · {{ $change['approval_state'] === 'approved' ? __('Current revision approved') : __('Current revision not approved') }}</flux:text>
                        <div class="grid gap-2 md:grid-cols-2">
                            <flux:text class="whitespace-pre-wrap break-words">{{ __('Published value') }}: {{ is_string($change['old_value']) ? $change['old_value'] : json_encode($change['old_value'], JSON_UNESCAPED_UNICODE) }}</flux:text>
                            <flux:text class="whitespace-pre-wrap break-words">{{ __('Stored value') }}: {{ is_string($change['new_value']) ? $change['new_value'] : json_encode($change['new_value'], JSON_UNESCAPED_UNICODE) }}</flux:text>
                        </div>
                    @endforeach
                    @foreach ($plan['pruned'] as $removed)
                        <flux:text class="break-all">{{ __('Deletion requires review') }} · {{ $removed['translation_key'] }}</flux:text>
                    @endforeach
                    @foreach ($plan['conflicts'] as $conflict)
                        <flux:text class="break-all">{{ __('Structure conflict') }} · {{ $conflict['translation_key'] }}</flux:text>
                    @endforeach
                @endforeach
            </flux:callout>
        @endif
        <flux:callout>
            <flux:callout.heading>{{ __('Publication runs for this translation') }}</flux:callout.heading>
            <flux:button size="sm" variant="outline" icon="arrow-path" wire:click="refreshValuePublicationRuns" wire:loading.attr="disabled">{{ __('Refresh runs') }}</flux:button>
            <flux:callout.text>{{ __('File status is checked when this list is loaded. Resuming rechecks data, approvals and files; conflicting changes are not overwritten.') }}</flux:callout.text>
            @forelse ($valuePublicationRuns ?? [] as $index => $run)
                <div class="mt-3 space-y-2 rounded border border-zinc-300 p-3 dark:border-zinc-700">
                    <flux:text class="break-all font-mono">{{ $run['id'] }} · {{ $run['created_at'] }}</flux:text>
                    <flux:badge :color="$run['state'] === 'complete' ? 'green' : 'amber'">{{ match ($run['state']) { 'complete' => __('Completed'), 'closed' => __('Closed without further writing'), default => __('Unfinished publication') } }}</flux:badge>
                    <flux:text>{{ $run['recorded'] ? __('Database completion recorded') : ($run['state'] === 'closed' ? __('Closure recorded; publication was not completed') : __('Database completion pending')) }}</flux:text>
                    @if ($run['closure_reason'] ?? null)
                        <flux:text class="whitespace-pre-wrap break-words">{{ __('Closure reason') }}: {{ $run['closure_reason'] }}</flux:text>
                    @endif
                    @if ($run['error'])
                        <flux:text class="whitespace-pre-wrap break-words text-red-600!">{{ $run['error'] }}</flux:text>
                    @endif
                    @foreach ($run['files'] as $file)
                        <flux:text class="break-all">
                            <code>{{ $file['path'] }}</code> ·
                            {{ match ($file['current_state']) {
                                'result' => __('Matches prepared result'),
                                'source' => __('Matches original file state'),
                                default => __('Changed outside this publication'),
                            } }}
                        </flux:text>
                    @endforeach
                    @if (! in_array($run['state'], ['complete', 'closed'], true))
                        <flux:button size="sm" variant="outline" wire:click="resumeValuePublication({{ $index }})" wire:loading.attr="disabled">{{ __('Recheck and resume publication') }}</flux:button>
                        @if (! $run['recorded'])
                            <flux:button size="sm" variant="outline" wire:click="previewValuePublicationClosure({{ $index }})" wire:loading.attr="disabled">{{ __('Review closure without further writing') }}</flux:button>
                        @endif
                    @endif
                </div>
            @empty
                <flux:callout.text>{{ __('No publication runs for this translation.') }}</flux:callout.text>
            @endforelse
        </flux:callout>
        @if ($valuePublicationClosure ?? null)
            <flux:callout variant="warning">
                <flux:callout.heading>{{ __('Close this attempt without further publication') }}</flux:callout.heading>
                <flux:callout.text class="break-all font-mono">{{ $valuePublicationClosure['id'] }}</flux:callout.text>
                <flux:callout.text>{{ __('Files stay exactly as they are, including partial results and external changes. This does not approve or complete publication. The original plan and closure decision remain recorded. Create a new preview and review the current values afterwards.') }}</flux:callout.text>
                @foreach ($valuePublicationClosure['files'] as $file)
                    <flux:text class="break-all"><code>{{ $file['path'] }}</code> · {{ match ($file['current_state']) {
                        'result' => __('Matches prepared result'),
                        'source' => __('Matches original file state'),
                        default => __('Changed outside this publication'),
                    } }}</flux:text>
                @endforeach
                <flux:textarea class="mt-3" wire:model="valuePublicationClosureReason" :label="__('Closure reason')" maxlength="2000" />
                <flux:button class="mt-3" variant="danger" wire:click="closeValuePublicationWithoutWriting" wire:loading.attr="disabled">{{ __('Keep files and close this attempt') }}</flux:button>
            </flux:callout>
        @endif
        <flux:button variant="subtle" wire:click="closeValueApproval">{{ __('Close') }}</flux:button>
    </div>
</flux:modal>
