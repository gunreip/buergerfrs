{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-summary.blade.php --}}

<flux:card class="mt-6">
        <x-ui.headers.card
            :title="__('ui.summary')"
            :description="__('Raw aggregate overview for the active database table.')"
        />

        <div class="mt-4 grid gap-4 xl:grid-cols-4">
            <div>
                <flux:heading
                    class="mb-2"
                    size="sm"
                >
                    {{ __('Ranges') }}
                </flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.table_card.column') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Min') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Max') }}</flux:table.column>
                        <flux:table.column align="end">∑</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($summary['ranges'] ?? [] as $range)
                            <flux:table.row>
                                <flux:table.cell class="font-mono text-xs">{{ $range['column'] }}</flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ $range['min'] ?? 'NULL' }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ $range['max'] ?? 'NULL' }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ number_format($range['count']) }}
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4">{{ __('No range columns.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div>
                <flux:heading
                    class="mb-2"
                    size="sm"
                >
                    {{ __('Distinct') }}
                </flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.table_card.column') }}</flux:table.column>
                        <flux:table.column align="end">∑</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($summary['distincts'] ?? [] as $distinct)
                            <flux:table.row>
                                <flux:table.cell class="font-mono text-xs">{{ $distinct['column'] }}</flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ number_format($distinct['count']) }}
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="2">{{ __('No distinct columns.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div>
                <flux:heading
                    class="mb-2"
                    size="sm"
                >
                    {{ __('Distributions') }}
                </flux:heading>

                <div class="space-y-3">
                    @forelse ($summary['distributions'] ?? [] as $distribution)
                        <flux:card>
                            <div class="mb-2 font-mono text-xs font-semibold">
                                {{ $distribution['column'] }}
                            </div>

                            <flux:table class="table-fixed">
                                <flux:table.rows>
                                    @foreach ($distribution['values'] as $value)
                                        <flux:table.row>
                                            <flux:table.cell>
                                                <div class="w-24 truncate font-mono text-xs">
                                                    {{ $value['value'] }}
                                                </div>
                                            </flux:table.cell>
                                            <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                                {{ number_format($value['count']) }}
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        </flux:card>
                    @empty
                        <flux:text>{{ __('No distribution columns.') }}</flux:text>
                    @endforelse
                </div>
            </div>

            <div>
                <flux:heading
                    class="mb-2"
                    size="sm"
                >
                    {{ __('Null / Filled') }}
                </flux:heading>

                <flux:table container:class="max-h-96 overflow-auto">
                    <flux:table.columns sticky>
                        <flux:table.column>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.table_card.column') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Filled') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('ui.null') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Empty') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($summary['nulls'] ?? [] as $nullSummary)
                            <flux:table.row>
                                <flux:table.cell class="font-mono text-xs">{{ $nullSummary['column'] }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs">
                                    {{ number_format($nullSummary['filled']) }}
                                </flux:table.cell>
                                <flux:table.cell @class([
                                    'font-mono text-xs text-right',
                                    'font-semibold text-amber-700 dark:text-amber-300' =>
                                        ($nullSummary['null'] ?? 0) > 0,
                                ])>
                                    {{ number_format($nullSummary['null']) }}
                                </flux:table.cell>
                                <flux:table.cell @class([
                                    'font-mono text-xs tabular-nums text-right',
                                    'font-semibold text-amber-700 dark:text-amber-300' =>
                                        ($nullSummary['empty'] ?? 0) > 0,
                                ])>
                                    {{ number_format($nullSummary['empty']) }}
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4">{{ __('No null summary columns.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>

        @if (($summary['key_namespaces'] ?? []) !== [])
            <div class="mt-6">
                <flux:heading
                    class="mb-2"
                    size="sm"
                >
                    {{ __('Key namespaces') }}
                </flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Namespace') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('translation_key') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('existing_key') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('suggested_key') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('ui.total') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($summary['key_namespaces'] as $namespaceSummary)
                            <flux:table.row>
                                <flux:table.cell class="font-mono text-xs font-semibold">
                                    {{ $namespaceSummary['namespace'] }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ number_format($namespaceSummary['translation_key']) }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ number_format($namespaceSummary['existing_key']) }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                    {{ number_format($namespaceSummary['suggested_key']) }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right font-mono text-xs font-semibold tabular-nums">
                                    {{ number_format($namespaceSummary['total']) }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif

        @if (($summary['duplicate_diagnostics'] ?? []) !== [])
            <div class="mt-6">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <flux:heading size="sm">
                        {{ __('Duplicate diagnostics') }}
                    </flux:heading>

                    <flux:badge
                        class="tabular-nums"
                        color="amber"
                    >
                        {{ __('Candidates') }} {{ number_format($summary['duplicate_diagnostics']['total'] ?? 0) }}
                    </flux:badge>
                </div>

                <div class="grid gap-4 xl:grid-cols-3">
                    <flux:card>
                        <div class="mb-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            {{ __('By type') }}
                        </div>

                        <flux:table>
                            <flux:table.rows>
                                @foreach ($summary['duplicate_diagnostics']['by_type'] ?? [] as $duplicateType)
                                    <flux:table.row>
                                        <flux:table.cell class="font-mono text-xs">
                                            {{ $duplicateType['label'] }}
                                        </flux:table.cell>
                                        <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                            {{ number_format($duplicateType['count']) }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </flux:card>

                    <flux:card>
                        <div class="mb-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            {{ __('By confidence') }}
                        </div>

                        <flux:table>
                            <flux:table.rows>
                                @foreach ($summary['duplicate_diagnostics']['by_confidence'] ?? [] as $confidence)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                :color="$confidence['label'] === 'high' ? 'red' : 'amber'"
                                            >
                                                {{ str($confidence['label'])->headline() }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                            {{ number_format($confidence['count']) }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </flux:card>

                    <flux:card>
                        <div class="mb-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            {{ __('Largest groups') }}
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('ui.type') }}</flux:table.column>
                                <flux:table.column align="end">{{ __('Size') }}</flux:table.column>
                                <flux:table.column align="end">{{ __('Rows') }}</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach ($summary['duplicate_diagnostics']['groups'] ?? [] as $group)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <x-ui.tooltip.simple
                                                class="max-w-36 truncate font-mono text-xs"
                                                :title="__('Duplicate group fingerprint')"
                                                :text="$group['group_fingerprint']"
                                            >
                                                {{ $group['duplicate_type'] }}
                                            </x-ui.tooltip.simple>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ str($group['confidence'])->headline() }}
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                            {{ number_format($group['group_size']) }}
                                        </flux:table.cell>
                                        <flux:table.cell class="text-right font-mono text-xs tabular-nums">
                                            {{ number_format($group['candidate_rows']) }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </flux:card>
                </div>
            </div>
        @endif
</flux:card>
