<x-ui.headers.page
    :title="__('Translation Workbench')"
    :description="__('Findings, key assignments and translation drafts.')"
>
    <flux:tooltip>
        <flux:button.group>
            <flux:button
                wire:click="openCurrentScan"
                wire:loading.attr="disabled"
                wire:target="openCurrentScan"
            >
                <flux:icon.arrow-path class="size-6" />
            </flux:button>
            <flux:button
                wire:click="openCurrentScan"
                wire:loading.attr="disabled"
                wire:target="openCurrentScan"
            >
                {{ ($scanFreshness['reusable'] ?? false) ? __('Open last scan') : __('Run new scan') }}
            </flux:button>
        </flux:button.group>
        <x-translation-workbench::ui.common.tooltip-content
            :heading="($scanFreshness['reusable'] ?? false) ? __('Open last scan') : __('Run new scan')"
            :text="__(
                $scanFreshness['reason'] ?? 'Check whether the latest scan can be reused. Saving new scan results remains a separate action.',
            )"
        />
    </flux:tooltip>
    @if (app()->environment('local', 'testing'))
        <flux:toggle
            class="w-42"
            wire:click="toggleTestcase"
            wire:loading.attr="disabled"
            wire:target="toggleTestcase"
            :checked="$testToken !== ''"
            :on-label="__('Testcase ON')"
            :off-label="__('Testcase OFF')"
            :tooltip="__('Each activation starts with fresh test datasets. Saving only changes the test database and test files.')"
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
