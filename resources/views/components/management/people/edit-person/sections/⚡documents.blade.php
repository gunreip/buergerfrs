{{-- resources/views/components/management/people/edit-person/sections/⚡documents.blade.php --}}

@php
    $emptyValue = __('Not set');
    $documentTypeLabel = $documentType !== '' ? __($documentTypeOptions[$documentType] ?? $documentType) : $emptyValue;
    $documentCategoryLabel =
        $documentCategory !== '' ? __($documentCategoryOptions[$documentCategory] ?? str($documentCategory)->headline()->toString()) : $emptyValue;
    $selectedDocument = $documentId !== null ? $documentOptions->firstWhere('id', $documentId) : null;
    $selectedDocumentLabel =
        $selectedDocument !== null
            ? (filled($selectedDocument->title)
                ? $selectedDocument->title
                : __($documentTypeOptions[$selectedDocument->type] ?? $selectedDocument->type))
            : __('New document');

    if ($selectedDocument !== null && filled($selectedDocument->document_number)) {
        $selectedDocumentLabel .= " ({$selectedDocument->document_number})";
    } elseif ($selectedDocument !== null && filled($selectedDocument->original_filename)) {
        $selectedDocumentLabel .= " ({$selectedDocument->original_filename})";
    }

    $imageDocumentsCount = $documentOptions
        ->filter(function ($document): bool {
            $mimeType = (string) ($document->mime_type ?? '');
            $filename = (string) ($document->original_filename ?? '');
            $extension = str($filename !== '' ? pathinfo($filename, PATHINFO_EXTENSION) : '')
                ->lower()
                ->toString();

            return str_starts_with($mimeType, 'image/') ||
                in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'], true);
        })
        ->count();
    $fileDocumentsCount = $documentOptions->count() - $imageDocumentsCount;
    $hasDocuments = $documentOptions->isNotEmpty();
    $documentArchiveTooltipText = $hasDocuments
        ? __('Open the document archive. Available: :total total, :images images, :documents documents.', [
            'total' => $documentOptions->count(),
            'images' => $imageDocumentsCount,
            'documents' => $fileDocumentsCount,
        ])
        : __('No documents or images have been added for this person yet.');
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('Person Document Information')"
        :description="__('Document metadata assigned to this person.')"
    >
        <x-ui.tooltip.trigger
            :title="__('Document archive')"
            :text="$documentArchiveTooltipText"
        >
            <span class="relative inline-flex">

                <flux:button.group>
                    {{-- Button Document Archive Image Counter --}}
                    <flux:button
                        aria-label="{{ __('Open document archive') }}"
                        size="sm"
                        variant="primary"
                        color="sky"
                        :disabled="!$hasDocuments"
                        wire:click="openDocumentArchive"
                    >
                        {{ $imageDocumentsCount }}
                    </flux:button>

                    {{-- Button Document Archive --}}
                    <flux:button
                        aria-label="{{ __('Open document archive') }}"
                        size="sm"
                        :disabled="!$hasDocuments"
                        wire:click="openDocumentArchive"
                    >
                        <flux:icon.folder-open />
                    </flux:button>

                    {{-- Button Document Archive File Counter --}}
                    <flux:button
                        aria-label="{{ __('Open document archive') }}"
                        size="sm"
                        variant="primary"
                        color="green"
                        :disabled="!$hasDocuments"
                        wire:click="openDocumentArchive"
                    >
                        {{ $fileDocumentsCount }}
                    </flux:button>
                </flux:button.group>

            </span>
        </x-ui.tooltip.trigger>

        <x-ui.tooltip.trigger
            :title="__('Add document')"
            :text="__('Upload a new document or image for this person.')"
        >
            <flux:button.group>
                {{-- Button Add Document Icon --}}
                <flux:button
                    aria-label="{{ __('Add document') }}"
                    size="sm"
                    wire:click="openAddDocumentModal"
                >
                    <flux:icon.file-plus-corner />
                </flux:button>

                {{-- Button Add Document Text --}}
                <flux:button
                    aria-label="{{ __('Add document') }}"
                    size="sm"
                    wire:click="openAddDocumentModal"
                >
                    {{ __('Add document') }}
                </flux:button>
            </flux:button.group>
        </x-ui.tooltip.trigger>

    </x-ui.headers.card>

    <div class="space-y-4">

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-8">

            {{-- Document Category --}}
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.folder-open />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Document category')"
                                :text="__('The category used to group this document in the archive.')"
                            >
                                {{ __('Document category') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentCategory')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-document-category"
                                variant="listbox"
                                searchable
                                clearable
                                copyable
                                wire:model.live="documentCategory"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($documentCategoryOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">
                                        {{ __($label) }}
                                    </flux:select.option>
                                @endforeach
                                <flux:select.option.create
                                    min-length="2"
                                    x-on:pointerdown="$wire.createNewDocumentCategory($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '', 'documentCategory')"
                                    x-on:click="$wire.createNewDocumentCategory($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '', 'documentCategory')"
                                >
                                    {{ __('Create new document category') }}
                                </flux:select.option.create>
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span class="truncate">{{ $documentCategoryLabel }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentCategory"
                            :title="__('Edit document category')"
                            :text="__('Click to edit the document category.')"
                            :changed="$this->isEditingFieldChanged('documentCategory')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentCategory" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Document Title --}}
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.tag />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Document title')"
                                :text="__('The title of the document, e.g. Passport, Driver License, etc.')"
                            >
                                {{ __('Document title') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentTitle')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-document-title"
                                type="text"
                                wire:model.live.debounce.500ms="documentTitle"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($documentTitle) ? $documentTitle : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentTitle"
                            :title="__('Edit document title')"
                            :text="__('Click to edit the document title.')"
                            :changed="$this->isEditingFieldChanged('documentTitle')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentTitle" />
            </flux:field>

            {{-- Document Number --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.hash />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Document number')"
                                :text="__(
                                    'The unique number of the document, e.g. Passport number, Driver License number, etc.',
                                )"
                            >
                                {{ __('Document number') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentNumber')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-document-number"
                                type="text"
                                wire:model.live.debounce.500ms="documentNumber"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($documentNumber) ? $documentNumber : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentNumber"
                            :title="__('Edit document number')"
                            :text="__('Click to edit the document number.')"
                            :changed="$this->isEditingFieldChanged('documentNumber')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentNumber" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Selected Document --}}
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.files />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Selected document')"
                                :text="__('The document currently selected for this person.')"
                            >
                                {{ __('Selected document') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentId')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-document-selection"
                                variant="listbox"
                                searchable
                                clearable
                                wire:model.live="documentId"
                                placeholder="{{ __('New document') }}"
                            >
                                @foreach ($documentOptions as $document)
                                    <flux:select.option value="{{ (string) $document->id }}">
                                        {{ filled($document->title) ? $document->title : __($documentTypeOptions[$document->type] ?? $document->type) }}
                                        @if (filled($document->document_number))
                                            ({{ $document->document_number }})
                                        @elseif (filled($document->original_filename))
                                            ({{ $document->original_filename }})
                                        @endif
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span class="truncate">{{ $selectedDocumentLabel }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentId"
                            :title="__('Edit selected document')"
                            :text="__('Click to edit the selected document.')"
                            :changed="$this->isEditingFieldChanged('documentId')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentId" />
            </flux:field>

            {{-- Document Type --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.file-text />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Document type')"
                                :text="__('The type of the document, e.g. Passport, Driver License, etc.')"
                            >
                                {{ __('Document type') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentType')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-document-type"
                                variant="listbox"
                                searchable
                                clearable
                                copyable
                                wire:model.live="documentType"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($documentTypeOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">
                                        {{ __($label) }}
                                    </flux:select.option>
                                @endforeach
                                <flux:select.option.create
                                    min-length="2"
                                    x-on:pointerdown="$wire.createNewDocumentType($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '', 'documentType')"
                                    x-on:click="$wire.createNewDocumentType($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '', 'documentType')"
                                >
                                    {{ __('Create new document type') }}
                                </flux:select.option.create>
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span class="truncate">{{ $documentTypeLabel }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentType"
                            :title="__('Edit document type')"
                            :text="__('Click to edit the document type.')"
                            :changed="$this->isEditingFieldChanged('documentType')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentType" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Issuing Authority --}}
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.building-library />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Issuing authority')"
                                :text="__(
                                    'The authority that issued the document, e.g. Passport Office, DMV, etc.',
                                )"
                            >
                                {{ __('Issuing authority') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentIssuingAuthority')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-document-issuing-authority"
                                variant="listbox"
                                searchable
                                clearable
                                copyable
                                wire:model.live="documentIssuingAuthority"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($documentIssuingAuthorityOptions as $issuingAuthority)
                                    <flux:select.option value="{{ $issuingAuthority }}">
                                        {{ $issuingAuthority }}
                                    </flux:select.option>
                                @endforeach

                                <flux:select.option.create
                                    min-length="2"
                                    x-on:click="$wire.useCreatedDocumentIssuingAuthority($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                                >
                                    {{ __('Use entered issuing authority') }}
                                </flux:select.option.create>
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($documentIssuingAuthority) ? $documentIssuingAuthority : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentIssuingAuthority"
                            :title="__('Edit issuing authority')"
                            :text="__('Click to edit the issuing authority.')"
                            :changed="$this->isEditingFieldChanged('documentIssuingAuthority')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentIssuingAuthority" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Issued At --}}
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.calendar />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Issued at')"
                                :text="__('The date when the document was issued.')"
                            >
                                {{ __('Issued at') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentIssuedAt')
                            <flux:date-picker
                                class="w-full min-w-0 rounded-l-none tabular-nums"
                                id="edit-person-document-issued-at"
                                type="input"
                                variant="custom"
                                fixed-weeks
                                selectable-header
                                clearable
                                wire:model.live="documentIssuedAt"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($documentIssuedAt) ? $documentIssuedAt : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentIssuedAt"
                            :title="__('Edit issued at date')"
                            :text="__('Click to edit the issued at date.')"
                            :changed="$this->isEditingFieldChanged('documentIssuedAt')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentIssuedAt" />
            </flux:field>

            {{-- Document Expires At --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.calendar />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Document expiration date')"
                                :text="__('The date when the document expires.')"
                            >
                                {{ __('Expires at') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'documentExpiresAt')
                            <flux:date-picker
                                class="w-full min-w-0 rounded-l-none tabular-nums"
                                id="edit-person-document-expires-at"
                                type="input"
                                variant="custom"
                                fixed-weeks
                                selectable-header
                                clearable
                                wire:model.live="documentExpiresAt"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($documentExpiresAt) ? $documentExpiresAt : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="documentExpiresAt"
                            :title="__('Edit document expiration date')"
                            :text="__('Click to edit the document expiration date.')"
                            :changed="$this->isEditingFieldChanged('documentExpiresAt')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="documentExpiresAt" />
            </flux:field>
        </div>
    </div>
</flux:card>

@include('components.management.people.edit-person.sections.documents.⚡archive-modal')
@include('components.management.people.edit-person.sections.documents.⚡add-modal')
