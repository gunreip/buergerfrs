<div class="mt-3 min-w-0 space-y-3">
    <flux:heading size="sm">{{ __('Failure details') }}</flux:heading>
    @foreach (\Gunreip\TranslationWorkbench\Support\TwGraph\TestFailureDetails::fromCheck($check) as $failure)
        <flux:callout color="red" icon="exclamation-circle" class="min-w-0 [&>.flex-1]:min-w-0">
            <flux:callout.heading class="min-w-0"><span class="min-w-0 whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $failure['message'] }}</span></flux:callout.heading>
            <flux:callout.text>
                @if ($failure['test'])
                    <p class="break-words [overflow-wrap:anywhere]"><strong>{{ __('Test') }}:</strong> {{ $failure['test'] }}</p>
                @endif
                @if ($failure['location'] !== '')
                    <p class="break-words [overflow-wrap:anywhere]"><strong>{{ __('File / line') }}:</strong> <code>{{ $failure['location'] }}</code></p>
                @endif
                @foreach (['expected' => __('Expected'), 'actual' => __('Actual')] as $key => $label)
                    @if ($failure[$key] !== null)
                        <p class="mt-2"><strong>{{ $label }}:</strong></p>
                        <pre class="max-h-48 overflow-auto whitespace-pre-wrap break-words [overflow-wrap:anywhere] text-xs">{{ $failure[$key] }}</pre>
                    @endif
                @endforeach
                @if ($failure['details'] !== '')
                    <flux:accordion class="mt-2">
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('Technical details / stack trace') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <pre class="max-h-96 max-w-full overflow-auto whitespace-pre-wrap break-words [overflow-wrap:anywhere] rounded-md bg-zinc-950 p-3 text-xs text-zinc-100">{{ $failure['details'] }}</pre>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                @endif
            </flux:callout.text>
        </flux:callout>
    @endforeach
</div>
