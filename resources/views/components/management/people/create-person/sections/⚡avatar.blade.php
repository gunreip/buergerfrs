{{-- resources/views/components/management/people/create-person/sections/⚡avatar.blade.php --}}

<flux:card class="space-y-4">
    <x-ui.headers.card
        :title="__('Avatar / passphoto')"
        :description="__('Upload a passphoto or profile image for this person.')"
    />

    <flux:field>
        <flux:label for="create-person-avatar-upload">
            {{ __('Avatar image') }}
        </flux:label>

        <div class="grid gap-6 p-4 md:grid-cols-[10rem_1fr] md:items-start">
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
                <flux:input
                    id="create-person-avatar-upload"
                    type="file"
                    wire:model="avatarUpload"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                />

                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Allowed file types: JPG, PNG, WEBP. Maximum size: 4 MB.') }}
                </p>

                <flux:error name="avatarUpload" />
            </div>
        </div>
    </flux:field>
</flux:card>
