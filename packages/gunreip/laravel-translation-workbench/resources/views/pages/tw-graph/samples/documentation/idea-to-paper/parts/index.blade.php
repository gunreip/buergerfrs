<div class="space-y-3">
    <flux:heading size="lg">{{ __('Parts') }}</flux:heading>
    <flux:text>
        {{ __('Parts are extended segment building blocks. They wrap individual segments or combine several segments into a connected unit and calculate its continuation anchor. For example, sideways connects an incoming arc, a bridge, and an outgoing arc.') }}
    </flux:text>
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('Parts in the component chain') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Primitives draw individual elements; segments assemble them into visual units. Parts and paths build on segments. Parts provide smaller reusable connections, while paths describe larger routes. Strangs and flow components use these building blocks; a path does not have to pass through the parts level.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:heading>{{ __('Available parts') }}</flux:heading>
    <flux:text><code>parts.start</code> — {{ __('A starting line with optional gradient, node labels, and a calculated end anchor.') }}</flux:text>
    <flux:text><code>parts.end</code> — {{ __('An ending line with a cap, optional end label, and a calculated end anchor.') }}</flux:text>
    <flux:text><code>parts.sideways</code> — {{ __('An arc–bridge–arc connection with optional bridge label and extension.') }}</flux:text>
    <flux:text><code>parts.chain</code> — {{ __('Connects a hand-authored sequence of start, sideways, and end parts by advancing the next anchor.') }}</flux:text>
</div>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.parts_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="idea-to-paper-parts-start">{{ __('Start') }}</flux:tab>
        <flux:tab name="idea-to-paper-parts-end">{{ __('End') }}</flux:tab>
        <flux:tab name="idea-to-paper-parts-sideways">{{ __('Sideways') }}</flux:tab>
        <flux:tab name="idea-to-paper-parts-chain">{{ __('Chain') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="idea-to-paper-parts-start">
        @if (!isset($documentationTabs) || $documentationTabs['parts_index'] === 'idea-to-paper-parts-start')
            <div wire:key="documentation-idea-to-paper-parts-start">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-start')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-parts-end">
        @if (!isset($documentationTabs) || $documentationTabs['parts_index'] === 'idea-to-paper-parts-end')
            <div wire:key="documentation-idea-to-paper-parts-end">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-end')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-parts-sideways">
        @if (!isset($documentationTabs) || $documentationTabs['parts_index'] === 'idea-to-paper-parts-sideways')
            <div wire:key="documentation-idea-to-paper-parts-sideways">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-sideways')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="idea-to-paper-parts-chain">
        @if (!isset($documentationTabs) || $documentationTabs['parts_index'] === 'idea-to-paper-parts-chain')
            <div wire:key="documentation-idea-to-paper-parts-chain">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-chain')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
