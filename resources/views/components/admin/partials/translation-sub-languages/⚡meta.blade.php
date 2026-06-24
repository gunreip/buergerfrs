{{-- resources/views/components/admin/partials/translation-sub-languages/⚡meta.blade.php --}}

<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                :title="__('Sub-Language Overview')"
                :description="__('Summary of active sub-languages, their coverage, and relation to main language keys.')"
            />
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <x-ui.button.show-hide
                size="xs"
                state="showMeta"
            />
        </div>
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <flux:callout
                class="hyphen-auto"
                color="sky"
                icon="languages"
                heading="{{ __('Matching sub-languages') }}"
                text="{{ __('Active locale variants matching the current filters.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($subLocales->count()) }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="hyphens-auto"
                color="{{ $activeSubLocalesTotal > 0 ? 'green' : 'zinc' }}"
                icon="badge-check"
                heading="{{ __('Active total') }}"
                text="{{ __('All active sub-languages available in locale management.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($activeSubLocalesTotal) }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="hyphens-auto"
                color="{{ $withOverridesCount > 0 ? 'amber' : 'zinc' }}"
                icon="replace"
                heading="{{ __('With overrides') }}"
                text="{{ __('Variants containing locale-specific translated values.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($withOverridesCount) }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="hyphens-auto"
                color="orange"
                icon="database"
                heading="{{ __('admin.translation_list.meta.total_keys') }}"
                text="{{ __('Reference total from translation keys table.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format($totalKeys) }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
