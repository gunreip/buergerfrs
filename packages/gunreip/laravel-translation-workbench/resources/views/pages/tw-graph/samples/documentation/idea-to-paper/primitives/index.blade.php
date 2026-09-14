{{-- Primitive documentation has its own section in the authoring story. --}}
<div class="space-y-3">
    <flux:heading size="lg">{{ __('Primitives') }}</flux:heading>
    <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
        {{ __('Primitives are the lowest level of the component chain and draw individual graphical elements. Segments combine them into reusable visual units, which paths and strangs assemble into complete flows.') }}
    </p>
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('Demonstration of the lowest component level') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('The direct primitive calls shown here serve only to demonstrate their appearance and properties. Never use primitives directly inside a tw-graph when authoring a graph. Use the appropriate strang, path, or segment components instead; they compose the primitives through the component chain and provide the required connections, labels, and diagnostics.') }}
        </flux:callout.text>
    </flux:callout>
</div>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.primitives_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="idea-to-paper-primitives-line">{{ __('Line') }}</flux:tab>
        <flux:tab name="idea-to-paper-primitives-arc">{{ __('Arc') }}</flux:tab>
        <flux:tab name="idea-to-paper-primitives-text-label">{{ __('Text Label') }}</flux:tab>
        <flux:tab name="idea-to-paper-primitives-markers-connectors">{{ __('Markers & Connectors') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="idea-to-paper-primitives-line">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_index'] === 'idea-to-paper-primitives-line')
            <div wire:key="documentation-idea-to-paper-primitives-line">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-primitives-arc">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_index'] === 'idea-to-paper-primitives-arc')
            <div wire:key="documentation-idea-to-paper-primitives-arc">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-arc')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-primitives-text-label">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_index'] === 'idea-to-paper-primitives-text-label')
            <div wire:key="documentation-idea-to-paper-primitives-text-label">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-text-label')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-primitives-markers-connectors">
        @if (!isset($documentationTabs) || $documentationTabs['primitives_index'] === 'idea-to-paper-primitives-markers-connectors')
            <div wire:key="documentation-idea-to-paper-primitives-markers-connectors">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-markers-connectors')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
