{{-- resources/views/components/admin/partials/translation-lang-ballast/table/⚡table-action-files.blade.php --}}

{{-- Translation-Lang-Ballast Table Action Files --}}
<flux:table container:class="app-table">

    {{-- Translation-Lang-Ballast-Header Row --}}
    <flux:table.columns
        class="bg-zinc-800 text-zinc-400"
        sticky
    >
        {{-- Table-Header Sequence-Number --}}
        <flux:table.column
            class="w-14 tabular-nums"
            align="center"
        >
            <flux:icon.tally-5
                class="ml-3"
                stroke-width="1"
            />
        </flux:table.column>

        {{-- Table-Header File --}}
        <flux:table.column
            sortable
            :sorted="$sortField === 'file'"
            :direction="$sortDirection"
            wire:click="sortBy('file')"
        >
            {{ __('File') }}
        </flux:table.column>

        {{-- Table-Header Entries --}}
        <flux:table.column
            class="w-px whitespace-nowrap"
            align="center"
            sortable
            :sorted="$sortField === 'entries'"
            :direction="$sortDirection"
            wire:click="sortBy('entries')"
        >
            {{ __('Entries') }}
        </flux:table.column>

        {{-- Table-Header ID --}}
        <flux:table.column>
            {{ __('IDs') }}
        </flux:table.column>

        {{-- Table-Header Decision --}}
        <flux:table.column
            class="w-px whitespace-nowrap"
            align="center"
            sortable
            :sorted="$sortField === 'decisions'"
            :direction="$sortDirection"
            wire:click="sortBy('decisions')"
        >
            {{ __('Decision') }}
        </flux:table.column>

        {{-- Table-Header Locales --}}
        <flux:table.column
            class="w-px whitespace-nowrap"
            align="center"
            sortable
            :sorted="$sortField === 'locales'"
            :direction="$sortDirection"
            wire:click="sortBy('locales')"
        >
            {{ __('Locales') }}
        </flux:table.column>

        {{-- Table-Header Namespaces --}}
        <flux:table.column
            sortable
            :sorted="$sortField === 'namespaces'"
            :direction="$sortDirection"
            wire:click="sortBy('namespaces')"
        >
            {{ __('Namespaces') }}
        </flux:table.column>

        {{-- Table-Header Groups --}}
        <flux:table.column
            sortable
            :sorted="$sortField === 'groups'"
            :direction="$sortDirection"
            wire:click="sortBy('groups')"
        >
            {{ __('Groups') }}
        </flux:table.column>

        {{-- Table-Header Reasons --}}
        <flux:table.column
            sortable
            :sorted="$sortField === 'reasons'"
            :direction="$sortDirection"
            wire:click="sortBy('reasons')"
        >
            {{ __('Reasons') }}
        </flux:table.column>
    </flux:table.columns>

    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($actionFileRows as $actionFileRow)
            @php
                $translationKeyIds = collect($actionFileRow['translation_key_ids'] ?? [])
                    ->filter()
                    ->values();

                $decisionStatuses = (array) ($actionFileRow['decision_statuses'] ?? []);
            @endphp

            {{-- Table Row --}}
            <flux:table.row
                wire:key="translation-lang-ballast-file-{{ md5((string) ($actionFileRow['file'] ?? $loop->index)) }}"
            >
                {{-- Table Cell Sequence-Number --}}
                <flux:table.cell
                    class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                    align="end"
                >
                    {{ $actionFileRows->firstItem() + $loop->index }}
                </flux:table.cell>

                {{-- Table Cell File --}}
                <flux:table.cell class="align-top">
                    <code class="wrap-anywhere block text-xs">
                        {{ $actionFileRow['file'] ?? '—' }}
                    </code>
                </flux:table.cell>

                {{-- Table Cell Entries --}}
                <flux:table.cell
                    class="w-px whitespace-nowrap align-top"
                    align="center"
                >
                    <flux:badge
                        class="tabular-nums"
                        size="sm"
                        variant="subtle"
                        color="sky"
                    >
                        {{ number_format((int) ($actionFileRow['entries'] ?? 0)) }}
                    </flux:badge>
                </flux:table.cell>

                {{-- Table Cell  --}}
                <flux:table.cell class="align-top">
                    <div class="flex flex-wrap gap-1">
                        @forelse ($translationKeyIds->take(8) as $translationKeyId)
                            {{-- TranslationKey-ID     --}}
                            <flux:badge
                                class="tabular-nums"
                                size="sm"
                                variant="subtle"
                                color="zinc"
                            >
                                #{{ $translationKeyId }}
                            </flux:badge>
                        @empty
                            <x-ui.badge.no-value />
                        @endforelse

                        @if ($translationKeyIds->count() > 8)
                            {{-- TranslationKey-Count --}}
                            <flux:badge
                                class="tabular-nums"
                                size="sm"
                                variant="subtle"
                                color="zinc"
                            >
                                &hellip; +{{ $translationKeyIds->count() - 8 }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:table.cell>

                {{-- Table Cell Decision --}}
                <flux:table.cell
                    class="w-px whitespace-nowrap align-top"
                    align="center"
                >
                    <div class="flex flex-col items-center gap-1">
                        @foreach ($decisionStatusMeta as $decisionStatusKey => $decisionStatus)
                            @php
                                $decisionStatusCount = (int) ($decisionStatuses[$decisionStatusKey] ?? 0);
                            @endphp

                            @if ($decisionStatusCount > 0)
                                <flux:badge
                                    class="tabular-nums"
                                    size="sm"
                                    variant="subtle"
                                    color="{{ $decisionStatus['color'] }}"
                                >
                                    {{ $decisionStatus['label'] }}
                                    {{ number_format($decisionStatusCount) }}
                                </flux:badge>
                            @endif
                        @endforeach
                    </div>
                </flux:table.cell>

                {{-- Table Cell Locale-Flag --}}
                <flux:table.cell
                    class="w-px whitespace-nowrap align-top"
                    align="center"
                >
                    <div class="flex flex-wrap justify-center gap-1">
                        @forelse (($actionFileRow['locales'] ?? []) as $locale)
                            @php
                                $displayLocale = trim((string) $locale);
                            @endphp

                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="zinc"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <x-ui.locale.flag
                                        :locale="$displayLocale"
                                        size="xs"
                                    />

                                    <span class="font-mono font-semibold uppercase">
                                        {{ $displayLocale }}
                                    </span>
                                </span>
                            </flux:badge>
                        @empty
                            —
                        @endforelse
                    </div>
                </flux:table.cell>

                {{-- Table Cell Namespace --}}
                <flux:table.cell class="align-top">
                    <div class="flex flex-wrap gap-1">
                        @forelse (($actionFileRow['namespaces'] ?? []) as $namespace)
                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="emerald"
                            >
                                {{ $namespace }}
                            </flux:badge>
                        @empty
                            —
                        @endforelse
                    </div>
                </flux:table.cell>

                {{-- Table Cell Group --}}
                <flux:table.cell class="align-top">
                    <div class="flex flex-wrap gap-1">
                        @forelse (($actionFileRow['groups'] ?? []) as $group)
                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="violet"
                            >
                                {{ $group }}
                            </flux:badge>
                        @empty
                            <x-ui.badge.no-value />
                        @endforelse
                    </div>
                </flux:table.cell>

                {{-- Table Cell Reason --}}
                <flux:table.cell class="align-top">
                    <div class="flex flex-wrap gap-1">
                        @forelse (($actionFileRow['reason_details'] ?? []) as $reasonDetail => $count)
                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="amber"
                            >
                                {{ $reasonDetail }}: {{ number_format((int) $count) }}
                            </flux:badge>
                        @empty
                            <x-ui.badge.no-value />
                        @endforelse
                    </div>
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Table Row No Data --}}
            <flux:table.row>
                {{-- Table Cell No Data --}}
                <flux:table.cell colspan="8">
                    <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No lang ballast files found for the selected filters. Run php artisan project:translations first if the audit files are missing.') }}
                    </div>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
