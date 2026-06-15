{{-- resources/views/components/admin/partials/html-view-audit/⚡statistic.blade.php --}}

@php
    $statistics = $statistics ?? [
        'total' => 0,
        'by_status' => [
            'open' => 0,
            'changed' => 0,
            'resolved' => 0,
            'ignored' => 0,
        ],
        'by_section' => [
            'native_html' => 0,
            'custom_components' => 0,
        ],
        'by_type' => [
            'unclosed' => 0,
            'mismatched' => 0,
            'unexpected_closing' => 0,
        ],
        'top_tags' => [],
        'top_files' => [],
    ];
@endphp

<flux:card class="mt-4">
    <div class="flex items-start justify-between gap-4">
        <x-ui.headers.card
            :title="__('Statistics')"
            :description="__('Statistics for the current filtered audit selection.')"
        />

        <x-ui.button.show-hide
            state="statisticOpen"
            show-label="{{ __('ui.Show') }}"
            hide-label="{{ __('ui.Hide') }}"
        />
    </div>

    <div
        class="mt-4 grid gap-3 md:grid-cols-12"
        x-cloak
        x-show="statisticOpen"
        x-collapse
    >
        <flux:callout
            class="md:col-span-3"
            color="sky"
            icon="chart-bar"
        >
            <flux:callout.heading>
                {{ __('Current selection') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $statistics['total'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="md:col-span-3"
            color="sky"
            icon="list-filter"
        >
            <flux:callout.heading>
                {{ __('By status') }}
            </flux:callout.heading>

            <flux:callout.text>
                <div class="flex flex-wrap gap-2">
                    <flux:badge
                        color="red"
                        variant="subtle"
                    >
                        {{ __('admin.translation_list.filter.open') }}: {{ $statistics['by_status']['open'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="amber"
                        variant="subtle"
                    >
                        {{ __('Changed') }}: {{ $statistics['by_status']['changed'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="green"
                        variant="subtle"
                    >
                        {{ __('Resolved') }}: {{ $statistics['by_status']['resolved'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="zinc"
                        variant="subtle"
                    >
                        {{ __('Ignored') }}: {{ $statistics['by_status']['ignored'] ?? 0 }}
                    </flux:badge>
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="md:col-span-3"
            color="sky"
            icon="code-xml"
        >
            <flux:callout.heading>
                {{ __('By section') }}
            </flux:callout.heading>

            <flux:callout.text>
                <div class="flex flex-wrap gap-2">
                    <flux:badge
                        color="amber"
                        variant="subtle"
                    >
                        {{ __('Native HTML') }}: {{ $statistics['by_section']['native_html'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="violet"
                        variant="subtle"
                    >
                        {{ __('Custom components') }}: {{ $statistics['by_section']['custom_components'] ?? 0 }}
                    </flux:badge>
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="md:col-span-3"
            color="sky"
            icon="bug"
        >
            <flux:callout.heading>
                {{ __('By type') }}
            </flux:callout.heading>

            <flux:callout.text>
                <div class="flex flex-wrap gap-2">
                    <flux:badge
                        color="red"
                        variant="subtle"
                    >
                        {{ __('Unclosed') }}: {{ $statistics['by_type']['unclosed'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="amber"
                        variant="subtle"
                    >
                        {{ __('Mismatched') }}: {{ $statistics['by_type']['mismatched'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="zinc"
                        variant="subtle"
                    >
                        {{ __('Unexpected closing') }}: {{ $statistics['by_type']['unexpected_closing'] ?? 0 }}
                    </flux:badge>
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="md:col-span-4"
            color="indigo"
            icon="tag"
        >
            <flux:callout.heading>
                {{ __('Top tags') }}
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('Top Tags in the current selection. Shows the most common HTML tags found in the audit results, along with their respective counts. This provides insight into which tags are most frequently associated with issues in the audited HTML content.') }}
            </flux:callout.text>

            <flux:callout.text>
                @if (!empty($statistics['top_tags']))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($statistics['top_tags'] as $tagStatistic)
                            <flux:badge
                                variant="subtle"
                                color="blue"
                            >
                                {{ $tagStatistic['tag'] }}: {{ $tagStatistic['total'] }}
                            </flux:badge>
                        @endforeach
                    </div>
                @else
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No tag statistics available.') }}
                    </span>
                @endif
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="md:col-span-8"
            color="indigo"
            icon="files"
        >
            <flux:callout.heading>
                {{ __('Top files') }}
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('Top files in the current selection. Shows the files with the most issues found in the audit results, along with their respective counts. This helps identify which files may require the most attention for fixing HTML-related problems.') }}
            </flux:callout.text>

            <flux:callout.text>
                @if (!empty($statistics['top_files']))
                    <div class="space-y-1">
                        @foreach ($statistics['top_files'] as $fileStatistic)
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <code class="elipsis-rtl truncate">
                                    {{ $fileStatistic['file'] }}
                                </code>

                                <flux:badge
                                    variant="subtle"
                                    color="blue"
                                >
                                    {{ $fileStatistic['total'] }}
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                @else
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No file statistics available.') }}
                    </span>
                @endif
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
