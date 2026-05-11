{{-- resources/views/components/management/people/create-person/sections/⚡avatar.blade.php --}}

<flux:card class="space-y-4">

    <flux:field>
        <div class="grid gap-6 px-4 md:grid-cols-[10rem_1fr] md:items-start">
            <div class="flex justify-center md:justify-start">
                <div
                    class="flex h-36 w-36 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                    @if ($avatarUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        <img
                            class="h-full w-full object-cover"
                            src="{{ $avatarUpload->temporaryUrl() }}"
                            alt="{{ __('Avatar preview') }}"
                        >
                    @else
                        <flux:icon.user class="size-12" />
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                <flux:field>
                    <x-ui.tooltip.trigger
                        title="{{ __('Avatar') }}"
                        text="{{ __('Please upload an avatar for the person. This will be displayed in the person\'s profile and can help with quickly identifying the person.') }}"
                    >
                        <flux:label for="create-person-avatar-upload">
                            {{ __('Avatar') }}
                        </flux:label>
                    </x-ui.tooltip.trigger>

                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.arrow-up-tray />
                        </flux:input.group.prefix>

                        <flux:input
                            id="create-person-avatar-upload"
                            type="file"
                            wire:model="avatarUpload"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        />
                    </flux:input.group>

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Allowed file types: JPG, PNG, WEBP. Maximum size: 4 MB.') }}
                    </p>

                    <flux:error name="avatarUpload" />
                </flux:field>
            </div>
        </div>
    </flux:field>
</flux:card>
