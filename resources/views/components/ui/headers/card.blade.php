{{-- resources/views/components/ui/headers/card.blade.php --}}

@props(['title', 'description' => null])

<div {{ $attributes->class('flex items-start justify-between gap-4') }}>
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

    @if ($slot->isNotEmpty())
        <div class="flex shrink-0 items-start gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
