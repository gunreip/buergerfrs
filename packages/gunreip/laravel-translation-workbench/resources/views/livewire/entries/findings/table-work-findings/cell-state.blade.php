{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-state.blade.php --}}

{{-- Table Findings Cell State --}}
                <flux:table.cell>
                    <div class="flex flex-wrap gap-1">
                        @if (($findingLifecycleStatus ?? '') === 'commented_out')
                            <x-ui.tooltip.simple
                                :title="__('Commented out')"
                                :text="__(
                                    'This finding is still present in the source file, but the scanned expression currently sits inside a code comment. It is kept for context and history, but should not be processed as an active translation finding until the code is uncommented.',
                                )"
                            >
                                {{-- Badge Commented Out --}}
                                <flux:badge
                                    size="sm"
                                    color="zinc"
                                >
                                    {{ __('Commented out') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
                        @endif

                        @if ($translationWorkflowEdited)
                            <x-ui.tooltip.simple
                                :title="__('Translation workflow complete')"
                                :text="__(
                                    'The finding has a reviewed translation key and stored source and target translation values.',
                                )"
                            >
                                {{-- Badge Translation Ready --}}
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ __('Translation ready') }}
                                </flux:badge>
                            </x-ui.tooltip.simple>

                            @if (!$isDynamicFinding)
                                <x-ui.tooltip.simple
                                    :title="$translationWorkflowWritten
                                        ? __('Lang file inventory synced')
                                        : __('Lang file export pending')"
                                    :text="$translationWorkflowWritten
                                        ? __(
                                            'The source and target values were seen during a lang-file import after being written to lang files.',
                                        )
                                        : __(
                                            'The translation values are stored in the workbench database, but have not yet been seen again by the lang-file import. Run the complete workbench pipeline after exporting lang files.',
                                        )"
                                >
                                    {{-- Badge Lang File Sync State --}}
                                    <flux:badge
                                        size="sm"
                                        color="{{ $translationWorkflowWritten ? 'green' : 'amber' }}"
                                    >
                                        {{ $translationWorkflowWritten ? __('Lang files synced') : __('Lang export pending') }}
                                    </flux:badge>
                                </x-ui.tooltip.simple>
                            @endif
                        @else
                            @if ($reviewStatus !== '')
                                @php
                                    $workflowStatusTitle = $reviewStatus === 'reviewed'
                                        ? __('Translation key reviewed')
                                        : __('Review pending');
                                    $workflowStatusText = $reviewStatus === 'reviewed'
                                        ? __(
                                            'The review/key step is complete. Translation values still decide whether the entry is ready.',
                                        )
                                        : __('This finding still needs review before translation values should be edited.');
                                    $workflowStatusColor = $reviewStatus === 'reviewed' ? 'amber' : $reviewStatusColor;
                                    $workflowStatusLabel = $reviewStatus === 'reviewed'
                                        ? __('Key reviewed')
                                        : str($reviewStatus)->headline();
                                @endphp

                                <x-ui.tooltip.simple
                                    :title="$workflowStatusTitle"
                                    :text="$workflowStatusText"
                                >
                                    {{-- Badge Review Status --}}
                                    <flux:badge
                                        size="sm"
                                        color="{{ $workflowStatusColor }}"
                                    >
                                        {{ $workflowStatusLabel }}
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
                                        ? __('A stored translation value exists for the source language.')
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
                                        {{ $hasSourceValue ? __('Source :locale ready', ['locale' => $sourceLocaleLabel]) : __('ui.source.source-locale-missing', ['locale' => $sourceLocaleLabel]) }}
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
                                        {{ $hasTargetValue ? __('ui.target.target-locale-ready', ['locale' => $targetLocaleLabel]) : __('Target :locale missing', ['locale' => $targetLocaleLabel]) }}
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
                                        {{ __('ui.target.target-locale-ready', ['locale' => $targetLocaleLabel]) }}
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
                                        {{ __('ui.source.source-locale-missing', ['locale' => $sourceLocaleLabel]) }}
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
                        @endif
                    </div>
                </flux:table.cell>
