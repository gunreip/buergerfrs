<div class="my-4 grid items-end gap-4 md:grid-cols-4">
    <flux:field>
        <flux:label>
            <flux:callout.heading>{{ __('Key ID') }}</flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                inputmode="numeric"
                wire:model.live.debounce.300ms="translationKeyId"
                :placeholder="__('Key ID')"
            />
        </flux:input.group>
    </flux:field>

    <flux:field class="md:col-span-1">
        <flux:label>
            <flux:callout.heading>{{ __('Translation key') }}</flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="translationSearch"
                :placeholder="__('Translation key')"
            />
        </flux:input.group>
    </flux:field>

    <div class="col-span-2 ml-auto flex items-end gap-2">
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:callout.text>{{ __('Per page') }}</flux:callout.text>
            </flux:input.group.prefix>
            <flux:select
                class="w-32"
                variant="listbox"
                options:class="min-w-48"
                wire:model.live="translationPerPage"
                :aria-label="__('Per page')"
            >
                @foreach ([10, 25, 50, 100] as $pageSize)
                    <flux:select.option
                        :value="$pageSize"
                        icon="list"
                    >{{ $pageSize }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>
    </div>
</div>
