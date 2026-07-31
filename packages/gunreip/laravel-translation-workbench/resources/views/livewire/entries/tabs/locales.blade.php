{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/locales.blade.php --}}

<flux:table>
    <flux:table.columns>
        <flux:table.column>{{ __('Locale') }}</flux:table.column>
        <flux:table.column>{{ __('Role') }}</flux:table.column>
        <flux:table.column>{{ __('Main') }}</flux:table.column>
        <flux:table.column>{{ __('Parent') }}</flux:table.column>
        <flux:table.column align="end">{{ __('ui.values.values') }}</flux:table.column>
        <flux:table.column align="end">{{ __('Matched') }}</flux:table.column>
        <flux:table.column align="end">{{ __('ui.missing') }}</flux:table.column>
        <flux:table.column align="end">{{ __('Extra') }}</flux:table.column>
        <flux:table.column align="end">{{ __('Coverage') }}</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @forelse ($localeCoverageRows as $row)
            <flux:table.row>
                <flux:table.cell>
                    <div class="flex items-center gap-2">
                        <x-ui.locale.flag :locale="$row['locale']" />
                        <span class="font-mono text-xs">{{ $row['locale'] }}</span>

                        @if ($row['locale'] === $sourceMainLocale)
                            <flux:badge
                                size="sm"
                                color="sky"
                            >
                                {{ __('ui.source') }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:badge
                        size="sm"
                        color="{{ $row['locale_role'] === 'source_main' ? 'sky' : ($row['locale_role'] === 'target_main' ? 'emerald' : 'zinc') }}"
                    >
                        {{ $row['locale_role'] }}
                    </flux:badge>
                </flux:table.cell>
                <flux:table.cell class="font-mono text-xs">
                    {{ $row['main_locale'] ?? '-' }}
                </flux:table.cell>
                <flux:table.cell class="font-mono text-xs">
                    {{ $row['parent_locale'] ?? '-' }}
                </flux:table.cell>
                <flux:table.cell
                    class="tabular-nums"
                    align="end"
                >
                    {{ number_format($row['values']) }}
                </flux:table.cell>
                <flux:table.cell
                    class="tabular-nums"
                    align="end"
                >
                    {{ number_format($row['matched']) }}
                </flux:table.cell>
                <flux:table.cell
                    class="tabular-nums"
                    align="end"
                >
                    {{ number_format($row['missing']) }}
                </flux:table.cell>
                <flux:table.cell
                    class="tabular-nums"
                    align="end"
                >
                    {{ number_format($row['extra']) }}
                </flux:table.cell>
                <flux:table.cell align="end">
                    <flux:badge
                        size="sm"
                        color="{{ $row['color'] }}"
                    >
                        {{ number_format($row['coverage'], 1) }}%
                    </flux:badge>
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="9">
                    <flux:text class="text-sm text-zinc-500">
                        {{ __('No locale coverage data available.') }}
                    </flux:text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
