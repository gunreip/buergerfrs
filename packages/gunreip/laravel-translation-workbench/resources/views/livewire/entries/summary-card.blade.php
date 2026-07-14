{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/summary-card.blade.php --}}

<flux:card>
    <div class="mb-3 flex items-center justify-between gap-3">
        <flux:heading size="sm">
            {{ $title }}
        </flux:heading>

        <flux:icon
            class="size-4 text-zinc-400"
            :name="$icon"
        />
    </div>

    <flux:table>
        <flux:table.rows>
            @forelse ($rows as $row)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="max-w-56 truncate font-mono text-xs">
                            {{ $row['label'] }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                        {{ number_format($row['count']) }}
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="2">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No data.') }}
                        </span>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</flux:card>
