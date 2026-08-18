{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-lang-cleanup-review.blade.php --}}

<flux:modal
    class="w-full max-w-6xl"
    name="translation-workbench-lang-cleanup-review"
    wire:model="langCleanupReviewModalOpen"
>
    @php
        $cleanupReview = $langCleanupReview ?? null;
        $inventory = $cleanupReview['inventory'] ?? null;
        $langValues = collect($cleanupReview['lang_values'] ?? []);
        $latestReview = $cleanupReview['latest_review'] ?? null;
        $cleanupDecision = (string) ($cleanupReview['decision'] ?? 'open');
        $sourceLocale = (string) ($cleanupReview['source_locale'] ?? 'en');
        $targetLocale = (string) ($cleanupReview['target_locale'] ?? app()->getLocale());
    @endphp

    <div class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 space-y-1">
                <flux:heading
                    size="xl"
                    level="3"
                >
                    {{ __('Review lang cleanup') }}
                </flux:heading>

                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Decide whether this translation key should stay in lang files, needs later review, or can be marked obsolete.') }}
                </flux:text>
            </div>

            @if ($inventory)
                <div class="mr-8 flex flex-wrap justify-end gap-2">
                    <flux:badge
                        size="sm"
                        color="{{ $cleanupDecision === 'obsolete' ? 'amber' : ($cleanupDecision === 'keep' ? 'green' : 'zinc') }}"
                    >
                        {{ __('Decision') }}: {{ str($cleanupDecision)->replace('_', ' ')->headline() }}
                    </flux:badge>
                    <flux:badge size="sm">I#{{ $inventory->id }}</flux:badge>
                    @if ($cleanupReview['key'] ?? null)
                        <flux:badge size="sm">K#{{ $cleanupReview['key']->id }}</flux:badge>
                    @endif
                </div>
            @endif
        </div>

        @if ($inventory)
            <div class="grid gap-3 lg:grid-cols-3">
                <flux:callout
                    class="lg:col-span-2"
                    color="amber"
                    icon="key-round"
                >
                    <flux:callout.heading>{{ __('ui.translation.translation-key') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="wrap-anywhere font-mono text-xs">
                            {{ $cleanupReview['translation_key'] }}
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="{{ $latestReview ? 'sky' : 'zinc' }}"
                    icon="{{ $latestReview ? 'badge-check' : 'circle-question-mark' }}"
                >
                    <flux:callout.heading>{{ __('Latest cleanup review') }}</flux:callout.heading>
                    <flux:callout.text>
                        @if ($latestReview)
                            <div class="space-y-1 text-sm">
                                <div>
                                    {{ str((string) $latestReview->decision)->replace('_', ' ')->headline() }}
                                </div>
                                <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    <x-ui.date-time.date-time
                                        :value="$latestReview->reviewed_at"
                                        size="xs"
                                    />
                                    <span>·</span>
                                    <x-ui.date-time.ago :value="$latestReview->reviewed_at" />
                                </div>
                            </div>
                        @else
                            {{ __('No cleanup review has been saved for this candidate yet.') }}
                        @endif
                    </flux:callout.text>
                </flux:callout>
            </div>

            <div class="grid gap-3 lg:grid-cols-3">
                <flux:callout
                    color="{{ (int) $inventory->finding_active_count > 0 ? 'red' : 'green' }}"
                    icon="scan-search"
                >
                    <flux:callout.heading>{{ __('Code usage') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="{{ (int) $inventory->finding_active_count > 0 ? 'red' : 'green' }}"
                            >
                                {{ __('Active') }}: {{ number_format((int) $inventory->finding_active_count) }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="{{ (int) $inventory->finding_commented_out_count > 0 ? 'amber' : 'zinc' }}"
                            >
                                {{ __('Commented') }}:
                                {{ number_format((int) $inventory->finding_commented_out_count) }}
                            </flux:badge>
                            <flux:badge size="sm">
                                {{ __('Obsolete') }}: {{ number_format((int) $inventory->finding_obsolete_count) }}
                            </flux:badge>
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="sky"
                    icon="database"
                >
                    <flux:callout.heading>{{ __('Inventory state') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge size="sm">{{ __('Type') }}: {{ $inventory->key_type ?: __('None') }}
                            </flux:badge>
                            @if ($inventory->is_ui)
                                <flux:badge
                                    size="sm"
                                    color="cyan"
                                >{{ __('UI') }}</flux:badge>
                            @endif
                            @if ($inventory->is_shared)
                                <flux:badge
                                    size="sm"
                                    color="violet"
                                >{{ __('Shared') }}</flux:badge>
                            @endif
                            @if ($inventory->is_dynamic_multi)
                                <flux:badge
                                    size="sm"
                                    color="pink"
                                >{{ __('Dynamic multi') }}</flux:badge>
                            @elseif ($inventory->is_dynamic)
                                <flux:badge
                                    size="sm"
                                    color="pink"
                                >{{ __('Dynamic') }}</flux:badge>
                            @endif
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="{{ $langValues->where('status', 'active')->count() > 0 ? 'amber' : 'zinc' }}"
                    icon="languages"
                >
                    <flux:callout.heading>{{ __('Affected lang values') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge size="sm">{{ __('Active') }}:
                                {{ number_format($langValues->where('status', 'active')->count()) }}</flux:badge>
                            <flux:badge size="sm">{{ __('Obsolete') }}:
                                {{ number_format($langValues->where('status', 'obsolete')->count()) }}</flux:badge>
                            <flux:badge size="sm">{{ __('Locales') }}:
                                {{ number_format($langValues->pluck('locale')->unique()->count()) }}</flux:badge>
                        </div>
                    </flux:callout.text>
                </flux:callout>
            </div>

            <flux:callout
                color="zinc"
                icon="file-text"
            >
                <flux:callout.heading>{{ __('Lang values') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="mt-2 max-h-72 overflow-y-auto rounded-md border border-zinc-200 dark:border-zinc-700">
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Locale') }}</flux:table.column>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                                <flux:table.column>{{ __('Value') }}</flux:table.column>
                                <flux:table.column>{{ __('Updated') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($langValues as $langValue)
                                    <flux:table.row
                                        wire:key="translation-workbench-lang-cleanup-review-value-{{ $langValue->id }}"
                                    >
                                        <flux:table.cell>
                                            <div class="flex items-center gap-2">
                                                <x-ui.locale.flag
                                                    :locale="$langValue->locale"
                                                    size="sm"
                                                />
                                                <span class="font-mono text-xs">{{ $langValue->locale }}</span>
                                                @if ($langValue->locale === $sourceLocale)
                                                    <flux:badge
                                                        size="sm"
                                                        color="sky"
                                                    >{{ __('Source') }}</flux:badge>
                                                @elseif ($langValue->locale === $targetLocale)
                                                    <flux:badge
                                                        size="sm"
                                                        color="green"
                                                    >{{ __('Target') }}</flux:badge>
                                                @endif
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                color="{{ $langValue->status === 'active' ? 'green' : ($langValue->status === 'obsolete' ? 'amber' : 'zinc') }}"
                                            >
                                                {{ $langValue->status }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="wrap-anywhere max-w-xl text-wrap text-sm">
                                                {{ filled($langValue->value) ? $langValue->value : __('No value') }}
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="space-y-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                                <x-ui.date-time.date-time
                                                    :value="$langValue->updated_at"
                                                    size="xs"
                                                />
                                                <x-ui.date-time.ago :value="$langValue->updated_at" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4">
                                            <flux:text class="text-sm text-zinc-500">
                                                {{ __('No lang values were found for this cleanup candidate.') }}
                                            </flux:text>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="amber"
                icon="triangle-alert"
            >
                <flux:callout.heading>{{ __('Review decision') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('ui.remove.removes.keep-leaves-the-lang-values-active-needs-review-only-records-the-decision-mark-obsolete') }}
                </flux:callout.text>
            </flux:callout>

            <div class="flex flex-wrap justify-end gap-2">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="closeLangCleanupReview"
                >
                    {{ __('ui.button.cancel') }}
                </flux:button>

                <flux:button
                    type="button"
                    variant="primary"
                    color="green"
                    icon="shield-check"
                    wire:click="saveLangCleanupReview('keep')"
                >
                    {{ __('Keep') }}
                </flux:button>

                <flux:button
                    type="button"
                    variant="primary"
                    color="amber"
                    icon="circle-question-mark"
                    wire:click="saveLangCleanupReview('needs_review')"
                >
                    {{ __('Needs review') }}
                </flux:button>

                <flux:button
                    type="button"
                    variant="danger"
                    icon="archive-x"
                    wire:click="saveLangCleanupReview('obsolete')"
                    :disabled="$langValues->where('status', 'active')->count() === 0"
                >
                    {{ __('Mark obsolete') }}
                </flux:button>
            </div>
        @else
            <flux:callout
                color="amber"
                icon="info"
            >
                <flux:callout.heading>{{ __('No lang cleanup context available') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Select a cleanup candidate from the lang cleanup tab first.') }}
                </flux:callout.text>
            </flux:callout>
        @endif
    </div>
</flux:modal>
