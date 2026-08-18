{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-dynamic-context.blade.php --}}

{{-- Table Findings Cell Dynamic Context --}}
                <flux:table.cell>
                    <div class="flex flex-wrap gap-1">
                        @if ($isDynamicNumeric)
                            <x-ui.tooltip.simple
                                :title="__('Non-translatable dynamic value')"
                                :text="__(
                                    'Numeric dynamic values are intentionally excluded from dynamic translation editing. The finding remains visible so scanner decisions can be audited.',
                                )"
                            >
                                {{-- Badge Non-Translatable --}}
                                <flux:badge
                                    size="sm"
                                    color="zinc"
                                >
                                    {{ __('Non-translatable') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @elseif ($isDynamicState)
                            <x-ui.tooltip.simple
                                :title="__('Current dynamic state')"
                                :text="__(
                                    'Current reviewed/key state. This is the effective state used by the edit workflow and may differ from the scanner kind.',
                                )"
                            >
                                {{-- Badge State / Dynamic Values --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('ui.state.state') }}:
                                    {{ __('Dynamic values') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @elseif ($isDynamicFinding)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic scanner candidate')"
                                :text="__(
                                    'The scanner detected a dynamic candidate, but no explicit Dynamic/DynamicMulti state has been reviewed yet.',
                                )"
                            >
                                {{-- Badge State / Candidate --}}
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('ui.state.state') }}: {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.candidate') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if (filled($dynamicDataState))
                            <x-ui.tooltip.simple
                                :title="$dynamicTranslationValuesComplete
                                    ? __('Dynamic data state completed')
                                    : __('Dynamic data state')"
                                :text="$dynamicTranslationValuesComplete
                                    ? __(
                                        'This was the original dynamic data state. The entry now has translated dynamic target values, so this task state is completed.',
                                    )
                                    : __(
                                        'This dynamic finding has not been normalized into dedicated dynamic translation value records yet. It is visible for review, but the dynamic edit workflow still needs structured data.',
                                    )"
                            >
                                <span
                                    class="{{ $dynamicTranslationValuesComplete && $dynamicDataState !== 'structured' ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                >
                                    {{-- Badge Dynamic Data State --}}
                                    <flux:badge
                                        size="sm"
                                        color="{{ $dynamicDataState === 'structured' ? 'green' : 'orange' }}"
                                    >
                                        {{ $dynamicDataState === 'structured' ? __('Data structured') : __('Data unstructured') }}
                                    </flux:badge>
                                </span>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($dynamicUnresolvedSourceCount > 0)
                            <x-ui.tooltip.simple
                                :title="$dynamicTranslationValuesComplete
                                    ? __('Unresolved source completed')
                                    : __('Unresolved dynamic sources')"
                                :text="$dynamicTranslationValuesComplete
                                    ? __(
                                        'Unresolved source markers are kept as original scanner context, but the translated dynamic target values are now complete.',
                                    )
                                    : __('Dynamic sources still reported unresolved scanner data.')"
                            >
                                <span
                                    class="{{ $dynamicTranslationValuesComplete ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                >
                                    {{-- Badge Unresolved Dynamic Sources --}}
                                    <flux:badge
                                        size="sm"
                                        color="{{ $dynamicTranslationValuesComplete ? 'zinc' : 'red' }}"
                                    >
                                        {{ __('ui.badge.unresolved') }}: {{ $dynamicUnresolvedSourceCount }}
                                    </flux:badge>
                                </span>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($dynamicTranslationValuesComplete)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic sources resolved')"
                                :text="__(
                                    'The dynamic entry has translated target values, so the unresolved source work state is complete for this finding.',
                                )"
                            >
                                {{-- Badge Dynamic Sources Resolved --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Resolved') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($dynamicTranslationValuesComplete)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic values translated')"
                                :text="__(
                                    'Target-language values exist for the stored dynamic value keys of this finding.',
                                )"
                            >
                                {{-- Badge Data Translated --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Data translated') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>

                            <x-ui.tooltip.simple
                                :title="__('Saved to lang file')"
                                :text="__(
                                    'The translated dynamic values are stored in the workbench database, but the lang file export/sync step has not written them to lang files yet.',
                                )"
                            >
                                {{-- Badge Saved To Langfile --}}
                                <flux:badge
                                    size="sm"
                                    color="red"
                                >
                                    {{ __('Saved to Langfile') }}: {{ __('No') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @foreach ($dynamicSourceTypes as $dynamicSourceType)
                            @continue($dynamicSourceType === 'unresolved')

                            <x-ui.tooltip.simple
                                :title="__('Dynamic value source')"
                                :text="__('Origin reported by the dynamic option discovery scanner.')"
                            >
                                {{-- Badge Dynamic Source --}}
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ str($dynamicSourceType)->replace('_', ' ')->headline() }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endforeach

                        @if ($dynamicValueCount > 0)
                            <x-ui.tooltip.simple
                                :title="__('Stored dynamic values')"
                                :text="__('Number of value keys already stored for this dynamic translation key.')"
                            >
                                {{-- Badge Stored Values --}}
                                <flux:badge
                                    size="sm"
                                    color="{{ $dynamicTranslationValuesComplete ? 'green' : ($dynamicValueCount > 1 ? 'cyan' : 'sky') }}"
                                >
                                    {{ __('ui.values.stored-values') }}: {{ $dynamicValueCount }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($dynamicDiscoveryCount > 0)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic discoveries')"
                                :text="__(
                                    'Number of scanner discoveries that may describe dynamic options for this finding or key.',
                                )"
                            >
                                {{-- Badge Dynamic Discoveries --}}
                                <flux:badge
                                    size="sm"
                                    color="{{ $dynamicTranslationValuesComplete ? 'green' : 'amber' }}"
                                >
                                    {{ __('Discoveries') }}: {{ $dynamicDiscoveryCount }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($dynamicOptionsCount > 0)
                            <x-ui.tooltip.simple
                                :title="__('Possible option values')"
                                :text="__(
                                    'Largest number of option values found by the dynamic option discovery scanner.',
                                )"
                            >
                                {{-- Badge Possible Options --}}
                                <flux:badge
                                    size="sm"
                                    color="{{ $dynamicOptionsCount > 1 ? 'cyan' : 'sky' }}"
                                >
                                    {{ __('Possible options') }}: {{ $dynamicOptionsCount }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif
                        {{-- Badge Scope --}}
                        @if (filled($dynamicScope))
                            <flux:badge
                                size="sm"
                                color="violet"
                            >
                                {{ __('scope') }}: {{ $dynamicScope }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:table.cell>
