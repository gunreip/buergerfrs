<flux:callout
    class="col-span-2"
    color="sky"
    icon="sparkles"
>
    <flux:callout.heading>
        {{ __('ui.source.source') }}
        <x-ui.tooltip.simple
            :header="__('ui.source.source')"
            :text="__(
                'Active key records sharing the saved Current key and their actively linked findings. Key ID and finding ID identify each assignment. Several findings may point to the same file. Unsaved key edits do not change this list; reopen the editor to refresh it.',
            )"
        />
    </flux:callout.heading>
    @include('translation-workbench::livewire.entries.review.finding-lineage', ['findingLineage' => $translationKeyFindingLineage ?? []])
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading>
                <flux:callout.heading>
                    <flux:icon.panel-top-open class="group-data-open/accordion-heading:hidden" aria-hidden="true" />
                    <flux:icon.panel-top-close class="hidden group-data-open/accordion-heading:block" aria-hidden="true" />
                    {{ __('Source locations') }} ({{ count($translationKeySources) }}
                    {{ __('active key records') }})
                </flux:callout.heading>
            </flux:accordion.heading>
            {{-- IMPORTANT: scrollbar verschieben --}}
            <flux:accordion.content class="-mr-4">
                <div class="scrollbar-gutter-stable -mr-4 max-h-80 max-w-full space-y-3 overflow-auto pr-4">
                    @forelse ($translationKeySources as $owner)
                        <div>
                            <flux:callout.heading
                                class="my-1"
                                size="sm"
                            >
                                <span class="w-8">
                                    {{ $loop->index + 1 }})
                                </span>
                                {{ __('Key') }} #{{ $owner['key_id'] }}
                                <span>
                                    ({{ count($owner['findings']) }} {{ __('Findings') }})
                                </span>
                            </flux:callout.heading>

                            {{-- Table Of Source Locations For This Key --}}
                            <div class="overflow-hidden rounded-t-lg">
                                <flux:table container:class="max-h-48 ">
                                    {{-- Table Headings --}}
                                    <flux:table.columns
                                        class="bg-white dark:bg-zinc-800"
                                        sticky
                                    >
                                        {{-- Sequential Number --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                <flux:icon.tally-5 class="ml-2" />
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Finding --}}
                                        <flux:table.column
                                            class="w-24 tabular-nums"
                                            sortable
                                            :sorted="($translationKeySourceSorts[$owner['key_id']]['field'] ??
                                                null) === 'id'"
                                            :direction="$translationKeySourceSorts[$owner['key_id']]['direction'] ?? 'asc'"
                                            wire:click="sortTranslationKeySources({{ $owner['key_id'] }}, 'id')"
                                        >
                                            <flux:callout.heading>
                                                {{ __('Finding') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Source --}}
                                        <flux:table.column
                                            sortable
                                            :sorted="($translationKeySourceSorts[$owner['key_id']]['field'] ??
                                                null) === 'path'"
                                            :direction="$translationKeySourceSorts[$owner['key_id']]['direction'] ?? 'asc'"
                                            wire:click="sortTranslationKeySources({{ $owner['key_id'] }}, 'path')"
                                        >
                                            <flux:callout.heading>
                                                {{ __('Source') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- State --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('State') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Open --}}
                                        <flux:table.column class="w-12">
                                            <flux:callout.heading class="pr-3">
                                                {{ __('Open') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                    </flux:table.columns>
                                    {{-- Table Rows --}}
                                    <flux:table.rows>
                                        @forelse ($owner['findings'] as $source)
                                            @php
                                                $path = (string) ($source['path'] ?? '');
                                                $editorPath = str_replace(
                                                    '%2F',
                                                    '/',
                                                    rawurlencode(str_replace('\\', '/', base_path($path))),
                                                );
                                                $editorUrl =
                                                    'vscode://vscode-remote/wsl+' .
                                                    rawurlencode(
                                                        (string) config(
                                                            'translation-workbench.editor.vscode_wsl_distro',
                                                        ),
                                                    ) .
                                                    $editorPath .
                                                    ($source['source_line'] ? ':' . $source['source_line'] : '');
                                            @endphp
                                            <flux:table.row>
                                                {{-- Sequential Number --}}
                                                <flux:table.cell>
                                                    <flux:callout.text class="text-right tabular-nums">
                                                        {{ $loop->index + 1 }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Source ID --}}
                                                <flux:table.cell
                                                    class="whitespace-normal align-top"
                                                    align="right"
                                                >
                                                    <flux:callout.text class="font-mono">
                                                        #{{ $source['id'] }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Source Path --}}
                                                <flux:table.cell
                                                    class="w-full whitespace-normal break-all align-top font-mono text-xs"
                                                >
                                                    <flux:callout.text class="font-mono">
                                                        {{ $path ?: __('Source path unavailable') }}{{ $source['source_line'] ? ':' . $source['source_line'] : '' }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Source Status --}}
                                                <flux:table.cell>
                                                    <div class="flex flex-col items-start gap-1">
                                                        <flux:badge size="sm">
                                                            {{ $source['status'] }}
                                                        </flux:badge>
                                                        @if ((int) $source['id'] === (int) $translationKeyFindingId)
                                                            <flux:badge
                                                                size="sm"
                                                                color="sky"
                                                            >
                                                                {{ __('Opened finding') }}
                                                            </flux:badge>
                                                        @endif
                                                    </div>
                                                </flux:table.cell>
                                                {{-- Open In VS Code --}}
                                                <flux:table.cell
                                                    class="pr-3 align-top"
                                                    align="center"
                                                >
                                                    @if ($path !== '')
                                                        <flux:tooltip :content="__('Open in VSC')">
                                                            <flux:button
                                                                type="button"
                                                                size="xs"
                                                                variant="ghost"
                                                                icon="external-link"
                                                                :href="$editorUrl"
                                                                :aria-label="__('Open source in VS Code')"
                                                            />
                                                        </flux:tooltip>
                                                    @else
                                                        <span aria-hidden="true">—</span>
                                                    @endif
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @empty
                                            {{-- IF Empty --}}
                                            <flux:table.row>
                                                <flux:table.cell colspan="4">
                                                    {{ __('No actively linked findings.') }}
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforelse
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                        </div>
                    @empty
                        {{-- IF Empty --}}
                        <flux:callout.text>
                            {{ __('No active key records.') }}
                        </flux:callout.text>
                    @endforelse
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:callout>
