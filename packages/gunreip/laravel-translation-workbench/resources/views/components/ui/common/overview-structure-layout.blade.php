{{-- Paired Overview source files. Section matches the structure/data filename stem. --}}
@props([
    'section',
    'heading',
    'overrideFile' => null,
    'structureText' => null,
    'dataText' => null,
])

<flux:accordion.item {{ $attributes->class(['relative']) }}>
    <x-translation-workbench::ui.common.component-marker
        name="translation-workbench::ui.common.overview-structure-layout"
    />
    <flux:callout
        color="indigo"
        icon="layout-freeform"
    >
        <flux:accordion.heading>
            <flux:callout.heading class="grid grid-cols-3">
                <span>{{ __('Structure and layout') }}</span>
                <span>{{ $heading }}</span>
                <code>
                    structure/{{ $section }}.blade.php
                    @if ($overrideFile !== null)
                        · data/{{ basename($overrideFile) }}
                    @endif
                </code>
            </flux:callout.heading>
        </flux:accordion.heading>
    </flux:callout>
    <flux:accordion.content>
        <div class="grid min-w-0 grid-cols-1 gap-2 xl:grid-cols-2">
            <flux:callout
                class="min-w-0"
                color="indigo"
                icon="view"
            >
                <flux:callout.heading class="grid grid-cols-2">
                    <span>{{ __('Rendering components') }}</span>
                    <code>structure/{{ $section }}.blade.php</code>
                </flux:callout.heading>
                @if (filled($structureText))
                    <flux:callout.text class="mb-3">
                        {{ $structureText }}
                    </flux:callout.text>
                @endif
                <x-translation-workbench::ui.tw-graph.code-box class="xl:h-[48rem]">
                    {{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.' . $section)->source() }}
                </x-translation-workbench::ui.tw-graph.code-box>
            </flux:callout>
            @if ($overrideFile !== null)
                <flux:callout
                    class="min-w-0"
                    color="red"
                    icon="database"
                >
                    <flux:callout.heading class="grid grid-cols-2">
                        <span>{{ __('Layout overrides') }}</span>
                        <code>data/{{ basename($overrideFile) }}</code>
                    </flux:callout.heading>
                    @if (filled($dataText))
                        <flux:callout.text class="mb-3">
                            {{ $dataText }}
                        </flux:callout.text>
                    @endif
                    <x-translation-workbench::ui.tw-graph.code-box class="xl:h-[48rem]">
                        {{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromFile($overrideFile)->source() }}
                    </x-translation-workbench::ui.tw-graph.code-box>
                </flux:callout>
            @endif
        </div>
    </flux:accordion.content>
</flux:accordion.item>
