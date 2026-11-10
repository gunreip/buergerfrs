<div class="flex-full my-4 flex items-end gap-3">
    {{-- Finding ID, Key ID, Translation Key Or Source Path --}}
    <flux:field class="flex-3 min-w-0">
        <flux:label>
            <flux:callout.heading>
                {{ __('Finding ID, key ID, translation key or source') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.magnifying-glass class="size-5" />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="workSearch"
            />
        </flux:input.group>
    </flux:field>

    <flux:field class="flex-2 min-w-0">
        <flux:label>
            <flux:callout.heading>
                {{ __('Edited by') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.user class="size-5" />
            </flux:input.group.prefix>
            <flux:select
                wire:model.live="workOnlyMine"
                variant="listbox"
            >
                @php
                    $currentWorkActor = $workActors[(int) auth()->id()] ?? null;
                    $currentWorkActorName = $currentWorkActor['name'] ?? '' ?: (auth()->user()?->name ?: __('Me'));
                @endphp
                <flux:select.option value="1">
                    <div class="flex min-w-0 items-center gap-2">
                        <flux:avatar
                            class="shrink-0"
                            size="xs"
                            circle
                            :src="$currentWorkActor['avatar_url'] ?? null"
                            :name="$currentWorkActorName"
                        />
                        <span class="truncate">{{ $currentWorkActorName }}</span>
                    </div>
                </flux:select.option>
                <flux:select.option value="0">{{ __('All users') }}</flux:select.option>
            </flux:select>
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
                wire:model.live="workPerPage"
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
