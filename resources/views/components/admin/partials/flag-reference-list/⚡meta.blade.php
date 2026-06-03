{{-- resources/views/components/admin/partials/flag-reference-list/⚡meta.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Overview')"
        :description="__('Current audit status and data source for this flag reference page.')"
    />

    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4">
        <flux:badge
            color="zinc"
            variant="subtle"
            size="lg"
        >
            {{ __('Total') }}: {{ number_format((int) ($summary['total'] ?? 0)) }}
        </flux:badge>

        <flux:badge
            color="green"
            variant="subtle"
            size="lg"
        >
            {{ __('Resolved') }}: {{ number_format((int) ($summary['resolved'] ?? 0)) }}
        </flux:badge>

        <flux:badge
            color="amber"
            variant="subtle"
            size="lg"
        >
            {{ __('Needs review') }}: {{ number_format((int) ($summary['needs_review'] ?? 0)) }}
        </flux:badge>

        <flux:badge
            color="sky"
            variant="subtle"
            size="lg"
        >
            {{ __('Filtered') }}: {{ number_format((int) ($summary['filtered'] ?? 0)) }}
        </flux:badge>
    </div>

    @if ($reportPath)
        <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            {{ __('Source report') }}: {{ $reportPath }}
        </div>
    @endif
</flux:card>
