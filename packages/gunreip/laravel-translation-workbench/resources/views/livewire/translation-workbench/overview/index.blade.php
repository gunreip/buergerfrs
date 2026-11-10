<flux:callout
    x-data="{ overviewOpen: false }"
    x-id="['workbench-overview-content']"
    :color="$scanRequirement['required'] ? 'amber' : 'sky'"
    icon="summary"
>
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading>
                <flux:callout.heading>
                    {{ __('Translation Workbench overview') }}
                    @if ($scanRequirement['required'])
                        <flux:badge
                            color="amber"
                            size="sm"
                        >
                            {{ __('New scan required') }}
                        </flux:badge>
                    @endif
                </flux:callout.heading>
            </flux:accordion.heading>

            <flux:accordion.content class="mt-3 flex flex-col gap-2">
                @include('translation-workbench::livewire.translation-workbench.overview.recent-work')
                @include('translation-workbench::livewire.translation-workbench.overview.working-data')
                @include('translation-workbench::livewire.translation-workbench.overview.scan-results')
                @include('translation-workbench::livewire.translation-workbench.overview.publication-runs')
                @include('translation-workbench::livewire.translation-workbench.overview.source-runs')
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>

    @if (!$scanRequirement['required'])
        <div class="mt-3 flex justify-end">
            <flux:button
                variant="outline"
                size="sm"
                icon="arrow-path"
                wire:click="openCurrentScan"
            >{{ $scanFreshness['reusable'] ?? false ? __('Open last scan') : __('Run new scan') }}</flux:button>
        </div>
    @endif
    @if ($scanRequirement['required'])
        <flux:callout
            class="mt-3 space-y-2"
            color="amber"
            icon="radar"
            heading="New scan required"
            text="{{ __('Stored positions for findings in source files no longer match or could not be verified.') }}"
        >
            <flux:callout.text>
                {{ __('Use Run scan to preview changes and then save the checked scan results. Source replacement remains blocked for affected files until the discrepancy is resolved.') }}
            </flux:callout.text>
            <div class="flex items-center justify-end gap-2">
                <flux:tooltip>
                    <flux:button.group>
                        <flux:button
                            variant="filled"
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
