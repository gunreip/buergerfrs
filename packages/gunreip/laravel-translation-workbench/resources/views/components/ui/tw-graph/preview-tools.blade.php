{{-- Preview controls change visibility only; the canvas keeps its diagnostic data. --}}
@props(['dev' => true, 'coordinates' => false, 'grid' => false])
<div
    data-tw-graph-preview-tools
    x-data="{ previewCalculated: $persist(false).as('tw-graph-preview-calculated'), previewDev: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev))).as('tw-graph-preview-dev'), previewBoxes: $persist(true).as('tw-graph-preview-boxes'), previewGrid: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($grid))).as('tw-graph-preview-grid'), previewCoordinates: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($coordinates))).as('tw-graph-preview-coordinates') }"
    :class="{ 'tw-graph-protocol-dev-disabled': !previewDev, 'tw-graph-protocol-coordinates-disabled': !previewCoordinates }"
    :data-calculated="previewDev && previewCalculated ? 'on' : 'off'"
    :data-grid="previewGrid ? 'on' : 'off'"
    :data-boxes="previewBoxes ? 'on' : 'off'"
>
    <flux:fieldset class="z-100 sticky top-0 rounded-lg bg-white p-3 dark:bg-zinc-900">
        <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.tw-graph.preview-tools" />
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
            <flux:toggle
                class="hover:cursor-pointer"
                size="sm"
                color="cyan"
                x-model="previewCalculated"
                label="{{ __('Calculated') }}"
            >
                <x-slot:icon><flux:icon.drafting-compass variant="mini" /></x-slot:icon>
            </flux:toggle>
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
        [data-tw-graph-calculated-marker] { display: none; }
        [data-tw-graph-preview-tools][data-calculated="on"] [data-tw-graph-calculated-marker] { display: inline-flex !important; }
        [data-tw-graph-preview-tools].tw-graph-protocol-dev-disabled [data-tw-graph-calculated-marker],
        [data-tw-graph-preview-tools] [data-tw-graph-dev="false"] [data-tw-graph-calculated-marker] { display: none !important; }
        [data-tw-graph-preview-tools][data-boxes="off"] [data-tw-graph-dev-box] {
            display: none;
        }
    </style>
    {{ $slot }}
</div>
