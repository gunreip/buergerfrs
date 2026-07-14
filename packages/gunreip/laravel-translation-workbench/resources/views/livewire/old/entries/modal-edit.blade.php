{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit.blade.php --}}

{{-- Modal Edit --}}
<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    id="translation-workbench-entry-edit"
    name="translation-workbench-entry-edit"
    wire:model="editModalOpen"
>
    @if ($entry)
        @php
            $isUiConfirmed = (bool) $entry->is_ui_key && !(bool) $entry->is_dynamic_key;
            $isUiRejected = (bool) $entry->is_ui_candidate_rejected && !$isUiConfirmed;
            $isDynamicConfirmed = (bool) $entry->is_dynamic_key;
            $isDynamicRejected = (bool) $entry->is_dynamic_candidate_rejected && !$isDynamicConfirmed;
            $isDynamicMultiConfirmed = $isDynamicConfirmed && (bool) $entry->is_dynamic_multi;
            $isUiCandidateVisible = $entry->candidate_type === 'ui' && !$isUiConfirmed && !$isUiRejected && !$isDynamicConfirmed;
            $isDynamicCandidateVisible =
                ($entry->candidate_type === 'dynamic' || $entry->entry_type === 'dynamic') && !$isDynamicConfirmed && !$isDynamicRejected && !$isUiConfirmed;
            $suggestedKey = trim((string) $entry->suggested_key);
            $translationKey = trim((string) $entry->translation_key);
            $keyState = match (true) {
                $suggestedKey !== '' && $translationKey === '' => [
                    'label' => __('Missing translation key'),
                    'color' => 'amber',
                ],
                $suggestedKey !== '' && $translationKey === $suggestedKey => [
                    'label' => __('Equal translation key'),
                    'color' => 'emerald',
                ],
                $suggestedKey !== '' && $translationKey !== '' && $translationKey !== $suggestedKey => [
                    'label' => __('Different translation key'),
                    'color' => 'sky',
                ],
                default => [
                    'label' => __('No suggested key'),
                    'color' => 'zinc',
                ],
            };
        @endphp

        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header --}}
                <x-ui.headers.card
                    :title="__('Translation edit')"
                    :description="__('Edit translation values for the selected workbench entry.')"
                />

                <div class="mr-8 mt-2 flex items-center gap-2">

                    {{-- Badge ID --}}
                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                        color="zinc"
                    >
                        #{{ $entry->id }}
                    </flux:badge>

                    {{-- Save Translation Button --}}
                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="save"
                        :aria-label="__('Save translation')"
                        wire:click="saveTranslationValue"
                    />

                    {{-- Open Next Translation Entry Button --}}
                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-right"
                        :disabled="$nextTranslationEntryId === null"
                        :aria-label="__('Next translation entry')"
                        wire:click="openNextTranslationEntry"
                    />

                    {{-- Close Button --}}
                    {{-- <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="x"
                        :aria-label="__('Close')"
                        wire:click="closeEditModal"
                    /> --}}
                </div>
            </div>

            <div class="min-h-0 overflow-y-auto pr-2">
                <div class="grid gap-3 md:grid-cols-5">
                    {{-- Callout Type --}}
                    <flux:callout
                        color="sky"
                        icon="tag"
                    >
                        {{-- Callout Type Heading --}}
                        <flux:callout.heading>{{ __('Type') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            <div class="flex flex-wrap gap-1.5">
                                {{-- Badge Kind --}}
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('Kind') }}: {{ str($entry->kind)->headline() }}
                                </flux:badge>
                                {{-- Badge Entry --}}
                                @if ($entry->entry_type)
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >
                                        {{ __('Entry') }}: {{ str($entry->entry_type)->headline() }}
                                    </flux:badge>
                                @endif
                                {{-- Badge UI Candidate --}}
                                @if ($isUiCandidateVisible)
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Candidate') }}: {{ __('UI') }}
                                    </flux:badge>
                                @endif
                                {{-- Badge Dynamic Candidate --}}
                                @if ($isDynamicCandidateVisible)
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Candidate') }}: {{ __('Dynamic') }}
                                    </flux:badge>
                                @endif
                                {{-- Badge UI Confirmed --}}
                                @if ($isUiConfirmed)
                                    <flux:badge
                                        size="sm"
                                        color="emerald"
                                    >
                                        {{ __('Is UI') }}
                                    </flux:badge>
                                @endif
                                {{-- Badge UI Rejected --}}
                                @if ($isUiRejected)
                                    <flux:badge
                                        size="sm"
                                        color="rose"
                                    >
                                        {{ __('Is no UI') }}
                                    </flux:badge>
                                @endif
                                {{-- Badge Dynamic Confirmed --}}
                                @if ($isDynamicConfirmed)
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >
                                        {{ __('Is dynamic') }}
                                    </flux:badge>
                                @endif
                                {{-- Badge Dynamic Rejected --}}
                                @if ($isDynamicRejected)
                                    <flux:badge
                                        size="sm"
                                        color="rose"
                                    >
                                        {{ __('Is no dynamic') }}
                                    </flux:badge>
                                @endif
                                {{-- Badge Dynamic Multi Confirmed --}}
                                @if ($isDynamicMultiConfirmed)
                                    <flux:badge
                                        size="sm"
                                        color="violet"
                                    >
                                        {{ __('Is dynamic multi') }}
                                    </flux:badge>
                                @endif
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $entry->candidate_reason ?: __('No candidate reason') }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Status --}}
                    <flux:callout
                        color="emerald"
                        icon="activity"
                    >
                        {{-- Callout Status Heading --}}
                        <flux:callout.heading>{{ __('Status') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            {{-- Badge Status --}}
                            <flux:badge
                                size="sm"
                                :color="$entry->status === 'obsolete' ? 'zinc' : 'emerald'"
                            >
                                {{ str($entry->status)->headline() }}
                            </flux:badge>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Review --}}
                    <flux:callout
                        color="lime"
                        icon="badge-check"
                    >
                        {{-- Callout Heading Review --}}
                        <flux:callout.heading>{{ __('Review') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            {{-- Badge Review Status --}}
                            <flux:badge
                                size="sm"
                                :color="match ($entry->review_status) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'reviewed', 'approved' => 'emerald',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'needs_attention' => 'amber',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    default => 'zinc',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }"
                            >
                                {{ str($entry->review_status)->headline() }}
                            </flux:badge>

                            <flux:badge
                                size="sm"
                                :color="match ($entry->translation_key_source) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'manual' => 'sky',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'suggested' => 'emerald',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'code' => 'violet',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    default => 'zinc',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }"
                            >
                                {{ __('Key') }}:
                                {{ match ($entry->translation_key_source) {
                                    'manual' => __('Manual'),
                                    'suggested' => __('Suggested'),
                                    'code' => __('Code'),
                                    default => __('Undecided'),
                                } }}
                            </flux:badge>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Namespace --}}
                    <flux:callout
                        color="violet"
                        icon="folder"
                    >
                        {{-- Callout Namespace Heading --}}
                        <flux:callout.heading>{{ __('Namespace') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-1 text-sm">
                            <div class="truncate font-mono text-xs">{{ $entry->namespace ?: '—' }}</div>
                            <div class="truncate font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $entry->group ?: '—' }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Source --}}
                    <flux:callout
                        color="amber"
                        icon="map-pin"
                    >
                        @php
                            $sourceAbsolutePath = str_replace('\\', '/', base_path($entry->source_path));
                            $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
                            $sourceEditorLine = $entry->source_line ? ':' . $entry->source_line : '';
                            $sourceEditorUrl =
                                'vscode://vscode-remote/wsl+' .
                                rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                                $sourceEditorPath .
                                $sourceEditorLine;
                        @endphp

                        {{-- Callout Source Heading --}}
                        <flux:callout.heading>
                            <span class="flex w-full items-center justify-between gap-2">
                                <span>{{ __('Source') }}</span>
                                <flux:button
                                    class="h-5 w-5 shrink-0"
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    icon="external-link"
                                    icon:class="text-red-500 dark:text-red-400"
                                    :href="$sourceEditorUrl"
                                    :aria-label="__('Open source in VS Code')"
                                />
                            </span>
                        </flux:callout.heading>

                        <flux:callout.text class="space-y-1 text-sm">

                            <div class="flex items-start gap-1.5">
                                <div
                                    class="wrap-anywhere text-wrap font-mono text-xs"
                                    title="{{ $entry->source_path }}{{ $entry->source_line ? ':' . $entry->source_line : '' }}"
                                >
                                    {{ $entry->source_path }}
                                    {{ $entry->source_line ? ':' . $entry->source_line : '' }}
                                </div>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ __('Scans') }} {{ number_format($entry->scan_count ?? 0) }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    {{-- Card Keys --}}
                    <flux:card>
                        <x-ui.headers.card
                            :title="__('Keys')"
                            :description="__('Read-only key metadata from review and scanner state.')"
                        >
                            <flux:badge
                                size="sm"
                                :color="$keyState['color']"
                            >
                                {{ $keyState['label'] }}
                            </flux:badge>
                        </x-ui.headers.card>

                        <div class="space-y-3 text-sm">
                            <div>
                                <div
                                    class="flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Translation key') }}</span>
                                    <x-ui.tooltip.trigger
                                        :title="__('Key reference')"
                                        :text="__('Existing and suggested key metadata from the review scanner.')"
                                    >
                                        <flux:icon.information-circle class="size-3.5" />

                                        <x-slot:tooltip>
                                            <div class="grid min-w-72 gap-2 text-xs">
                                                <div>
                                                    <div class="mb-1 font-medium text-amber-300">
                                                        {{ __('Existing key') }}
                                                    </div>
                                                    <div class="break-all font-mono">{{ $entry->existing_key ?: '—' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="mb-1 font-medium text-amber-300">
                                                        {{ __('Suggested key') }}
                                                    </div>
                                                    <div class="break-all font-mono">{{ $entry->suggested_key ?: '—' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </x-slot:tooltip>
                                    </x-ui.tooltip.trigger>
                                </div>
                                <div class="break-all font-mono text-xs">{{ $entry->translation_key ?: '—' }}</div>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Card Source Values --}}
                    <flux:card>
                        <x-ui.headers.card
                            :title="__('Source value')"
                            :description="__('Literal, suggested literal and raw expression from the scanner.')"
                        />

                        <div class="space-y-3 text-sm">
                            <div>
                                <div
                                    class="flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Literal text') }}</span>
                                    <x-ui.tooltip.trigger
                                        :title="__('Source reference')"
                                        :text="__(
                                            'Suggested literal and raw expression metadata from the scanner.',
                                        )"
                                    >
                                        <flux:icon.information-circle class="size-3.5" />

                                        <x-slot:tooltip>
                                            <div class="grid min-w-72 gap-2 text-xs">
                                                <div>
                                                    <div class="mb-1 font-medium text-amber-300">
                                                        {{ __('Literal text suggested') }}
                                                    </div>
                                                    <div class="break-words">
                                                        {{ $entry->literal_text_suggested ?: '—' }}</div>
                                                </div>
                                                <div>
                                                    <div class="mb-1 font-medium text-amber-300">
                                                        {{ __('Raw expression') }}
                                                    </div>
                                                    <div class="max-h-40 overflow-auto break-all font-mono">
                                                        {{ $entry->raw_expression ?: '—' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </x-slot:tooltip>
                                    </x-ui.tooltip.trigger>
                                </div>
                                <div class="break-words">{{ $entry->literal_text ?: '—' }}</div>
                            </div>

                            @if (blank($entry->literal_text))
                                <div>
                                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ __('Literal text suggested') }}
                                    </div>
                                    <div class="break-words">{{ $entry->literal_text_suggested ?: '—' }}</div>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                </div>

                {{-- Card Translation Values --}}
                <flux:card class="mt-6">
                    <x-ui.headers.card
                        :title="__('Translation values')"
                        :description="__('Translation value editing will be handled here.')"
                    >
                        @php
                            $editLocales = $editLocales ?? [
                                'source' => 'en',
                                'active' => app()->getLocale(),
                                'sub' => [],
                            ];
                            $sourceLocale = (string) ($editLocales['source'] ?? 'en');
                            $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
                            $subLocales = collect((array) ($editLocales['sub'] ?? []))
                                ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
                                ->values();
                        @endphp

                        <div class="flex items-center gap-2">
                            <x-ui.tooltip.trigger
                                :title="__('Source language')"
                                :text="strtoupper($sourceLocale)"
                            >
                                <span
                                    class="inline-flex items-center gap-1 rounded border border-zinc-700/60 px-1.5 py-1 dark:border-zinc-200/60"
                                >
                                    <x-ui.locale.flag
                                        :locale="$sourceLocale"
                                        size="md"
                                        :title="strtoupper($sourceLocale)"
                                    />
                                    <span class="font-mono text-sm uppercase">{{ $sourceLocale }}</span>
                                </span>
                            </x-ui.tooltip.trigger>

                            <span class="h-4 w-px bg-zinc-200 dark:bg-zinc-700"></span>

                            <x-ui.tooltip.trigger
                                :title="__('Active language')"
                                :text="strtoupper($activeLocale)"
                            >
                                <span
                                    class="inline-flex items-center gap-1 rounded border border-zinc-700/60 px-1.5 py-1 dark:border-zinc-200/60"
                                >
                                    <x-ui.locale.flag
                                        :locale="$activeLocale"
                                        size="md"
                                        :title="strtoupper($activeLocale)"
                                    />
                                    <span class="font-mono text-sm uppercase">{{ $activeLocale }}</span>
                                </span>
                            </x-ui.tooltip.trigger>

                            @if ($subLocales->isNotEmpty())
                                <span class="h-4 w-px bg-zinc-200 dark:bg-zinc-700"></span>

                                <span
                                    class="inline-flex flex-wrap items-center gap-2 rounded border border-zinc-700/60 px-1.5 py-1 dark:border-zinc-200/60"
                                >
                                    @foreach ($subLocales as $subLocale)
                                        <x-ui.tooltip.trigger
                                            :title="__('Active sub-language')"
                                            :text="strtoupper((string) $subLocale)"
                                        >
                                            <span class="inline-flex items-center gap-1">
                                                <x-ui.locale.flag
                                                    :locale="$subLocale"
                                                    size="md"
                                                    :title="strtoupper((string) $subLocale)"
                                                />
                                                <span class="font-mono text-sm uppercase">{{ $subLocale }}</span>
                                            </span>
                                        </x-ui.tooltip.trigger>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </x-ui.headers.card>

                    @php
                        $editValues = $editValues ?? [
                            'source' => null,
                            'target' => null,
                            'source_exists' => false,
                            'target_exists' => false,
                            'source_origin' => 'missing',
                        ];
                        $sourceOrigin = (string) ($editValues['source_origin'] ?? 'missing');
                        $sourceBadge = match ($sourceOrigin) {
                            'translation_value' => [
                                'label' => __('Translation exists'),
                                'color' => 'emerald',
                            ],
                            'workbench_value' => [
                                'label' => __('Workbench source'),
                                'color' => 'sky',
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

                        if (blank($entry->translation_key)) {
                            $editWarnings[] = [
                                'label' => __('Translation key missing'),
                                'text' => __('This entry should be reviewed before translation values are edited.'),
                                'color' => 'red',
                            ];
                        }

                        if (blank($editValues['source'] ?? null)) {
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
                            filled($editValues['source'] ?? null) &&
                            filled($targetTranslationValue) &&
                            trim((string) ($editValues['source'] ?? '')) === trim((string) $targetTranslationValue)
                        ) {
                            $editWarnings[] = [
                                'label' => __('Source equals target'),
                                'text' => __('The main target-language value is identical to the source-language value.'),
                                'color' => 'sky',
                            ];
                        }
                    @endphp

                    @if ($editWarnings !== [])
                        <div class="mb-3 flex flex-wrap gap-2">
                            @foreach ($editWarnings as $warning)
                                <x-ui.tooltip.trigger
                                    :title="$warning['label']"
                                    :text="$warning['text']"
                                >
                                    <flux:badge
                                        size="sm"
                                        :color="$warning['color']"
                                    >
                                        {{ $warning['label'] }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-3 grid gap-4 lg:grid-cols-2">
                        <flux:field>
                            <flux:label>
                                <span class="flex w-full items-center gap-2">
                                    <span class="inline-flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$sourceLocale"
                                            size="lg"
                                            :title="strtoupper($sourceLocale)"
                                        />
                                        <span>{{ __('Source language') }}</span>
                                        <span
                                            class="-mb-1 font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400"
                                        >
                                            {{ $sourceLocale }}
                                        </span>
                                        <flux:badge
                                            class="-mb-1"
                                            size="sm"
                                            :color="$sourceBadge['color']"
                                        >
                                            {{ $sourceBadge['label'] }}
                                        </flux:badge>
                                    </span>

                                    <span class="ms-auto inline-flex items-center gap-2">
                                        {{-- Copy Source to Target Button --}}
                                        <flux:button
                                            class="-mb-1 h-6 w-6 shrink-0"
                                            type="button"
                                            size="xs"
                                            variant="ghost"
                                            icon="copy"
                                            :disabled="blank($editValues['source'] ?? null)"
                                            :aria-label="__('Copy source to target')"
                                            wire:click="copySourceToTargetValue"
                                        />
                                    </span>
                                </span>
                            </flux:label>
                            <flux:textarea
                                rows="2"
                                readonly
                            >{{ $editValues['source'] ?? '' }}</flux:textarea>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <span class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$activeLocale"
                                            size="lg"
                                            :title="strtoupper($activeLocale)"
                                        />
                                        <span>{{ __('Target language') }}</span>
                                        <span
                                            class="-mb-1 font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400"
                                        >
                                            {{ $activeLocale }}
                                        </span>
                                    </span>
                                    <flux:badge
                                        class="-mb-1"
                                        size="sm"
                                        :color="($editValues['target_exists'] ?? false) ? 'emerald' : 'amber'"
                                    >
                                        {{ $editValues['target_exists'] ?? false ? __('Translation exists') : __('Translation missing') }}
                                    </flux:badge>
                                </span>
                            </flux:label>
                            <flux:textarea
                                id="translation-workbench-target-translation-value"
                                rows="2"
                                wire:model="targetTranslationValue"
                            />
                        </flux:field>
                    </div>

                    <flux:separator text="{{ __('Sub-languages') }}" />

                    @if ($subLocales->isNotEmpty())
                        @php
                            $selectedTargetSubLocales = collect($selectedTargetSubLocales ?? [])
                                ->filter(static fn(mixed $locale): bool => is_string($locale) && trim($locale) !== '')
                                ->values();
                            $visibleSubLocales = $subLocales
                                ->filter(
                                    static fn(string $locale): bool => $selectedTargetSubLocales->contains($locale),
                                )
                                ->values();
                        @endphp

                        {{-- Sub Target Language Section --}}

                        {{-- Sub Target Language Toggle Buttons --}}
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            @foreach ($subLocales as $subLocale)
                                @php
                                    $isSelectedSubLocale = $selectedTargetSubLocales->contains($subLocale);
                                @endphp

                                <x-ui.tooltip.trigger
                                    :title="__('Target sub-language')"
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
                                </x-ui.tooltip.trigger>
                            @endforeach
                        </div>

                        <flux:separator />

                        @if ($visibleSubLocales->isNotEmpty())
                            {{-- Sub Target Language Textareas --}}
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
                                                <span>{{ __('Target sub-language') }}</span>
                                                <span
                                                    class="-mb-1 font-mono text-sm uppercase text-zinc-500 dark:text-zinc-400"
                                                >
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
            </div>
        </div>
    @endif
</flux:modal>
