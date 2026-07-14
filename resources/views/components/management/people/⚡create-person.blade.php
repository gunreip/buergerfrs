{{-- resources/views/components/management/people/⚡create-person.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Create Person')"
        :description="__('Create a natural person and prepare the related login account.')"
    >
        <div class="flex items-center gap-3">
            <div
                class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-100 text-zinc-400 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:text-zinc-500 dark:ring-zinc-700">
                @if ($avatarUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                    <img
                        class="h-full w-full object-cover"
                        src="{{ $avatarUpload->temporaryUrl() }}"
                        alt="{{ __('Avatar preview') }}"
                    >
                @else
                    <flux:icon.user class="size-8" />
                @endif
            </div>
        </div>
    </x-ui.headers.page>

    <form
        class="mt-6"
        id="create-person-form"
        wire:submit="create"
    >
        {{-- Partial: Person form --}}
        @include('components.management.people.create-person.⚡form-person')

        {{-- Partial: Actions --}}
        @include('components.management.people.create-person.⚡actions')
    </form>
</flux:card>
