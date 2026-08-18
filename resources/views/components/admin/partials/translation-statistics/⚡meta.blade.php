{{-- resources/views/components/admin/partials/translation-statistics/⚡meta.blade.php --}}

{{-- Key Health: summary callouts --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>

    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                name="translation-statistics-meta"
                :title="__('Key Health')"
                :description="__('Overview of translation key states across the audit table.')"
            />
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <x-ui.button.show-hide
                size="xs"
                state="showMeta"
                show-label="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.show_overview') }}"
                hide-label="{{ __('Hide overview') }}"
            />
        </div>
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Total Keys --}}
            <flux:callout
                class="hyphens-auto"
                color="orange"
                icon="database"
                heading="{{ __('Audit entries') }}"
                text="{{ __('Rows in the translation key audit table.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($totalKeys) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Marked as OK --}}
            <flux:callout
                class="hyphens-auto"
                color="{{ ($keysByStatus['ok'] ?? 0) > 0 ? 'green' : 'zinc' }}"
                icon="check-circle"
                heading="{{ __('admin.translation_list.meta.ok') }}"
                text="{{ __('Keys marked as OK.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($keysByStatus['ok'] ?? 0) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Missing Values --}}
            <flux:callout
                class="hyphens-auto"
                color="{{ ($keysByStatus['missing'] ?? 0) > 0 ? 'amber' : 'green' }}"
                icon="shield-alert"
                heading="{{ __('ui.state.missing') }}"
                text="{{ __('Keys with missing values.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($keysByStatus['missing'] ?? 0) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Marked as Obsolete --}}
            <flux:callout
                class="hyphens-auto"
                color="{{ ($keysByStatus['obsolete'] ?? 0) > 0 ? 'amber' : 'green' }}"
                icon="archive"
                heading="{{ __('admin.translation_list.meta.obsolete') }}"
                text="{{ __('Keys marked as obsolete.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($keysByStatus['obsolete'] ?? 0) }}
                </flux:callout.text>
            </flux:callout>

        </div>

        <div class="-mb-4 ml-auto mt-3 flex shrink-0 items-center justify-end gap-3">
            <x-ui.info.last-update :value="$recentlySyncedAt" />
        </div>

    </div>
</flux:card>
