{{-- resources/views/components/admin/partials/html-view-audit/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">

    {{-- Card-Header --}}
    <x-ui.headers.card
        :title="__('HTML-Audit')"
        :description="__(
            'Review the list of identified HTML structure problems, their status, and details about each finding.',
        )"
    />

    <div class="mx-auto max-w-full">

        <div class="overflow-hidden rounded-t-lg">

            {{-- Table part --}}
            <flux:table class="w-full">

                <flux:table.columns class="bg-zinc-800 text-zinc-400">

                    {{-- Column ID --}}
                    <flux:table.column
                        class="w-32"
                        sortable
                        align="center"
                        wire:click="sortBy('id')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ID')"
                            :text="__('Unique identifier of the finding, useful for tracking and reference.')"
                        >
                            {{ __('ID') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        class="w-22"
                        align="center"
                        sortable
                        wire:click="sortBy('status')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Status')"
                            :text="$legendTexts['status'] ??
                                __('History status of the finding: open, changed, resolved, or ignored.')"
                        >
                            {{ __('Status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        class="w-22"
                        align="center"
                        sortable
                        wire:click="sortBy('section')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Section')"
                            :text="$legendTexts['section'] ??
                                __(
                                    'The section of the audit where the problem was found, e.g. native HTML or custom components.',
                                )"
                        >
                            {{ __('Section') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        class="w-22"
                        align="center"
                        sortable
                        wire:click="sortBy('type')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Problem type')"
                            :text="$legendTexts['type'] ??
                                __('Type of the problem, e.g. unclosed tag, unexpected closing tag, etc.')"
                        >
                            {{ __('Type') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        class="w-124"
                        sortable
                        wire:click="sortBy('file')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('File')"
                            :text="__('The file where the problem was found.')"
                        >
                            {{ __('File') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('tag')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Tag')"
                            :text="__('The HTML tag where the problem was found.')"
                        >
                            {{ __('Tag') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        class="w-36"
                        sortable
                        wire:click="sortBy('opened_line')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Line')"
                            :text="__('Line numbers where the tag is opened and, if available, closed.')"
                        >
                            {{ __('Line') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('expected_closing')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Expected')"
                            :text="__('The expected closing tag.')"
                        >
                            {{ __('Expected') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('actual_closing')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Actual')"
                            :text="__('The actual closing tag.')"
                        >
                            {{ __('Actual') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($problems as $problem)
                        <flux:table.row wire:key="html-view-audit-finding-{{ $problem['id'] ?? $loop->index }}">
                            <flux:table.cell
                                class="tabular-nums"
                                align="end"
                            >
                                {{ $problem['id'] ?? 'n. a.' }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @php($statusMeta = $tableLegend['status'][$problem['status'] ?? ''] ?? [])

                                <flux:badge
                                    :color="$statusMeta['color'] ?? 'zinc'"
                                    variant="subtle"
                                    :title="$statusMeta['label'] ?? ($problem['status'] ?? 'n/a')"
                                >
                                    <x-ui.flux-icon
                                        class="size-4"
                                        :name="$statusMeta['icon'] ?? 'bug'"
                                    />
                                </flux:badge>

                                @if (($problem['status'] ?? null) === 'open' && !empty($problem['previous_finding_id']))
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ __('moved from') }} #{{ $problem['previous_finding_id'] }}
                                    </div>

                                    @if (!empty($problem['previous_opened_line']) || !empty($problem['previous_closing_line']))
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('line') }}
                                            {{ $problem['previous_opened_line'] ?? 'n. a.' }}
                                            /
                                            {{ $problem['previous_closing_line'] ?? 'n. a.' }}
                                        </div>
                                    @endif
                                @elseif (($problem['status'] ?? null) === 'changed')
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ __('old variant') }}
                                    </div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @php($sectionMeta = $tableLegend['section'][$problem['section'] ?? ''] ?? [])

                                <flux:badge
                                    :color="$sectionMeta['color'] ?? 'zinc'"
                                    variant="subtle"
                                    :title="$sectionMeta['label'] ?? ($problem['section'] ?? 'n/a')"
                                >
                                    <x-ui.flux-icon
                                        class="size-4"
                                        :name="$sectionMeta['icon'] ?? 'code-xml'"
                                    />
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @php($typeMeta = $tableLegend['type'][$problem['type'] ?? ''] ?? [])

                                <flux:badge
                                    :color="$typeMeta['color'] ?? 'zinc'"
                                    variant="subtle"
                                    :title="$typeMeta['label'] ?? ($problem['type'] ?? 'n/a')"
                                >
                                    <x-ui.flux-icon
                                        class="size-4"
                                        :name="$typeMeta['icon'] ?? 'tag'"
                                    />
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div
                                    class="ellipsis-rtl max-w-xl truncate font-mono"
                                    title="{{ $problem['file'] ?? '' }}"
                                >
                                    {{ $problem['file'] ?? 'n/a' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $problem['tag'] ?? 'n/a' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="inline-grid grid-cols-[max-content_max-content] gap-x-3 leading-5">
                                    <span>
                                        {{ __('opened') }}
                                    </span>
                                    <span class="tabular-nums">
                                        {{ $problem['opened_line'] ?? 'n. a.' }}
                                    </span>

                                    <span>
                                        {{ __('closing') }}
                                    </span>
                                    <span class="tabular-nums">
                                        {{ $problem['closing_line'] ?? 'n. a.' }}
                                    </span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $problem['expected_closing'] ?? 'n. a.' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $problem['actual_closing'] ?? 'n. a.' }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="9">
                                <div class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No audit problems found for the current filter.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($problems->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('Pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$problems" />
            </div>
        @endif

    </div>
</flux:card>
