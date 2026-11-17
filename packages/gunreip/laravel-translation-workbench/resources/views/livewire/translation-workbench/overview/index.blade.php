<flux:callout
    :color="$scanRequirement['required'] ? 'amber' : 'sky'"
    icon="summary"
>
    <flux:accordion transition>
        <flux:accordion.item wire:model.live="overviewOpen">
            <flux:accordion.heading>
                <flux:callout.heading>
                    {{ __('translation-workbench.literal.translation-workbench.translation_workbench_overview') }}
                    @if ($scanRequirement['required'])
                        <flux:badge
                            color="amber"
                            size="sm"
                        >
                            {{ __('translation-workbench.buttons.new_scan_required') }}
                        </flux:badge>
                    @endif
                </flux:callout.heading>
            </flux:accordion.heading>

            <flux:accordion.content class="mt-3 flex flex-col gap-2">
                @if ($overviewOpen)
                    @include('translation-workbench::livewire.translation-workbench.overview.recent-work')
                    @include('translation-workbench::livewire.translation-workbench.overview.working-data')
                    @include('translation-workbench::livewire.translation-workbench.overview.scan-results')
                    @include('translation-workbench::livewire.translation-workbench.overview.publication-runs')
                    @include('translation-workbench::livewire.translation-workbench.overview.source-runs')
                @endif
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>

    @if (!$scanRequirement['required'])
        <div class="mt-3 flex justify-end">
            <flux:button.group>
                {{-- <flux:button
                    variant="outline"
                    square
                    size="sm"
                    wire:click="openCurrentScan"
                >
                    <flux:icon.arrow-path class="size-6" />
                </flux:button> --}}
                <flux:button
                    class="hover:cursor-pointer"
                    variant="outline"
                    size="sm"
                    wire:click="openCurrentScan"
                >
                    {{ $scanAction['reusable'] ?? false ? __('translation-workbench.buttons.open_last_scan') : __('translation-workbench.buttons.run_new_scan') }}
                </flux:button>
            </flux:button.group>
        </div>
    @endif
    @if ($scanRequirement['required'])
        <flux:callout
            class="mt-3 space-y-2"
            color="amber"
            icon="radar"
            heading="{{ __('translation-workbench.buttons.new_scan_required') }}"
            text="{{ __('translation-workbench.literal.stored_positions_for_findings_in_source_files_no_longer_match_or_could_not_be_verified') }}"
        >
            <flux:callout.text>
                {{ __('translation-workbench.literal.use_run_scan_to_preview_changes_and_then_save_the_checked_scan_results_source_replace') }}
            </flux:callout.text>
            <div class="flex items-center justify-end gap-2">
                <flux:tooltip>
                    <flux:button.group>
                        <flux:button
                            variant="filled"
                            square
                            color="amber"
                            size="sm"
                        >
                            <flux:icon.information-circle class="size-5" />
                        </flux:button>
                        <flux:button
                            variant="filled"
                            color="amber"
                            size="sm"
                        >
                            {{ __('Affected findings') }}
                        </flux:button>
                    </flux:button.group>
                    <x-translation-workbench::ui.common.tooltip-content-tabular
                        max-width="32rem"
                        :heading="__('Affected findings — first five')"
                        :rows="collect($scanRequirement['examples'])
                            ->map(
                                fn($example) => [
                                    'label' => '#' . $example['finding'],
                                    'value' => $example['path'] . ':' . $example['line'] . ':' . $example['column'],
                                ],
                            )
                            ->all()"
                    />
                </flux:tooltip>

                <flux:tooltip>
                    <flux:button.group>
                        <flux:button
                            variant="filled"
                            square
                            color="red"
                            size="sm"
                            wire:click="refreshScanRequirement"
                            wire:loading.attr="disabled"
                            wire:target="refreshScanRequirement"
                        >
                            <flux:icon.arrow-path class="size-5" />
                        </flux:button>
                        <flux:button
                            variant="filled"
                            color="red"
                            size="sm"
                            wire:click="refreshScanRequirement"
                            wire:loading.attr="disabled"
                            wire:target="refreshScanRequirement"
                        >
                            {{ __('Refresh source check') }}
                        </flux:button>
                    </flux:button.group>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Refresh source check')"
                        :text="__(
                            'Checks the stored positions of active findings against the current source files and refreshes this notice immediately, bypassing the cached result. This does not run the scanner, update finding records or change source or translation files. Use Run scan to record source changes through the regular scan workflow.',
                        )"
                    />
                </flux:tooltip>
            </div>
        </flux:callout>
    @endif

</flux:callout>
