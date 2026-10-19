<flux:card class="translation-workbench">
    <x-ui.headers.page
        :title="__('Translation Workbench Workflow')"
        :description="__('Current processing paths and the proposed shared workflow.')"
    />

    <flux:callout class="mt-6" color="zinc" icon="information-circle">
        <flux:callout.heading>{{ __('Workflow analysis') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('These graphs describe the workflow; they do not run commands or change translation data. Legacy and Workbench show the current commands, optional steps and write boundaries. Target workflow remains a proposed direction.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:tab.group class="mt-6">
        <flux:tabs wire:model.live="activeTab">
            <flux:tab name="legacy">{{ __('Legacy workflow') }}</flux:tab>
            <flux:tab name="workbench">{{ __('Workbench workflow') }}</flux:tab>
            <flux:tab name="target">{{ __('Target workflow') }}</flux:tab>
            <flux:tab name="target-ui">{{ __('Target workflow UI') }}</flux:tab>
            <flux:tab name="write-contract">{{ __('Write contract') }}</flux:tab>
        </flux:tabs>

        <div wire:loading wire:target="activeTab" role="status" class="py-3">
            <span class="inline-flex items-center gap-2">
                <flux:icon.loading class="size-4" />
                {{ __('Loading workflow…') }}
            </span>
        </div>

        <flux:tab.panel name="legacy">
            @if ($activeTab === 'legacy')
                @include('translation-workbench::livewire.workflow.legacy')
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="workbench">
            @if ($activeTab === 'workbench')
                @include('translation-workbench::livewire.workflow.workbench')
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="target">
            @if ($activeTab === 'target')
                @include('translation-workbench::livewire.workflow.target')
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="target-ui">
            @if ($activeTab === 'target-ui')
                @include('translation-workbench::livewire.workflow.target-ui')
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="write-contract">
            @if ($activeTab === 'write-contract')
                @include('translation-workbench::livewire.workflow.write-contract')
            @endif
        </flux:tab.panel>
    </flux:tab.group>
</flux:card>
