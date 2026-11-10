@props(['source' => 'en', 'target', 'countries' => [], 'focus' => null])
@php($model = $attributes->wire('model')->value())

<div
    {{ $attributes->whereDoesntStartWith('wire:model')->class('inline-flex') }}
    x-data="{
        switching: false,
        get sourceSelected() { return $wire[@js($model)] === @js($source) },
        set sourceSelected(value) { this.switchLanguage(value) },
        async switchLanguage(value) {
            if (this.switching) return;
            this.switching = true;
            try {
                await $wire.$set(@js($model), value ? @js($source) : @js($target));
                await $nextTick();
                const field = document.getElementById(@js($focus));
                if (field) field.focus({ preventScroll: true });
            } finally {
                this.switching = false;
            }
        }
    }"
>
    @if ($source === $target)
        <span
            class="inline-flex items-center"
            {{-- title="{{ __('Input language') . ': ' . $source }}" --}}
        >
            @if ($countries[$source] ?? null)
                <flux:flag
                    :country="$countries[$source]"
                    size="xs"
                />
            @else
                <span>{{ $source }}</span>
            @endif
        </span>
    @else
        <flux:tooltip>
            <div class="inline-flex">
                <flux:toggle
                    class="rounded-full"
                    size="xs"
                    variant="ghost"
                    x-model="sourceSelected"
                    x-bind:disabled="switching"
                    :aria-label="__('Set input / select language').
                    ': '.$source.
                    ' / '.$target"
                >
                    <x-slot:icon>
                        <span class="group-data-checked:inline-flex hidden hover:cursor-pointer">
                            @if ($countries[$source] ?? null)
                                <flux:flag
                                    :country="$countries[$source]"
                                    size="xs"
                                    circle
                                />
                            @else
                                <span>{{ $source }}</span>
                            @endif
                        </span>
                        <span class="group-data-checked:hidden inline-flex hover:cursor-pointer">
                            @if ($countries[$target] ?? null)
                                <flux:flag
                                    :country="$countries[$target]"
                                    size="xs"
                                    circle
                                />
                            @else
                                <span>{{ $target }}</span>
                            @endif
                        </span>
                    </x-slot:icon>
                </flux:toggle>
            </div>
            <x-translation-workbench::ui.common.tooltip-content :heading="__('Set input / select language')">
                <div class="space-y-2 text-start">
                    @foreach (['Source language' => $source, 'Target language' => $target] as $label => $code)
                        <div class="flex flex-nowrap items-center gap-3 whitespace-nowrap">
                            <span>{{ __($label) }}</span>
                            @if ($countries[$code] ?? null)
                                <flux:flag
                                    class="shrink-0"
                                    :country="$countries[$code]"
                                    size="xs"
                                    circle
                                />
                            @endif
                            <span>{{ $code }}</span>
                        </div>
                    @endforeach
                </div>
            </x-translation-workbench::ui.common.tooltip-content>
        </flux:tooltip>
    @endif
</div>
