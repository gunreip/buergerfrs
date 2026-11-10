<div @if ($scanRunning) wire:poll.500ms="refreshProgress" @endif>
    <flux:callout
        transition
        color="sky"
        icon="arrow-path"
        :heading="!$scanRunning ? __('Scan steps') : (in_array($scanState, ['queued_apply', 'applying'],
                true) ?
            __('Checking and saving scan results …') : __('Preparing scan preview …'))"
    >
        <div class="my-3 space-y-3">
            @forelse ($scanWorkflow['progress']['steps'] ?? [] as $stepIndex => $step)
                @php
                    $stepLabel = match ($step['phase']) {
                        'scanning' => __('Reading source files'),
                        'comparing' => __('Comparing findings'),
                        'preview_complete' => __('Finalizing scan preview'),
                        'saving' => __('Processing findings for saving'),
                        'final_check' => __('Checking source files before completion'),
                        default => __('Scan progress'),
                    };
                    $stepColor = match ($step['status']) {
                        'completed' => 'green',
                        'failed' => 'red',
                        default => 'sky',
                    };
                @endphp
                <div wire:key="scan-step-{{ $scanWorkflowId }}-{{ $stepIndex }}">
                    <div class="flex items-center justify-between gap-3">
                        <flux:callout.text>
                            {{ $stepIndex + 1 }}. {{ $stepLabel }}
                        </flux:callout.text>
                        <flux:callout.text class="shrink-0 tabular-nums">
                            {{ match ($step['status']) {
                                'completed' => __('Completed'),
                                'failed' => __('Failed'),
                                default => __('In progress'),
                            } }}
                            · {{ $step['percent'] }}%
                        </flux:callout.text>
                    </div>
                    <flux:progress
                        class="mt-1 h-2"
                        style="--flux-progress: {{ $step['percent'] }}; --flux-progress-percentage: {{ $step['percent'] }}%"
                        :color="$stepColor"
                        :value="$step['percent']"
                        wire:model="scanWorkflow.progress.steps.{{ $stepIndex }}.percent"
                        :aria-label="$stepLabel"
                    />
                </div>
            @empty
                <flux:callout.text class="flex items-start">
                    {{ __('Waiting for the scanner') }}
                    <flux:icon.loading class="ml-2" />
                </flux:callout.text>
            @endforelse
        </div>
        @if ($scanRunning)
            <flux:callout.text>
                {{ __('The scan runs in the background. You can close this modal and reopen Run scan to see its progress.') }}
            </flux:callout.text>
        @endif
    </flux:callout>
</div>
