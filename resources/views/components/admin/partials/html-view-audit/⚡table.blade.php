{{-- resources/views/components/admin/partials/html-view-audit/⚡table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('HTML-Audit')"
        :description="__('Description')"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">

            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column
                        class="w-24"
                        align="center"
                    >
                        {{ _('#') }}
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('section')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Section')"
                            :text="__(
                                'The section of the audit where the problem was found, e.g. native HTML or custom components.',
                            )"
                        >
                            {{ __('Section') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('Problem type')"
                            :text="__('Type of the problem, e.g. unclosed tag, unexpected closing tag, etc.')"
                        >
                            {{ __('Type') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('File')"
                            :text="__('The file where the problem was found.')"
                        >
                            {{ __('File') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('Tag')"
                            :text="__('The HTML tag where the problem was found.')"
                        >
                            {{ __('Tag') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('Opened')"
                            :text="__('Line number where the tag is opened.')"
                        >
                            {{ __('Opened') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('Closing')"
                            :text="__('Line number where the tag is closed.')"
                        >
                            {{ __('Closing') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('Expected')"
                            :text="__('The expected closing tag.')"
                        >
                            {{ __('Expected') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
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
                        <flux:table.row wire:key="html-view-audit-{{ $loop->index }}-{{ md5(json_encode($problem)) }}">
                            <flux:table.cell align="center">
                                {{ __('sequential number!?') }}
                                {{--
                                TODO: sequential number
                                --}}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <flux:badge variant="subtle">
                                    {{ $problem['section'] ?? 'n/a' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell
                                align="center"
                                sortable
                            >
                                <flux:badge
                                    :color="($problem['type'] ?? null) === 'unclosed' ? 'red' : 'amber'"
                                    variant="subtle"
                                >
                                    {{ $problem['type'] ?? 'n/a' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell sortable>
                                <div
                                    class="ellipsis-rtl max-w-xl truncate font-mono"
                                    title="{{ $problem['file'] ?? '' }}"
                                >
                                    {{ $problem['file'] ?? 'n/a' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell sortable>
                                <span class="font-mono">{{ $problem['tag'] ?? 'n/a' }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-mono">{{ $problem['opened_line'] ?? '—' }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-mono">{{ $problem['closing_line'] ?? '—' }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-mono">{{ $problem['expected_closing'] ?? '—' }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-mono">{{ $problem['actual_closing'] ?? '—' }}</span>
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
        {{-- </div> --}}
</flux:card>
