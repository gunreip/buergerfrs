{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/scanner-runs.blade.php --}}

@php
    $scannerReportTimezone = config('buergerfrs_formats.date_time.timezone', config('app.timezone'));
@endphp

<div class="space-y-6">
    <flux:field class="grid gap-4 md:grid-cols-2 xl:grid-cols-8">
        {{-- Scanner run callout props are prepared in TranslationWorkbenchEntries::scannerRunCallouts(). --}}
        {{-- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php --}}
        @foreach ($scannerRunCallouts as $callout)
            <flux:callout
                class="col-span-2 h-full"
                color="{{ $callout['color'] }}"
                icon="{{ $callout['icon'] }}"
                heading="{{ $callout['title'] }}"
                text="{{ $callout['text'] }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($callout['count']) }}
                </flux:callout.text>
            </flux:callout>
        @endforeach
    </flux:field>

    {{-- Table of Scanner Runs --}}
    <flux:table>
        {{-- Table of Scanner Runs Header Row --}}
        <flux:table.columns>
            {{-- Table of Scanner Runs Command --}}
            <flux:table.column>{{ __('Command') }}</flux:table.column>
            {{-- Table of Scanner Runs Generated At --}}
            <flux:table.column class="min-w-56">
                <div class="flex w-full items-center justify-between gap-2">
                    <span>{{ __('Generated at') }}</span>
                    <flux:icon.clock-arrow-down
                        class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500"
                        variant="mini"
                    />
                </div>
            </flux:table.column>
            {{-- Table of Scanner Runs Files --}}
            <flux:table.column align="end">{{ __('Files') }}</flux:table.column>
            {{-- Table of Scanner Runs Found --}}
            <flux:table.column align="end">{{ __('Found') }}</flux:table.column>
            {{-- Table of Scanner Runs Files Found --}}
            <flux:table.column align="end">{{ __('Files found') }}</flux:table.column>
            {{-- Table of Scanner Runs Paths --}}
            <flux:table.column align="end">{{ __('Paths') }}</flux:table.column>
            {{-- Table of Scanner Runs Patterns --}}
            <flux:table.column align="end">{{ __('Patterns') }}</flux:table.column>
            {{-- Table of Scanner Runs Size --}}
            <flux:table.column align="end">{{ __('Size') }}</flux:table.column>
        </flux:table.columns>

        {{-- Table of Scanner Runs Body --}}
        <flux:table.rows>
            @forelse ($scannerReportRows as $row)
                {{-- Table of Scanner Runs Row --}}
                <flux:table.row>
                    {{-- Table of Scanner Runs Cell Command and File --}}
                    <flux:table.cell>
                        <div class="space-y-1">
                            <div class="font-mono text-xs">{{ $row['command'] }}</div>
                            <div class="font-mono text-xs text-zinc-500">{{ $row['file'] }}</div>
                        </div>
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Generated At --}}
                    <flux:table.cell>
                        <div
                            class="flex flex-wrap items-center gap-2"
                            title="{{ $row['generated_at'] ?? ($row['modified_at'] ?? '-') }}"
                        >
                            <x-ui.date-time.date-time
                                class="font-mono"
                                format="ddd, DD.MMM.YYYY, HH:mm:ss.SSSSSS"
                                :value="$row['generated_at'] ?? ($row['modified_at'] ?? null)"
                                :timezone="$scannerReportTimezone"
                                size="xs"
                                color="default"
                            />

                            <flux:badge
                                class="font-mono"
                                size="sm"
                                variant="subtle"
                            >
                                {{ $scannerReportTimezone }}
                            </flux:badge>
                        </div>
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Files --}}
                    <flux:table.cell
                        class="tabular-nums"
                        align="end"
                    >
                        {{ number_format($row['files']) }}
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Found --}}
                    <flux:table.cell
                        class="tabular-nums"
                        align="end"
                    >
                        {{ number_format($row['found']) }}
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Files Found --}}
                    <flux:table.cell
                        class="tabular-nums"
                        align="end"
                    >
                        {{ number_format($row['files_found']) }}
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Paths --}}
                    <flux:table.cell
                        class="tabular-nums"
                        align="end"
                    >
                        {{ number_format($row['scanned_paths']) }}
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Patterns --}}
                    <flux:table.cell
                        class="tabular-nums"
                        align="end"
                    >
                        {{ number_format($row['file_patterns']) }}
                    </flux:table.cell>
                    {{-- Table of Scanner Runs Cell Size --}}
                    <flux:table.cell
                        class="tabular-nums"
                        align="end"
                    >
                        {{ number_format($row['size_kb']) }} KB
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Of Scanner Runs Empty State Row --}}
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('No scanner reports available.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
