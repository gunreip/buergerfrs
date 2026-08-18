{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/pipeline-run-progress.blade.php --}}

@php
    $status = $pipelineRunStatus ?? null;
@endphp

{{--
TODO: Add a "Run complete" button to start the complete Translation Workbench pipeline in a background process. This will run the following command in the background: php artisan translation:workbench --complete. The current pipeline step will be tracked in the database and displayed in the "Last run" tab.
Unfortunately, this keeps causing a "Failed to open stream: Permission denied" error. --}}

@if ($status)
    @if ($status['is_active'])
        <div wire:poll.2s="refreshPipelineRunStatus">
        @else
            <div>
    @endif

    <flux:callout
        class="mt-4"
        color="{{ $status['color'] }}"
        icon="{{ $status['is_active'] ? 'loader-circle' : ($status['status'] === 'failed' ? 'octagon-alert' : 'circle-check') }}"
    >
        <flux:callout.heading>
            <span class="inline-flex flex-wrap items-center gap-2">
                <span>{{ __('Translation Workbench pipeline') }}</span>
                <flux:badge
                    size="sm"
                    color="{{ $status['color'] }}"
                >
                    {{ $status['status_label'] }}
                </flux:badge>
                <flux:badge
                    size="sm"
                    color="zinc"
                >
                    #{{ $status['id'] }}
                </flux:badge>
            </span>
        </flux:callout.heading>

        <flux:callout.text>
            <div class="space-y-3">
                <div class="grid grid-cols-1 gap-2 lg:grid-cols-10 lg:items-center">
                    <div class="space-y-1">
                        <flux:field class="col-span-5 grid-cols-5 items-center lg:col-span-5">
                            <flux:callout.heading
                                class="text-xs"
                                icon="loader"
                            >
                                {{ __('Progress') }}
                            </flux:callout.heading>
                            <flux:callout.text class="col-span-4">
                                {{ __('Step') }}
                                <span class="text-xs tabular-nums">
                                    {{ number_format((int) $status['current_step']) }}
                                </span>
                                /
                                <span class="text-xs tabular-nums">
                                    {{ number_format((int) $status['total_steps']) }}
                                </span>

                                @if ($status['current_step_label'])
                                    <span class="text-xs font-medium">
                                        {{ $status['current_step_label'] }}
                                    </span>
                                @endif
                            </flux:callout.text>
                        </flux:field>

                        @if ($status['current_step_command'])
                            <flux:field class="col-span-5 grid-cols-5 items-center lg:col-span-5">
                                <flux:callout.heading icon="terminal">
                                    {{ __('Command') }}
                                </flux:callout.heading>
                                <flux:callout.text class="col-span-4">
                                    {{ $status['current_step_command'] }}
                                </flux:callout.text>
                            </flux:field>
                        @endif
                        {{-- </div> --}}

                        {{-- <div class="flex flex-wrap items-center gap-3"> --}}
                        @if ($status['started_at'])
                            <flux:field class="col-span-5 grid-cols-5 items-center lg:col-span-5">
                                <flux:callout.heading icon="alarm-clock">
                                    {{ __('Started') }}
                                </flux:callout.heading>
                                <flux:callout.text class="col-span-4">
                                    <x-ui.date-time.date
                                        class="text-inherit"
                                        :value="$status['started_at']"
                                    />
                                    <x-ui.date-time.time
                                        class="text-inherit"
                                        :value="$status['started_at']"
                                    />
                                </flux:callout.text>
                            </flux:field>
                        @endif

                        @if ($status['finished_at'])
                            <flux:field class="col-span-5 grid-cols-5 items-center lg:col-span-5">
                                <flux:callout.heading icon="alarm-clock-check">
                                    {{ __('Finished') }}
                                </flux:callout.heading>
                                <flux:callout.text class="col-span-3">
                                    <x-ui.date-time.date
                                        class="text-inherit"
                                        :value="$status['finished_at']"
                                    />
                                    <x-ui.date-time.time
                                        class="text-inherit"
                                        :value="$status['finished_at']"
                                    />
                                </flux:callout.text>
                            </flux:field>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <flux:progress
                        class="h-2"
                        value="{{ $status['progress'] }}"
                        color="{{ $status['color'] }}"
                    />
                    <span class="w-12 text-right text-xs tabular-nums text-zinc-500 dark:text-zinc-400">
                        {{ $status['progress'] }}%
                    </span>
                </div>

                @if ($status['error_message'])
                    <div class="wrap-anywhere text-sm text-red-600 dark:text-red-300">
                        {{ $status['error_message'] }}
                    </div>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>
    </div>
@endif
