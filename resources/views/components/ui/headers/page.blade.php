{{-- resources/views/components/ui/headers/page.blade.php --}}

<flux:field
    {{ $attributes->class('space-y-0') }}
    space="md"
>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <flux:heading
                class="mb-1"
                size="{{ $headingSize }}"
            >
                {{ $title }}
            </flux:heading>

            @if ($description !== null && $description !== '')
                <flux:text>
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
</flux:field>
