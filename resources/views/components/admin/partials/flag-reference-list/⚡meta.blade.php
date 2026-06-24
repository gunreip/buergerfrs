{{-- resources/views/components/admin/partials/flag-reference-list/⚡meta.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.overview.title')"
        :description="__('Current audit status and data source for this flag reference page.')"
    />

    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4">

        {{-- Callout Total Flags --}}
        <flux:callout
            class="hyphens-auto md:col-span-1"
            color="blue"
            icon="flag"
            heading="{{ __('Total flags') }}"
            text="{{ __('The flag reference data is sourced from the ISO 3166-1 standard, which defines country codes and related information.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format((int) ($summary['total'] ?? 0)) }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout Resolved Issues --}}
        <flux:callout
            class="hyphens-auto md:col-span-1"
            color="green"
            icon="check-circle"
            heading="{{ __('Resolved') }}"
            text="{{ __('Number of fallback reports that have been resolved.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format((int) ($summary['resolved'] ?? 0)) }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout Needs Review --}}
        <flux:callout
            class="hyphens-auto md:col-span-1"
            color="red"
            icon="x-circle"
            heading="{{ __('Needs review') }}"
            text="{{ __('Number of fallback reports that need review.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format((int) ($summary['needs_review'] ?? 0)) }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout Total Reports --}}
        <flux:callout
            class="hyphens-auto md:col-span-1"
            color="yellow"
            icon="file-stack"
            heading="{{ __('Total reports') }}"
            text="{{ __('Total number of fallback reports.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format((int) ($summary['total_reports'] ?? 0)) }}
            </flux:callout.text>
        </flux:callout>

    </div>

    @if ($reportPath)
        <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            {{ __('Source report') }}: {{ $reportPath }}
        </div>
    @endif
</flux:card>
