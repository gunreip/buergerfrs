<div class="flex flex-wrap items-center gap-3">
    <flux:callout.heading>
        {{ __('Review source usage') }} · {{ __('Open') }}: {{ $openCount }}
    </flux:callout.heading>
    {{-- <div class="ml-auto flex items-center gap-2">
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:callout.text>
                    {{ __('Per page') }}
                </flux:callout.text>
            </flux:input.group.prefix>
            <flux:select
                class="w-32"
                size="sm"
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
    </div> --}}
</div>
<flux:callout.text>
    {{ __('Decide how each source occurrence should be handled. DatabaseValue completes this usage review; Translate queues translation work and does not mean that a translation is finished.') }}
</flux:callout.text>
