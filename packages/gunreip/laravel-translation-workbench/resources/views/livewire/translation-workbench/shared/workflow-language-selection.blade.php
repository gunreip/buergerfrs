<flux:callout
    size="sm"
    variant="success"
    icon="languages"
    heading="{{ __('Publication languages') }}"
    text="{{ __('Select the approved English source, main language and language variants to publish together. Drafts need approval first. The next step previews the file changes; it does not write files yet.') }}"
>
    <flux:checkbox.group>
        {{-- Table Confirm Revision Approval --}}
        <flux:table
            class="mt-3 min-w-full"
            container:class="max-h-96 overflow-auto rounded-t-lg"
        >
            {{-- Table Header --}}
            <flux:table.columns class="bg-white dark:bg-zinc-800">
                {{-- Table Header Column Select --}}
                <flux:table.column>
                    <flux:tooltip>
                        <flux:checkbox.all
                            class="ml-2"
                            :disabled="!collect($languageChoices)->contains(fn ($choice) => $choice['unchanged'] && (!$languageApprovalRequired || $choice['approved']))"
                            :aria-label="__('Select or clear all available language revisions')"
                            :tooltip="__('Select or clear all available language revisions. Disabled entries are excluded.')"
                        />
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Select All')"
                            :text="__(
                                'Select or clear all available language revisions. Disabled entries are excluded.',
                            )"
                        />
                    </flux:tooltip>
                    <flux:tooltip>
                        <flux:callout.heading class="ml-2 flex items-center gap-2">
                            {{ __('Select') }}
                        </flux:callout.heading>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Select')"
                            :text="__('Select or clear all available language revisions.')"
                        />
                    </flux:tooltip>
                </flux:table.column>
                {{-- Table Header Column Revision --}}
                <flux:table.column>
                    <flux:tooltip>
                        <flux:callout.heading class="flex max-w-24">
                            {{ __('Revision') }}
                        </flux:callout.heading>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Revision')"
                            :text="__('The revision of the translation.')"
                        />
                    </flux:tooltip>
                </flux:table.column>
                {{-- Table Header Column Language --}}
                <flux:table.column>
                    <flux:tooltip>
                        <flux:callout.heading>
                            {{ __('Language') }}
                        </flux:callout.heading>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Language')"
                            :text="__('The language of the translation.')"
                        />
                    </flux:tooltip>
                </flux:table.column>
                {{-- Table Header Column Saved Text --}}
                <flux:table.column>
                    <flux:tooltip>
                        <flux:callout.heading>
                            {{ __('Saved text') }}
                        </flux:callout.heading>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Saved text')"
                            :text="__('The saved text of the translation.')"
                        />
                    </flux:tooltip>
                </flux:table.column>
                {{-- Table Header Column Status --}}
                <flux:table.column>
                    <flux:tooltip>
                        <flux:callout.heading>
                            {{ __('Status') }}
                        </flux:callout.heading>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Status')"
                            :text="__('The status of the translation.')"
                        />
                    </flux:tooltip>
                </flux:table.column>
            </flux:table.columns>
            {{-- Table Rows --}}
            <flux:table.rows>
                @foreach ($languageChoices as $code => $choice)
                    {{-- Table Row --}}
                    <flux:table.row :key="$languageSelection.
                    '-'.$code">
                        {{-- Table Row Column Select --}}
                        <flux:table.cell
                            class="w-24"
                            align="center"
                        >
                            <flux:checkbox
                                class="ml-2"
                                wire:model.live="{{ $languageSelection }}"
                                :value="$code"
                                :disabled="!$choice['unchanged'] || ($languageApprovalRequired && !$choice['approved'])"
                                :aria-label="__('Select :locale', ['locale' => $code])"
                            />
                        </flux:table.cell>
                        {{-- Table Row Column Revision --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:callout.text class="tabular-nums">
                                    #{{ $choice['revision'] }}
                                </flux:callout.text>
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Column Language --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                @if ($translationFlagCountries[$code] ?? null)
                                    <flux:flag
                                        :country="$translationFlagCountries[$code]"
                                        size="xs"
                                        circle
                                    />
                                @endif
                                <flux:callout.text>
                                    {{ $code }}
                                </flux:callout.text>
                            </div>
                        </flux:table.cell>
                        {{-- Table Row Column Saved Text --}}
                        <flux:table.cell class="max-w-lg">
                            <x-translation-workbench::ui.common.text-preview
                                :text="$choice['value']"
                                :lang="str_replace('_', '-', $code)"
                                :length="120"
                                :heading="__('Saved text')"
                            />
                        </flux:table.cell>
                        {{-- Table Row Column Status --}}
                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                :color="$choice['unchanged'] ? ($choice['approved'] ? 'green' : 'amber') : 'red'"
                            >
                                {{ !$choice['unchanged'] ? __('Unsaved changes or changed revision') : ($choice['approved'] ? __('Approved') : __('Draft')) }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:checkbox.group>
</flux:callout>
