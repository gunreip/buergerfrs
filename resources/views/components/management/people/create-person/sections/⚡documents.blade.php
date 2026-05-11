{{-- resources/views/components/management/people/create-person/sections/⚡documents.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Document type --}}
            <flux:field>
                <flux:label for="create-person-document-type">
                    {{ __('Document type') }}
                </flux:label>

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
                <flux:label for="create-person-document-title">
                    {{ __('Document title') }}
                </flux:label>

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
                <flux:label for="create-person-document-number">
                    {{ __('Document number') }}
                </flux:label>

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
                <flux:label for="create-person-document-issuing-authority">
                    {{ __('Issuing authority') }}
                </flux:label>

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
                <flux:label for="create-person-document-issued-at">
                    {{ __('Issued at') }}
                </flux:label>

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
                <flux:label for="create-person-document-expires-at">
                    {{ __('Expires at') }}
                </flux:label>

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
                <flux:label for="create-person-document-upload">
                    {{ __('Document file') }}
                </flux:label>

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
