<flux:callout
    class="group/replacement"
    color="sky"
    heading="{{ __('Source replacement') }}"
    icon="code-xml"
>
    <div class="mt-3 grid items-start gap-3 lg:grid-cols-5">
        {{-- Callout Source Replacement --}}
        <flux:callout
            class="col-span-3 min-h-full"
            color="sky"
            heading="{{ __('Source replacement') }}"
            icon="code-xml"
        >
            <flux:callout.text>
                {{ __('translation-workbench.literal.active_findings_linked_to_keys_with_a_completed_publication_each_source_occurrence_is') }}
            </flux:callout.text>
            <flux:callout.text>
                {{ __('translation-workbench.literal.the_displayed_page_is_checked_against_the_current_source_when_loaded_positions_are_ca') }}
            </flux:callout.text>
        </flux:callout>
        {{-- Callout Current Selection --}}
        <flux:callout
            class="col-span-2 min-h-full min-w-0"
            :color="$sourceReplacementGroup === [] ? 'amber' : 'green'"
            icon="list-check"
        >
            @if ($sourceReplacementGroup === [])
                <div class="hidden group-has-[[data-source-all-assigned]]/replacement:block">
                    <flux:callout.heading>{{ __('Current selection') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('All matching findings already use their assigned key in source.') }}</flux:callout.text>
                    <div class="hidden group-has-[[data-source-awaiting-scan]]/replacement:block">
                        <flux:callout.text>{{ __('A new scan is needed to confirm the updated source positions.') }}</flux:callout.text>
                    </div>
                </div>
                <div class="hidden group-has-[[data-source-list-empty]]/replacement:block">
                    <flux:callout.heading>{{ __('Current selection') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('No findings match the current filters.') }}</flux:callout.text>
                </div>
            @endif
            <div @class(['group-has-[[data-source-all-assigned]]/replacement:hidden group-has-[[data-source-list-empty]]/replacement:hidden' => $sourceReplacementGroup === []])>
                @if ($sourceReplacementGroup !== [])
                    <flux:callout.heading>
                        {{ __('Current selection') }}
                        <flux:icon.dot class="size=6" />
                        {{ __('Key ID') }}
                        #{{ $sourceReplacementGroup['key_id'] }}
                    </flux:callout.heading>
                @else
                    <flux:callout.heading>
                        {{ __('Current selection') }}
                        <flux:callout.text class="uppercase">
                            {{ __('No selection performed') }}
                        </flux:callout.text>
                    </flux:callout.heading>
                @endif
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item :label="__('translation-workbench.translation.key.translation-key')">
                        @if ($sourceReplacementGroup !== [])
                            <x-translation-workbench::ui.common.text-preview
                                :text="$sourceReplacementGroup['full_name']"
                                :mono="true"
                                :length="80"
                            />
                        @else
                            <flux:callout.text>{{ __('None selected') }}</flux:callout.text>
                        @endif
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Selected findings')"
                        :item="count($sourceReplacementGroup['finding_ids'] ?? [])"
                    />
                </x-translation-workbench::ui.common.details>
                <flux:callout.text>
                    @if ($sourceReplacementGroup === [])
                        {{ __('Select a finding in the table to include the remaining findings linked to its translation key.') }}
                    @else
                        {{ __('The group includes findings on other pages and outside the current filters. Release the selection to choose another key.') }}
                    @endif
                </flux:callout.text>
                @if ($sourceReplacementGroup !== [])
                    <div class="mt-2 flex flex-wrap justify-end gap-2">
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="sky"
                                square
                                wire:click="previewSelectedSourceGroup"
                                wire:loading.attr="disabled"
                            >
                                <flux:icon.replace-all class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="sky"
                                wire:click="previewSelectedSourceGroup"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Preview selected findings (:count)', ['count' => count($sourceReplacementGroup['finding_ids'])]) }}
                            </flux:button>
                        </flux:button.group>
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="green"
                                square
                                wire:click="checkSelectedSourceFindings"
                                wire:loading.attr="disabled"
                            >
                                <flux:icon.eye class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="green"
                                wire:click="checkSelectedSourceFindings"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Check selected findings') }}
                            </flux:button>
                        </flux:button.group>
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="red"
                                square
                                wire:click="releaseSourceReplacementGroup"
                                wire:loading.attr="disabled"
                            >
                                <flux:icon.eye class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="red"
                                wire:click="releaseSourceReplacementGroup"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Release selection') }}
                            </flux:button>
                        </flux:button.group>
                    </div>
                @endif
            </div>
        </flux:callout>
    </div>

    @include('translation-workbench::livewire.translation-workbench.tabs.source-replacement.filters')

    <flux:error name="sourceReplacementGroup" />

    <flux:error name="sourcePreview" />

    @include('translation-workbench::livewire.translation-workbench.tabs.source-replacement.group-check')

    <livewire:translation-workbench.source-replacement-table
        :search="$sourceReplacementSearch"
        :finding-id="$sourceReplacementFindingId"
        :key-id="$sourceReplacementKeyId"
        :case-sensitive="$textSearchOptions['sourceReplacementSearch']['case'] ?? false"
        :whole-word="$textSearchOptions['sourceReplacementSearch']['word'] ?? false"
        :per-page="$sourceReplacementPerPage"
        :last-event="$sourceReplacementLastEvent"
        :check-version="$sourceReplacementCheckVersion"
        :test-token="$testToken"
        :key="'source-replacement-table-' . $testToken"
    />

</flux:callout>
