<flux:button
    variant="outline"
    size="sm"
    wire:click="closePreservedFindings"
>{{ __('Back to scan results') }}</flux:button>
<flux:heading>{{ __('Captured source occurrences') }}</flux:heading>
<flux:text class="break-all">{{ $scanReviewPath }}</flux:text>
<flux:callout color="amber">
    {{ __('These are source occurrences recorded during this scan, not assigned Finding IDs. The source may have changed since then. Usage classification is available for confirmed dynamic calls; it does not approve a translation.') }}
</flux:callout>
<flux:error name="scanUsage" />
@if ($scanUsageNotice)
    <flux:callout color="green">{{ $scanUsageNotice }}</flux:callout>
@endif
@if ($scanDecisionReturnRun !== '')
    <flux:callout color="sky">
        {{ __('You are viewing the original captured source and its latest usage decision. The decision recorded by the selected scan remains unchanged. Back to scan results returns to that scan.') }}
    </flux:callout>
@endif
@if ($scanUsageContext)
    <flux:callout color="sky">
        <flux:callout.heading>{{ __('Classify source usage') }}</flux:callout.heading>
        <flux:textarea
            readonly
            rows="3"
            :label="__('Selected source expression')"
        >{{ $scanUsageContext['capture']['raw_expression'] }}</flux:textarea>
        <flux:callout.text>
            {{ __('DatabaseValue excludes this unchanged dynamic usage from translation capture in subsequent scans. Translate keeps it open for translation work; it does not mean translated or approved.') }}
        </flux:callout.text>
        @if ($scanUsageContext['decision'])
            <flux:text>{{ __('Last decision') }}:
                {{ $scanUsageContext['decision']['decision'] === 'database_value' ? __('DatabaseValue') : __('Translate') }}
                · #{{ $scanUsageContext['decision']['actor_id'] }} · <x-ui.date-time.date-time
                    :value="$scanUsageContext['decision']['occurred_at']"
                /></flux:text>
        @endif
        <flux:select
            wire:model.live="scanUsageChoice"
            :label="__('Usage')"
        >
            <flux:select.option value="">{{ __('Choose a classification') }}
            </flux:select.option>
            <flux:select.option value="database_value">{{ __('DatabaseValue') }}
            </flux:select.option>
            <flux:select.option value="translate">{{ __('Translate') }}</flux:select.option>
        </flux:select>
        <div class="mt-3">
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', ['reasonField' => 'scanUsageReason', 'reasonLabel' => __('Reason')])
        </div>
        <flux:text class="mt-2">
            {{ __('Choose a usage classification to see suggestions. Select an existing reason or create your own. Reasons are saved with the confirmed decision.') }}
        </flux:text>
        <flux:button
            class="mt-3"
            variant="primary"
            wire:click="saveScanUsage"
        >{{ __('Confirm usage decision') }}</flux:button>
    </flux:callout>
@endif
@forelse ($captureRows as $capture)
    <flux:callout>
        <flux:callout.heading>
            {{ __('Line :line, column :column', ['line' => $capture['source_line'], 'column' => $capture['source_column']]) }}
        </flux:callout.heading>
        <flux:textarea
            readonly
            rows="3"
            :label="__('Captured source expression')"
        >{{ $capture['raw_expression'] }}</flux:textarea>
        <dl class="mt-3 grid gap-2 sm:grid-cols-2">
            <dt>{{ __('Literal from source') }}</dt>
            <dd class="whitespace-pre-wrap break-all">{{ $capture['raw_literal'] ?? '—' }}</dd>
            <dt>{{ __('Translation key found') }}</dt>
            <dd class="break-all">{{ $capture['found_translation_key'] ?? '—' }}</dd>
            <dt>{{ __('Capture reason') }}</dt>
            <dd class="break-all"><code>{{ $capture['reason'] }}</code></dd>
        </dl>
        @if ($capture['can_classify'] ?? false)
            @if ($capture['usage_decision'] ?? null)
                <div class="mt-3 space-y-2">
                    <flux:badge
                        :color="$capture['usage_decision']['decision'] === 'database_value' ? 'sky' :
                            'amber'"
                    >
                        {{ $capture['usage_decision']['decision'] === 'database_value' ? __('DatabaseValue · decision saved') : __('Translate · work remains open') }}
                    </flux:badge>
                    <flux:text class="break-words">
                        <span class="whitespace-pre-wrap">{{ $capture['usage_decision']['reason'] }}</span>
                    </flux:text>
                    <flux:text>{{ __('User') }} #{{ $capture['usage_decision']['actor_id'] }}
                        · <x-ui.date-time.date-time :value="$capture['usage_decision']['occurred_at']" /></flux:text>
                    <flux:text>
                        {{ __('This is the latest decision for the exact captured source. It does not change this historical scan result. A subsequent scan checks whether it can be applied.') }}
                    </flux:text>
                </div>
            @else
                <flux:badge
                    class="mt-3"
                    color="amber"
                >{{ __('No usage decision saved') }}</flux:badge>
            @endif
            <flux:button
                class="mt-3"
                size="sm"
                variant="outline"
                wire:click="openScanUsage({{ $capture['capture_index'] }})"
            >{{ $capture['usage_decision'] ?? null ? __('Review usage decision') : __('Classify source usage') }}
            </flux:button>
        @endif
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('Source evidence') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text class="break-all">SHA-256: {{ $capture['source_sha256'] }}
                    </flux:text>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
@empty
    <flux:text>
        {{ __('This scan did not retain individual source captures for this file. Review the recorded reasons and preserved findings.') }}
    </flux:text>
@endforelse
<flux:pagination :paginator="$captureRows" />
