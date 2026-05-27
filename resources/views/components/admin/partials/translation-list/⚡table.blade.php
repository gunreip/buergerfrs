{{-- resources/views/components/admin/partials/translation-list/⚡table.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Translation List')"
        :description="__('Review and manage translation keys, their values across languages, and associated metadata.')"
    >
        @php
            $appLanguages = $translationLanguages->where('is_enabled_for_app', true);
        @endphp

        @if ($appLanguages->isNotEmpty())
            <div class="flex flex-wrap items-center justify-end gap-1.5">
                @foreach ($appLanguages as $translationLanguage)
                    <flux:badge
                        label="{{ $translationLanguage->native_name ?: $translationLanguage->name ?: $translationLanguage->locale }}"
                    >
                        <x-ui.locale.flag
                            :locale="$translationLanguage->locale"
                            size="lg"
                        />

                        <span class="ml-2 font-mono uppercase">
                            {{ $translationLanguage->locale }}
                        </span>
                    </flux:badge>
                @endforeach
            </div>
        @endif
    </x-ui.headers.card>

    <div
        class="mx-auto max-w-full scroll-mt-6"
        id="translation-list-table"
    >
        <div class="overflow-hidden rounded-t-lg">

            {{-- Table --}}
            {{-- ID, Status, Key/Suggested Key, Native Text, Values, Usage, Actions --}}
            <flux:table class="app-table">

                {{-- Table Headers with tooltips for additional context on each column --}}
                <flux:table.columns class="bg-zinc-800 text-zinc-400">

                    {{-- Column ID --}}
                    <flux:table.column
                        class="w-32 tabular-nums"
                        sortable
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('ID')"
                            :text="__('Internal database ID of the translation key.')"
                        >
                            {{ __('ID') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Status --}}
                    <flux:table.column
                        class="w-24"
                        sortable
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('Status')"
                            :text="__(
                                'Current status of the translation key, useful for identification and reference.',
                            )"
                        >
                            {{ __('Status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Key / Suggested Key --}}
                    <flux:table.column
                        class="w-(--translation-balanced-column-width)"
                        sortable
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Key / Suggested Key')"
                            :text="__('Translation key or suggested key, useful for identification and reference.')"
                        >
                            {{ __('Key / Suggested Key') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Native Text --}}
                    <flux:table.column
                        class="w-(--translation-balanced-column-width)"
                        sortable
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Native Text')"
                            :text="__(
                                'Original text in the source language, useful for identification and reference.',
                            )"
                        >
                            {{ __('Native Text') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Values --}}
                    <flux:table.column class="w-(--translation-balanced-column-width)">
                        <x-ui.tooltip.trigger
                            :title="__('Values')"
                            :text="__(
                                'Translated values for the key across different languages, useful for identification and reference.',
                            )"
                        >
                            {{ __('Values') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Usage --}}
                    <flux:table.column
                        class="w-36"
                        sortable
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Usage')"
                            :text="__(
                                'Usage information of the translation key, useful for identification and reference.',
                            )"
                        >
                            {{ __('Usage') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actions --}}
                    <flux:table.column
                        class="w-32"
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            class="mr-3"
                            :title="__('Actions')"
                            :text="__('Open the translation key review modal.')"
                        >
                            {{ __('Actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                {{-- Table rows --}}
                <flux:table.rows>
                    @forelse ($translationKeys as $translationKey)

                        {{-- Table row --}}
                        <flux:table.row
                            wire:key="translation-key-{{ $translationKey->id }}"
                            @class([
                                'transition-colors',
                                'bg-sky-50/80 ring-1 ring-inset ring-sky-300 dark:bg-sky-950/30 dark:ring-sky-700' =>
                                    $focusedTranslationKeyId === $translationKey->id,
                            ])
                        >

                            {{-- Cell ID --}}
                            <flux:table.cell
                                class="w-32 align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                align="end"
                            >
                                #{{ $translationKey->id }}
                            </flux:table.cell>

                            {{-- Cell Status --}}
                            <flux:table.cell
                                class="align-top"
                                align="center"
                            >
                                <div class="space-y-2">
                                    <div class="space-y-2">
                                        <x-ui.badge.context
                                            context="translation.key.status"
                                            :value="$translationKey->status"
                                            :label="str($translationKey->status)->headline()"
                                            size="sm"
                                        />
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- Cell Key / Suggested Key --}}
                            <flux:table.cell class="align-top">
                                @php
                                    $key = trim((string) ($translationKey->key ?? ''));
                                    $suggestedKey = trim((string) ($translationKey->suggested_key ?? ''));
                                    $canEditTranslations = $key !== '';
                                    $canOpenHistory = (int) ($translationKey->history_events_count ?? 0) > 0;

                                    if ($key === '' && $suggestedKey !== '') {
                                        $keySuggestionState = 'missing_key';
                                        $keySuggestionLabel = __('Missing key');
                                    } elseif ($key !== '' && $suggestedKey !== '' && $key === $suggestedKey) {
                                        $keySuggestionState = 'matches_suggested_key';
                                        $keySuggestionLabel = __('Matches suggested key');
                                    } elseif ($key !== '' && $suggestedKey !== '' && $key !== $suggestedKey) {
                                        $keySuggestionState = 'differs_from_suggested_key';
                                        $keySuggestionLabel = __('Differs from suggested key');
                                    } else {
                                        $keySuggestionState = 'no_suggestion';
                                        $keySuggestionLabel = __('No suggestion');
                                    }
                                @endphp

                                <div class="space-y-2">
                                    <div class="space-y-1">
                                        <div
                                            class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                            {{ __('Key') }}
                                        </div>

                                        @if ($key !== '')
                                            <div
                                                class="wrap-anywhere whitespace-normal font-mono text-zinc-900 dark:text-zinc-100">
                                                {{ $key }}
                                            </div>
                                        @else
                                            <div class="text-zinc-400">
                                                —
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-1">
                                        <div
                                            class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                            {{ __('Suggested key') }}
                                        </div>

                                        @if ($suggestedKey !== '')
                                            <div
                                                class="wrap-anywhere whitespace-normal font-mono text-amber-700 dark:text-amber-300">
                                                {{ $suggestedKey }}
                                            </div>
                                        @else
                                            <div class="text-zinc-400">
                                                —
                                            </div>
                                        @endif
                                    </div>

                                    <x-ui.badge.context
                                        context="translation.key.suggestion"
                                        :value="$keySuggestionState"
                                        :label="$keySuggestionLabel"
                                        size="sm"
                                    />

                                    <div class="text-zinc-500 dark:text-zinc-400">
                                        {{ $translationKey->namespace ?? '—' }}
                                        @if ($translationKey->group)
                                            / {{ $translationKey->group }}
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- Cell Native Text --}}
                            <flux:table.cell class="align-top">
                                <div class="max-w-md text-wrap text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $translationKey->native_text ?: '—' }}
                                </div>
                            </flux:table.cell>

                            {{-- Cell Values --}}
                            <flux:table.cell class="align-top">
                                <div class="space-y-2">
                                    @forelse ($translationKey->values as $value)
                                        <div
                                            class="max-w-full rounded-lg border border-zinc-200 p-2 dark:border-zinc-200/30">
                                            <div class="mb-1 flex items-center justify-between gap-2">
                                                <span class="min-w-0 font-mono font-semibold">
                                                    <x-ui.locale.flag
                                                        class="-mt-1"
                                                        :locale="$value->locale"
                                                        size="sm"
                                                    />

                                                    {{ $value->locale }}
                                                </span>

                                                <x-ui.badge.context
                                                    context="translation.value.status"
                                                    :value="$value->status"
                                                    size="sm"
                                                />
                                            </div>

                                            <div
                                                class="wrap-anywhere max-h-36 max-w-full overflow-y-auto hyphens-auto whitespace-normal pr-1 text-zinc-600 dark:text-zinc-300"
                                                lang="{{ $value->locale }}"
                                            >
                                                {{ $value->value ?: '—' }}
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-zinc-400">
                                            —
                                        </span>
                                    @endforelse
                                </div>
                            </flux:table.cell>

                            {{-- Cell Last seen --}}
                            <flux:table.cell class="align-top tabular-nums">
                                <div class="space-y-1 text-zinc-500 dark:text-zinc-400">
                                    <div>
                                        <span
                                            class="font-semibold tabular-nums">{{ $translationKey->usages_count }}</span>
                                        {{ __('usage(s)') }}
                                    </div>

                                    @if ($translationKey->last_seen_at)
                                        <div>
                                            <div class="app-table-cell-item-header">
                                                {{ __('Last seen') }}:
                                            </div>
                                            <div class="app-table-cell-item-timestamp">
                                                <x-ui.date-time.date :value="$translationKey->last_seen_at" />
                                            </div>
                                            <div class="app-table-cell-item-timestamp">
                                                <x-ui.date-time.time :value="$translationKey->last_seen_at" />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Cell Actions / Review / Edit --}}
                            <flux:table.cell
                                class="align-top"
                                align="center"
                            >
                                <div class="grid grid-cols-1 place-items-center space-y-3">
                                    {{-- Review button --}}
                                    <x-ui.button.review
                                        size="sm"
                                        wire:click="openTranslationKey({{ $translationKey->id }})"
                                    />

                                    {{-- Edit button --}}
                                    <x-ui.button.edit
                                        size="sm"
                                        :disabled="!$canEditTranslations"
                                        wire:click="openTranslationEdit({{ $translationKey->id }})"
                                    />

                                    {{-- History button --}}
                                    <flux:button
                                        type="button"
                                        size="sm"
                                        variant="primary"
                                        color="zinc"
                                        icon="history"
                                        :disabled="!$canOpenHistory"
                                        :title="$canOpenHistory ? __('Open history') : __('No history entries available')"
                                        wire:click="openTranslationHistory({{ $translationKey->id }})"
                                    >
                                        {{ __('History') }}
                                    </flux:button>

                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>

                            {{-- No translation records found --}}
                            <flux:table.cell colspan="7">
                                <div class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No translation records found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($translationKeys->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('Pagination') }}"
            />

            {{-- Pagination --}}
            <div class="mt-4">
                <flux:pagination
                    :paginator="$translationKeys"
                    scroll-to="#translation-list-table"
                />
            </div>
        @endif

    </div>
</flux:card>
