<flux:callout
    color="sky"
    icon="code-xml"
    :heading="__('Source replacement')"
>
    <flux:callout.text>
        {{ __('Active findings linked to keys with a completed publication. Each source occurrence is listed separately. Publication alone does not mean that source replacement is ready.') }}
    </flux:callout.text>
    <flux:callout.text>
        {{ __('The displayed page is checked against the current source when loaded. Positions are captured scan positions, not automatically corrected locations. In Compare source you can explicitly confirm a move or removal with a reason. Source replacement preview checks one selected occurrence again. Preparing records a plan; only the subsequent confirmation writes the source file.') }}
    </flux:callout.text>

    @include('translation-workbench::livewire.translation-workbench.tabs.source-replacement.filters')

    <flux:error name="sourceReplacementGroup" />
    <flux:error name="sourcePreview" />

    <flux:callout
        class="mb-2 mt-3"
        :color="$sourceReplacementGroup === [] ? 'amber' : 'green'"
        icon="list-check"
    >
        @if ($sourceReplacementGroup !== [])
            <flux:callout.heading>
                {{ __('Current selection') }} · {{ __('Key ID') }}
                #{{ $sourceReplacementGroup['key_id'] }}
            </flux:callout.heading>
            <x-translation-workbench::ui.common.text-preview
                :text="$sourceReplacementGroup['full_name']"
                :mono="true"
                :length="80"
            />
            <flux:callout.text>
                {{ __(':count active findings selected across all pages and filters. Selection is fixed until released. No replacement has been performed.', ['count' => count($sourceReplacementGroup['finding_ids'])]) }}
            </flux:callout.text>
            <x-slot name="controls">
                <div class="flex justify-end gap-1">
                    <flux:button size="sm" variant="outline" icon="replace-all"
                        wire:click="previewSelectedSourceGroup" wire:loading.attr="disabled">
                        {{ __('Preview selected findings (:count)', ['count' => count($sourceReplacementGroup['finding_ids'])]) }}
                    </flux:button>
                    <flux:button.group>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="checkSelectedSourceFindings"
                            wire:loading.attr="disabled"
                        >
                            <flux:icon.eye class="size-5" />
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="checkSelectedSourceFindings"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Check selected findings') }}
                        </flux:button>
                    </flux:button.group>
                    <flux:button.group>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="releaseSourceReplacementGroup"
                            wire:loading.attr="disabled"
                        >
                            <flux:icon.eye class="size-5" />
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="releaseSourceReplacementGroup"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Release selection') }}
                        </flux:button>
                    </flux:button.group>
                </div>
            </x-slot>
        @else
            <flux:callout.heading>
                {{ __('Current selection') }}
                <flux:callout.text class="uppercase">
                    {{ __('No selection performed') }}
                </flux:callout.text>
            </flux:callout.heading>
            <flux:callout.text class="uppercase">
                {{ __('No translation key') }}
            </flux:callout.text>
            <flux:callout.text>
                {{ __('0 active findings selected across all pages and filters.') }}
            </flux:callout.text>
        @endif
    </flux:callout>

    @include('translation-workbench::livewire.translation-workbench.tabs.source-replacement.group-check')

    <livewire:translation-workbench.source-replacement-table
        :search="$sourceReplacementSearch"
        :per-page="$sourceReplacementPerPage"
        :selected-ids="implode(',', $sourceReplacementGroup['finding_ids'] ?? [])"
        :last-event="$sourceReplacementLastEvent"
        :check-version="$sourceReplacementCheckVersion"
        :test-token="$testToken"
        :key="'source-replacement-table-'.$testToken"
    />

</flux:callout>
