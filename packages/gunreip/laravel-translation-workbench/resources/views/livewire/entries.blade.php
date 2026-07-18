{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries.blade.php --}}

{{-- View Entries of the new Translation Workbench data model. --}}
<div class="space-y-6">
    <x-ui.headers.page
        :title="__('Translation Workbench')"
        :description="__('Foundation overview based on the new Translation Workbench data model.')"
    />

    {{-- Card DEV-Playground --}}
    <flux:card class="mt-6">
        <x-ui.headers.card
            :title="__('DEV-Playground')"
            :description="__('This is a sample Text for DEV-playground, testing the review, edit and timline-part.')"
        />
    </flux:card>

    {{-- Card Overview Tabs --}}
    <flux:card
        id="translation-workbench-overview"
        x-data="{ showOverviewTabs: {{ $showOverviewTabs ? 'true' : 'false' }} }"
        x-on:buergerfrs:refresh-show-hide-layout.window="$wire.set('showOverviewTabs', showOverviewTabs)"
    >
        <x-ui.headers.card
            :title="__('Overview')"
            :description="__('Compact read-only foundation dashboard for scanner data, keys, lang files and coverage.')"
        >
            <div class="flex flex-col items-stretch gap-2">
                <flux:button
                    icon="database"
                    variant="primary"
                    size="xs"
                    :href="route('admin.translation-workbench.raw-data-new')"
                    wire:navigate
                >
                    {{ __('Open Raw-Data New') }}
                </flux:button>

                <x-ui.button.show-hide
                    size="xs"
                    state="showOverviewTabs"
                    show-label="{{ __('Show overview') }}"
                    hide-label="{{ __('Hide overview') }}"
                    width="min-w-28 text-left"
                />
            </div>
        </x-ui.headers.card>

        {{-- Tab Group for Overview Tabs --}}
        <flux:tab.group
            class="mt-4 min-w-0 max-w-full"
            x-show="showOverviewTabs"
            x-collapse
        >
            {{-- Tab navigation for the overview --}}
            <flux:tabs
                class="min-w-max"
                scrollable
                scrollable:fade
                scrollable:scrollbar="hide"
            >
                {{-- Tab Scanner --}}
                <flux:tab name="scanner">
                    {{ __('Scanner') }}
                </flux:tab>
                {{-- Tab Health --}}
                <flux:tab name="health">
                    {{ __('Health') }}
                </flux:tab>
                {{-- Tab Source --}}
                <flux:tab name="source">
                    {{ __('Source') }}
                </flux:tab>
                {{-- Tab Lang Files --}}
                <flux:tab name="lang-files">
                    {{ __('Lang files') }}
                </flux:tab>
                {{-- Tab Keys --}}
                <flux:tab name="keys">
                    {{ __('Keys') }}
                </flux:tab>
                {{-- Tab Dynamic --}}
                <flux:tab name="dynamic">
                    {{ __('Dynamic') }}
                </flux:tab>
                {{-- Tab Locales --}}
                <flux:tab name="locales">
                    {{ __('Locales') }}
                </flux:tab>
                {{-- Tab Timeline --}}
                <flux:tab name="timeline">
                    {{ __('Timeline') }}
                </flux:tab>
                {{-- Tab Tables --}}
                <flux:tab name="tables">
                    {{ __('Tables') }}
                </flux:tab>
                {{-- Tab Summary --}}
                <flux:tab name="summary">
                    {{ __('Summary') }}
                </flux:tab>
            </flux:tabs>

            {{-- Tab Panel Scanner --}}
            <flux:tab.panel name="scanner">
                @include('translation-workbench::livewire.entries.tabs.scanner-runs')
            </flux:tab.panel>

            {{-- Tab Panel Health --}}
            <flux:tab.panel name="health">
                @include('translation-workbench::livewire.entries.tabs.health')
            </flux:tab.panel>

            {{-- Tab Panel Source --}}
            <flux:tab.panel name="source">
                @include('translation-workbench::livewire.entries.tabs.source-main')
            </flux:tab.panel>

            {{-- Tab Panel Lang Files --}}
            <flux:tab.panel name="lang-files">
                @include('translation-workbench::livewire.entries.tabs.lang-files')
            </flux:tab.panel>

            {{-- Tab Panel Keys --}}
            <flux:tab.panel name="keys">
                @include('translation-workbench::livewire.entries.tabs.keys')
            </flux:tab.panel>

            {{-- Tab Panel Dynamic --}}
            <flux:tab.panel name="dynamic">
                @include('translation-workbench::livewire.entries.tabs.dynamic-values')
            </flux:tab.panel>

            {{-- Tab Panel Locales --}}
            <flux:tab.panel name="locales">
                @include('translation-workbench::livewire.entries.tabs.locales')
            </flux:tab.panel>

            {{-- Tab Panel Timeline --}}
            <flux:tab.panel name="timeline">
                @include('translation-workbench::livewire.entries.tabs.timeline')
            </flux:tab.panel>

            {{-- Tab Panel Tables --}}
            <flux:tab.panel name="tables">
                @include('translation-workbench::livewire.entries.tabs.tables')
            </flux:tab.panel>

            {{-- Tab Panel Summary --}}
            <flux:tab.panel name="summary">
                @include('translation-workbench::livewire.entries.tabs.summary')
            </flux:tab.panel>
        </flux:tab.group>
    </flux:card>

    {{-- Table of Findings --}}
    @include('translation-workbench::livewire.entries.findings-table')

    {{-- Finding Workflow Modals --}}
    @include('translation-workbench::livewire.entries.modal-review')
    @include('translation-workbench::livewire.entries.review.modal-edit-translation-key')
    @include('translation-workbench::livewire.entries.modal-dynamic-review')
    @include('translation-workbench::livewire.entries.modal-dynamic-source-link-confirm')
    @include('translation-workbench::livewire.entries.modal-edit')
    @include('translation-workbench::livewire.entries.modal-edit-dynamic')
    @include('translation-workbench::livewire.entries.modal-edit-dynamic-multi')
    @include('translation-workbench::livewire.entries.modal-timeline')
</div>
