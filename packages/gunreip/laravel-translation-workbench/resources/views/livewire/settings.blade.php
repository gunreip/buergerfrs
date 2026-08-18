{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/settings.blade.php --}}

<div class="translation-workbench space-y-6">
    <x-ui.headers.page
        :title="__('Translation Workbench Settings')"
        :description="__(
            'Inspect and reset persisted Translation Workbench UI settings for the current user and package defaults.',
        )"
    >
        <x-slot:meta>
            <flux:badge
                class="text-[0.65rem] font-normal leading-none"
                size="sm"
                color="zinc"
            >
                {{ $workbenchVersion['label'] ?? 'v0.7.0-dev' }}
            </flux:badge>
        </x-slot:meta>

        <flux:button
            icon="rotate-ccw"
            variant="danger"
            size="sm"
            wire:click="resetAllTranslationWorkbenchSettings"
            wire:confirm="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.reset_all_translation_workbench_ui_settings_for_the_current_user') }}"
        >
            {{ __('Reset all') }}
        </flux:button>
    </x-ui.headers.page>

    <flux:card>
        <x-ui.headers.card
            :title="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.settings_overview')"
            :description="__(
                'User settings are stored on the user record. Export files are diagnostic snapshots that can be reviewed or reused as package defaults later.',
            )"
        />

        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4">
            @foreach ($configSummary as $summary)
                <flux:callout
                    color="{{ $summary['color'] }}"
                    icon="info"
                >
                    <flux:callout.heading>{{ __($summary['label']) }}</flux:callout.heading>
                    <flux:callout.text>
                        <span class="font-mono text-sm">{{ $summary['value'] }}</span>
                    </flux:callout.text>
                </flux:callout>
            @endforeach
        </div>
    </flux:card>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @foreach ($settingsGroups as $group)
            @php
                $defaultsJson = json_encode(
                    $group['defaults'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                );
                $userJson = json_encode(
                    $group['user_state'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                );
                $exportJson = json_encode(
                    $group['export'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                );
            @endphp

            <flux:card>
                {{-- Translation hint:
                    Entries title literal: Entries
                    Entries description literal: Settings for the main Translation Workbench page: overview visibility, findings filters, sorting, pagination and modal behavior.
                    Raw data title literal: Raw data
                    Raw data description literal: Settings for the Translation Workbench raw-data page: selected table, table filters, sorting and pagination.
                --}}
                <x-ui.headers.card
                    :title="__($group['title'])"
                    :description="__($group['description'])"
                >
                    <flux:button
                        icon="rotate-ccw"
                        variant="ghost"
                        color="{{ $group['has_user_state'] ? 'amber' : 'zinc' }}"
                        size="xs"
                        wire:click="{{ $group['reset_action'] }}"
                        wire:confirm="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.reset_these_settings_for_the_current_user') }}"
                    >
                        {{ __('Reset') }}
                    </flux:button>
                </x-ui.headers.card>

                <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                    <flux:callout
                        color="{{ $group['has_user_state'] ? 'cyan' : 'zinc' }}"
                        icon="{{ $group['icon'] }}"
                    >
                        <flux:callout.heading>{{ __('User keys') }}</flux:callout.heading>
                        <flux:callout.text>
                            <span class="font-mono text-lg">{{ number_format($group['user_count']) }}</span>
                        </flux:callout.text>
                    </flux:callout>

                    <flux:callout
                        color="sky"
                        icon="file-braces"
                    >
                        <flux:callout.heading>{{ __('Defaults') }}</flux:callout.heading>
                        <flux:callout.text>
                            <span class="font-mono text-lg">{{ number_format($group['default_count']) }}</span>
                        </flux:callout.text>
                    </flux:callout>

                    <flux:callout
                        color="{{ $group['has_export_file'] ? 'green' : 'amber' }}"
                        icon="file-clock"
                    >
                        <flux:callout.heading>{{ __('Export keys') }}</flux:callout.heading>
                        <flux:callout.text>
                            <span class="font-mono text-lg">{{ number_format($group['export_count']) }}</span>
                        </flux:callout.text>
                    </flux:callout>
                </div>

                <flux:separator class="my-4" />

                <flux:tab.group>
                    <flux:tabs
                        scrollable
                        scrollable:fade
                        scrollable:scrollbar="hide"
                    >
                        <flux:tab name="user-state-{{ Str::slug($group['title']) }}">{{ __('User state') }}</flux:tab>
                        <flux:tab name="defaults-{{ Str::slug($group['title']) }}">{{ __('Defaults') }}</flux:tab>
                        <flux:tab name="files-{{ Str::slug($group['title']) }}">{{ __('Files') }}</flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="user-state-{{ Str::slug($group['title']) }}">
                        <flux:textarea
                            class="mt-3 font-mono text-xs"
                            rows="14"
                            readonly
                        >{{ $userJson }}</flux:textarea>
                    </flux:tab.panel>

                    <flux:tab.panel name="defaults-{{ Str::slug($group['title']) }}">
                        <flux:textarea
                            class="mt-3 font-mono text-xs"
                            rows="14"
                            readonly
                        >{{ $defaultsJson }}</flux:textarea>
                    </flux:tab.panel>

                    <flux:tab.panel name="files-{{ Str::slug($group['title']) }}">
                        <div class="mt-3 space-y-3">
                            <flux:callout
                                color="zinc"
                                icon="file"
                            >
                                <flux:callout.heading>{{ __('Defaults file') }}</flux:callout.heading>
                                <flux:callout.text>
                                    <span class="wrap-anywhere font-mono text-xs">{{ $group['defaults_file'] }}</span>
                                </flux:callout.text>
                            </flux:callout>

                            <flux:callout
                                color="{{ $group['has_export_file'] ? 'green' : 'amber' }}"
                                icon="file-output"
                            >
                                <flux:callout.heading>{{ __('Export file') }}</flux:callout.heading>
                                <flux:callout.text>
                                    <span class="wrap-anywhere font-mono text-xs">{{ $group['export_file'] }}</span>
                                </flux:callout.text>
                            </flux:callout>

                            <flux:textarea
                                class="font-mono text-xs"
                                rows="9"
                                readonly
                            >{{ $exportJson }}</flux:textarea>
                        </div>
                    </flux:tab.panel>
                </flux:tab.group>
            </flux:card>
        @endforeach
    </div>
</div>
