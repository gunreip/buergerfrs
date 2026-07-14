{{-- resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php --}}

@php
    $archiveFilters = [
        'current' => __('admin.app_settings.locale.current'),
        'all' => __('ui.states.all'),
        'expired' => __('Expired'),
        'replaced' => __('Replaced'),
        'correspondence' => __('Correspondence'),
        'archived' => __('Archived'),
    ];

    $documentStatusColors = [
        'active' => 'green',
        'expired' => 'amber',
        'replaced' => 'sky',
        'archived' => 'zinc',
        'draft' => 'zinc',
    ];
@endphp

<flux:modal
    class="w-full max-w-7xl"
    name="person-document-archive"
    wire:model.self="documentArchiveModalOpen"
>
    <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
        <div class="flex shrink-0 items-start justify-between gap-4">
            <x-ui.headers.card
                :title="__('Document archive')"
                :description="__('Select an existing document for this person.')"
            />

            <flux:badge
                class="mr-8 tabular-nums"
                variant="subtle"
                color="zinc"
            >
                {{ $documentArchiveRows->count() }}
            </flux:badge>
        </div>

        <flux:tabs
            class="px-1"
            wire:model.live="documentArchiveFilter"
        >
            @foreach ($archiveFilters as $filter => $label)
                <flux:tab
                    class="hover:cursor-pointer"
                    name="{{ $filter }}"
                    :disabled="($documentArchiveCounts[$filter] ?? 0) === 0"
                >
                    <span class="inline-flex items-center gap-2">
                        <span>{{ $label }}</span>

                        <flux:badge
                            class="tabular-nums"
                            size="sm"
                            variant="{{ $documentArchiveFilter === $filter ? 'solid' : 'subtle' }}"
                            color="zinc"
                        >
                            {{ $documentArchiveCounts[$filter] ?? 0 }}
                        </flux:badge>
                    </span>
                </flux:tab>
            @endforeach
        </flux:tabs>

        <div class="min-h-0 overflow-y-auto">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    {{-- Column Status --}}
                    <flux:table.column>
                        {{ __('admin.app_settings.table_icon_registry.status') }}
                    </flux:table.column>
                    {{-- Column Category --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'category'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('category')"
                    >
                        {{ __('ui.labels.category') }}
                    </flux:table.column>
                    {{-- Column Title --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'title'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('title')"
                    >
                        {{ __('Title') }}
                    </flux:table.column>
                    {{-- Column Type --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'type'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('type')"
                    >
                        {{ __('admin.client_list.table.type') }}
                    </flux:table.column>
                    {{-- Column Number --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'number'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('number')"
                    >
                        {{ __('Number') }}
                    </flux:table.column>
                    {{-- Column Date --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'date'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('date')"
                    >
                        {{ __('Date') }}
                    </flux:table.column>
                    {{-- Column Valid Until --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'valid_until'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('valid_until')"
                    >
                        {{ __('Valid until') }}
                    </flux:table.column>
                    {{-- Column Source --}}
                    <flux:table.column
                        sortable
                        :sorted="$documentArchiveSortField === 'source'"
                        :direction="$documentArchiveSortDirection"
                        wire:click="sortDocumentArchiveBy('source')"
                    >
                        {{ __('admin.translation_list.modal.source') }}
                    </flux:table.column>
                    {{-- Column Actions --}}
                    <flux:table.column align="center">
                        {{ __('ui.labels.actions') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($documentArchiveRows as $document)
                        @php
                            $documentLabel = filled($document->title)
                                ? $document->title
                                : ($document->original_filename ?:
                                __($documentTypeOptions[$document->type] ?? $document->type));
                            $documentDate =
                                $document->document_date?->toDateString() ??
                                ($document->issued_at?->toDateString() ?? $document->created_at?->toDateString());
                            $validUntil =
                                $document->valid_until?->toDateString() ?? $document->expires_at?->toDateString();
                        @endphp

                        <flux:table.row wire:key="person-document-archive-row-{{ $document->id }}">
                            <flux:table.cell>
                                <flux:badge
                                    variant="subtle"
                                    color="{{ $documentStatusColors[$document->status] ?? 'zinc' }}"
                                >
                                    {{ __(str($document->status)->headline()->toString()) }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ __(str($document->category)->headline()->toString()) }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="max-w-72 truncate font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $documentLabel }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ __($documentTypeOptions[$document->type] ?? str($document->type)->headline()->toString()) }}
                            </flux:table.cell>

                            <flux:table.cell class="tabular-nums">
                                {{ filled($document->document_number) ? $document->document_number : '—' }}
                            </flux:table.cell>

                            <flux:table.cell class="tabular-nums">
                                {{ $documentDate ?? '—' }}
                            </flux:table.cell>

                            <flux:table.cell class="tabular-nums">
                                {{ $validUntil ?? '—' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ __(str($document->source)->headline()->toString()) }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="primary"
                                    color="zinc"
                                    icon="check"
                                    wire:click="selectDocumentFromArchive({{ $document->id }})"
                                >
                                    {{ __('Select') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="9">
                                <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No documents found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        {{-- <div class="shrink-0 border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <div class="flex justify-end">
                <x-ui.button.close wire:click="closeDocumentArchive" />
            </div>
        </div> --}}
    </div>
</flux:modal>
