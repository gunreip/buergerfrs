{{-- resources/views/components/management/people/create-person/sections/⚡documents.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <flux:field>
            <div class="flex items-start justify-between gap-4">
                <flux:heading size="lg">
                    <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                        <flux:icon.document-text class="mr-2 inline-block" />
                        {{ __('Person Document Information') }}
                    </span>
                </flux:heading>

                <div class="flex flex-wrap items-center justify-end gap-2">
                    <x-ui.badge.created-password :password="$generatedPassword" />

                    <x-ui.badge.test-data :show="$isTestData" />
                </div>
            </div>
        </flux:field>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="space-y-4">
                {{-- Document type --}}
                <flux:field>
                    <x-ui.tooltip.trigger
                        title="{{ __('Document type') }}"
                        field="documentType"
                        text="{{ __('Please select the type of document for the person. This is important for correctly identifying the person\'s document and for any document-specific validations.') }}"
                        :required="$this->isRequiredField('documentType')"
                    >
                        <flux:label for="create-person-document-type">
                            {{ __('Document type') }}
                            <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentType')" />
                        </flux:label>
                    </x-ui.tooltip.trigger>

                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.document-text />
                        </flux:input.group.prefix>

                        <flux:select
                            id="create-person-document-type"
                            name="documentType"
                            wire:model.live="documentType"
                            placeholder="{{ __('Please select') }}"
                            variant="listbox"
                            searchable
                            clearable
                            copyable
                        >
                            @foreach ($documentTypeOptions as $value => $label)
                                <flux:select.option :value="$value">
                                    {{ __($label) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>

                    <flux:error name="documentType" />
                </flux:field>

                {{-- Document title --}}
                <flux:field>
                    <x-ui.tooltip.trigger
                        title="{{ __('Document title') }}"
                        field="documentTitle"
                        text="{{ __('Please enter the title of the document for the person. This is important for correctly identifying the person\'s document and for any document-specific validations.') }}"
                        :required="$this->isRequiredField('documentTitle')"
                    >
                        <flux:label for="create-person-document-title">
                            {{ __('Document title') }}
                            <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentTitle')" />
                        </flux:label>
                    </x-ui.tooltip.trigger>

                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.tag />
                        </flux:input.group.prefix>

                        <flux:input
                            id="create-person-document-title"
                            name="documentTitle"
                            type="text"
                            wire:model.live.debounce.500ms="documentTitle"
                            autocomplete="new-password"
                            copyable
                            clearable
                        />
                    </flux:input.group>

                    <flux:error name="documentTitle" />
                </flux:field>

                {{-- Document number --}}
                <flux:field>
                    <x-ui.tooltip.trigger
                        title="{{ __('Document number') }}"
                        field="documentNumber"
                        text="{{ __('Please enter the document number for the person. This is important for correctly identifying the person\'s document and for any document-specific validations.') }}"
                        :required="$this->isRequiredField('documentNumber')"
                    >
                        <flux:label for="create-person-document-number">
                            {{ __('Document number') }}
                            <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentNumber')" />
                        </flux:label>
                    </x-ui.tooltip.trigger>

                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.hashtag />
                        </flux:input.group.prefix>

                        <flux:input
                            id="create-person-document-number"
                            name="documentNumber"
                            type="text"
                            wire:model.live.debounce.500ms="documentNumber"
                            autocomplete="new-password"
                            copyable
                            clearable
                        />
                    </flux:input.group>

                    <flux:error name="documentNumber" />
                </flux:field>

                {{-- Document issuing authority --}}
                <flux:field>
                    <x-ui.tooltip.trigger
                        title="{{ __('Issuing authority') }}"
                        field="documentIssuingAuthority"
                        text="{{ __('management.people.create_person.sections.documents.please_enter_the_issuing_authority_for_the_document_this_is_important_for_correc') }}"
                        :required="$this->isRequiredField('documentIssuingAuthority')"
                    >
                        <flux:label for="create-person-document-issuing-authority">
                            {{ __('Issuing authority') }}
                            <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentIssuingAuthority')" />
                        </flux:label>
                    </x-ui.tooltip.trigger>

                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.building-library />
                        </flux:input.group.prefix>

                        <flux:select
                            id="create-person-document-issuing-authority"
                            name="documentIssuingAuthority"
                            autocomplete="new-password"
                            variant="listbox"
                            placeholder="{{ __('Please select') }}"
                            searchable
                            clearable
                            copyable
                            wire:model.live="documentIssuingAuthority"
                        >
                            @foreach ($documentIssuingAuthorityOptions as $issuingAuthority)
                                <flux:select.option :value="$issuingAuthority">
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
                    </flux:input.group>

                    <flux:error name="documentIssuingAuthority" />
                </flux:field>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Document issued at --}}
                    <flux:field>
                        <x-ui.tooltip.trigger
                            title="{{ __('Issued at') }}"
                            field="documentIssuedAt"
                            text="{{ __('Please enter the date when the document was issued. This is important for correctly identifying the person\'s document and for any document-specific validations.') }}"
                            :required="$this->isRequiredField('documentIssuedAt')"
                        >
                            <flux:label for="create-person-document-issued-at">
                                {{ __('Issued at') }}
                                <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentIssuedAt')" />
                            </flux:label>
                        </x-ui.tooltip.trigger>

                        <flux:input.group>
                            <flux:input.group.prefix>
                                <flux:icon.calendar />
                            </flux:input.group.prefix>

                            <flux:date-picker
                                class="w-full tabular-nums"
                                id="create-person-document-issued-at"
                                name="documentIssuedAt"
                                type="input"
                                variant="custom"
                                fixed-weeks
                                selectable-header
                                clearable
                                wire:model.live="documentIssuedAt"
                            />
                        </flux:input.group>

                        <flux:error name="documentIssuedAt" />
                    </flux:field>

                    {{-- Document expires at --}}
                    <flux:field>
                        <x-ui.tooltip.trigger
                            title="{{ __('Expires at') }}"
                            field="documentExpiresAt"
                            text="{{ __('Please enter the date when the document expires. This is important for correctly identifying the person\'s document and for any document-specific validations.') }}"
                            :required="$this->isRequiredField('documentExpiresAt')"
                        >
                            <flux:label for="create-person-document-expires-at">
                                {{ __('Expires at') }}
                                <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentExpiresAt')" />
                            </flux:label>
                        </x-ui.tooltip.trigger>

                        <flux:input.group>
                            <flux:input.group.prefix>
                                <flux:icon.calendar />
                            </flux:input.group.prefix>

                            <flux:date-picker
                                class="w-full tabular-nums"
                                id="create-person-document-expires-at"
                                name="documentExpiresAt"
                                type="input"
                                variant="custom"
                                fixed-weeks
                                selectable-header
                                clearable
                                wire:model.live="documentExpiresAt"
                            />
                        </flux:input.group>

                        <flux:error name="documentExpiresAt" />
                    </flux:field>
                </div>
            </div>

            {{-- Document File Upload --}}
            <flux:field class="self-start">
                <flux:file-upload
                    class="self-start"
                    id="create-person-document-upload"
                    name="documentUpload"
                    multiple
                    wire:model="documentUpload"
                    accept=".pdf,.jpg,.jpeg,.png,.webp"
                    label="{{ __('Document file') }}"
                    :badge="$this->isRequiredField('documentUpload') ? __('ui.form.tab_status_dot.required') : null"
                    description="{{ __('Please upload a file for the document. Allowed file types: PDF, JPG, PNG, WebP. Maximum size: 10 MB.') }}"
                >
                    <flux:file-upload.dropzone
                        class="self-start"
                        with-progress
                        heading="{{ __('Drop file here or click to browse') }}"
                        text="{{ __('PDF, JPG, PNG, WebP up to 10 MB') }}"
                    />
                </flux:file-upload>

                @if (collect($documentUpload)->isNotEmpty())
                    <div class="mt-4 flex flex-col gap-2">
                        @foreach ($documentUpload as $uploadIndex => $uploadedDocument)
                            @if ($uploadedDocument instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                <flux:file-item
                                    heading="{{ $uploadedDocument->getClientOriginalName() }}"
                                    :size="$uploadedDocument->getSize()"
                                >
                                    <x-slot name="actions">
                                        <flux:file-item.remove
                                            wire:click="removeDocumentUpload({{ $uploadIndex }})" />
                                    </x-slot>
                                </flux:file-item>
                            @endif
                        @endforeach
                    </div>
                @endif

                <flux:error name="documentUpload" />
                <flux:error name="documentUpload.*" />
            </flux:field>
        </div>
    </div>
</flux:card>
