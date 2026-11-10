<div class="flex flex-wrap items-end gap-4">
    {{-- Finding ID, Key ID, Translation Key Or Source Path --}}
    <flux:field class="min-w-0 flex-1">
        <flux:label>
            <flux:callout.heading>
                {{ __('Finding ID, key ID, translation key or source path') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.eye size="5" />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="sourceReplacementSearch"
            />
        </flux:input.group>
    </flux:field>

    <div class="ml-auto flex items-end gap-2">
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:callout.text>{{ __('Per page') }}</flux:callout.text>
            </flux:input.group.prefix>
            <flux:select
                class="w-32"
                aria-labe="__('Per Page')"
                variant="listbox"
                options:class="min-w-48"
                wire:model.live="sourceReplacementPerPage"
            >
                @foreach ([10, 25, 50, 100] as $size)
                    <flux:select.option
                        :value="$size"
                        icon="list"
                    >
                        {{ $size }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>
    </div>
</div>
