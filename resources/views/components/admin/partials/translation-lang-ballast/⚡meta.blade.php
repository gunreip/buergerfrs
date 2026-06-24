{{-- resources/views/components/admin/partials/translation-lang-ballast/⚡meta.blade.php --}}

@php
    $summary = $summary ?? [];
    $generatedAt = $generatedAt ?? null;

    $reconciliation = (array) data_get($summary, 'reconciliation', []);
    $database = (array) data_get($summary, 'database', []);

    $localeRows = collect((array) data_get($summary, 'by_locale', []))
        ->map(function (array $row, string $locale): array {
            $fileOnlyEntries = (int) data_get($row, 'file_only_entries', 0);
            $dbOnlyEntries = (int) data_get($row, 'db_only_entries', 0);

            return [
                'locale' => $locale,
                'lang_entries' => (int) data_get($row, 'lang_entries', 0),
                'db_exportable_entries' => (int) data_get($row, 'db_exportable_entries', 0),
                'matched_entries' => (int) data_get($row, 'matched_entries', 0),
                'file_only_entries' => $fileOnlyEntries,
                'db_only_entries' => $dbOnlyEntries,
                'net_file_surplus_entries' => (int) data_get($row, 'net_file_surplus_entries', 0),
                'color' => $fileOnlyEntries > 0 ? 'red' : ($dbOnlyEntries > 0 ? 'amber' : 'green'),
            ];
        })
        ->sortKeys()
        ->values();

    $metaCards = [
        [
            'label' => __('Lang entries'),
            'value' => (int) data_get($reconciliation, 'lang_entries', data_get($summary, 'lang_entries', 0)),
            'color' => 'sky',
            'icon' => 'files',
        ],
        [
            'label' => __('DB exportable'),
            'value' => (int) data_get(
                $reconciliation,
                'db_exportable_entries',
                data_get($summary, 'exportable_entries', 0),
            ),
            'color' => 'emerald',
            'icon' => 'database',
        ],
        [
            'label' => __('Matched'),
            'value' => (int) data_get($reconciliation, 'matched_entries', data_get($summary, 'matched_entries', 0)),
            'color' => 'green',
            'icon' => 'git-compare-arrows',
        ],
        [
            'label' => __('File-only cleanup'),
            'value' => (int) data_get(
                $reconciliation,
                'file_only_entries',
                data_get($summary, 'lang_file_cleanup_candidate_entries', 0),
            ),
            'color' => 'red',
            'icon' => 'archive-x',
        ],
        [
            'label' => __('DB-only missing'),
            'value' => (int) data_get(
                $reconciliation,
                'db_only_entries',
                data_get($summary, 'missing_from_lang_entries', 0),
            ),
            'color' => 'amber',
            'icon' => 'file-plus-corner',
        ],
        [
            'label' => __('Sub-language duplicates'),
            'value' => (int) data_get($summary, 'sub_language_base_duplicate_entries', 0),
            'color' => 'blue',
            'icon' => 'languages',
        ],
        [
            'label' => __('Net file surplus'),
            'value' => (int) data_get(
                $reconciliation,
                'net_file_surplus_entries',
                data_get($summary, 'net_file_surplus_entries', 0),
            ),
            'color' => 'purple',
            'icon' => 'scale',
        ],
    ];

    $databaseCards = [
        [
            'label' => __('Translation keys'),
            'value' => (int) data_get($database, 'translation_keys_total', 0),
            'color' => 'blue',
            'icon' => 'key-round',
        ],
        [
            'label' => __('admin.translation_list.modal_edit.translation_values'),
            'value' => (int) data_get($database, 'translation_values_total', 0),
            'color' => 'blue',
            'icon' => 'languages',
        ],
        [
            'label' => __('Known locale/key entries'),
            'value' => (int) data_get($database, 'known_locale_key_entries', 0),
            'color' => 'blue',
            'icon' => 'list-tree',
        ],
        [
            'label' => __('Known not exportable'),
            'value' => (int) data_get($database, 'known_not_exportable_locale_key_entries', 0),
            'color' => 'amber',
            'icon' => 'circle-slash',
        ],
        [
            'label' => __('Reviewed obsolete'),
            'value' => (int) data_get($database, 'reviewed_obsolete_locale_key_entries', 0),
            'color' => 'red',
            'icon' => 'badge-check',
        ],
    ];
@endphp

{{-- Meta card --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>

    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">

            <x-ui.headers.card
                name="meta"
                :title="__('Meta')"
                :description="__(
                    'Last generated lang ballast audit reconciliation, database context, and lang file candidate counts.',
                )"
            />
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <x-ui.button.show-hide
                size="xs"
                state="showMeta"
            />
        </div>
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="grid flex-1 gap-3 md:grid-cols-3">
            <flux:callout
                class="col-span-1 hyphens-auto"
                color="emerald"
                icon="archive-x"
                heading="{{ __('Audit') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ __('Language Ballast') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-1 hyphens-auto"
                color="purple"
                icon="folder-search"
                heading="{{ __('admin.translation_list.modal.source') }}"
            >
                <flux:callout.text class="text-2xl! font-mono font-semibold">
                    {{ __('lang/{ll}|{ll-CC}/*') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-1 hyphens-auto"
                color="sky"
                icon="calendar"
                heading="{{ __('Generated at') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    @if ($generatedAt)
                        <span class="mr-2">
                            <x-ui.date-time.date
                                class="text-2xl! font-semibold tabular-nums"
                                :value="$generatedAt"
                                color="callout-text-sky"
                            />
                        </span>
                        <span>
                            <x-ui.date-time.time
                                class="text-2xl! font-semibold tabular-nums"
                                :value="$generatedAt"
                                color="callout-text-sky"
                            />
                        </span>
                    @else
                        <x-ui.badge.no-value />
                    @endif
                </flux:callout.text>
            </flux:callout>
        </div>

        <div class="mt-3 grid flex-1 gap-3 md:grid-cols-7">

            @foreach ($metaCards as $metaCard)
                <flux:callout
                    class="col-span-1 hyphens-auto"
                    color="{{ $metaCard['color'] }}"
                    icon="{{ $metaCard['icon'] }}"
                    :heading="$metaCard['label']"
                >
                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ number_format((int) $metaCard['value']) }}
                    </flux:callout.text>
                </flux:callout>
            @endforeach
        </div>

        <div class="mt-3 grid flex-1 gap-3 md:grid-cols-5">
            @foreach ($databaseCards as $databaseCard)
                <flux:callout
                    class="col-span-1 hyphens-auto"
                    color="{{ $databaseCard['color'] }}"
                    icon="{{ $databaseCard['icon'] }}"
                    :heading="$databaseCard['label']"
                >
                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ number_format((int) $databaseCard['value']) }}
                    </flux:callout.text>
                </flux:callout>
            @endforeach
        </div>
    </div>

    {{-- Audit locales badges list --}}
    @include('components.admin.partials.translation-lang-ballast.⚡meta-locale')

</flux:card>
