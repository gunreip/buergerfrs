{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit-dynamic-multi.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-finding-edit-dynamic-multi"
    wire:model="dynamicMultiEditModalOpen"
>
    <div class="space-y-4">
        <div class="flex items-start gap-3">
            <div class="min-w-0 space-y-1">
                <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <flux:heading
                        size="xl"
                        level="3"
                    >
                        {{ __('Edit dynamic-multi options') }}
                    </flux:heading>

                    @if ($dynamicMultiEditFinding)
                        <flux:badge
                            color="cyan"
                            size="sm"
                        >
                            {{ __('Dynamic options') }}
                        </flux:badge>

                        <flux:badge
                            color="{{ $dynamicMultiEditFinding->review_status === 'reviewed' ? 'green' : 'red' }}"
                            size="sm"
                        >
                            {{ str((string) $dynamicMultiEditFinding->review_status)->headline() }}
                        </flux:badge>
                    @endif
                </div>

                <flux:text class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Review and edit multiple runtime option values for this dynamic translation key.') }}
                </flux:text>
            </div>

            <div class="mr-8 ms-auto flex shrink-0 items-center gap-2">
                @if ($dynamicMultiEditFinding)
                    <flux:badge
                        class="h-6 tabular-nums"
                        variant="subtle"
                    >
                        #{{ $dynamicMultiEditFinding->id }}
                    </flux:badge>

                    <flux:button
                        type="button"
                        size="xs"
                        variant="primary"
                        icon="save"
                        wire:click="saveDynamicMultiTranslationValues"
                    >
                        {{ __('Save') }}
                    </flux:button>
                @endif
            </div>
        </div>

        @if ($dynamicMultiEditFinding)
            @php
                $editLocales = $editLocales ?? [
                    'source' => 'en',
                    'active' => app()->getLocale(),
                    'sub' => [],
                ];
                $sourceLocale = (string) ($editLocales['source'] ?? 'en');
                $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
                $rows = collect($dynamicMultiRows ?? []);
            @endphp

            <flux:card>
                <x-ui.headers.card
                    :title="__('Dynamic context')"
                    :description="$dynamicMultiEditFinding->translation_key ?: __('No translation key set.')"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        @if (filled($dynamicMultiEditFinding->dynamic_scope))
                            <flux:badge
                                size="sm"
                                color="violet"
                            >
                                {{ __('Scope') }}: {{ $dynamicMultiEditFinding->dynamic_scope }}
                            </flux:badge>
                        @endif

                        <flux:badge
                            size="sm"
                            color="{{ $rows->count() > 0 ? 'cyan' : 'amber' }}"
                        >
                            {{ __('Stored values') }}: {{ number_format($rows->count()) }}
                        </flux:badge>
                    </div>
                </x-ui.headers.card>
            </flux:card>

            <flux:card>
                <x-ui.headers.card
                    :title="__('Option values')"
                    :description="__('Each value key can have a source value and a target-language translation.')"
                />

                @if ($rows->isEmpty())
                    <flux:callout
                        class="mt-4"
                        color="amber"
                        icon="info"
                    >
                        <flux:callout.heading>{{ __('No dynamic option values yet') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('No value rows are stored for this dynamic key yet. The next step is to fill these rows from option discoveries or database-backed option sources.') }}
                        </flux:callout.text>
                    </flux:callout>
                @else
                    <flux:table class="mt-4 table-fixed">
                        <flux:table.columns>
                            <flux:table.column class="w-[28%]">{{ __('Value key') }}</flux:table.column>
                            <flux:table.column class="w-[28%]">
                                <span class="inline-flex items-center gap-2">
                                    <x-ui.locale.flag
                                        :locale="$sourceLocale"
                                        size="lg"
                                        :title="strtoupper($sourceLocale)"
                                    />
                                    <span class="font-mono text-sm font-semibold uppercase">{{ $sourceLocale }}</span>
                                </span>
                            </flux:table.column>
                            <flux:table.column class="w-[28%]">
                                <span class="flex items-center gap-2">
                                    <flux:button
                                        class="ms-auto h-6 w-6 shrink-0"
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        icon="copy"
                                        :aria-label="__('Copy all source values to target')"
                                        wire:click="copyAllDynamicMultiSourceToTarget"
                                    />
                                    <span class="inline-flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$activeLocale"
                                            size="lg"
                                            :title="strtoupper($activeLocale)"
                                        />
                                        <span
                                            class="font-mono text-sm font-semibold uppercase">{{ $activeLocale }}</span>
                                    </span>
                                </span>
                            </flux:table.column>
                            <flux:table.column class="w-40">{{ __('State') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($rows as $row)
                                @php
                                    $sourceValue = trim((string) ($row['source'] ?? ($row['native_label'] ?? '')));
                                    $targetValue = trim(
                                        (string) ($dynamicMultiTargetValues[$row['field_key']] ??
                                            ($row['target'] ?? '')),
                                    );
                                    $targetEqualsSource =
                                        $sourceValue !== '' && $targetValue !== '' && $sourceValue === $targetValue;
                                    $targetEqualsSourceOverridden =
                                        (bool) ($dynamicMultiSourceEqualsTargetOverrides[$row['field_key']] ?? false);
                                    $targetEqualsSourceInvalid = $targetEqualsSource && !$targetEqualsSourceOverridden;
                                    $targetEditable = (bool) ($dynamicMultiEditableTargetFields[$row['field_key']] ?? false);
                                @endphp

                                <flux:table.row>
                                    <flux:table.cell class="align-top">
                                        <div class="space-y-1">
                                            <div class="wrap-anywhere text-wrap font-mono text-xs">
                                                {{ $row['value_key'] }}</div>
                                            @if (filled($row['native_label'] ?? null))
                                                <div
                                                    class="hyphens-auto text-wrap text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $row['native_label'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell class="align-top">
                                        <div class="hyphens-auto text-wrap text-sm">
                                            {{ $row['source'] ?: '—' }}
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell class="align-top">
                                        <div class="flex items-start gap-2">
                                            <flux:button
                                                class="mt-1 h-6 w-6 shrink-0"
                                                type="button"
                                                size="xs"
                                                variant="ghost"
                                                icon="copy"
                                                :disabled="blank($row['source'] ?? $row['native_label'] ?? null)"
                                                :aria-label="__('Copy source value to target')"
                                                wire:click="copyDynamicMultiSourceToTarget('{{ $row['field_key'] }}')"
                                            />
                                            <flux:textarea
                                                class="min-w-0 flex-1 hyphens-auto"
                                                id="translation-workbench-dynamic-multi-target-{{ $row['field_key'] }}"
                                                rows="1"
                                                wire:model.live.debounce.500ms="dynamicMultiTargetValues.{{ $row['field_key'] }}"
                                                placeholder="{{ __('Enter translation') }}"
                                                :readonly="!$targetEditable"
                                                :invalid="$targetEqualsSourceInvalid"
                                            />

                                            <flux:button
                                                class="mt-1 h-6 w-6 shrink-0"
                                                type="button"
                                                size="xs"
                                                variant="ghost"
                                                icon="pencil"
                                                :disabled="$targetEditable"
                                                :aria-label="__('Edit target value')"
                                                wire:click="editDynamicMultiTargetValue('{{ $row['field_key'] }}')"
                                            />
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell class="w-64 align-top">
                                        @if (blank($targetValue))
                                            <flux:badge
                                                size="sm"
                                                color="amber"
                                            >
                                                {{ __('Target missing') }}
                                            </flux:badge>
                                        @elseif ($targetEqualsSourceInvalid)
                                            <div class="flex-col-2 flex items-start gap-1.5">
                                                <flux:badge
                                                    size="sm"
                                                    color="red"
                                                >
                                                    {{ __('Target equals source') }}
                                                </flux:badge>

                                                <flux:button
                                                    type="button"
                                                    size="xs"
                                                    variant="primary"
                                                    color="amber"
                                                    icon="check"
                                                    wire:click="overrideDynamicMultiSourceEqualsTarget('{{ $row['field_key'] }}')"
                                                >
                                                    {{ __('Override') }}
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:badge
                                                size="sm"
                                                color="{{ $targetEqualsSourceOverridden ? 'amber' : 'green' }}"
                                            >
                                                {{ $targetEqualsSourceOverridden ? __('Target equals source accepted') : __('Target ready') }}
                                            </flux:badge>
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </flux:card>
        @endif
    </div>
</flux:modal>
