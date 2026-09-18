<div class="space-y-3">
    <flux:heading size="lg">{{ __('Segments') }}</flux:heading>
    <flux:text>
        {{ __('Segments combine primitives into reusable visual units: for example, a line or arc with its anchor nodes, joint arrows, labels, and diagnostics. Paths connect these segments and calculate the route between them.') }}
    </flux:text>
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('Segments in the component chain') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('From the drawing level upwards: primitives → segments → parts or paths → strangs. Segments sit directly above primitives. Parts combine them into smaller reusable connections, while paths assemble larger routes. This tab documents the underlying segment building blocks.') }}
        </flux:callout.text>
    </flux:callout>
</div>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.segments_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="idea-to-paper-segments-path">{{ __('Path') }}</flux:tab>
        <flux:tab name="idea-to-paper-segments-start-end">{{ __('Start + End') }}</flux:tab>
        <flux:tab name="idea-to-paper-segments-arc">{{ __('Arc') }}</flux:tab>
        <flux:tab name="idea-to-paper-segments-labels">{{ __('Labels') }}</flux:tab>
        <flux:tab name="idea-to-paper-segments-step">{{ __('Step') }}</flux:tab>
        <flux:tab name="idea-to-paper-segments-stem-compressed">{{ __('Stem compressed') }}</flux:tab>
        <flux:tab name="idea-to-paper-segments-fusion">{{ __('Fusion') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="idea-to-paper-segments-path">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-path')
            <div wire:key="documentation-idea-to-paper-segments-path">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-path')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-segments-start-end">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-start-end')
            <div wire:key="documentation-idea-to-paper-segments-start-end">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-start-end')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-segments-arc">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-arc')
            <div wire:key="documentation-idea-to-paper-segments-arc">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-arc')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-segments-labels">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-labels')
            <div wire:key="documentation-idea-to-paper-segments-labels">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-labels')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-segments-step">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-step')
            <div wire:key="documentation-idea-to-paper-segments-step">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-step')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-segments-stem-compressed">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-stem-compressed')
            <div wire:key="documentation-idea-to-paper-segments-stem-compressed">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-stem-compressed')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-segments-fusion">
        @if (!isset($documentationTabs) || $documentationTabs['segments_index'] === 'idea-to-paper-segments-fusion')
            <div wire:key="documentation-idea-to-paper-segments-fusion">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-fusion')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
