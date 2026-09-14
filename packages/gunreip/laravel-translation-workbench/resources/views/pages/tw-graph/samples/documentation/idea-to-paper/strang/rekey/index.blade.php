<flux:heading size="lg">Strang Rekey</flux:heading>
<flux:text>Each example contains its own handmade components, source code, props table and preview controls.</flux:text>

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs wire:model.live="tabs.strang_rekey_index" scrollable scrollable:fade scrollable:scrollbar="hide">
        <flux:tab name="rekey-default">{{ __('Default') }}</flux:tab>
        <flux:tab name="rekey-source">{{ __('Source') }}</flux:tab>
        <flux:tab name="rekey-target">{{ __('Target') }}</flux:tab>
        <flux:tab name="rekey-compressed">{{ __('Compressed') }}</flux:tab>
    </flux:tabs>
    <flux:tab.panel name="rekey-default">
        @if (!isset($documentationTabs) || $documentationTabs['strang_rekey_index'] === 'rekey-default')
            <div wire:key="documentation-rekey-default">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-default')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="rekey-source">
        @if (!isset($documentationTabs) || $documentationTabs['strang_rekey_index'] === 'rekey-source')
            <div wire:key="documentation-rekey-source">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-source')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="rekey-target">
        @if (!isset($documentationTabs) || $documentationTabs['strang_rekey_index'] === 'rekey-target')
            <div wire:key="documentation-rekey-target">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-target')
            </div>
        @endif
    </flux:tab.panel>
    <flux:tab.panel name="rekey-compressed">
        @if (!isset($documentationTabs) || $documentationTabs['strang_rekey_index'] === 'rekey-compressed')
            <div wire:key="documentation-rekey-compressed">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-compressed')
            </div>
        @endif
    </flux:tab.panel>
</flux:tab.group>
