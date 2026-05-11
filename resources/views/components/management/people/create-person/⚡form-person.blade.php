{{-- resources/views/components/management/people/create-person/⚡form-person.blade.php --}}

<div
    class="space-y-6"
    x-data="{ activeSection: 'person' }"
>
    {{-- Full-width section tabs --}}
    <div
        class="w-full overflow-x-auto rounded-2xl border border-zinc-200 bg-white/60 p-2 dark:border-zinc-700 dark:bg-zinc-900/40">
        <div class="grid min-w-max grid-cols-8 gap-2 xl:min-w-0">
            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="circle-user-round"
                variant="ghost"
                x-bind:class="activeSection === 'person' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'person'"
            >
                {{ __('Person') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="contact-round"
                variant="ghost"
                x-bind:class="activeSection === 'contact' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'contact'"
            >
                {{ __('Contact') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="map-pin-house"
                variant="ghost"
                x-bind:class="activeSection === 'address' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'address'"
            >
                {{ __('Address') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="globe"
                variant="ghost"
                x-bind:class="activeSection === 'international' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'international'"
            >
                {{ __('International') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="id-card"
                variant="ghost"
                x-bind:class="activeSection === 'identification' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'identification'"
            >
                {{ __('Identification') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="heart-pulse"
                variant="ghost"
                x-bind:class="activeSection === 'health-insurance' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' :
                    ''"
                x-on:click="activeSection = 'health-insurance'"
            >
                {{ __('Health insurance') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="scroll-text"
                variant="ghost"
                x-bind:class="activeSection === 'documents' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'documents'"
            >
                {{ __('Documents') }}
            </flux:button>

            <flux:button
                class="w-full justify-center whitespace-nowrap"
                type="button"
                size="sm"
                icon="siren"
                variant="ghost"
                x-bind:class="activeSection === 'emergency' ? '!bg-zinc-900 !text-white dark:!bg-white dark:!text-zinc-900' : ''"
                x-on:click="activeSection = 'emergency'"
            >
                {{ __('Emergency') }}
            </flux:button>
        </div>
    </div>

    {{-- Main 2/3 + sidebar 1/3 layout --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        {{-- Main form area --}}
        <div class="space-y-6 xl:col-span-9">
            <div
                x-show="activeSection === 'person'"
                x-cloak
            >
                <flux:card class="mb-6 space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Person') }}"
                        description="{{ __('Core person data will be placed here.') }}"
                    >
                        <flux:icon.circle-user-round
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>

                    {{-- Person core data fields --}}
                    @include('components.management.people.create-person.sections.⚡person-core')
                </flux:card>

                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        :title="__('Avatar / Passphoto')"
                        :description="__('Upload a passphoto or profile image for this person.')"
                    >
                        <flux:icon.camera
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡avatar')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'contact'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Contact') }}"
                        description="{{ __('Contact fields will be placed here.') }}"
                    >
                        <flux:icon.contact-round
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡contact')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'address'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Address') }}"
                        description="{{ __('Address fields will be placed here.') }}"
                    >
                        <flux:icon.map-pin-house
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡address')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'international'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('International') }}"
                        description="{{ __('Nationality and language fields will be placed here.') }}"
                    >
                        <flux:icon.globe
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡international')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'identification'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Identification') }}"
                        description="{{ __('Identifier fields will be placed here.') }}"
                    >
                        <flux:icon.id-card
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡identification')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'health-insurance'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Health insurance') }}"
                        description="{{ __('Health insurance fields will be placed here.') }}"
                    >
                        <flux:icon.heart-pulse
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡health-insurance')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'documents'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Documents') }}"
                        description="{{ __('Document metadata and upload fields will be placed here.') }}"
                    >
                        <flux:icon.scroll-text
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡documents')
                </flux:card>
            </div>

            <div
                x-show="activeSection === 'emergency'"
                x-cloak
            >
                <flux:card class="space-y-4">
                    <x-ui.headers.card
                        title="{{ __('Emergency contact') }}"
                        description="{{ __('Emergency contact fields will be placed here.') }}"
                    >
                        <flux:icon.siren
                            class="size-12"
                            stroke-width="1"
                        />
                    </x-ui.headers.card>
                    @include('components.management.people.create-person.sections.⚡emergency-contact')
                </flux:card>
            </div>
        </div>

        {{-- Right sidebar --}}
        <aside class="space-y-6 xl:col-span-3">
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

            {{-- Partial: Login form --}}
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

                <div
                    class="rounded-xl border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                    {{ __('No documents uploaded yet.') }}
                </div>
            </flux:card>
        </aside>
    </div>
</div>
