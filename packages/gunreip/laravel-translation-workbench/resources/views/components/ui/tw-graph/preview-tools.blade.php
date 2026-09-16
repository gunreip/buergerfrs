{{-- Preview controls change visibility only; the canvas keeps its diagnostic data. --}}
@props(['dev' => true, 'coordinates' => false])
<div
    data-tw-graph-preview-tools
    x-data="{ previewDev: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev))).as('tw-graph-preview-dev'), previewBoxes: $persist(true).as('tw-graph-preview-boxes'), previewCoordinates: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($coordinates))).as('tw-graph-preview-coordinates') }"
    :class="{ 'tw-graph-protocol-dev-disabled': !previewDev, 'tw-graph-protocol-coordinates-disabled': !previewCoordinates }"
    :data-boxes="previewBoxes ? 'on' : 'off'"
>
    <flux:fieldset class="z-100 sticky top-0 rounded-lg bg-white p-3 dark:bg-zinc-900">
        <div class="flex items-center gap-4 *:gap-x-2">
            <flux:checkbox.group variant="pills">
                <flux:checkbox
                    class="hover:cursor-pointer"
                    x-model="previewDev"
                    label="{{ __('DEV mode') }}"
                />
                <flux:checkbox
                    class="hover:cursor-pointer"
                    x-model="previewBoxes"
                    label="{{ __('Bounding boxes') }}"
                />
                <flux:checkbox
                    class="hover:cursor-pointer"
                    x-model="previewCoordinates"
                    label="{{ __('Coordinates') }}"
                />
            </flux:checkbox.group>
            <div class="ml-auto shrink-0">
                <flux:button
                    type="button"
                    variant="ghost"
                    size="sm"
                    square
                    icon="arrow-path"
                    :aria-label="__('Refresh preview')"
                    :tooltip="__('Refresh preview')"
                    wire:click="$refresh"
                    wire:loading.attr="disabled"
                    wire:target="$refresh"
                />
            </div>
        </div>
    </flux:fieldset>

    <style>
        [data-tw-graph-preview-tools][data-boxes="off"] [data-tw-graph-dev-box] {
            display: none;
        }
    </style>
    {{ $slot }}
</div>
