<flux:error name="sourceRun" />

@if ($sourceRuns)
    <flux:callout color="sky">
        <flux:callout.heading>{{ __('Source replacement runs') }}</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('Open or resume source replacement') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="max-h-64 space-y-2 overflow-auto">
                        @foreach ($sourceRuns as $run)
                            <div
                                class="flex items-center justify-between gap-3 text-sm"
                                wire:key="source-run-{{ $run['id'] }}"
                            >
                                <span class="break-all">{{ $run['path'] }} · {{ __($run['state']) }} ·
                                    #{{ $run['actor'] }}</span>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="openSourceRun('{{ $run['id'] }}')"
                                >{{ __('Open') }}</flux:button>
                            </div>
                        @endforeach
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
@endif
