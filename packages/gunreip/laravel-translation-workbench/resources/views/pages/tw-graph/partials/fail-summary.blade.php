@php
    $failureSummary = \Gunreip\TranslationWorkbench\Support\TwGraph\TestFailureDetails::summary($checks);
@endphp

<flux:callout
    class="mt-6"
    :color="$failureSummary !== '' ? 'red' : 'zinc'"
    icon="list-x"
>
    <flux:callout.heading>
        {{ __('Fail Summary') }}
    </flux:callout.heading>
    <flux:callout.text>
        @if ($failureSummary === '')
            {{ __('No failed checks in this report.') }}
        @else
            <div
                class="grid grid-cols-[minmax(0,1fr)_auto] items-start gap-x-4 gap-y-2"
                x-data="{
                    async copySummary() {
                        try {
                            await navigator.clipboard.writeText(this.$refs.failSummary.textContent);
                        } catch (error) {
                            this.$flux.toast(@js(__('translation-workbench.tw-graph-diagnostics.summary.fail-summary-failed.please-copy-the-list-manually')), { variant: 'danger', duration: 8000 });
                            return;
                        }
                        this.$flux.toast(@js(__('Fail summary copied.')), { variant: 'success', duration: 3000 });
                    }
                }"
            >
                <div class="col-start-2 row-start-1 flex justify-end">
                    <flux:button.group>
                        <flux:button
                            size="sm"
                            variant="outline"
                            x-on:click="copySummary()"
                        >
                            <flux:icon.circle-x class="size-5" />
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="outline"
                            x-on:click="copySummary()"
                        >
                            {{ __('Copy fail summary') }}
                        </flux:button>
                    </flux:button.group>
                </div>
                <pre
                    class="wrap-break-word col-start-1 row-start-1 max-h-96 min-w-0 overflow-auto whitespace-pre-wrap font-mono text-xs text-red-700 dark:text-red-300"
                    data-tw-graph-fail-summary
                    x-ref="failSummary"
                >{{ $failureSummary }}</pre>
            </div>
        @endif
    </flux:callout.text>
</flux:callout>
