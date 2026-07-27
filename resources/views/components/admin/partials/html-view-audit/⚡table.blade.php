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

                {{-- Table columns --}}
                <flux:table.columns class="bg-zinc-800 text-zinc-400">

                    {{-- Column ID --}}
                    <flux:table.column
                        class="w-32"
                        aria-label="{{ __('admin.user_list.table.id') }}"
                        sortable
                        align="center"
                        wire:click="sortBy('id')"
                    >
                        {{-- Tooltip ID: Unique identifier of the finding, useful for tracking and reference. --}}
                        <x-ui.tooltip.trigger
                            :title="__('admin.user_list.table.id')"
                            :text="__('Unique identifier of the finding, useful for tracking and reference.')"
                        >
                            {{ __('admin.user_list.table.id') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Status --}}
                    <flux:table.column
                        class="w-22"
                        aria-label="{{ __('admin.app_settings.table_icon_registry.status') }}"
                        align="center"
                        sortable
                        wire:click="sortBy('status')"
                    >
                        {{-- Tooltip Status: History status of the finding, open, changed, resolved, or ignored. --}}
                        <x-ui.tooltip.trigger
                            :title="__('admin.app_settings.table_icon_registry.status')"
                            :text="$legendTexts['status'] ??
                                __('History status of the finding: open, changed, resolved, or ignored.')"
                        >
                            {{ __('admin.app_settings.table_icon_registry.status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Section --}}
                    <flux:table.column
                        class="w-22"
                        aria-label="{{ __('Section') }}"
                        align="center"
                        sortable
                        wire:click="sortBy('section')"
                    >
                        {{-- Tooltip Section: The section of the audit where the problem was found, e.g. native HTML or custom components. --}}
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

                    {{-- Column Type --}}
                    <flux:table.column
                        class="w-22"
                        aria-label="{{ __('Problem type') }}"
                        align="center"
                        sortable
                        wire:click="sortBy('type')"
                    >
                        {{-- Tooltip Type: Type of the problem, e.g. unclosed tag, unexpected closing tag, etc. --}}
                        <x-ui.tooltip.trigger
                            :title="__('Problem type')"
                            :text="$legendTexts['type'] ??
                                __('Type of the problem, e.g. unclosed tag, unexpected closing tag, etc.')"
                        >
                            {{ __('admin.client_list.table.type') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column File --}}
                    <flux:table.column
                        class="w-124"
                        aria-label="{{ __('File') }}"
                        sortable
                        wire:click="sortBy('file')"
                    >
                        {{-- Tooltip File: The file where the problem was found. --}}
                        <x-ui.tooltip.trigger
                            :title="__('File')"
                            :text="__('The file where the problem was found.')"
                        >
                            {{ __('File') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Tag --}}
                    <flux:table.column
                        aria-label="{{ __('Tag') }}"
                        sortable
                        wire:click="sortBy('tag')"
                    >
                        {{-- Tooltip Tag: The HTML tag where the problem was found. --}}
                        <x-ui.tooltip.trigger
                            :title="__('Tag')"
                            :text="__('The HTML tag where the problem was found.')"
                        >
                            {{ __('Tag') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Line --}}
                    <flux:table.column
                        class="w-36"
                        aria-label="{{ __('admin.translation_list.modal.line') }}"
                        sortable
                        wire:click="sortBy('opened_line')"
                    >
                        {{-- Tooltip Line: Line numbers where the tag is opened and, if available, closed. --}}
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.modal.line')"
                            :text="__('Line numbers where the tag is opened and, if available, closed.')"
                        >
                            {{ __('admin.translation_list.modal.line') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Expected --}}
                    <flux:table.column
                        aria-label="{{ __('Expected closing tag') }}"
                        sortable
                        wire:click="sortBy('expected_closing')"
                    >
                        {{-- Tooltip Expected: The expected closing tag, if applicable. --}}
                        <x-ui.tooltip.trigger
                            :title="__('Expected')"
                            :text="__('The expected closing tag.')"
                        >
                            {{ __('Expected') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actual --}}
                    <flux:table.column
                        aria-label="{{ __('Actual closing tag') }}"
                        sortable
                        wire:click="sortBy('actual_closing')"
                    >
                        {{-- Tooltip Actual: The actual closing tag, if applicable. --}}
                        <x-ui.tooltip.trigger
                            :title="__('ui.actual')"
                            :text="__('The actual closing tag.')"
                        >
                            {{ __('ui.actual') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                {{-- Table rows --}}
                <flux:table.rows>
                    @forelse ($problems as $problem)

                        {{-- Table row --}}
                        <flux:table.row wire:key="html-view-audit-finding-{{ $problem['id'] ?? $loop->index }}">

                            {{-- Column ID --}}
                            <flux:table.cell
                                class="tabular-nums"
                                aria-label="{{ $problem['id'] ?? 'n. a.' }}"
                                align="end"
                            >
                                {{ $problem['id'] ?? 'n. a.' }}
                            </flux:table.cell>

                            {{-- Column Status --}}
                            <flux:table.cell
                                aria-label="{{ $problem['status'] ?? 'n. a.' }}"
                                align="center"
                            >
                                @php($statusMeta = $tableLegend['status'][$problem['status'] ?? ''] ?? [])

                                <button
                                    class="inline-flex rounded-md"
                                    type="button"
                                    wire:click="showFindingDetails({{ (int) ($problem['id'] ?? 0) }})"
                                >
                                    {{-- Tooltip Status: The history status of the finding, such as open, changed, resolved, or ignored. Click to view more details about the finding's history and related information. --}}
                                    <x-ui.tooltip.trigger
                                        :title="__('admin.partials.html_view_audit.table.show_finding_details')"
                                        :text="__(
                                            'Click to view more information about this finding, including its history and any related findings.',
                                        )"
                                    >
                                        <flux:badge
                                            :color="$statusMeta['color'] ?? 'zinc'"
                                            variant="subtle"
                                        >
                                            <x-ui.flux-icon
                                                class="size-4"
                                                :name="$statusMeta['icon'] ?? 'bug'"
                                            />
                                        </flux:badge>
                                    </x-ui.tooltip.trigger>
                                </button>

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

                            {{-- Column Section --}}
                            <flux:table.cell
                                aria-label="{{ $problem['section'] ?? 'n. a.' }}"
                                align="center"
                            >
                                @php($sectionMeta = $tableLegend['section'][$problem['section'] ?? ''] ?? [])

                                <flux:badge
                                    :color="$sectionMeta['color'] ?? 'zinc'"
                                    variant="subtle"
                                >
                                    <x-ui.flux-icon
                                        class="size-4"
                                        :name="$sectionMeta['icon'] ?? 'code-xml'"
                                    />
                                </flux:badge>
                            </flux:table.cell>

                            {{-- Column Type --}}
                            <flux:table.cell
                                aria-label="{{ $problem['type'] ?? 'n. a.' }}"
                                align="center"
                            >
                                @php($typeMeta = $tableLegend['type'][$problem['type'] ?? ''] ?? [])

                                <flux:badge
                                    :color="$typeMeta['color'] ?? 'zinc'"
                                    variant="subtle"
                                >
                                    <x-ui.flux-icon
                                        class="size-4"
                                        :name="$typeMeta['icon'] ?? 'tag'"
                                    />
                                </flux:badge>
                            </flux:table.cell>

                            {{-- Column File --}}
                            <flux:table.cell>
                                <div class="ellipsis-rtl max-w-xl truncate font-mono">
                                    {{ $problem['file'] ?? 'n/a' }}
                                </div>
                            </flux:table.cell>

                            {{-- Column Tag --}}
                            <flux:table.cell>
                                {{ $problem['tag'] ?? 'n/a' }}
                            </flux:table.cell>

                            {{-- Column Line --}}
                            <flux:table.cell>
                                <div class="inline-grid grid-cols-[max-content_max-content] gap-x-3 leading-5">
                                    <span aria-label="{{ __('opened') }}">
                                        {{ __('opened') }}
                                    </span>
                                    <span
                                        class="tabular-nums"
                                        aria-label="{{ $problem['opened_line'] ?? 'n. a.' }}"
                                    >
                                        {{ $problem['opened_line'] ?? 'n. a.' }}
                                    </span>

                                    <span aria-label="{{ __('closing') }}">
                                        {{ __('closing') }}
                                    </span>
                                    <span
                                        class="tabular-nums"
                                        aria-label="{{ $problem['closing_line'] ?? 'n. a.' }}"
                                    >
                                        {{ $problem['closing_line'] ?? 'n. a.' }}
                                    </span>
                                </div>
                            </flux:table.cell>

                            {{-- Column Expected --}}
                            <flux:table.cell>
                                <span aria-label="{{ $problem['expected_closing'] ?? 'n. a.' }}">
                                    {{ $problem['expected_closing'] ?? 'n. a.' }}
                                </span>
                            </flux:table.cell>

                            {{-- Column Actual --}}
                            <flux:table.cell>
                                <span aria-label="{{ $problem['actual_closing'] ?? 'n. a.' }}">
                                    {{ $problem['actual_closing'] ?? 'n. a.' }}
                                </span>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        {{-- Table row --}}
                        <flux:table.row>

                            {{-- Column with "no data" message, shown when there are no problems to display based on the current filters. --}}
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

        {{-- Pagination: If the total number of audit problems exceeds the items per page limit, a pagination component is displayed below the table, allowing navigation through multiple pages of findings. The pagination controls include page numbers and next/previous buttons for easy navigation. --}}
        @if ($problems->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('admin.client_list.table.pagination') }}"
            />

            <div class="mt-4">
                {{-- Pagination component, allowing navigation through multiple pages of audit problems if the total exceeds the items per page limit. Displays page numbers and navigation controls. --}}
                <x-ui.table.pagination :paginator="$problems" />
            </div>
        @endif

    </div>
</flux:card>
