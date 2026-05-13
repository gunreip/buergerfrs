{{-- resources/views/components/admin/partials/fallback-report-list/meta.blade.php --}}

{{-- Overview --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Overview') }}
    </flux:heading>

    <div class="grid grid-cols-3 gap-3">
        <flux:callout
            class="col-span-3 md:col-span-1"
            color="orange"
            icon="triangle-alert"
        >
            <flux:callout.heading>
                {{ __('Open reports') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Number of fallback reports that are still open and need review.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['open'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-3 md:col-span-1"
            color="green"
            icon="check-circle"
        >
            <flux:callout.heading>
                {{ __('Reviewed reports') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Number of fallback reports that have been reviewed.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['reviewed'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-3 md:col-span-1"
            color="blue"
            icon="file-stack"
        >
            <flux:callout.heading>
                {{ __('Total reports') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Total number of fallback reports.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['total'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
