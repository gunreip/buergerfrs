{{-- resources/views/components/admin/partials/fallback-report-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">

    {{-- Card Header --}}
    <x-ui.headers.card
        :title="__('layouts.sidebar.administration.fallback_reports')"
        :description="__(
            'List of technical fallback events such as missing icons, missing config values or other recoverable UI fallbacks.',
        )"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            {{-- Table --}}
            <flux:table>

                {{-- Table Haed --}}
                <flux:table.columns class="bg-zinc-800 text-zinc-400">

                    {{-- Column ID --}}
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'id'"
                        :direction="$sortDirection"
                        align="center"
                        wire:click="sortBy('id')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.user_list.table.id')"
                            :text="__(
                                'Unique identifier of the fallback report, useful for tracking and reference.',
                            )"
                        >
                            {{ __('ui.labels.number_short') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Status --}}
                    <flux:table.column
                        sortable
                        align="center"
                        :sorted="$sortField === 'status'"
                        :direction="$sortDirection"
                        wire:click="sortBy('status')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.app_settings.table_icon_registry.status')"
                            :text="__('Status of the fallback report, useful for tracking and reference.')"
                        >
                            {{ __('admin.app_settings.table_icon_registry.status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Type --}}
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'type'"
                        :direction="$sortDirection"
                        wire:click="sortBy('type')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.client_list.table.type')"
                            :text="__('Type of the fallback report, useful for tracking and reference.')"
                        >
                            {{ __('admin.client_list.table.type') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Key --}}
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'key'"
                        :direction="$sortDirection"
                        wire:click="sortBy('key')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.table.key')"
                            :text="__('Key of the fallback report, useful for tracking and reference.')"
                        >
                            {{ __('admin.translation_list.table.key') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Fallback --}}
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'fallback'"
                        :direction="$sortDirection"
                        wire:click="sortBy('fallback')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Fallback')"
                            :text="__('Fallback of the fallback report, useful for tracking and reference.')"
                        >
                            {{ __('Fallback') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Count --}}
                    <flux:table.column
                        sortable
                        align="center"
                        :sorted="$sortField === 'count'"
                        :direction="$sortDirection"
                        wire:click="sortBy('count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Count')"
                            :text="__('Count of the fallback report, useful for tracking and reference.')"
                        >
                            {{ __('Count') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Last Reported --}}
                    <flux:table.column
                        sortable
                        :sorted="$sortField === 'last_seen_at'"
                        :direction="$sortDirection"
                        wire:click="sortBy('last_seen_at')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Last reported')"
                            :text="__(
                                'Last reported date of the fallback report, useful for tracking and reference.',
                            )"
                        >
                            {{ __('Last reported') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Context --}}
                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.modal_history.context')"
                            :text="__('Context of the fallback report, useful for tracking and reference.')"
                        >
                            {{ __('admin.translation_list.modal_history.context') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actions --}}
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.actions')"
                            :text="__(
                                'Actions available for the fallback report, useful for tracking and reference.',
                            )"
                        >
                            {{ __('ui.labels.actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                {{-- Table Body  --}}
                <flux:table.rows>
                    @forelse ($reports as $report)
                        {{-- Table Row --}}
                        <flux:table.row wire:key="fallback-report-{{ $report->id }}">

                            {{-- Column ID --}}
                            <flux:table.cell
                                class="w-32 tabular-nums text-zinc-400"
                                align="end"
                            >
                                {{ $report->id }}
                            </flux:table.cell>

                            {{-- Column Status --}}
                            <flux:table.cell align="center">
                                @if ($report->reviewed)
                                    <flux:badge
                                        color="green"
                                        variant="subtle"
                                    >
                                        {{ __('admin.translation_list.modal_edit.reviewed') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="amber"
                                        variant="subtle"
                                    >
                                        {{ __('admin.translation_list.filter.open') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            {{-- Column Type --}}
                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$report->type"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            {{-- Column Key --}}
                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$report->key"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            {{-- Column Fallback --}}
                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$report->fallback ?: '—'"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            {{-- Column Count --}}
                            <flux:table.cell align="center">
                                {{ $report->count }}
                            </flux:table.cell>

                            {{-- Column Last Reported --}}
                            <flux:table.cell>
                                {{ $report->last_seen_at?->format('Y-m-d H:i:s') ?? '—' }}
                            </flux:table.cell>

                            {{-- Column Context --}}
                            <flux:table.cell>
                                <pre
                                    class="max-w-md overflow-auto rounded-md bg-zinc-950/5 p-2 text-xs text-zinc-700 dark:bg-white/5 dark:text-zinc-300">
                                <x-ui.text.highlight
:value="json_encode($report->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)" :search="$search"/>
                            </pre>
                            </flux:table.cell>

                            {{-- Column Actions --}}
                            <flux:table.cell align="center">
                                @if ($report->reviewed)
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        wire:click="markUnreviewed({{ $report->id }})"
                                    >
                                        {{ __('Reopen') }}
                                    </flux:button>
                                @else
                                    <flux:button
                                        type="button"
                                        size="xs"
                                        color="sky"
                                        variant="primary"
                                        wire:click="markReviewed({{ $report->id }})"
                                    >
                                        {{ __('admin.translation_list.table.mark_reviewed') }}
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>

                            {{-- Column Table Empty --}}
                            <flux:table.cell colspan="9">
                                <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No fallback reports found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        {{-- Pagination --}}
        @if ($reports->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('admin.client_list.table.pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$reports" />
            </div>
        @endif

    </div>
</flux:card>
