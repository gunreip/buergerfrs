{{-- resources/views/components/admin/partials/translation-list/⚡modal.blade.php --}}

<flux:modal
    class="w-full max-w-7xl"
    wire:model="translationKeyModalOpen"
>
    @if ($selectedTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header with ID badge --}}
                <x-ui.headers.card
                    :title="__('Translation key review')"
                    :description="__('Read-only review of the selected translation key, its values and usage metadata.')"
                />

                {{-- Badge with translation key ID --}}
                <flux:badge
                    class="mr-8 mt-2"
                    variant="subtle"
                    color="zinc"
                >
                    #{{ $selectedTranslationKey->id }}
                </flux:badge>
            </div>

            <div class="grid gap-3 md:grid-cols-4">

                {{-- Callout components for key metadata --}}
                <flux:callout
                    color="sky"
                    icon="tag"
                >
                    {{-- Status --}}
                    <flux:callout.heading>
                        {{ __('Status') }}
                    </flux:callout.heading>

                    <flux:callout.text class="space-y-2">

                        {{-- Badge with the translation key status --}}
                        <x-ui.badge.context
                            context="translation.key.status"
                            :value="$selectedTranslationKey->status"
                            :label="str($selectedTranslationKey->status)->headline()"
                        />

                    </flux:callout.text>
                </flux:callout>

                {{-- Group Callout with namespace and group information --}}
                <flux:callout
                    color="violet"
                    icon="folder"
                >
                    {{-- Group --}}
                    <flux:callout.heading>
                        {{ __('Group') }}
                    </flux:callout.heading>

                    <flux:callout.text class="text-sm">
                        <div>
                            <span class="font-semibold">{{ __('Namespace') }}:</span>
                            {{ $selectedTranslationKey->namespace ?? '—' }}
                        </div>

                        <div>
                            <span class="font-semibold">{{ __('Group') }}:</span>
                            {{ $selectedTranslationKey->group ?? '—' }}
                        </div>
                    </flux:callout.text>
                </flux:callout>

                {{-- Source and usage callouts --}}
                <flux:callout
                    color="amber"
                    icon="scan-search"
                >
                    {{-- Source --}}
                    <flux:callout.heading>
                        {{ __('Source') }}
                    </flux:callout.heading>

                    {{-- Source-Path information --}}
                    <flux:callout.text class="text-sm">
                        {{ $selectedTranslationKey->source ?? '—' }}
                    </flux:callout.text>
                </flux:callout>

                {{-- Usage callout --}}
                <flux:callout
                    color="green"
                    icon="route"
                >
                    {{-- Usage --}}
                    <flux:callout.heading>
                        {{ __('Usage') }}
                    </flux:callout.heading>

                    {{-- Counter --}}
                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ $selectedTranslationKey->usages->count() }}
                    </flux:callout.text>
                </flux:callout>
            </div>

            @php
                $selectedKey = trim((string) ($selectedTranslationKey->key ?? ''));
                $selectedSuggestedKey = trim((string) ($selectedTranslationKey->suggested_key ?? ''));

                if ($selectedKey === '' && $selectedSuggestedKey !== '') {
                    $keySuggestionState = 'missing_key';
                    $keySuggestionLabel = __('Missing key');
                    $keySuggestionText = __(
                        'No translation key is set. The suggested key can be used as a starting point.',
                    );
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey === $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'matches_suggested_key';
                    $keySuggestionLabel = __('Matches suggested key');
                    $keySuggestionText = __('The current key matches the generated suggestion.');
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey !== $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'differs_from_suggested_key';
                    $keySuggestionLabel = __('Differs from suggested key');
                    $keySuggestionText = __(
                        'The current key differs from the generated suggestion. This can be intentional, but should be reviewed.',
                    );
                } else {
                    $keySuggestionState = 'no_suggestion';
                    $keySuggestionLabel = __('No suggestion');
                    $keySuggestionText = __('No suggested key is available for this entry.');
                }
            @endphp

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Key suggestion check') }}
                        </div>

                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $keySuggestionText }}
                        </div>
                    </div>

                    <x-ui.badge.context
                        context="translation.key.suggestion"
                        :value="$keySuggestionState"
                        :label="$keySuggestionLabel"
                    />
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-2">

                {{-- Translation Key and Native Text --}}
                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Key --}}
                    <x-ui.text.copyable-field
                        :title="__('Key')"
                        :value="$selectedTranslationKey->key"
                        :mono="true"
                    />
                </div>

                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Suggested key --}}
                    <x-ui.text.copyable-field
                        :title="__('Suggested key')"
                        :value="$selectedTranslationKey->suggested_key"
                        :mono="true"
                    />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                {{-- Native text --}}
                <div class="dark:border-zinc-700">

                    {{-- Native Text --}}
                    <x-ui.text.copyable-field
                        :title="__('Native text')"
                        :value="$selectedTranslationKey->native_text"
                    />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                {{-- Values and Usages --}}
                <div class="mb-3 flex items-center justify-between gap-3">

                    {{-- Values --}}
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Values') }}
                    </div>

                    {{-- Counter --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $selectedTranslationKey->values->count() }}
                    </flux:badge>
                </div>

                {{-- Values List --}}
                <div class="grid gap-3 md:grid-cols-2">

                    @forelse ($selectedTranslationKey->values as $value)
                        <div class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">

                            {{-- Value locale --}}
                            <x-ui.text.copyable-field
                                :value="$value->value"
                                :badge="$value->status"
                                badge-context="translation.value.status"
                            >
                                <x-slot:label>
                                    <span class="inline-flex items-center gap-2">

                                        {{-- Locale flag --}}
                                        <x-ui.locale.flag
                                            :locale="$value->locale"
                                            size="sm"
                                        />

                                        {{-- Locale code --}}
                                        <code>{{ $value->locale }}</code>
                                    </span>
                                </x-slot:label>
                            </x-ui.text.copyable-field>
                        </div>

                        {{-- No translation values available. --}}
                    @empty
                        <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No translation values available.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex min-h-0 flex-1 flex-col rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                {{-- Usages --}}
                <div class="mb-3 flex shrink-0 items-center justify-between gap-3">

                    {{-- Usages --}}
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Usages') }}
                    </div>

                    {{-- Counter --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $selectedTranslationKey->usages->count() }}
                    </flux:badge>
                </div>

                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto pr-2">
                    @forelse ($selectedTranslationKey->usages as $usage)
                        @php
                            $usageRaw = trim((string) ($usage->raw ?? ''));
                            $usageOriginalRaw = trim((string) ($usage->original_raw ?? ''));

                            $usageHasOriginalRaw = $usageOriginalRaw !== '';
                            $usageOriginalMatchesRaw = $usageHasOriginalRaw && $usageRaw === $usageOriginalRaw;
                            $usageOriginalDiffersRaw = $usageHasOriginalRaw && $usageRaw !== $usageOriginalRaw;
                        @endphp

                        <div class="text-sm dark:border-zinc-700">
                            <div
                                class="flex flex-wrap items-center justify-between gap-2 border-t pt-1 dark:border-zinc-700">
                                <div class="">
                                    <span class="font-semibold">{{ __('Path') }}:</span>
                                    <code class="wrap-anywhere whitespace-normal px-3 text-xs">
                                        {{ $usage->file ?? '—' }}
                                    </code>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if ($usageOriginalMatchesRaw)
                                        {{--
                                        TODO: Z-Index für die Tooltips!
                                        --}}
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('Original raw unchanged')"
                                            :text="__(
                                                'The current raw usage snippet matches the original raw reference captured for this usage.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="green"
                                            >
                                                {{ __('Original unchanged') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @elseif ($usageOriginalDiffersRaw)
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('Original raw differs')"
                                            :text="__(
                                                'The current raw usage snippet differs from the original raw reference. The original raw value is preserved below.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ __('Original changed') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @else
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('No original raw reference')"
                                            :text="__(
                                                'No original raw reference has been captured for this usage yet.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                {{ __('No original raw') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif

                                    @if (!empty($usage->line))
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ __('Line') }} {{ $usage->line }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>

                            @if ($usageRaw !== '')
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('Current raw')"
                                    :value="$usage->raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif

                            @if ($usageOriginalDiffersRaw)
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('Original raw')"
                                    :value="$usage->original_raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif
                        </div>
                    @empty
                        <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No usage records available.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex shrink-0 justify-end gap-3">
                <flux:button
                    type="button"
                    variant="primary"
                    color="amber"
                    icon="pen-line"
                    :disabled="$selectedKey === ''"
                    wire:click="openTranslationEditFromReview({{ $selectedTranslationKey->id }})"
                >
                    {{ __('Edit') }}
                </flux:button>

                <x-ui.button.cancel
                    label="{{ __('Close') }}"
                    icon="circle-x"
                    wire:click="closeTranslationKey"
                />
            </div>
        </div>
    @else
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('No translation key selected.') }}
        </div>
    @endif
</flux:modal>
