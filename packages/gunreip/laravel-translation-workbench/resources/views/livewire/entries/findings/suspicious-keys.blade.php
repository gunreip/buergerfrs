{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/suspicious-keys.blade.php --}}

@php
    $report = $suspiciousKeyedAdditionsReport ?? null;
    $summary = is_array($report['summary'] ?? null) ? $report['summary'] : [];
    $results = collect($report['results'] ?? []);
    $suspicious = (int) ($summary['suspicious'] ?? 0);
@endphp

<div class="mt-4 space-y-4">
    @if (!$report)
        {{-- Callout No Suspicious-Key Report Found --}}
        <flux:callout
            color="amber"
            icon="file-warning"
        >
            <flux:callout.heading>{{ __('No suspicious-key report found') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Run php artisan translation-workbench:detect-suspicious-keyed-additions to generate the report before reviewing direct keyed translation calls here.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        {{-- Callout Suspicious-Keyed Additions Summary --}}
        <flux:callout
            color="{{ $suspicious > 0 ? 'red' : 'green' }}"
            icon="{{ $suspicious > 0 ? 'octagon-alert' : 'circle-check' }}"
        >
            <flux:callout.heading>
                <span class="inline-flex flex-wrap items-center gap-2">
                    <span>{{ __('Suspicious keyed additions') }}</span>
                    <flux:badge
                        size="sm"
                        color="{{ $suspicious > 0 ? 'red' : 'green' }}"
                    >
                        {{ $suspicious > 0 ? __('Needs review') : __('Clear') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>
            <flux:callout.text>
                <span>
                    {{ __('Detects translation-key calls that already exist in code but do not have reviewed Workbench or code-update provenance.') }}
                </span>
                @if ($report['generated_at'])
                    <span class="ml-1">
                        {{ __('Report') }}:
                        <x-ui.date-time.date
                            color="inherit"
                            :value="$report['generated_at']"
                        />
                        <x-ui.date-time.time
                            color="inherit"
                            :value="$report['generated_at']"
                        />
                    </span>
                @endif
            </flux:callout.text>
        </flux:callout>

        <div class="grid grid-cols-1 gap-3 xl:grid-cols-4">
            @foreach ([['title' => __('Keyed findings scanned'), 'count' => $summary['keyed_findings_scanned'] ?? 0, 'color' => 'sky', 'icon' => 'scan-search'], ['title' => __('Open suspicious'), 'count' => $suspicious, 'color' => $suspicious > 0 ? 'red' : 'green', 'icon' => $suspicious > 0 ? 'octagon-alert' : 'circle-check'], ['title' => __('Reviewed'), 'count' => $summary['reviewed'] ?? 0, 'color' => ($summary['reviewed'] ?? 0) > 0 ? 'green' : 'zinc', 'icon' => 'clipboard-check'], ['title' => __('Since'), 'count' => $summary['since_at'] ?? __('All'), 'color' => 'zinc', 'icon' => 'calendar-clock']] as $callout)
                {{-- Callout Summary Item --}}
                <flux:callout
                    color="{{ $callout['color'] }}"
                    icon="{{ $callout['icon'] }}"
                >
                    <flux:callout.heading>{{ $callout['title'] }}</flux:callout.heading>
                    <flux:callout.text>
                        @if ($callout['title'] === __('Since') && !empty($summary['since_at']))
                            <span class="whitespace-nowrap">
                                <x-ui.date-time.date :value="$summary['since_at']" />
                                <x-ui.date-time.time :value="$summary['since_at']" />
                            </span>
                        @else
                            <span class="text-lg font-semibold tabular-nums">
                                {{ is_numeric($callout['count']) ? number_format((int) $callout['count']) : $callout['count'] }}
                            </span>
                        @endif
                    </flux:callout.text>
                </flux:callout>
            @endforeach
        </div>

        @if ($summary['recent_only'] ?? false)
            {{-- Callout Detection Window --}}
            <flux:callout
                color="sky"
                icon="clock"
            >
                <flux:callout.heading>{{ __('Detection window') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="grid grid-cols-1 gap-2 text-sm lg:grid-cols-4">
                        @foreach ([['label' => __('Effective since'), 'value' => $summary['since_at'] ?? null], ['label' => __('Default since'), 'value' => $summary['default_since_at'] ?? null], ['label' => __('Previous detector report'), 'value' => $summary['previous_detector_report_generated_at'] ?? null], ['label' => __('Pipeline report'), 'value' => $summary['pipeline_report_generated_at'] ?? null]] as $dateRow)
                            <div class="space-y-1">
                                <flux:callout.heading class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    {{ $dateRow['label'] }}
                                </flux:callout.heading>
                                @if ($dateRow['value'])
                                    <flux:callout.text class="whitespace-nowrap">
                                        <x-ui.date-time.date
                                            color="inherit"
                                            :value="$dateRow['value']"
                                        />
                                        <x-ui.date-time.time
                                            color="inherit"
                                            :value="$dateRow['value']"
                                        />
                                    </flux:callout.text>
                                @else
                                    <span class="text-sky-500">{{ __('NULL') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </flux:callout.text>
            </flux:callout>
        @endif

        {{-- Callout Review List --}}
        <flux:callout
            color="{{ $suspicious > 0 ? 'red' : 'zinc' }}"
            icon="list-checks"
        >
            <flux:callout.heading>{{ __('Review list') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Rows shown here should be checked before committing because they may indicate direct translation-key edits that bypassed the Workbench review flow.') }}
            </flux:callout.text>

            <div class="mt-3 overflow-x-auto rounded-md">
                <flux:table class="min-w-[96rem] table-fixed">
                    <flux:table.columns sticky>
                        <flux:table.column class="w-12 text-center">#</flux:table.column>
                        <flux:table.column class="w-36">{{ __('ui.state.state') }}</flux:table.column>
                        <flux:table.column class="w-44">{{ __('Decision') }}</flux:table.column>
                        <flux:table.column class="w-[24rem]">{{ __('Source') }}</flux:table.column>
                        <flux:table.column class="w-[18rem]">{{ __('Translation key') }}</flux:table.column>
                        <flux:table.column class="w-[18rem]">{{ __('Suggested key') }}</flux:table.column>
                        <flux:table.column class="w-[18rem]">{{ __('Context') }}</flux:table.column>
                        <flux:table.column class="w-32 text-right">{{ __('ui.table.headers.actions') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($results as $row)
                            <flux:table.row>
                                @php
                                    $reviewDecision = (string) ($row['review_decision'] ?? '');
                                    $decisionColor = match ($reviewDecision) {
                                        'mark_as_valid_existing_key' => 'green',
                                        'needs_literal_restore' => 'red',
                                        'needs_key_review' => 'amber',
                                        'ignore_for_now' => 'zinc',
                                        default => 'red',
                                    };
                                    $decisionLabel = match ($reviewDecision) {
                                        'mark_as_valid_existing_key' => __('Accepted existing key'),
                                        'needs_literal_restore' => __('Will restore literal'),
                                        'needs_key_review' => __('Queued for key review'),
                                        'ignore_for_now' => __('Deferred'),
                                        default => $row['review_decision_label'] ?: str($reviewDecision)->replace('_', ' ')->headline(),
                                    };
                                @endphp
                                <flux:table.cell class="text-center tabular-nums text-zinc-500">
                                    {{ $loop->iteration }}
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <div class="flex flex-col items-start gap-1">
                                        <flux:badge
                                            size="sm"
                                            color="{{ ($row['state'] ?? '') === 'missing_finding' ? 'amber' : 'red' }}"
                                        >
                                            {{ str((string) ($row['state'] ?? ''))->replace('_', ' ')->headline() }}
                                        </flux:badge>
                                        @if (!empty($row['key_review_status']))
                                            <flux:badge
                                                size="sm"
                                                color="{{ ($row['key_review_status'] ?? '') === 'reviewed' ? 'green' : 'amber' }}"
                                            >
                                                {{ __('Key') }}: {{ $row['key_review_status'] }}
                                            </flux:badge>
                                        @endif
                                        <flux:badge
                                            size="sm"
                                            color="{{ !empty($row['source_lang_value_exists']) ? 'green' : 'red' }}"
                                        >
                                            {{ __('Source') }} {{ $row['source_locale'] ?? 'en' }}:
                                            {{ !empty($row['source_lang_value_exists']) ? __('available') : __('missing') }}
                                        </flux:badge>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <div class="flex flex-col items-start gap-1">
                                        <flux:badge
                                            size="sm"
                                            color="{{ $decisionColor }}"
                                        >
                                            {{ $reviewDecision !== '' ? $decisionLabel : __('Open') }}
                                        </flux:badge>

                                        @if (!empty($row['reviewed_at']))
                                            <div class="whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400">
                                                <x-ui.date-time.date
                                                    color="inherit"
                                                    :value="$row['reviewed_at']"
                                                />
                                                <x-ui.date-time.time
                                                    color="inherit"
                                                    :value="$row['reviewed_at']"
                                                />
                                            </div>
                                        @else
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ __('Not reviewed yet') }}
                                            </div>
                                        @endif
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    @php
                                        $sourceEditorUrl = null;

                                        if (!empty($row['source_path'])) {
                                            $sourceAbsolutePath = str_replace('\\', '/', base_path($row['source_path']));
                                            $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
                                            $sourceEditorLine = !empty($row['source_line']) ? ':' . $row['source_line'] : '';
                                            $sourceEditorUrl =
                                                'vscode://vscode-remote/wsl+' .
                                                rawurlencode(
                                                    (string) config('translation-workbench.editor.vscode_wsl_distro'),
                                                ) .
                                                $sourceEditorPath .
                                                $sourceEditorLine;
                                        }
                                    @endphp
                                    <div class="max-w-[23rem] space-y-2 whitespace-normal">
                                        <div class="flex items-start gap-2">
                                            @if ($sourceEditorUrl)
                                                <x-ui.tooltip.simple
                                                    :title="__('Open in VSC')"
                                                    :text="__(
                                                        'Opens the detected source location directly in the running VS Code WSL workspace.',
                                                    )"
                                                >
                                                    <flux:button
                                                        class="mt-0.5 h-5 w-5 shrink-0"
                                                        type="button"
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="external-link"
                                                        icon:class="text-sky-500 dark:text-sky-400"
                                                        :href="$sourceEditorUrl"
                                                        :aria-label="__('Open in VSC')"
                                                    />
                                                </x-ui.tooltip.simple>
                                            @endif
                                            <span class="wrap-anywhere whitespace-normal font-mono text-xs leading-relaxed">
                                                {{ $row['source_path'] }}
                                            </span>
                                            @if (!empty($row['source_line']))
                                                <span class="ml-1 inline-flex">
                                                    <flux:badge
                                                        size="sm"
                                                        color="sky"
                                                    >
                                                        {{ __('Line') }}: {{ $row['source_line'] }}
                                                    </flux:badge>
                                                </span>
                                            @endif
                                        </div>
                                        <div
                                            class="wrap-anywhere whitespace-normal border-t border-zinc-200 pt-2 font-mono text-xs leading-relaxed text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                                            {{ $row['raw_expression'] }}
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <div
                                        class="wrap-anywhere max-w-[17rem] whitespace-normal font-mono text-xs leading-relaxed">
                                        {{ $row['translation_key'] }}
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <div
                                        class="wrap-anywhere max-w-[17rem] whitespace-normal font-mono text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                                        {{ $row['suggested_key'] }}
                                    </div>
                                    @if (!empty($row['literal_text_suggested']))
                                        <div
                                            class="wrap-anywhere mt-2 max-w-[17rem] whitespace-normal text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                                            {{ __('Literal hint') }}: {{ $row['literal_text_suggested'] }}
                                        </div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <div class="max-w-[17rem] space-y-2 whitespace-normal">
                                        <div class="flex flex-wrap gap-1">
                                            <flux:badge
                                                size="sm"
                                                color="{{ empty($row['finding_id']) ? 'amber' : 'sky' }}"
                                            >
                                                F# {{ $row['finding_id'] ?? __('missing') }}
                                            </flux:badge>
                                            <flux:badge
                                                size="sm"
                                                color="{{ empty($row['key_id']) ? 'amber' : 'sky' }}"
                                            >
                                                K# {{ $row['key_id'] ?? __('missing') }}
                                            </flux:badge>
                                            @if (!empty($row['has_active_key_link']))
                                                <flux:badge
                                                    size="sm"
                                                    color="sky"
                                                >
                                                    {{ __('Linked') }}
                                                </flux:badge>
                                            @endif
                                            <flux:badge
                                                size="sm"
                                                color="{{ ((int) ($row['active_usage_count'] ?? 0)) > 1 ? 'green' : 'amber' }}"
                                            >
                                                {{ __('Usage') }}:
                                                {{ number_format((int) ($row['active_usage_count'] ?? 0)) }}
                                            </flux:badge>
                                            <flux:badge
                                                size="sm"
                                                color="{{ ((int) ($row['reviewed_usage_count'] ?? 0)) > 0 ? 'green' : 'zinc' }}"
                                            >
                                                {{ __('Reviewed') }}:
                                                {{ number_format((int) ($row['reviewed_usage_count'] ?? 0)) }}
                                            </flux:badge>
                                            <flux:badge
                                                size="sm"
                                                color="{{ ((int) ($row['direct_code_usage_count'] ?? 0)) > 1 ? 'sky' : 'zinc' }}"
                                            >
                                                {{ __('Code') }}:
                                                {{ number_format((int) ($row['direct_code_usage_count'] ?? 0)) }}
                                            </flux:badge>
                                        </div>
                                        @if (!empty($row['source_lang_value']))
                                            <div class="wrap-anywhere whitespace-normal border-t border-zinc-200 pt-2 text-xs leading-relaxed text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                                                {{ __('Source value') }}:
                                                {{ str((string) $row['source_lang_value'])->limit(140) }}
                                            </div>
                                        @endif
                                        <div
                                            class="wrap-anywhere whitespace-normal text-wrap text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                                            {{ $row['reason'] }}
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="text-right align-top">
                                    @if (!empty($row['source_signature']))
                                        <flux:button
                                            type="button"
                                            size="xs"
                                            variant="primary"
                                            color="amber"
                                            icon="clipboard-check"
                                            wire:click="openSuspiciousKeyReviewModal('{{ $row['source_signature'] }}')"
                                        >
                                            {{ $reviewDecision !== '' ? __('Review again') : __('Review') }}
                                        </flux:button>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8">
                                    <div class="py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ __('No suspicious keyed additions were detected.') }}
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>
    @endif
</div>
