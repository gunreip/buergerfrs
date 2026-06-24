{{-- resources/views/components/management/people/create-person/sections/⚡documents.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

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
                        wire:model.blur="documentType"
                        placeholder="{{ __('Please select') }}"
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
                        type="text"
                        wire:model.blur="documentTitle"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="documentTitle" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-2">

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
                        type="text"
                        wire:model.blur="documentNumber"
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

                    <flux:input
                        id="create-person-document-issuing-authority"
                        type="text"
                        wire:model.blur="documentIssuingAuthority"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="documentIssuingAuthority" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-2">

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

                    <flux:input
                        id="create-person-document-issued-at"
                        type="date"
                        wire:model.blur="documentIssuedAt"
                        autocomplete="new-password"
                        copyable
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
                        <flux:icon.calendar-days />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-document-expires-at"
                        type="date"
                        wire:model.blur="documentExpiresAt"
                        autocomplete="new-password"
                        copyable
                    />
                </flux:input.group>

                <flux:error name="documentExpiresAt" />
            </flux:field>

            <flux:field class="col-span-2">
                <x-ui.tooltip.trigger
                    title="{{ __('Document file') }}"
                    field="documentUpload"
                    text="{{ __('Please upload a file for the document. This is important for correctly identifying the person\'s document and for any document-specific validations.') }}"
                    :required="$this->isRequiredField('documentUpload')"
                >
                    <flux:label for="create-person-document-upload">
                        {{ __('Document file') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('documentUpload')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.arrow-up-tray />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-document-upload"
                        type="file"
                        wire:model="documentUpload"
                        accept=".pdf,.jpg,.jpeg,.png,.webp"
                    />
                </flux:input.group>

                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Allowed file types: PDF, JPG, PNG, WebP. Maximum size: 10 MB.') }}
                </p>

                <flux:error name="documentUpload" />
            </flux:field>
        </div>
    </div>
</flux:card>
