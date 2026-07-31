{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-translation-key.blade.php --}}

{{-- Table Findings Cell Translation Key --}}
<flux:table.cell>
    <div class="max-w-md space-y-2">
        @if ($translationWorkflowEdited && $hasTranslationKey)
            <div class="wrap-anywhere text-wrap font-mono text-xs">
                <x-translation-workbench::text.highlight
                    :value="$translationKey"
                    :search="$findingSearch"
                    :exact="$findingSearchExact"
                    :case-sensitive="$findingSearchCaseSensitive"
                />
            </div>
        @else
            <div class="flex flex-wrap gap-1">
                <flux:badge
                    size="sm"
                    color="{{ $hasTranslationKey ? 'green' : 'amber' }}"
                >
                    {{ $hasTranslationKey ? __('ui.translation.translation-key') : __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing') }}
                </flux:badge>

                @if ($wasBulkEqualized)
                    <x-ui.tooltip.simple
                        :title="__('ui.badge.bulk-equalized')"
                        :text="__(
                            'This reviewed translation key was set through the bulk equalize workflow. Suggested and existing key context remain unchanged for audit/review history.',
                        )"
                    >
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('ui.badge.bulk-equalized') }}
                        </flux:badge>
                    </x-ui.tooltip.simple>
                @endif
            </div>

            @if ($hasTranslationKey)
                <div class="space-y-0.5">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('ui.translation.translation-key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs">
                        <x-translation-workbench::text.highlight
                            :value="$translationKey"
                            :search="$findingSearch"
                            :exact="$findingSearchExact"
                            :case-sensitive="$findingSearchCaseSensitive"
                        />
                    </div>
                </div>
            @endif

            @if (filled($keySuggestedKey))
                <div class="space-y-0.5">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Suggested translation key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        <x-translation-workbench::text.highlight
                            :value="$keySuggestedKey"
                            :search="$findingSearch"
                            :exact="$findingSearchExact"
                            :case-sensitive="$findingSearchCaseSensitive"
                        />
                    </div>
                </div>
            @elseif (filled($findingSuggestedKey))
                <div class="space-y-0.5">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Finding suggested translation key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        <x-translation-workbench::text.highlight
                            :value="$findingSuggestedKey"
                            :search="$findingSearch"
                            :exact="$findingSearchExact"
                            :case-sensitive="$findingSearchCaseSensitive"
                        />
                    </div>
                </div>
            @endif

            @if (filled($existingKey) && $existingKey !== $translationKey)
                <div class="space-y-0.5">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Existing translation key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        <x-translation-workbench::text.highlight
                            :value="$existingKey"
                            :search="$findingSearch"
                            :exact="$findingSearchExact"
                            :case-sensitive="$findingSearchCaseSensitive"
                        />
                    </div>
                </div>
            @endif

            @if (filled($foundTranslationKey) && $foundTranslationKey !== $translationKey && $foundTranslationKey !== $existingKey)
                <div class="space-y-0.5">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Found translation key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        <x-translation-workbench::text.highlight
                            :value="$foundTranslationKey"
                            :search="$findingSearch"
                            :exact="$findingSearchExact"
                            :case-sensitive="$findingSearchCaseSensitive"
                        />
                    </div>
                </div>
            @endif
        @endif
    </div>
</flux:table.cell>
