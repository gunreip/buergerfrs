{{-- Preview controls change visibility only; the canvas keeps its diagnostic data. --}}
@props(['dev' => true, 'coordinates' => false, 'grid' => false])
<div
    data-tw-graph-preview-tools
    x-data="{ previewDev: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev))).as('tw-graph-preview-dev'), previewBoxes: $persist(true).as('tw-graph-preview-boxes'), previewGrid: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($grid))).as('tw-graph-preview-grid'), previewCoordinates: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($coordinates))).as('tw-graph-preview-coordinates') }"
    :class="{ 'tw-graph-protocol-dev-disabled': !previewDev, 'tw-graph-protocol-coordinates-disabled': !previewCoordinates }"
    :data-grid="previewGrid ? 'on' : 'off'"
    :data-boxes="previewBoxes ? 'on' : 'off'"
>
    <flux:fieldset class="z-100 sticky top-0 rounded-lg bg-white p-3 dark:bg-zinc-900">
        <div class="flex flex-wrap items-center gap-4 *:gap-x-2">
            <flux:toggle
                class="hover:cursor-pointer"
                size="sm"
                color="cyan"
                icon="code-bracket"
                x-model="previewDev"
                label="{{ __('DEV mode') }}"
            />
            <flux:toggle
                class="hover:cursor-pointer"
                size="sm"
                color="cyan"
                icon="square-2-stack"
                x-model="previewBoxes"
                label="{{ __('Bounding boxes') }}"
            />
            <flux:toggle
                class="hover:cursor-pointer"
                size="sm"
                color="cyan"
                icon="arrows-pointing-out"
                x-model="previewCoordinates"
                label="{{ __('Coordinates') }}"
            />
            <flux:toggle
                class="hover:cursor-pointer"
                size="sm"
                color="cyan"
                icon="squares-2x2"
                x-model="previewGrid"
                label="{{ __('Grid X/Y') }}"
            />
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
        <x-translation-workbench::ui.tw-graph.preview-legend />
    </flux:fieldset>

    <style>
        [data-tw-graph-preview-tools][data-boxes="off"] [data-tw-graph-dev-box] {
            display: none;
        }
    </style>
    {{ $slot }}
</div>
