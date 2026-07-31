{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit.blade.php --}}

{{-- Modal Translation Edit --}}
<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-finding-edit"
    wire:model="editModalOpen"
>
    <div class="space-y-4">
        <div class="mr-2 flex items-start gap-3">
            <div class="min-w-0 space-y-1">
                <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <flux:heading
                        size="xl"
                        level="3"
                    >
                        {{ __('Edit translation values') }}
                    </flux:heading>

                    @if ($editFinding)
                        <flux:badge
                            color="{{ $editFinding->translation_key ? 'green' : 'red' }}"
                            size="sm"
                        >
                            {{ $editFinding->translation_key ? __('ui.translation.translation-key') : __('Missing key') }}
                        </flux:badge>

                        <flux:badge
                            color="{{ $editFinding->review_status === 'reviewed' ? 'green' : 'red' }}"
                            size="sm"
                        >
                            {{ str((string) $editFinding->review_status)->headline() }}
                        </flux:badge>
                    @endif
                </div>

                <flux:text class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Edit static translation values for the reviewed translation key.') }}
                </flux:text>
            </div>

            <div class="mr-8 ms-auto flex shrink-0 items-center gap-2">
                @if ($editFinding)
                    <flux:badge
                        class="h-6 tabular-nums"
                        variant="subtle"
                    >
                        #{{ $editFinding->id }}
                    </flux:badge>

                    <div
                        class="flex h-6 items-center"
                        @if ($editModalAutoCloseCountdown > 0) wire:poll.1s="tickEditModalAutoClose" @endif
                    >
                        <x-ui.tooltip.simple
                            class="inline-flex items-center"
                            :title="$editModalAutoCloseAfterSave
                                ? __('Auto-close enabled')
                                : __('Auto-close disabled')"
                            :text="__(
                                'When enabled, this modal closes automatically three seconds after the translation values have been saved successfully.',
                            )"
                        >
                            <flux:button
                                type="button tabular-nums"
                                size="xs"
                                variant="{{ $editModalAutoCloseAfterSave ? 'primary' : 'subtle' }}"
                                color="{{ $editModalAutoCloseAfterSave ? 'cyan' : 'zinc' }}"
                                icon="clock"
                                wire:click="toggleEditModalAutoCloseAfterSave"
                            >
                                {{ $editModalAutoCloseCountdown > 0 ? $editModalAutoCloseCountdown . 's' : __('3s') }}
                            </flux:button>
                        </x-ui.tooltip.simple>
                    </div>

                    <flux:button
                        type="button"
                        size="xs"
                        variant="primary"
                        icon="save"
                        wire:click="saveTranslationValue"
                    >
                        {{ __('ui.save') }}
                    </flux:button>
                @endif
            </div>
        </div>

        @if ($editFinding)
            @php
                $editLocales = $editLocales ?? [
                    'source' => 'en',
                    'active' => app()->getLocale(),
                    'sub' => [],
                ];
                $editValues = $editValues ?? [
                    'source' => null,
                    'target' => null,
                    'source_exists' => false,
                    'target_exists' => false,
                    'source_origin' => 'missing',
                ];
                $sourceLocale = (string) ($editLocales['source'] ?? 'en');
                $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
                $subLocales = collect((array) ($editLocales['sub'] ?? []))
                    ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
                    ->values();
                $selectedTargetSubLocales = collect($selectedTargetSubLocales ?? [])
                    ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
                    ->values();
                $visibleSubLocales = $subLocales
                    ->filter(static fn(string $locale): bool => $selectedTargetSubLocales->contains($locale))
                    ->values();
                $sourceOrigin = (string) ($editValues['source_origin'] ?? 'missing');
                $sourceBadge = match ($sourceOrigin) {
                    'translation_value' => [
                        'label' => __('ui.translation.translation-exists'),
                        'color' => 'green',
                    ],
                    'literal_text' => [
                        'label' => __('Scanned literal'),
                        'color' => 'amber',
                    ],
                    'literal_text_suggested' => [
                        'label' => __('Suggested literal'),
                        'color' => 'amber',
                    ],
                    default => [
                        'label' => __('Source missing'),
                        'color' => 'red',
                    ],
                };
                $editWarnings = [];

                if (blank($editFinding->translation_key)) {
                    $editWarnings[] = [
                        'label' => __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing'),
                        'text' => __('Review this finding and set a translation key before editing values.'),
                        'color' => 'red',
                    ];
                }

                if (blank($sourceTranslationValue)) {
                    $editWarnings[] = [
                        'label' => __('Source empty'),
                        'text' => __('No source-language value is available yet.'),
                        'color' => 'amber',
                    ];
                }

                if (blank($targetTranslationValue)) {
                    $editWarnings[] = [
                        'label' => __('Target empty'),
                        'text' => __('The main target-language value is still empty.'),
                        'color' => 'amber',
                    ];
                }

                if (
                    filled($sourceTranslationValue) &&
                    filled($targetTranslationValue) &&
                    trim((string) $sourceTranslationValue) === trim((string) $targetTranslationValue)
                ) {
                    $editWarnings[] = [
                        'label' => __('Source equals target'),
                        'text' => __('The target value is currently identical to the source value.'),
                        'color' => 'sky',
                    ];
                }

                $extractTranslationVariables = static function (?string $value): array {
                    $value = (string) $value;
                    $variables = [];

                    if (preg_match_all('/(?<!:):[A-Za-z][A-Za-z0-9_]*/', $value, $matches)) {
                        $variables = array_merge($variables, $matches[0]);
                    }

                    if (preg_match_all('/\{[A-Za-z][A-Za-z0-9_]*\}/', $value, $matches)) {
                        $variables = array_merge($variables, $matches[0]);
                    }

                    return collect($variables)
                        ->unique()
                        ->values()
                        ->all();
                };
                $sourceTranslationVariables = $extractTranslationVariables($sourceTranslationValue);
                $targetTranslationVariables = $extractTranslationVariables($targetTranslationValue);
                $missingTargetTranslationVariables = collect($sourceTranslationVariables)
                    ->reject(static fn(string $variable): bool => in_array($variable, $targetTranslationVariables, true))
                    ->values()
                    ->all();
            @endphp

            {{-- Card Translation Key --}}
            <flux:card>
                <x-ui.headers.card
                    :title="__('ui.translation.translation-key')"
                    :description="$editFinding->translation_key ?: __('No translation key set.')"
                >
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if ($editWarnings !== [])
                            @foreach ($editWarnings as $warning)
                                <x-ui.tooltip.simple
                                    :title="$warning['label']"
                                    :text="$warning['text']"
                                >
                                    <flux:badge
                                        size="sm"
                                        color="{{ $warning['color'] }}"
                                    >
                                        {{ $warning['label'] }}
                                    </flux:badge>
                                </x-ui.tooltip.simple>
                            @endforeach
                        @endif

                        <x-ui.tooltip.simple
                            :title="__('ui.source-language')"
                            :text="strtoupper($sourceLocale)"
                        >
                            <span
                                class="inline-flex items-center gap-1 rounded border border-zinc-300 px-1.5 py-1 dark:border-zinc-600"
                            >
                                <x-ui.locale.flag
                                    :locale="$sourceLocale"
                                    size="md"
                                    :title="strtoupper($sourceLocale)"
                                />
                                <span class="font-mono text-xs uppercase">{{ $sourceLocale }}</span>
                            </span>
                        </x-ui.tooltip.simple>

                        <x-ui.tooltip.simple
                            :title="__('ui.target.target-language')"
                            :text="strtoupper($activeLocale)"
                        >
                            <span
                                class="inline-flex items-center gap-1 rounded border border-zinc-300 px-1.5 py-1 dark:border-zinc-600"
                            >
                                <x-ui.locale.flag
                                    :locale="$activeLocale"
                                    size="md"
                                    :title="strtoupper($activeLocale)"
                                />
                                <span class="font-mono text-xs uppercase">{{ $activeLocale }}</span>
                            </span>
                        </x-ui.tooltip.simple>

                        @if ($subLocales->isNotEmpty())
                            <span
                                class="inline-flex flex-wrap items-center gap-1 rounded border border-zinc-300 px-1.5 py-1 dark:border-zinc-600"
                            >
                                @foreach ($subLocales as $subLocale)
                                    <span class="inline-flex items-center gap-1">
                                        <x-ui.locale.flag
                                            :locale="$subLocale"
                                            size="md"
                                            :title="strtoupper((string) $subLocale)"
                                        />
                                        <span class="mr-1 font-mono text-xs uppercase">{{ $subLocale }}</span>
                                    </span>
                                @endforeach
                            </span>
                        @endif
                    </div>
                </x-ui.headers.card>
            </flux:card>

            <flux:card>
                <x-ui.headers.card
                    :title="__('ui.translation-values')"
                    :description="__(
                        'Source value is read-only by default; use the edit button if the source-language value must be corrected explicitly.',
                    )"
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
                                    <span class="mb-1">{{ __('ui.source-language') }}</span>
                                    <span class="font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400">
                                        {{ $sourceLocale }}
                                    </span>
                                    <flux:badge
                                        size="sm"
                                        color="{{ $sourceBadge['color'] }}"
                                    >
                                        {{ $sourceBadge['label'] }}
                                    </flux:badge>
                                    @if ($sourceTranslationVariables !== [])
                                        <x-ui.tooltip.simple
                                            :title="__('Translation variables')"
                                            :text="__(
                                                'This source value contains placeholders that must be preserved in the target translation: :variables',
                                                ['variables' => implode(', ', $sourceTranslationVariables)],
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                color="red"
                                            >
                                                {{ __('Variables warning') }}
                                            </flux:badge>
                                        </x-ui.tooltip.simple>
                                    @endif
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
                            rows="2"
                            :readonly="!$sourceTranslationEditable"
                            wire:model.live.debounce.300ms="sourceTranslationValue"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            <span class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-2">
                                    <x-ui.locale.flag
                                        class="mb-1"
                                        :locale="$activeLocale"
                                        size="lg"
                                        :title="strtoupper($activeLocale)"
                                    />
                                    <span class="mb-1">{{ __('ui.target.target-language') }}</span>
                                    <span class="font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400">
                                        {{ $activeLocale }}
                                    </span>
                                </span>

                                <flux:badge
                                    size="sm"
                                    color="{{ $editValues['target_exists'] ?? false ? 'green' : 'amber' }}"
                                >
                                    {{ $editValues['target_exists'] ?? false ? __('ui.translation.translation-exists') : __('Translation missing') }}
                                </flux:badge>

                                @if ($sourceTranslationVariables !== [])
                                    <x-ui.tooltip.simple
                                        :title="$missingTargetTranslationVariables !== [] ? __('ui.variables.variable-missing') : __('ui.variables.variables-ok')"
                                        :text="$missingTargetTranslationVariables !== []
                                            ? __(
                                                'The target translation is missing placeholders from the source value: :variables',
                                                ['variables' => implode(', ', $missingTargetTranslationVariables)],
                                            )
                                            : __(
                                                'All source placeholders are present in the target translation: :variables',
                                                ['variables' => implode(', ', $sourceTranslationVariables)],
                                            )"
                                    >
                                        <flux:badge
                                            size="sm"
                                            color="{{ $missingTargetTranslationVariables !== [] ? 'red' : 'green' }}"
                                        >
                                            {{ $missingTargetTranslationVariables !== [] ? __('ui.variables.variable-missing') : __('ui.variables.variables-ok') }}
                                        </flux:badge>
                                    </x-ui.tooltip.simple>
                                @endif
                            </span>
                        </flux:label>

                        <flux:textarea
                            id="translation-workbench-target-translation-value"
                            rows="2"
                            wire:model.live.debounce.300ms="targetTranslationValue"
                        />
                    </flux:field>
                </div>

                @if ($subLocales->isNotEmpty())
                    <flux:separator
                        class="my-4"
                        text="{{ __('ui.languages.sub-languages') }}"
                    />

                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($subLocales as $subLocale)
                            @php
                                $isSelectedSubLocale = $selectedTargetSubLocales->contains($subLocale);
                            @endphp

                            <x-ui.tooltip.simple
                                :title="__('ui.language.target-sub-language')"
                                :text="strtoupper((string) $subLocale)"
                            >
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="subtle"
                                    wire:click="toggleTargetSubLocale('{{ $subLocale }}')"
                                    :aria-label="__('Toggle target sub-language').
                                    ' '.strtoupper((string) $subLocale)"
                                    @class([
                                        'h-8 min-w-16 items-center gap-1.5 border px-2',
                                        'border-sky-500 bg-sky-500/10 text-sky-700 dark:border-sky-400 dark:bg-sky-400/10 dark:text-sky-200' => $isSelectedSubLocale,
                                        'border-zinc-200 text-zinc-500 dark:border-zinc-700 dark:text-zinc-400' => !$isSelectedSubLocale,
                                    ])
                                >
                                    <x-ui.locale.flag
                                        :locale="$subLocale"
                                        size="md"
                                        :title="strtoupper((string) $subLocale)"
                                    />
                                    <span class="ml-2 font-mono text-sm uppercase">{{ $subLocale }}</span>
                                </flux:button>
                            </x-ui.tooltip.simple>
                        @endforeach
                    </div>

                    @if ($visibleSubLocales->isNotEmpty())
                        <div class="mt-4 grid gap-4 lg:grid-cols-2">
                            @foreach ($visibleSubLocales as $subLocale)
                                <flux:field>
                                    <flux:label>
                                        <span class="inline-flex items-center gap-2">
                                            <x-ui.locale.flag
                                                :locale="$subLocale"
                                                size="lg"
                                                :title="strtoupper((string) $subLocale)"
                                            />
                                            <span>{{ __('ui.language.target-sub-language') }}</span>
                                            <span class="font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400">
                                                {{ $subLocale }}
                                            </span>
                                        </span>
                                    </flux:label>

                                    <flux:textarea
                                        id="translation-workbench-target-sub-{{ str_replace('-', '_', (string) $subLocale) }}"
                                        rows="2"
                                        wire:model="targetSubTranslationValues.{{ $subLocale }}"
                                    />
                                </flux:field>
                            @endforeach
                        </div>
                    @endif
                @endif
            </flux:card>
        @else
            <flux:text class="text-sm text-zinc-500">
                {{ __('No finding selected.') }}
            </flux:text>
        @endif
    </div>
</flux:modal>
