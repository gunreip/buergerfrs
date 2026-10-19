<div wire:key="translation-workbench-key-ownership-host">
    <flux:modal name="translation-workbench-key-ownership" wire:model.self="keyOwnershipModalOpen"
        wire:key="translation-workbench-key-ownership-modal" class="w-full max-w-6xl">
        @if ($keyOwnershipPlan)
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('Resolve duplicate key records') }}</flux:heading>
                <flux:text class="break-all font-mono">{{ $keyOwnershipPlan['text'] }}</flux:text>
                <flux:callout :color="$keyOwnershipPlan['same_literals'] ? 'sky' : 'amber'">
                    <flux:callout.heading>{{ $keyOwnershipPlan['same_literals'] ? __('Consider a shared translation task') : __('Review the source context before choosing') }}</flux:callout.heading>
                    <flux:callout.text>{{ $keyOwnershipPlan['same_literals']
                        ? __('The available original literals are identical. This is a recommendation to compare their meaning, not an automatic merge decision.')
                        : __('Original literals differ or are missing. Matching translation keys or source translations alone do not prove that these tasks belong together.') }}</flux:callout.text>
                </flux:callout>
                <flux:accordion>
                    <flux:accordion.item expanded>
                        <flux:accordion.heading>{{ __('Key records and affected findings') }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:table container:class="max-h-80 overflow-auto">
                                <flux:table.columns>
                                    <flux:table.column>{{ __('Key / Suggested key') }}</flux:table.column>
                                    <flux:table.column>{{ __('Finding / Source') }}</flux:table.column>
                                    <flux:table.column>{{ __('Original literal') }}</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @foreach ($keyOwnershipPlan['owners'] as $owner)
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal">
                                                <flux:badge size="sm">#{{ $owner['id'] }} · {{ $owner['review_status'] }}</flux:badge>
                                                <div class="break-all font-mono">{{ $owner['suggested_key'] }}</div>
                                            </flux:table.cell>
                                            <flux:table.cell colspan="2" class="whitespace-normal">
                                                @forelse (collect($keyOwnershipPlan['findings'])->filter(fn ($row) => in_array($owner['id'], $row['key_ids'], true)) as $finding)
                                                    <div class="grid gap-3 border-b border-zinc-200 py-2 md:grid-cols-2 dark:border-zinc-700">
                                                        <div>#{{ $finding['id'] }} <span class="break-all font-mono">{{ $finding['path'] }}:{{ $finding['source_line'] }}</span></div>
                                                        <div class="whitespace-pre-wrap">{{ trim((string) $finding['literal_text']) !== '' ? $finding['literal_text'] : __('Original literal not available') }}</div>
                                                    </div>
                                                @empty
                                                    {{ __('No active links.') }}
                                                @endforelse
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('Stored translations and review history') }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:text>{{ __('These shared translations belong to the translation key, not to the original literal. They remain unchanged during consolidation.') }}</flux:text>
                            <flux:table container:class="max-h-64 overflow-auto">
                                <flux:table.columns><flux:table.column>{{ __('Locale / Status') }}</flux:table.column><flux:table.column>{{ __('Value') }}</flux:table.column></flux:table.columns>
                                <flux:table.rows>
                                    @foreach ($keyOwnershipPlan['values'] as $value)
                                        <flux:table.row><flux:table.cell>{{ $value['locale'] }} · {{ $value['status'] }} @if ($value['locale'] === ($keyOwnershipPlan['source_locale'] ?? 'en')) · {{ __('Source translation') }} @endif</flux:table.cell><flux:table.cell class="whitespace-pre-wrap">{{ $value['value'] }}</flux:table.cell></flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                            @if ($keyOwnershipPlan['own_values'])
                                <flux:callout color="amber" class="mt-3"><flux:callout.text>{{ __('Owner-specific values prevent consolidation. Review these values first.') }}</flux:callout.text></flux:callout>
                                <flux:table container:class="mt-3 max-h-64 overflow-auto">
                                    <flux:table.columns><flux:table.column>{{ __('Key / Locale') }}</flux:table.column><flux:table.column>{{ __('Value') }}</flux:table.column></flux:table.columns>
                                    <flux:table.rows>
                                        @foreach ($keyOwnershipPlan['own_values'] as $value)
                                            <flux:table.row><flux:table.cell>#{{ $value['key_id'] }} · {{ $value['locale'] }}</flux:table.cell><flux:table.cell class="whitespace-pre-wrap">{{ $value['value'] ?? '—' }}</flux:table.cell></flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @endif
                            <div class="mt-3 max-h-64 overflow-auto space-y-2">
                                @forelse ($keyOwnershipPlan['reviews'] as $review)
                                    <flux:text>#{{ $review['id'] }} · Key #{{ $review['key_id'] }} · {{ $review['decision'] }} · {{ $review['reviewed_at'] }}</flux:text>
                                @empty
                                    <flux:text>{{ __('No review decisions yet.') }}</flux:text>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
                <div class="grid gap-4 lg:grid-cols-2">
                    <flux:callout color="red">
                        <flux:callout.heading>{{ __('Merge into one translation task') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('Choose the key record to retain. The distinct findings shown above will use that record through their links; no finding owns it or absorbs another finding. The other active key records are archived. Original literals, suggested keys, translations and review history are retained.') }}</flux:callout.text>
                        <flux:select class="mt-3" variant="listbox" searchable wire:model.live="ownershipTargetKeyId" :label="__('Surviving key record')" :placeholder="__('Choose a key record')">
                            @foreach ($keyOwnershipPlan['owners'] as $owner)
                                <flux:select.option :value="$owner['id']">#{{ $owner['id'] }} · {{ $owner['suggested_key'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @if ($ownershipTargetKeyId)
                            <flux:text class="mt-3">{{ __('Surviving key record') }}: #{{ $ownershipTargetKeyId }}</flux:text>
                            <flux:text>{{ __('Key records to archive') }}: {{ collect($keyOwnershipPlan['owners'])->where('id', '!=', (int) $ownershipTargetKeyId)->pluck('id')->map(fn ($id) => '#'.$id)->implode(', ') }}</flux:text>
                            <flux:text>{{ __('Affected findings') }}: {{ collect($keyOwnershipPlan['findings'])->pluck('id')->map(fn ($id) => '#'.$id)->implode(', ') }}</flux:text>
                        @endif
                        @foreach ($keyOwnershipPlan['issues'] as $issue)
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $issue }}</flux:text>
                        @endforeach
                        <flux:checkbox class="mt-3" wire:model.live="ownershipMergeConfirmed" :label="__('I reviewed the source context and confirm these tasks belong together.')" />
                        <flux:error name="ownershipTargetKeyId" /><flux:error name="ownershipMergeConfirmed" />
                        <flux:button type="button" class="mt-3" variant="danger" wire:click="confirmKeyOwnershipMerge" wire:loading.attr="disabled"
                            :disabled="!$keyOwnershipPlan['proof'] || !$ownershipTargetKeyId || !$ownershipMergeConfirmed">
                            {{ __('Confirm consolidation') }}
                        </flux:button>
                    </flux:callout>
                    <flux:callout color="amber">
                        <flux:callout.heading>{{ __('Keep translation tasks separate') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('Give one task an independent translation key. Shared translations stay at the old key. New values, publication and the targeted source update follow through the existing workflow.') }}</flux:callout.text>
                        <flux:select class="mt-3" variant="listbox" searchable wire:model.live="ownershipSeparateKeyId" :label="__('Key record to separate')" :placeholder="__('Choose a key record')">
                            @foreach ($keyOwnershipPlan['owners'] as $owner)
                                <flux:select.option :value="$owner['id']">#{{ $owner['id'] }} · {{ $owner['suggested_key'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input class="mt-3" wire:model="ownershipSeparateTarget" :label="__('New translation key')" />
                        <flux:error name="ownershipSeparateKeyId" /><flux:error name="ownershipSeparateTarget" /><flux:error name="translationKeyValue" />
                        <flux:button type="button" class="mt-3" variant="outline" wire:click="previewOwnershipSeparation" wire:loading.attr="disabled">{{ __('Preview separation') }}</flux:button>
                        @if ($ownershipSeparationPlan)
                            <flux:text class="mt-3">#{{ $ownershipSeparationPlan['key_id'] }}: {{ $ownershipSeparationPlan['source'] }} → {{ $ownershipSeparationPlan['target'] }}</flux:text>
                            <flux:text>{{ __('Affected findings') }}: {{ collect($ownershipSeparationPlan['findings'])->pluck('id')->map(fn ($id) => '#'.$id)->implode(', ') }}</flux:text>
                            @foreach ($ownershipSeparationPlan['issues'] as $issue)
                                <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $issue }}</flux:text>
                            @endforeach
                            <flux:button type="button" class="mt-3" variant="danger" wire:click="confirmOwnershipSeparation" wire:loading.attr="disabled" :disabled="!$ownershipSeparationPlan['proof']">{{ __('Confirm separation') }}</flux:button>
                        @endif
                    </flux:callout>
                </div>
                <div class="flex justify-end"><flux:modal.close><flux:button type="button" variant="outline">{{ __('Close') }}</flux:button></flux:modal.close></div>
            </div>
        @endif
    </flux:modal>
</div>
