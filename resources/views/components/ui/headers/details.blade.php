{{-- resources/views/components/ui/headers/details.blade.php --}}

@props([
    'title',
    'description' => null,
    'name' => null,
    'open' => false,
])

@php
    $isOpen = filter_var($open, FILTER_VALIDATE_BOOLEAN);
@endphp

<details
    {{ $attributes->class('group') }}
    @if ($name !== null && $name !== '') name="{{ $name }}" @endif
    @if ($isOpen) open @endif
>
    <summary
        class="list-none cursor-pointer rounded-lg [&::-webkit-details-marker]:hidden"
        aria-label="{{ $title }}"
    >
        <div class="flex items-start justify-between gap-4">
            <div class="mb-4 min-w-0 space-y-1">
                <flux:heading
                    size="xl"
                    level="3"
                >
                    {{ $title }}
                </flux:heading>

                @if (filled($description))
                    <flux:text class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $description }}
                    </flux:text>
                @endif
            </div>

            <div class="mt-1 inline-flex size-7 shrink-0 items-center justify-center rounded-md border border-zinc-300 text-zinc-600 dark:border-zinc-600 dark:text-zinc-300">
                <flux:icon.plus class="size-4 group-open:hidden" />
                <flux:icon.minus class="hidden size-4 group-open:block" />
            </div>
        </div>
    </summary>

    {{ $slot }}
</details>
