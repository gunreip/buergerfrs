{{-- resources/views/components/admin/partials/translation-sub-languages/⚡translations-table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Translation Entry Details')"
        :description="__(
            'OK translation keys with values for en, the selected main language and currently selected sub-languages.',
        )"
    >
        @if ($baseLocaleFilter !== '')
            <div class="flex items-center justify-end gap-3">
                <x-ui.table.per-page-selector
                    id="translation-entries-per-page"
                    name="translation-entries-per-page"
                    model="translationRowsPerPage"
                    :label="__('Per Page')"
                />
            </div>
        @endif
    </x-ui.headers.card>

    @if ($baseLocaleFilter === '')
        <flux:callout
            class="mt-6"
            color="amber"
            icon="triangle-alert"
        >
            <flux:callout.heading>
                {{ __('Main language required') }}
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('Select a main language to load translation entries for en and the selected locale scope.') }}
            </flux:callout.text>
        </flux:callout>
    @elseif ($translationRows->total() === 0)
        <flux:callout
            class="mt-6"
            color="zinc"
            icon="database"
        >
            <flux:callout.heading>
                {{ __('No matching translation entries found') }}
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('No OK translation keys with en and :locale values were found for the current selection.', ['locale' => strtoupper($baseLocaleFilter)]) }}
            </flux:callout.text>
        </flux:callout>
    @else
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <flux:badge
                color="sky"
                variant="subtle"
            >
                {{ __('Rows') }}: {{ number_format($translationRows->total()) }}
            </flux:badge>

            <flux:badge
                color="amber"
                variant="subtle"
            >
                Duplicate badge: same as main language
            </flux:badge>

            <flux:badge
                color="zinc"
                variant="subtle"
            >
                <span class="inline-flex items-center gap-1">
                    {{ __('Locales') }}:
                </span>

                <span class="ml-1 inline-flex flex-wrap items-center gap-2">
                    @foreach ($translationScopeLocales as $scopeLocale)
                        <span class="inline-flex items-center gap-1">
                            <x-ui.locale.flag
                                :locale="$scopeLocale"
                                size="sm"
                                :title="strtoupper($scopeLocale)"
                            />
                            <span>{{ strtoupper($scopeLocale) }}</span>
                        </span>
                    @endforeach
                </span>
            </flux:badge>
        </div>

        <div class="mt-4 overflow-hidden rounded-t-lg">
            <flux:table class="app-table">
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column
                        class="w-24"
                        align="center"
                        sortable
                        :sorted="$translationRowsSortField === 'id'"
                        :direction="$translationRowsSortDirection"
                        wire:click="sortTranslationRowsBy('id')"
                    >
                        {{ __('ID') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        :sorted="$translationRowsSortField === 'key'"
                        :direction="$translationRowsSortDirection"
                        wire:click="sortTranslationRowsBy('key')"
                    >
                        {{ __('Translation key') }}
                    </flux:table.column>

                    <flux:table.column
                        class="w-56"
                        sortable
                        :sorted="$translationRowsSortField === 'value:en'"
                        :direction="$translationRowsSortDirection"
                        wire:click="sortTranslationRowsBy('value:en')"
                    >
                        <span class="inline-flex items-center gap-1">
                            <x-ui.locale.flag
                                title="EN"
                                locale="en"
                                size="sm"
                            />
                            EN
                        </span>
                    </flux:table.column>

                    <flux:table.column
                        class="w-56"
                        sortable
                        :sorted="$translationRowsSortField === 'value:'.$baseLocaleFilter"
                        :direction="$translationRowsSortDirection"
                        wire:click="sortTranslationRowsBy('value:{{ $baseLocaleFilter }}')"
                    >
                        <span class="inline-flex items-center gap-1">
                            <x-ui.locale.flag
                                :locale="$baseLocaleFilter"
                                size="sm"
                                :title="strtoupper($baseLocaleFilter)"
                            />
                            {{ strtoupper($baseLocaleFilter) }}
                        </span>
                    </flux:table.column>

                    @foreach ($selectedSubLanguageLocales as $selectedSubLocale)
                        <flux:table.column
                            class="w-56"
                            sortable
                            :sorted="$translationRowsSortField === 'value:'.$selectedSubLocale"
                            :direction="$translationRowsSortDirection"
                            wire:click="sortTranslationRowsBy('value:{{ $selectedSubLocale }}')"
                        >
                            <span class="inline-flex items-center gap-1">
                                <x-ui.locale.flag
                                    :locale="$selectedSubLocale"
                                    size="sm"
                                    :title="strtoupper($selectedSubLocale)"
                                />
                                {{ strtoupper($selectedSubLocale) }}
                            </span>
                        </flux:table.column>
                    @endforeach

                    <flux:table.column
                        class="w-36"
                        align="center"
                    >
                        {{ __('Action') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($translationRows as $translationRow)
                        <flux:table.row wire:key="translation-scope-row-{{ $translationRow->id }}">
                            <flux:table.cell
                                class="tabular-nums text-zinc-500 dark:text-zinc-400"
                                align="end"
                            >
                                #{{ $translationRow->id }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="min-w-0">
                                    <div
                                        class="truncate font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ $translationRow->key }}
                                    </div>

                                    <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $translationRow->namespace ?: '—' }} / {{ $translationRow->group ?: '—' }}
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="line-clamp-2 text-sm text-zinc-700 dark:text-zinc-200">
                                    {{ trim((string) ($translationRow->values['en'] ?? '')) !== '' ? $translationRow->values['en'] : '—' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="line-clamp-2 text-sm text-zinc-700 dark:text-zinc-200">
                                    {{ trim((string) ($translationRow->values[$baseLocaleFilter] ?? '')) !== '' ? $translationRow->values[$baseLocaleFilter] : '—' }}
                                </div>
                            </flux:table.cell>

                            @foreach ($selectedSubLanguageLocales as $selectedSubLocale)
                                <flux:table.cell>
                                    @php
                                        $subValue = trim((string) ($translationRow->values[$selectedSubLocale] ?? ''));
                                        $subValueMeta = $translationRow->value_meta[$selectedSubLocale] ?? null;
                                        $isBaseDuplicate = is_array($subValueMeta)
                                            ? $subValueMeta['is_base_duplicate'] ?? null
                                            : null;
                                    @endphp

                                    <div class="space-y-2">
                                        <div class="line-clamp-2 text-sm text-zinc-700 dark:text-zinc-200">
                                            {{ $subValue !== '' ? $subValue : '—' }}
                                        </div>

                                        @if ($subValue !== '')
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                @if ($isBaseDuplicate === true)
                                                    <flux:badge
                                                        size="sm"
                                                        color="amber"
                                                        variant="subtle"
                                                    >
                                                        Duplicate
                                                    </flux:badge>
                                                @elseif ($isBaseDuplicate === false)
                                                    <flux:badge
                                                        size="sm"
                                                        color="sky"
                                                        variant="subtle"
                                                    >
                                                        Keep override
                                                    </flux:badge>
                                                @else
                                                    <flux:badge
                                                        size="sm"
                                                        color="zinc"
                                                        variant="subtle"
                                                    >
                                                        Unchecked
                                                    </flux:badge>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <flux:button
                                                    type="button"
                                                    size="xs"
                                                    variant="ghost"
                                                    wire:click="markSubLanguageAsDuplicate({{ $translationRow->id }}, '{{ $selectedSubLocale }}')"
                                                    wire:loading.attr="disabled"
                                                    :disabled="$isBaseDuplicate === true"
                                                >
                                                    Als Duplikat markieren
                                                </flux:button>

                                                <flux:button
                                                    type="button"
                                                    size="xs"
                                                    color="sky"
                                                    variant="ghost"
                                                    wire:click="keepSubLanguageAsOverride({{ $translationRow->id }}, '{{ $selectedSubLocale }}')"
                                                    wire:loading.attr="disabled"
                                                    :disabled="$isBaseDuplicate === false"
                                                >
                                                    Als Override behalten
                                                </flux:button>
                                            </div>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            @endforeach

                            <flux:table.cell align="center">
                                <x-ui.button.edit
                                    icon="pencil-square"
                                    label="{{ __('Edit values') }}"
                                    size="sm"
                                    wire:click="openTranslationEntryEditModal({{ $translationRow->id }})"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <flux:separator
            class="mt-4"
            text="{{ __('Pagination') }}"
        />

        @if ($translationRows->hasPages())
            <div class="mt-4">
                <x-ui.table.pagination :paginator="$translationRows" />
            </div>
        @endif
    @endif

</flux:card>
