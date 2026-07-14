{{-- resources/views/components/management/people/create-person/sections/⚡avatar.blade.php --}}

<flux:card class="space-y-4">
    @php($hasAvatarUpload = $avatarUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)

    <flux:field class="col-span-3 mb-6">
        <div class="flex items-start justify-between gap-4">
            <flux:heading size="lg">
                <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                    <flux:icon.camera class="mr-2 inline-block" />
                    {{ __('Person Avatar') }}
                </span>
            </flux:heading>
        </div>
    </flux:field>

    <flux:field>
        <div class="grid gap-6 px-4 md:grid-cols-[10rem_1fr] md:items-start">
            <div class="flex justify-center md:justify-start">
                <div
                    class="flex h-36 w-36 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                    @if ($hasAvatarUpload)
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

            <div class="space-y-3 self-start">
                <flux:file-upload
                    class="self-start"
                    id="create-person-avatar-upload"
                    name="avatarUpload"
                    wire:model="avatarUpload"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    label="{{ __('Avatar') }}"
                    :badge="$this->isRequiredField('avatarUpload') ? __('ui.form.tab_status_dot.required') : null"
                    description="{{ __('Please upload an avatar for the person. Allowed file types: JPG, PNG, WebP. Maximum size: 4 MB.') }}"
                >
                    <div @class([
                        'grid items-stretch gap-3',
                        'md:grid-cols-2' => $hasAvatarUpload,
                    ])>
                        <flux:file-upload.dropzone
                            class="h-full"
                            inline
                            with-progress
                            icon="photo"
                            heading="{{ __('Drop avatar here or click to browse') }}"
                            text="{{ __('JPG, PNG, WebP up to 4 MB') }}"
                        />

                        @if ($hasAvatarUpload)
                            <flux:file-item
                                class="h-full"
                                heading="{{ $avatarUpload->getClientOriginalName() }}"
                                :image="$avatarUpload->temporaryUrl()"
                                :size="$avatarUpload->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove wire:click="removeAvatarUpload" />
                                </x-slot>
                            </flux:file-item>
                        @endif
                    </div>
                </flux:file-upload>

                <flux:error name="avatarUpload" />
            </div>
        </div>
    </flux:field>
</flux:card>
