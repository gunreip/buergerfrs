<div class="mt-3 min-w-0 space-y-3">
    <flux:heading size="sm">{{ __('Failure details') }}</flux:heading>
    @foreach (\Gunreip\TranslationWorkbench\Support\TwGraph\TestFailureDetails::fromCheck($check) as $failure)
        <flux:callout
            class="min-w-0 [&>.flex-1]:min-w-0"
            color="red"
            icon="exclamation-circle"
        >
            <flux:callout.heading class="min-w-0"><span
                    class="wrap-anywhere min-w-0 whitespace-pre-wrap">{{ $failure['message'] }}</span>
            </flux:callout.heading>
            <flux:callout.text>
                @if ($failure['test'])
                    <p class="wrap-break-word"><strong>{{ __('Test') }}:</strong> {{ $failure['test'] }}</p>
                @endif
                @if ($failure['location'] !== '')
                    <p class="wrap-break-word"><strong>{{ __('File / line') }}:</strong>
                        <code>{{ $failure['location'] }}</code>
                    </p>
                @endif
                @foreach (['expected' => __('Expected'), 'actual' => __('Actual')] as $key => $label)
                    @if ($failure[$key] !== null)
                        <p class="mt-2"><strong>{{ $label }}:</strong></p>
                        <pre class="wrap-break-word max-h-48 overflow-auto whitespace-pre-wrap text-xs">{{ $failure[$key] }}</pre>
                    @endif
                @endforeach
                @if ($failure['details'] !== '')
                    <flux:accordion class="mt-2">
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('Technical details / stack trace') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <x-translation-workbench::ui.common.code-preview
                                    :content="$failure['details']"
                                    :copy-context="__('Technical details / stack trace')"
                                />
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                @endif
            </flux:callout.text>
        </flux:callout>
    @endforeach
</div>
