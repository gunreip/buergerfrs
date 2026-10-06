{{-- Visibility follows the preview-tools Alpine scope; samples are diagnostics only. --}}
<div
    class="relative mt-2 border-t border-zinc-200 pt-2 dark:border-zinc-700"
    data-tw-graph-dev-legend
    x-cloak
    x-show="previewDev && (previewBoxes || previewCoordinates || previewGrid || previewCalculated || previewFiles)"
>
    <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.tw-graph.preview-legend" />
    <flux:heading class="mb-3">{{ __('DEV legend') }}</flux:heading>
    {{-- <div class="mt-1 flex min-h-24 flex-wrap content-start gap-x-4 gap-y-1 text-xs text-zinc-600 dark:text-zinc-300"> --}}
    <div class="mt-1 grid min-h-12 grid-cols-3 content-start gap-x-4 gap-y-1 text-sm text-zinc-600 dark:text-zinc-300">
        <span class="inline-flex items-center gap-2" x-show="previewFiles">
            <span class="inline-flex h-5 w-10 items-center justify-center rounded-sm border border-zinc-700 bg-white"><span class="w-6" style="border-top:1px dashed #db2777"></span></span>
            {{ __('File regions — click the caption for paths') }}
        </span>
        <div
            class="contents"
            data-tw-graph-length-legend
            x-show="previewDev && previewCalculated"
        >
            <span class="inline-flex items-center gap-2">
                <span
                    class="inline-flex h-5 w-10 items-center justify-center rounded-sm border border-zinc-700 bg-white text-red-600"
                >
                    <flux:icon.drafting-compass class="size-4" />
                </span>
                {{ __('Calculated length — click for details and copying') }}
            </span>
            <span class="inline-flex items-center gap-2">
                <span
                    class="inline-flex h-5 w-10 items-center justify-center rounded-sm border border-zinc-700 bg-white"
                    style="color:#2563eb"
                >
                    <flux:icon.drafting-compass class="size-4" />
                </span>
                {{ __('Length set by prop — click for details') }}
            </span>
            <span class="inline-flex items-center gap-2">
                <span
                    class="inline-flex h-5 w-10 items-center justify-center rounded-sm border border-zinc-700 bg-white"
                    style="color:#71717a"
                >
                    <flux:icon.drafting-compass class="size-4" />
                </span>
                {{ __('Default length — click for details') }}
            </span>
        </div>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewBoxes"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px solid rgb(14 165 233);"
                ></span>
            </span>
            {{ __('Component call regions (component color)') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewBoxes"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px dashed rgb(14 165 233 / 1);"
                ></span>
            </span>
            {{ __('Element bounds') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewBoxes"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px dashed rgb(14 165 233 / 1);"
                ></span>
            </span>
            {{ __('Text label bounds') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewBoxes"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px dashed rgb(56 189 248 / 1);"
                ></span>
            </span>
            {{ __('Entire graph bounds') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewCoordinates"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px solid rgb(234 179 8);"
                ></span>
            </span>
            {{ __('Minimum canvas') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewCoordinates"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px dashed rgb(234 179 8 / .85);"
                ></span>
            </span>
            {{ __('Canvas padding') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewCoordinates"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px solid rgb(244 114 182 / .8);"
                ></span>
            </span>
            {{ __('Left content extent') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewCoordinates"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px solid rgb(56 189 248 / .8);"
                ></span>
            </span>
            {{ __('X = 0 / upper content extent') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewCoordinates"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px solid rgb(168 85 247 / .8);"
                ></span>
            </span>
            {{ __('Right content extent') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewCoordinates"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6"
                    style="border-top: 1px solid rgb(239 68 68 / .75);"
                ></span>
            </span>
            {{ __('Y = 0') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewGrid"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6 text-slate-500 dark:text-slate-400"
                    style="border-top: 1px solid currentColor; opacity: .15;"
                ></span>
            </span>
            {{ __('Grid: 1rem') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewGrid"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6 text-slate-500 dark:text-slate-400"
                    style="border-top: 1px solid currentColor; opacity: .35;"
                ></span>
            </span>
            {{ __('Grid: 5rem') }}
        </span>
        <span
            class="inline-flex items-center gap-2"
            x-show="previewGrid"
        >
            <span
                class="bg-emerald inline-flex h-3 w-10 shrink-0 items-center justify-center rounded-sm border border-zinc-700"
                aria-hidden="true"
            >
                <span
                    class="inline-block w-6 text-slate-500 dark:text-slate-400"
                    style="border-top: 1.5px solid currentColor; opacity: .8;"
                ></span>
            </span>
            {{ __('Grid: X/Y axes, origin (0|0)') }}
        </span>
    </div>
</div>
