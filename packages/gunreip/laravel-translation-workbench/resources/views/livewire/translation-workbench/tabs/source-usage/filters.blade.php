<div class="my-4 grid gap-4 md:grid-cols-4">

    {{-- Search Field Search source or expression --}}
    <flux:field class="col-span-2">
        <flux:label>
            <flux:callout.heading>
                {{ __('Search source or expression') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search class="size-4" />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search source or expression') }}"
            />
        </flux:input.group>
    </flux:field>
    {{-- Select Field Review status --}}
    <flux:field>
        <flux:label>
            <flux:callout.heading>
                {{ __('Review status') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.list-check class="size-4" />
            </flux:input.group.prefix>
            <flux:select
                clearable
                variant="listbox"
                wire:model.live="filter"
            >
                <flux:select.option
                    value="open"
                    selected
                    icon="scale"
                    label="{{ __('Open decisions') }}"
                />
                <flux:select.option
                    value="all"
                    icon="gavel"
                    label="{{ __('All cases including decisions') }}"
                />
            </flux:select>
        </flux:input.group>
    </flux:field>

    <div class="ml-auto flex items-end gap-2">
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:callout.text>
                    {{ __('Per page') }}
                </flux:callout.text>
            </flux:input.group.prefix>
            <flux:select
                class="w-32"
                {{-- size="sm" --}}
                variant="listbox"
                options:class="min-w-48"
                wire:model.live="perPage"
                :aria-label="__('Per page')"
            >
                @foreach ([10, 25, 50, 100] as $pageSize)
                    <flux:select.option
                        :value="$pageSize"
                        icon="list"
                    >
                        {{ $pageSize }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>
    </div>
</div>
