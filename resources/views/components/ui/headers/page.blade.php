{{-- resources/views/components/ui/headers/page.blade.php --}}

@props(['title', 'description' => null, 'headingSize' => '2xl'])

<flux:field
    {{ $attributes->class('space-y-0') }}
    space="md"
>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <div class="mb-1 flex min-w-0 flex-wrap items-baseline gap-2">
                <flux:heading
                    class="min-w-0"
                    size="{{ $headingSize }}"
                >
                    {{ $title }}
                </flux:heading>

                @if (isset($meta) && $meta->isNotEmpty())
                    <div class="shrink-0">
                        {{ $meta }}
                    </div>
                @endif
            </div>

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
