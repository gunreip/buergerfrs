{{-- resources/views/components/admin/partials/fallback-report-list/⚡meta.blade.php --}}

{{-- Overview --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.overview.title')"
        :description="__(
            'Summary of technical fallback events such as missing icons, missing config values or other recoverable UI fallbacks.',
        )"
    />

    <div class="grid grid-cols-3 gap-3">
        {{-- Callout Open Reports --}}
        <flux:callout
            class="col-span-3 hyphens-auto md:col-span-1"
            color="orange"
            icon="triangle-alert"
            heading="{{ __('Open reports') }}"
            text="{{ __('Number of fallback reports that are still open and need review.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['open'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout Reviewed Reports --}}
        <flux:callout
            class="col-span-3 hyphens-auto md:col-span-1"
            color="green"
            icon="check-circle"
            heading="{{ __('Reviewed reports') }}"
            text="{{ __('Number of fallback reports that have been reviewed.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['reviewed'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Callout Total Reports --}}
        <flux:callout
            class="col-span-3 hyphens-auto md:col-span-1"
            color="blue"
            icon="file-stack"
            heading="{{ __('Total reports') }}"
            text="{{ __('Total number of fallback reports.') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['total'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
