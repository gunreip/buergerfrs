{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-bulk-equalize-translation-key.blade.php --}}

@php
    $bulkContext = $bulkEqualizeContext ?? [];
    $bulkRows = collect($bulkContext['rows'] ?? []);
    $bulkTranslationKeys = collect($bulkContext['translation_keys'] ?? []);
    $bulkSuggestedKeys = collect($bulkContext['suggested_keys'] ?? []);
@endphp

<flux:modal
    class="w-[min(72rem,calc(100vw-4rem))] max-w-full"
    wire:model.self="bulkEqualizeTranslationKeyModalOpen"
>
    <div class="flex h-[calc(100vh-8rem)] max-h-[calc(100vh-8rem)] flex-col gap-4 overflow-hidden">
        <x-ui.headers.card
            :title="__('Equalize translation keys')"
            :description="__('Set one reviewed translation key for selected findings that share the same literal.')"
        >
            <div class="flex flex-wrap items-center gap-2">
                <flux:badge
                    size="sm"
                    color="{{ $bulkContext['can_confirm'] ?? false ? 'green' : 'amber' }}"
                >
                    {{ __('Selected') }}: {{ $bulkContext['selected_count'] ?? 0 }}
                </flux:badge>

                <flux:badge
                    size="sm"
                    color="{{ ($bulkContext['literal_count'] ?? 0) === 1 ? 'green' : 'red' }}"
                >
                    {{ __('Literals') }}: {{ $bulkContext['literal_count'] ?? 0 }}
                </flux:badge>

                @if (($bulkContext['missing_key_count'] ?? 0) > 0)
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Missing linked keys') }}: {{ $bulkContext['missing_key_count'] }}
                    </flux:badge>
                @endif
            </div>
        </x-ui.headers.card>

        <div class="grid grid-cols-1 gap-3 xl:grid-cols-3">
            <flux:callout
                color="sky"
                icon="type"
            >
                <flux:callout.heading>{{ __('Shared literal') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="wrap-anywhere text-sm font-semibold">
                        {{ $bulkContext['literal'] ?? __('No literal selected') }}
                    </div>
                    <div class="wrap-anywhere mt-1 font-mono text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $bulkContext['normalized_literal'] ?? __('No normalized literal') }}
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="xl:col-span-2"
                color="amber"
                icon="key-round"
            >
                <flux:callout.heading>{{ __('Target translation key') }}</flux:callout.heading>
                <flux:callout.text>
                    <flux:field>
                        <flux:input
                            class="font-mono"
                            wire:model.live="bulkEqualizeTranslationKey"
                            placeholder="ui.common.all"
                        />
                        <flux:error name="bulkEqualizeTranslationKey" />
                    </flux:field>
                </flux:callout.text>
            </flux:callout>
        </div>

        @if (!($bulkContext['can_confirm'] ?? false))
            <flux:callout
                color="red"
                icon="circle-alert"
            >
                <flux:callout.heading>{{ __('Selection cannot be saved yet') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Select at least two findings with linked keys and exactly one shared literal. Different literals must be reviewed separately.') }}
                </flux:callout.text>
            </flux:callout>
        @elseif ($bulkContext['equalize_to_existing_shared_key'] ?? false)
            <flux:callout
                color="cyan"
                icon="git-merge"
            >
                <flux:callout.heading>{{ __('Existing shared key found') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="space-y-1">
                        <div>
                            {{ __(
                                'The selected open candidates can be equalized to an already existing shared key.',
                            ) }}
                        </div>
                        <div class="wrap-anywhere font-mono text-xs">
                            {{ $bulkContext['existing_shared_key'] }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Existing shared entries') }}:
                            {{ number_format((int) ($bulkContext['existing_shared_key_count'] ?? 0)) }}
                        </div>
                    </div>
                </flux:callout.text>
            </flux:callout>
        @endif

        {{-- Current Translation Keys and Suggested Keys for Selected Findings --}}
        <div class="grid shrink-0 grid-cols-1 gap-3 xl:grid-cols-2">
            <flux:callout
                color="zinc"
                icon="list-checks"
            >
                <flux:callout.heading>{{ __('Current translation keys') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="max-h-96 overflow-auto pr-1">
                        @if ($bulkTranslationKeys->isEmpty())
                            <flux:text class="text-sm text-zinc-500">{{ __('No reviewed translation key set yet.') }}
                            </flux:text>
                        @else
                            <div class="space-y-1">
                                @foreach ($bulkTranslationKeys as $translationKey)
                                    <div class="wrap-anywhere font-mono text-xs">{{ $translationKey }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="zinc"
                icon="sparkles"
            >
                <flux:callout.heading>{{ __('Suggested keys') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="max-h-96 overflow-auto pr-1">
                        @if ($bulkSuggestedKeys->isEmpty())
                            <flux:text class="text-sm text-zinc-500">{{ __('No suggested keys available.') }}
                            </flux:text>
                        @else
                            <div class="space-y-1">
                                @foreach ($bulkSuggestedKeys as $suggestedKey)
                                    <div class="wrap-anywhere font-mono text-xs">{{ $suggestedKey }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>

        {{-- Table of selected findings with their current and suggested translation keys --}}
        <div class="min-h-0 flex-1">
            <flux:table container:class="max-h-96 overflow-auto">
                <flux:table.columns
                    class="rounded-t-lg bg-white dark:bg-zinc-800"
                    sticky
                >
                    <flux:table.column class="rounded-tl-lg">{{ __('Finding') }}</flux:table.column>
                    <flux:table.column>{{ __('Key') }}</flux:table.column>
                    <flux:table.column>{{ __('ui.current') }}</flux:table.column>
                    <flux:table.column class="rounded-tr-lg">{{ __('ui.suggested') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($bulkRows as $bulkRow)
                        <flux:table.row>
                            <flux:table.cell>
                                <flux:badge size="sm">F#{{ $bulkRow['id'] }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($bulkRow['key_id'])
                                    <flux:badge size="sm">K#{{ $bulkRow['key_id'] }}</flux:badge>
                                @else
                                    <flux:badge
                                        size="sm"
                                        color="red"
                                    >
                                        {{ __('ui.missing') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="wrap-anywhere font-mono text-xs">
                                    {{ $bulkRow['translation_key'] ?? __('No existing key') }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="wrap-anywhere font-mono text-xs">
                                    {{ $bulkRow['key_suggested_key'] ?? ($bulkRow['finding_suggested_key'] ?? __('No suggested key')) }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4">
                                <flux:text class="text-sm text-zinc-500">{{ __('No selected findings.') }}</flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="flex shrink-0 items-center justify-end gap-2">
            <flux:button
                type="button"
                variant="subtle"
                color="zinc"
                wire:click="closeBulkEqualizeTranslationKeyModal"
            >
                {{ __('ui.cancel') }}
            </flux:button>

            <flux:button
                type="button"
                variant="primary"
                color="amber"
                icon="git-merge"
                :disabled="!($bulkContext['can_confirm'] ?? false)"
                wire:click="confirmBulkEqualizeTranslationKey"
            >
                {{ __('Set shared key') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
