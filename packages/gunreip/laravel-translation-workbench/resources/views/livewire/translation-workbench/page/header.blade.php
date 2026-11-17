<x-ui.headers.page
    :title="__('Translation Workbench')"
    :description="__('Findings, key assignments and translation drafts.')"
>
    <flux:tooltip>
        <flux:button.group>
            <flux:button
                size="sm"
                square
                wire:click="openCurrentScan"
                wire:loading.attr="disabled"
                wire:target="openCurrentScan"
            >
                <flux:icon.arrow-path class="size-6" />
            </flux:button>
            <flux:button
                size="sm"
                wire:click="openCurrentScan"
                wire:loading.attr="disabled"
                wire:target="openCurrentScan"
            >
                {{ $scanAction['reusable'] ?? false ? __('translation-workbench.buttons.open_last_scan') : __('translation-workbench.buttons.run_new_scan') }}
            </flux:button>
        </flux:button.group>
        <x-translation-workbench::ui.common.tooltip-content
            :heading="$scanAction['reusable'] ?? false ? __('translation-workbench.buttons.open_last_scan') : __('translation-workbench.buttons.run_new_scan')"
            :text="__(
                $scanAction['reason'] ??
                    'Check whether the latest scan can be reused. Saving new scan results remains a separate action.',
            )"
        />
    </flux:tooltip>
    @if (app()->environment('local', 'testing'))
        <flux:toggle
            class="hover:cursor-pointer"
            size="sm"
            wire:click="toggleTestcase"
            wire:loading.attr="disabled"
            wire:target="toggleTestcase"
            :checked="$testToken !== ''"
            :on-label="__('translation-workbench.testcase.button.testcase-on')"
            :off-label="__('translation-workbench.testcase.button.testcase-off')"
            :tooltip="__('translation-workbench.testcase.literals.each_activation_starts_with_fresh_test_datasets_saving_only_changes_the_test_database')"
        >
            <x-slot:icon>
                <flux:icon.check
                    class="group-data-checked:block hidden size-6"
                    variant="outline"
                    color="green"
                />
                <flux:icon.x
                    class="group-data-checked:hidden size-6"
                    variant="outline"
                    color="red"
                />
            </x-slot:icon>
        </flux:toggle>
    @endif
</x-ui.headers.page>
