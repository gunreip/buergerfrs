{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit-dynamic.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-finding-edit-dynamic"
    wire:model="dynamicEditModalOpen"
>
    <div class="space-y-4">
        <div class="flex items-start gap-3">
            <div class="min-w-0 space-y-1">
                <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <flux:heading
                        size="xl"
                        level="3"
                    >
                        {{ __('Edit dynamic translation') }}
                    </flux:heading>

                    @if ($dynamicEditFinding)
                        <flux:badge
                            color="teal"
                            size="sm"
                        >
                            {{ __('Dynamic translation') }}
                        </flux:badge>

                        <flux:badge
                            color="{{ $dynamicEditFinding->review_status === 'reviewed' ? 'green' : 'red' }}"
                            size="sm"
                        >
                            {{ str((string) $dynamicEditFinding->review_status)->headline() }}
                        </flux:badge>
                    @endif
                </div>

                <flux:text class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Edit a reviewed dynamic translation value. Runtime data selects the value, but the language values are edited like a normal translation.') }}
                </flux:text>
            </div>

            <div class="ms-auto flex shrink-0 items-center gap-2">
                @if ($dynamicEditFinding)
                    <flux:badge
                        class="h-6 tabular-nums"
                        variant="subtle"
                    >
                        #{{ $dynamicEditFinding->id }}
                    </flux:badge>

                    <flux:button
                        type="button"
                        size="xs"
                        variant="primary"
                        icon="save"
                        wire:click="saveDynamicTranslationValue"
                    >
                        {{ __('Save') }}
                    </flux:button>
                @endif
            </div>
        </div>

        @if ($dynamicEditFinding)
            @php
                $editLocales = $editLocales ?? [
                    'source' => 'en',
                    'active' => app()->getLocale(),
                    'sub' => [],
                ];
                $sourceLocale = (string) ($editLocales['source'] ?? 'en');
                $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
            @endphp

            <flux:card>
                <x-ui.headers.card
                    :title="__('Dynamic context')"
                    :description="$dynamicEditFinding->translation_key ?: __('No translation key set.')"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        @if (filled($dynamicEditFinding->dynamic_scope))
                            <flux:badge
                                size="sm"
                                color="violet"
                            >
                                {{ __('Scope') }}: {{ $dynamicEditFinding->dynamic_scope }}
                            </flux:badge>
                        @endif

                        <flux:badge
                            size="sm"
                            color="teal"
                        >
                            {{ __('Single dynamic value') }}
                        </flux:badge>
                    </div>
                </x-ui.headers.card>
            </flux:card>

            <flux:card>
                <x-ui.headers.card
                    :title="__('Translation values')"
                    :description="__('Source is editable on purpose; target is the active application language.')"
                />

                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <flux:field>
                        <flux:label>
                            <span class="flex w-full items-center gap-2">
                                <span class="inline-flex items-center gap-2">
                                    <x-ui.locale.flag
                                        class="mb-1"
                                        :locale="$sourceLocale"
                                        size="lg"
                                        :title="strtoupper($sourceLocale)"
                                    />
                                    <span class="mb-1">{{ __('Source language') }}</span>
                                    <span class="font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400">
                                        {{ $sourceLocale }}
                                    </span>
                                </span>

                                <flux:button
                                    class="ms-auto h-6 w-6 shrink-0"
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    icon="pencil"
                                    :aria-label="__('Edit source value')"
                                    wire:click="editSourceTranslationValue"
                                />

                                <flux:button
                                    class="h-6 w-6 shrink-0"
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    icon="copy"
                                    :disabled="blank($sourceTranslationValue)"
                                    :aria-label="__('Copy source to target')"
                                    wire:click="copySourceToTargetValue"
                                />
                            </span>
                        </flux:label>

                        <flux:textarea
                            id="translation-workbench-source-translation-value"
                            rows="1"
                            :readonly="!$sourceTranslationEditable"
                            wire:model="sourceTranslationValue"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            <span class="inline-flex items-center gap-2">
                                <x-ui.locale.flag
                                    class="mb-1"
                                    :locale="$activeLocale"
                                    size="lg"
                                    :title="strtoupper($activeLocale)"
                                />
                                <span class="mb-1">{{ __('Target language') }}</span>
                                <span class="font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400">
                                    {{ $activeLocale }}
                                </span>
                            </span>
                        </flux:label>

                        <flux:textarea
                            id="translation-workbench-dynamic-target-value"
                            rows="1"
                            wire:model="targetTranslationValue"
                        />
                    </flux:field>
                </div>
            </flux:card>
        @endif
    </div>
</flux:modal>
