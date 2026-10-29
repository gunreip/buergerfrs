<x-ui.headers.page
    :title="__('Translation Workbench')"
    :description="__('Findings, key assignments and translation drafts.')"
>
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
