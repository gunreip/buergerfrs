{{-- resources/views/components/management/people/⚡edit-person.blade.php --}}

@php($avatarUrl = $this->avatarUrl())

<flux:card>
    <x-ui.headers.page
        :title="__('Person details')"
        :description="$person->displayName()"
    >
        <div class="flex items-center gap-3">
            {{-- <div
                class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-100 text-zinc-400 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:text-zinc-500 dark:ring-zinc-700">
                @if ($avatarUrl !== null)
                    <img
                        class="h-full w-full object-cover"
                        src="{{ $avatarUrl }}"
                        alt="{{ __('ui.user_avatar.avatar_for_name', ['name' => $person->displayName()]) }}"
                    >
                @else
                    <flux:icon.user class="size-8" />
                @endif
            </div> --}}
            <flux:avatar
                name="{{ $person->displayName() }}"
                src="{{ $avatarUrl }}"
                {{-- circle --}}
                size="lg"
                color="auto"
            />
        </div>
    </x-ui.headers.page>

    @include('components.management.people.edit-person.⚡meta')

    <form
        class="mt-6 space-y-6"
        wire:submit="save"
    >
        @include('components.management.people.edit-person.⚡form-person')

        {{-- @include('components.management.people.edit-person.⚡actions') --}}
    </form>
</flux:card>
