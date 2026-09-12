<div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
    <flux:table container:class="max-h-80">
        <flux:table.columns
            class="bg-white dark:bg-zinc-900"
            sticky
        >
            <flux:table.column class="w-36">{{ __('Prop') }}</flux:table.column>
            <flux:table.column class="w-36">{{ __('Default') }}</flux:table.column>
            <flux:table.column class="w-52">{{ __('Array keys') }}</flux:table.column>
            <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($flowProps as $prop)
                <flux:table.row>
                    <flux:table.cell class="align-top whitespace-normal">
                        @foreach (explode(' / ', $prop['name']) as $propName)
                            <code class="block break-words text-xs">{{ $propName }}</code>
                        @endforeach
                    </flux:table.cell>
                    <flux:table.cell class="align-top">
                        <code class="break-words text-xs">{{ $prop['default'] }}</code>
                    </flux:table.cell>
                    <flux:table.cell
                        class="min-w-0 whitespace-normal break-words text-xs leading-5 text-zinc-500 dark:text-zinc-400"
                    >
                        @if (filled($prop['keys'] ?? ''))
                            <code>{{ $prop['keys'] }}</code>
                        @else
                            <span class="text-zinc-400 dark:text-zinc-500">{{ __('-') }}</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell
                        class="min-w-0 whitespace-normal break-words text-xs leading-5 text-zinc-600 dark:text-zinc-300"
                    >
                        {{ $prop['effect'] }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
