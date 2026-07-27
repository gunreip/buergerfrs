{{-- resources/views/components/management/people/edit-person/sections/documents/⚡add-modal.blade.php --}}

@php
    $uploadedDocuments = is_array($newDocumentUpload) ? $newDocumentUpload : array_filter([$newDocumentUpload]);
    $hasNewDocumentUpload = collect($uploadedDocuments)->contains(
        fn($uploadedDocument) => $uploadedDocument instanceof
            \Livewire\Features\SupportFileUploads\TemporaryUploadedFile,
    );
    $canAddDocument =
        filled($newDocumentType) && filled($newDocumentTitle) && filled($newDocumentIssuedAt) && $hasNewDocumentUpload;
@endphp

<flux:modal
    class="w-full max-w-6xl"
    name="person-document-add"
    wire:model.self="addDocumentModalOpen"
>
    <form
        class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden"
        wire:submit="addDocument"
    >
        <x-ui.headers.card
            :title="__('ui.add-document')"
            :description="__('Upload one or more documents or images for this person.')"
        />

        <div class="min-h-0 overflow-y-auto pr-2">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-4">

                    {{-- Document Type --}}
                    <flux:field class="pt-2">
                        <flux:label for="edit-person-new-document-type">
                            <x-ui.tooltip.trigger
                                :title="__('Document type')"
                                field="newDocumentType"
                                :text="__(
                                    'Select the type of document. This is required so the document can be classified correctly.',
                                )"
                                :required="$this->isRequiredField('newDocumentType')"
                            >
                                {{ __('Document type') }}
                                <x-ui.tooltip.badge-required :required="$this->isRequiredField('newDocumentType')" />
                            </x-ui.tooltip.trigger>
                        </flux:label>
                        <flux:input.group class="w-full min-w-0">
                            <flux:input.group.prefix>
                                <flux:icon.document-text stroke-width="1" />
                            </flux:input.group.prefix>
                            <flux:select
                                id="edit-person-new-document-type"
                                name="newDocumentType"
                                variant="listbox"
                                searchable
                                clearable
                                wire:model.live="newDocumentType"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($documentTypeOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">
                                        {{ __($label) }}
                                    </flux:select.option>
                                @endforeach
                                <flux:select.option.create
                                    min-length="2"
                                    x-on:pointerdown="$wire.createNewDocumentType($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                                    x-on:click="$wire.createNewDocumentType($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                                >
                                    {{ __('Create new document type') }}
                                </flux:select.option.create>
                            </flux:select>
                        </flux:input.group>
                        <flux:error name="newDocumentType" />
                    </flux:field>

                    {{-- Document Title --}}
                    <flux:field>
                        <flux:label for="edit-person-new-document-title">
                            <x-ui.tooltip.trigger
                                :title="__('Document title')"
                                field="newDocumentTitle"
                                :text="__(
                                    'Enter a short title for the document. This is required so the document can be recognized later in the archive.',
                                )"
                                :required="$this->isRequiredField('newDocumentTitle')"
                            >
                                {{ __('Document title') }}
                                <x-ui.tooltip.badge-required :required="$this->isRequiredField('newDocumentTitle')" />
                            </x-ui.tooltip.trigger>
                        </flux:label>
                        <flux:input.group class="w-full min-w-0">
                            <flux:input.group.prefix>
                                <flux:icon.pencil-square stroke-width="1" />
                            </flux:input.group.prefix>
                            <flux:input
                                id="edit-person-new-document-title"
                                name="newDocumentTitle"
                                wire:model.live.debounce.500ms="newDocumentTitle"
                                autocomplete="new-password"
                                clearable
                            />
                        </flux:input.group>
                        <flux:error name="newDocumentTitle" />
                    </flux:field>

                    {{-- Document Number --}}
                    <flux:field>
                        <flux:label for="edit-person-new-document-number">
                            <x-ui.tooltip.trigger
                                :title="__('Document number')"
                                field="newDocumentNumber"
                                :text="__(
                                    'Enter the document number if available. This is optional but can help with document identification.',
                                )"
                            >
                                {{ __('Document number') }}
                            </x-ui.tooltip.trigger>
                        </flux:label>
                        <flux:input.group class="w-full min-w-0">
                            <flux:input.group.prefix>
                                <flux:icon.hashtag stroke-width="1" />
                            </flux:input.group.prefix>
                            <flux:input
                                id="edit-person-new-document-number"
                                wire:model.live.debounce.500ms="newDocumentNumber"
                                autocomplete="new-password"
                                clearable
                            />
                        </flux:input.group>
                        <flux:error name="newDocumentNumber" />
                    </flux:field>

                    {{-- Issued Authority --}}
                    <flux:field>
                        <flux:label for="edit-person-new-document-issuing-authority">
                            <x-ui.tooltip.trigger
                                :title="__('Issuing authority')"
                                field="newDocumentIssuingAuthority"
                                :text="__(
                                    'Enter the issuing authority of the document if available. This is optional but can help with document identification.',
                                )"
                            >
                                {{ __('Issuing authority') }}
                            </x-ui.tooltip.trigger>
                        </flux:label>
                        <flux:input.group class="w-full min-w-0">
                            <flux:input.group.prefix>
                                <flux:icon.building stroke-width="1" />
                            </flux:input.group.prefix>
                            <flux:input
                                id="edit-person-new-document-issuing-authority"
                                wire:model.live.debounce.500ms="newDocumentIssuingAuthority"
                                autocomplete="new-password"
                                clearable
                            />
                        </flux:input.group>
                        <flux:error name="newDocumentIssuingAuthority" />
                    </flux:field>

                    <div class="mb-4 grid gap-4 sm:grid-cols-2">

                        {{-- Issued At --}}
                        <flux:field>
                            <flux:label for="edit-person-new-document-issued-at">
                                <x-ui.tooltip.trigger
                                    :title="__('Issued at')"
                                    field="newDocumentIssuedAt"
                                    :text="__(
                                        'Enter the date when the document was issued. This is required for document history and validity tracking.',
                                    )"
                                    :required="$this->isRequiredField('newDocumentIssuedAt')"
                                >
                                    {{ __('Issued at') }}
                                    <x-ui.tooltip.badge-required :required="$this->isRequiredField('newDocumentIssuedAt')" />
                                </x-ui.tooltip.trigger>
                            </flux:label>
                            <flux:input.group class="w-full min-w-0">
                                <flux:input.group.prefix>
                                    <flux:icon.calendar-days stroke-width="1" />
                                </flux:input.group.prefix>
                                <flux:date-picker
                                    class="w-full tabular-nums"
                                    id="edit-person-new-document-issued-at"
                                    type="input"
                                    variant="custom"
                                    {{-- locale="{{ }}" --}}
                                    fixed-weeks
                                    week-numbers
                                    with-today
                                    selectable-header
                                    clearable
                                    wire:model.live="newDocumentIssuedAt"
                                />
                            </flux:input.group>
                            <flux:error name="newDocumentIssuedAt" />
                        </flux:field>

                        {{-- Expires At --}}
                        <flux:field>
                            <flux:label for="edit-person-new-document-expires-at">
                                <x-ui.tooltip.trigger
                                    :title="__('Expires at')"
                                    field="newDocumentExpiresAt"
                                    :text="__(
                                        'Enter the date when the document expires if applicable. This is optional but can help with document validity tracking.',
                                    )"
                                >
                                    {{ __('Expires at') }}
                                </x-ui.tooltip.trigger>
                            </flux:label>
                            <flux:input.group class="w-full min-w-0">
                                <flux:input.group.prefix>
                                    <flux:icon.calendar-days stroke-width="1" />
                                </flux:input.group.prefix>
                                <flux:date-picker
                                    class="w-full tabular-nums"
                                    id="edit-person-new-document-expires-at"
                                    type="input"
                                    variant="custom"
                                    {{-- locale="{{ }}" --}}
                                    fixed-weeks
                                    week-numbers
                                    with-today
                                    selectable-header
                                    clearable
                                    wire:model.live="newDocumentExpiresAt"
                                />
                            </flux:input.group>
                            <flux:error name="newDocumentExpiresAt" />
                        </flux:field>

                    </div>
                </div>

                <div class="space-y-4">

                    {{-- Document Category --}}
                    <flux:field class="pt-2">
                        <flux:label for="edit-person-new-document-category">
                            <x-ui.tooltip.trigger
                                :title="__('Document category')"
                                field="newDocumentCategory"
                                :text="__(
                                    'Select the category used to group this document in the archive. If left empty, the category is derived from the document type.',
                                )"
                            >
                                {{ __('Document category') }}
                            </x-ui.tooltip.trigger>
                        </flux:label>
                        <flux:input.group class="w-full min-w-0">
                            <flux:input.group.prefix>
                                <flux:icon.folder-open stroke-width="1" />
                            </flux:input.group.prefix>
                            <flux:select
                                id="edit-person-new-document-category"
                                name="newDocumentCategory"
                                variant="listbox"
                                searchable
                                clearable
                                wire:model.live="newDocumentCategory"
                                placeholder="{{ __('Derived from document type') }}"
                            >
                                @foreach ($documentCategoryOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">
                                        {{ __($label) }}
                                    </flux:select.option>
                                @endforeach
                                <flux:select.option.create
                                    min-length="2"
                                    x-on:pointerdown="$wire.createNewDocumentCategory($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                                    x-on:click="$wire.createNewDocumentCategory($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                                >
                                    {{ __('Create new document category') }}
                                </flux:select.option.create>
                            </flux:select>
                        </flux:input.group>
                        <flux:error name="newDocumentCategory" />
                    </flux:field>

                    {{-- File Upload --}}
                    <flux:field class="space-y-2">
                        <flux:label>
                            <x-ui.tooltip.trigger
                                :title="__('Document files')"
                                field="newDocumentUpload"
                                :text="__(
                                    'Upload the document file. This is required; allowed formats are PDF, JPG, PNG and WebP up to 10 MB.',
                                )"
                                :required="$this->isRequiredField('newDocumentUpload')"
                            >
                                {{ __('Document files') }}
                                <x-ui.tooltip.badge-required :required="$this->isRequiredField('newDocumentUpload')" />
                            </x-ui.tooltip.trigger>
                        </flux:label>

                        <flux:file-upload
                            id="edit-person-new-document-upload"
                            wire:model="newDocumentUpload"
                            accept=".pdf,.jpg,.jpeg,.png,.webp"
                            description="{{ __('Upload PDF, JPG, PNG or WebP files up to 10 MB each.') }}"
                        >
                            <flux:file-upload.dropzone
                                with-progress
                                heading="{{ __('Drop file here or click to browse') }}"
                                text="{{ __('PDF, JPG, PNG, WebP up to 10 MB') }}"
                            />
                        </flux:file-upload>

                        @if (collect($uploadedDocuments)->isNotEmpty())
                            <div class="mt-4 flex flex-col gap-2">
                                @foreach ($uploadedDocuments as $uploadIndex => $uploadedDocument)
                                    @if ($uploadedDocument instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                        <flux:file-item heading="{{ $uploadedDocument->getClientOriginalName() }}">
                                            <x-slot name="actions">
                                                <flux:file-item.remove
                                                    wire:click="removeNewDocumentUpload({{ $uploadIndex }})"
                                                />
                                            </x-slot>
                                        </flux:file-item>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <flux:error name="newDocumentUpload" />
                    </flux:field>
                </div>
            </div>
        </div>

        <div class="shrink-0 border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <div class="flex justify-end gap-3">

                {{-- Save Button --}}
                <flux:button
                    type="button"
                    variant="primary"
                    color="green"
                    icon="plus"
                    :disabled="!$canAddDocument"
                    wire:click="addDocument"
                    wire:loading.attr="disabled"
                    wire:target="addDocument,newDocumentUpload"
                >
                    {{ __('ui.add-document') }}
                </flux:button>
            </div>
        </div>
    </form>
</flux:modal>
