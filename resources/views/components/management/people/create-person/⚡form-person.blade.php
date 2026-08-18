{{-- resources/views/components/management/people/create-person/⚡form-person.blade.php --}}

@php
    $formTabs = [
        [
            'name' => 'person',
            'label' => __('Person'),
            'icon' => 'circle-user-round',
        ],
        [
            'name' => 'address',
            'label' => __('ui.labels.address.address'),
            'icon' => 'map-pin-house',
        ],
        [
            'name' => 'contact',
            'label' => __('Contact'),
            'icon' => 'contact-round',
        ],
        [
            'name' => 'international',
            'label' => __('International'),
            'icon' => 'globe',
        ],
        [
            'name' => 'identification',
            'label' => __('Identification'),
            'icon' => 'id-card',
        ],
        [
            'name' => 'health-insurance',
            'label' => __('Health insurance'),
            'icon' => 'heart-pulse',
        ],
        [
            'name' => 'documents',
            'label' => __('Documents'),
            'icon' => 'scroll-text',
        ],
        [
            'name' => 'emergency-contact',
            'label' => __('Emergency contact'),
            'icon' => 'siren',
        ],
    ];
@endphp

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
    <flux:card class="min-w-0">
        <flux:tab.group>
            <flux:tabs
                class="min-h-0"
                wire:model.live="activeFormTab"
            >
                @foreach ($formTabs as $formTab)
                    <flux:tab
                        class="px-4 hover:cursor-pointer"
                        name="{{ $formTab['name'] }}"
                        data-form-tab-trigger="{{ $formTab['name'] }}"
                        icon="{{ $formTab['icon'] }}"
                    >
                        {{ $formTab['label'] }}
                    </flux:tab>
                @endforeach
            </flux:tabs>

            <flux:tab.panel
                class="space-y-6"
                name="person"
            >
                @include('components.management.people.create-person.sections.⚡person-core')
                @include('components.management.people.create-person.sections.⚡avatar')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="address"
            >
                @include('components.management.people.create-person.sections.⚡address')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="contact"
            >
                @include('components.management.people.create-person.sections.⚡contact')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="international"
            >
                @include('components.management.people.create-person.sections.⚡international')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="identification"
            >
                @include('components.management.people.create-person.sections.⚡identification')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="health-insurance"
            >
                @include('components.management.people.create-person.sections.⚡health-insurance')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="documents"
            >
                @include('components.management.people.create-person.sections.⚡documents')
            </flux:tab.panel>

            <flux:tab.panel
                class="space-y-6"
                name="emergency-contact"
            >
                @include('components.management.people.create-person.sections.⚡emergency-contact')
            </flux:tab.panel>
        </flux:tab.group>
    </flux:card>

    <aside class="space-y-6">
        <flux:card>
            <x-ui.headers.card
                title="{{ __('Person number') }}"
                description="{{ __('Person number field will be filled automatically.') }}"
            >
                <flux:icon.fingerprint-pattern
                    class="size-12"
                    stroke-width="1"
                />
            </x-ui.headers.card>

            <flux:field>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.fingerprint-pattern />
                    </flux:input.group.prefix>

                    <flux:input
                        class="tabular-nums tracking-wide"
                        id="create-person-person-number"
                        type="text"
                        value="{{ $createdPersonNumber !== '' ? $createdPersonNumber : '' }}"
                        placeholder="{{ __('Will be filled automatically') }}"
                        autocomplete="new-password"
                        readonly
                        copyable
                    />
                </flux:input.group>
            </flux:field>
        </flux:card>

        @include('components.management.people.create-person.⚡form-login')

        <flux:card class="space-y-4">
            <x-ui.headers.card
                :title="__('Document summary')"
                :description="__('Uploaded or prepared documents will be summarized here.')"
            >
                <flux:icon.table-of-contents
                    class="size-12"
                    stroke-width="1"
                />
            </x-ui.headers.card>

            @if (collect($documentUpload)->isNotEmpty())
                <div class="space-y-3 rounded-xl border border-zinc-200 p-4 text-sm dark:border-zinc-700">
                    <div class="space-y-2">
                        @foreach ($documentUpload as $uploadedDocument)
                            @if ($uploadedDocument instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                <div class="flex items-start gap-3">
                                    <flux:icon.document-text
                                        class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400"
                                    />

                                    <div class="min-w-0 space-y-1">
                                        <div class="truncate font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $uploadedDocument->getClientOriginalName() }}
                                        </div>

                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ number_format($uploadedDocument->getSize() / 1024, 1) }} KB
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if ($documentTitle !== '' || $documentType !== '')
                        <flux:separator />

                        <dl class="grid grid-cols-[auto_minmax(0,1fr)] gap-x-3 gap-y-2 text-xs">
                            @if ($documentType !== '')
                                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('ui.type') }}
                                </dt>
                                <dd class="truncate text-zinc-700 dark:text-zinc-300">
                                    {{ __($documentTypeOptions[$documentType] ?? $documentType) }}
                                </dd>
                            @endif

                            @if ($documentTitle !== '')
                                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Title') }}</dt>
                                <dd class="truncate text-zinc-700 dark:text-zinc-300">{{ $documentTitle }}</dd>
                            @endif
                        </dl>
                    @endif
                </div>
            @elseif ($documentUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                <div class="space-y-3 rounded-xl border border-zinc-200 p-4 text-sm dark:border-zinc-700">
                    <div class="flex items-start gap-3">
                        <flux:icon.document-text
                            class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />

                        <div class="min-w-0 space-y-1">
                            <div class="truncate font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $documentUpload->getClientOriginalName() }}
                            </div>

                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ number_format($documentUpload->getSize() / 1024, 1) }} KB
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="rounded-xl border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                    {{ __('No documents uploaded yet.') }}
                </div>
            @endif
        </flux:card>
    </aside>
</div>
