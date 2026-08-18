{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-candidate.blade.php --}}

{{-- Table Findings Cell Candidate Type --}}
                <flux:table.cell>
                    <div class="flex flex-wrap gap-1">
                        @if ($isUiState)
                            <x-ui.tooltip.simple
                                :title="__('ui.ui.ui-translation')"
                                :text="__(
                                    'This finding was reviewed and confirmed as a reusable user-interface translation.',
                                )"
                            >
                                {{-- Badge Is UI  --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Is UI') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($finding->candidate_type === 'ui')
                            <x-ui.tooltip.simple
                                :title="$isUiState ? __('UI candidate completed') : __('Possible UI translation')"
                                :text="$isUiState
                                    ? __(
                                        'The scanner originally suggested a UI candidate. Review has confirmed the current UI state, so the candidate task is completed.',
                                    )
                                    : __(
                                        'The scanner suggests that this may be a reusable UI translation, but it has not been confirmed in review yet.',
                                    )"
                            >
                                <span
                                    class="{{ $isUiState ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                >
                                    {{-- Badge UI Candidate --}}
                                    <flux:badge
                                        size="sm"
                                        color="{{ $isUiState ? 'zinc' : 'violet' }}"
                                    >
                                        {{ __('ui.ui.ui-candidate') }}
                                    </flux:badge>
                                </span>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($isDynamicNumeric)
                            <x-ui.tooltip.simple
                                :title="__('Numeric dynamic value')"
                                :text="__(
                                    'The scanner detected a dynamic numeric or technical value. It is tracked for review/audit context, but it is not sent into the translation-value workflow.',
                                )"
                            >
                                {{-- Badge Numeric Dynamic Value --}}
                                <flux:badge
                                    size="sm"
                                    color="zinc"
                                >
                                    {{ __('Numeric dynamic') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @elseif ($isDynamicMultiState)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic option list')"
                                :text="__(
                                    'This key was reviewed as dynamic and can have multiple option values that need their own translations.',
                                )"
                            >
                                {{-- Badge Dynamic Values --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Dynamic values') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @elseif ($isDynamicState)
                            <x-ui.tooltip.simple
                                :title="__('Dynamic translation')"
                                :text="__(
                                    'This key was reviewed as dynamic. The displayed value is resolved from runtime data instead of a fixed text call only.',
                                )"
                            >
                                {{-- Badge Dynamic Values Resolved --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Dynamic values') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($finding->candidate_type === 'dynamic' && !$isDynamicNumeric)
                            <x-ui.tooltip.simple
                                :title="$isDynamicState
                                    ? __('Dynamic values candidate completed')
                                    : __('Possible dynamic values')"
                                :text="$isDynamicState
                                    ? __(
                                        'The scanner originally detected a dynamic candidate. Review has confirmed the current dynamic state, so the candidate task is completed.',
                                    )
                                    : __(
                                        'The scanner found signs of dynamic text values. Review must decide whether these values belong in the dynamic-values workflow or should be handled as a normal/non-translatable finding.',
                                    )"
                            >
                                <span
                                    class="{{ $isDynamicState ? 'relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-[\'\']' : 'inline-flex' }}"
                                >
                                    {{-- Badge Dynamic Values Candidate --}}
                                    <flux:badge
                                        size="sm"
                                        color="{{ $isDynamicState ? 'zinc' : 'violet' }}"
                                    >
                                        {{ __('Dynamic values candidate') }}
                                    </flux:badge>
                                </span>
                            </x-ui.tooltip.simple>
                        @endif

                        @if (
                            !$isDynamicNumeric &&
                                !$finding->is_ui_key &&
                                !$finding->is_dynamic_key &&
                                !$finding->is_dynamic_multi &&
                                !$finding->candidate_type)
                            <x-ui.tooltip.simple
                                :title="__('Normal translation')"
                                :text="__(
                                    'No UI or dynamic candidate state is currently attached to this finding.',
                                )"
                            >
                                {{-- Badge Normal --}}
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Normal') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif
                    </div>
                </flux:table.cell>
