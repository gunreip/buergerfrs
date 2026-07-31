{{-- resources/views/components/ui/headers/card.blade.php --}}

@props(['title', 'description' => null])

<div {{ $attributes->class('flex items-start justify-between gap-4') }}>
    <div class="mb-4 min-w-0 space-y-1">
        <div class="flex min-w-0 flex-wrap items-baseline gap-2">
            <flux:heading
                class="min-w-0"
                size="xl"
                level="3"
            >
                {{ $title }}
            </flux:heading>

            @if (isset($meta) && $meta->isNotEmpty())
                <div class="shrink-0">
                    {{ $meta }}
                </div>
            @endif
        </div>

        @if (isset($descriptionSlot) && $descriptionSlot->isNotEmpty())
            <div class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $descriptionSlot }}
            </div>
        @elseif (filled($description))
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
