@php
    $failureSummary = \Gunreip\TranslationWorkbench\Support\TwGraph\TestFailureDetails::summary($checks);
@endphp

<flux:callout class="mt-6" :color="$failureSummary !== '' ? 'red' : 'zinc'" icon="list-x">
    <flux:callout.heading>{{ __('Fail Summary') }}</flux:callout.heading>
    <flux:callout.text>
        @if ($failureSummary === '')
            {{ __('No failed checks in this report.') }}
        @else
            <div x-data="{ copied: false, copyFailed: false }">
                <div class="mb-3 flex justify-end">
                    <flux:button
                        size="sm"
                        variant="outline"
                        icon="clipboard"
                        x-on:click="
                            copied = false; copyFailed = false;
                            try {
                                await navigator.clipboard.writeText($refs.failSummary.textContent);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            } catch (error) { copyFailed = true; }
                        "
                    >
                        {{ __('Copy fail summary') }}
                    </flux:button>
                </div>
                <span x-cloak x-show="copied" role="status">{{ __('Copied.') }}</span>
                <span x-cloak x-show="copyFailed" role="alert">{{ __('Copy failed. Please select and copy the list manually.') }}</span>
                <pre x-ref="failSummary" class="max-h-96 overflow-auto whitespace-pre-wrap break-words font-mono text-xs text-red-700 dark:text-red-300" data-tw-graph-fail-summary>{{ $failureSummary }}</pre>
            </div>
        @endif
    </flux:callout.text>
</flux:callout>
