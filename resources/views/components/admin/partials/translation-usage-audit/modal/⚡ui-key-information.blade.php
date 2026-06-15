{{-- resources/views/components/admin/partials/translation-usage-audit/modal/⚡ui-key-information.blade.php --}}

{{-- UI key information --}}
<flux:callout
    icon="key-round"
    color="{{ (bool) ($selectedItem['already_has_ui_candidate'] ?? false) || $selectedSuggestedUiKey !== '' ? 'emerald' : 'zinc' }}"
    stroke-width="1"
>
    <div class="flex items-center justify-between gap-3">
        <flux:callout.heading>
            {{ __('UI key information') }}
        </flux:callout.heading>

        <x-ui.button.show-hide
            state="showUiKeyInformation"
            size="xs"
        />
    </div>

    <div
        class="mt-3 grid gap-3 lg:grid-cols-3"
        x-show="showUiKeyInformation"
        x-collapse
    >
        <div class="col-span-2">
            <flux:callout.text class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Suggested UI key') }}
            </flux:callout.text>

            <div class="mt-2 flex min-h-7 items-center">
                @if ($selectedSuggestedUiKey !== '')
                    <code class="wrap-anywhere block text-sm leading-6">
                        {{ $selectedSuggestedUiKey }}
                    </code>
                @else
                    <div class="text-sm leading-6 text-zinc-400">—</div>
                @endif
            </div>
        </div>

        <div>
            <flux:callout.text class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Existing UI keys') }}
            </flux:callout.text>

            <div class="mt-2 flex min-h-7 items-center">
                @if ($selectedUiKeys->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach ($selectedUiKeys as $uiKey)
                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="emerald"
                            >
                                {{ $uiKey }}
                            </flux:badge>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm leading-6 text-zinc-400">—</div>
                @endif
            </div>
        </div>
    </div>
</flux:callout>
