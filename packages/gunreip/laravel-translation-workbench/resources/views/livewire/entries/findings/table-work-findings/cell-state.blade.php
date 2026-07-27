{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-state.blade.php --}}

{{-- Table Findings Cell State --}}
                <flux:table.cell>
                    <div class="flex flex-wrap gap-1">
                        @if ($reviewStatus !== '')
                            <x-ui.tooltip.simple
                                :title="__('Review status')"
                                :text="$reviewStatus === 'reviewed' ? __('This finding has passed the review step and can be edited.') : __('This finding still needs review before translation values should be edited.')"
                            >
                                {{-- Badge Review Status --}}
                                <flux:badge
                                    size="sm"
                                    color="{{ $reviewStatusColor }}"
                                >
                                    {{ $reviewStatus === 'reviewed' ? __('Reviewed') : str($reviewStatus)->headline() }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        <x-ui.tooltip.simple
                            :title="$hasKey ? __('Key relation available') : __('Key relation missing')"
                            :text="$hasKey
                                ? __('This finding is linked to a workbench translation key record.')
                                : __(
                                    'This finding still needs a linked translation key record before values can be edited.',
                                )"
                        >
                            {{-- Badge Key Linked --}}
                            <flux:badge
                                size="sm"
                                color="{{ $hasKey ? 'green' : 'amber' }}"
                            >
                                {{ $hasKey ? __('Key linked') : __('Key missing') }}
                            </flux:badge>
                        </x-ui.tooltip.simple>

                        @if (!$isDynamicFinding)
                            <x-ui.tooltip.simple
                                :title="$hasSourceValue
                                    ? __('Source translation available')
                                    : ($hasSourceLiteral
                                        ? __('Source translation not stored yet')
                                        : __('Source translation missing'))"
                                :text="$hasSourceValue
                                    ? __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_state.a_stored_translation_value_exists_for_the_source_language')
                                    : ($hasSourceLiteral
                                        ? __(
                                            'No stored source-language translation value exists yet, but a scanned source literal is available and can be saved from the edit workflow.',
                                        )
                                        : __('No stored source-language value and no scanned source literal are available.'))"
                            >
                                {{-- Badge Source Translation Value --}}
                                <flux:badge
                                    size="sm"
                                    color="{{ $hasSourceValue ? 'green' : ($hasSourceLiteral ? 'amber' : 'red') }}"
                                >
                                    {{ $hasSourceValue ? __('Source :locale ready', ['locale' => $sourceLocaleLabel]) : __('Source :locale missing', ['locale' => $sourceLocaleLabel]) }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        <x-ui.tooltip.simple
                            :title="$hasTargetValue
                                ? __('Target translation available')
                                : ($dynamicTranslationValuesComplete
                                    ? __('Target translation completed')
                                    : __('Target translation missing'))"
                            :text="$hasTargetValue
                                ? __('A translation value exists for the active target language.')
                                : ($dynamicTranslationValuesComplete
                                    ? __(
                                        'The normal target-value check is not applicable here, because this dynamic entry stores translated option values separately. The original missing state is therefore completed.',
                                    )
                                    : __('No translation value exists yet for the active target language.'))"
                        >
                            <span
                                class="{{ !$hasTargetValue && $dynamicTranslationValuesComplete ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                            >
                                {{-- Badge Has Target Value --}}
                                <flux:badge
                                    size="sm"
                                    color="{{ $hasTargetValue ? 'green' : 'amber' }}"
                                >
                                    {{ $hasTargetValue ? __('Target :locale ready', ['locale' => $targetLocaleLabel]) : __('Target :locale missing', ['locale' => $targetLocaleLabel]) }}
                                </flux:badge>
                            </span>
                        </x-ui.tooltip.simple>

                        @if (!$hasTargetValue && $dynamicTranslationValuesComplete)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic target translations ready')"
                                :text="__(
                                    'Target-language values exist for the dynamic option values of this finding.',
                                )"
                            >
                                {{-- Badge Target Locale Ready --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Target :locale ready', ['locale' => $targetLocaleLabel]) }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if (!$hasSourceLiteral)
                            <x-ui.tooltip.simple
                                :title="__('Source literal missing')"
                                :text="__(
                                    'Neither a scanned source literal nor a suggested source literal is available. This should be checked before translating.',
                                )"
                            >
                                {{-- Badge Source Label Missing --}}
                                <flux:badge
                                    size="sm"
                                    color="red"
                                >
                                    {{ __('Source :locale missing', ['locale' => $sourceLocaleLabel]) }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($sourceValueDiffers)
                            <x-ui.tooltip.simple
                                :title="__('Stored source differs')"
                                :text="__(
                                    'The stored source-language value differs from the currently scanned literal text. This does not compare against scanner-suggested literal context.',
                                )"
                            >
                                {{-- Badge Source Label Changed --}}
                                <flux:badge
                                    size="sm"
                                    color="pink"
                                >
                                    {{ __('Stored :locale differs', ['locale' => $sourceLocaleLabel]) }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif
                    </div>
                </flux:table.cell>
