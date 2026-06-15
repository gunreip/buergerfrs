{{-- resources/views/components/admin/partials/translation-usage-audit/modal/⚡usage-locations.blade.php --}}

{{-- Usage locations --}}
<flux:callout
    icon="radar"
    color="sky"
    stroke-width="1"
    x-data="{ showUsageLocations: false }"
>

    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <flux:callout.heading>
                {{ __('admin.translation_list.modal_edit.usage_locations') }}
            </flux:callout.heading>
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <flux:badge
                variant="subtle"
                color="sky"
            >
                {{ $selectedUsageLocations->count() }}
            </flux:badge>

            <x-ui.button.show-hide
                size="xs"
                state="showUsageLocations"
            />
        </div>
    </div>

    <div
        {{-- class="-mr-3 mt-3 max-h-64 space-y-2 overflow-y-auto pr-2" --}}
        x-show="showUsageLocations"
        x-collapse
    >
        <div
            class="-mr-3 mt-3 max-h-64 space-y-2 overflow-y-auto pr-2"
            {{-- x-show="showUsageLocations" --}}
            {{-- x-collapse --}}
        >
            @forelse ($selectedUsageLocations as $usage)
                @php
                    $usagePath = trim((string) ($usage['view_path'] ?? ''));
                    $usageLine = (int) ($usage['line'] ?? 0);
                    $usageContext = trim((string) ($usage['context'] ?? ''));
                    $usageFunction = trim((string) ($usage['function'] ?? ''));
                    $usageClassification = trim((string) ($usage['classification'] ?? ''));
                    $usageReason = trim((string) ($usage['reason'] ?? ''));
                    $usageRaw = trim((string) ($usage['raw'] ?? ''));
                    $usageOriginalRaw = trim((string) ($usage['original_raw'] ?? ''));

                    $usageHasOriginalRaw = $usageOriginalRaw !== '';
                    $usageOriginalDiffersRaw = $usageHasOriginalRaw && $usageRaw !== $usageOriginalRaw;
                    $usageHasRawDetails = $usageRaw !== '' || $usageOriginalDiffersRaw;

                    $isStaleUsage = (bool) ($usage['is_stale'] ?? false);
                @endphp

                <div
                    class="rounded-lg border border-zinc-200 bg-white/60 p-3 text-sm dark:border-zinc-700 dark:bg-zinc-950/20"
                    x-data="{ showUsageRaw: false }"
                >
                    <div>
                        <div class="min-w-0 space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 flex-wrap items-center gap-2">
                                    <flux:badge
                                        class="tabular-nums"
                                        size="sm"
                                        variant="subtle"
                                        color="zinc"
                                    >
                                        #{{ $usage['translation_key_id'] ?? '—' }}
                                    </flux:badge>

                                    @if ($usageClassification !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="sky"
                                        >
                                            {{ $usageClassification }}
                                        </flux:badge>
                                    @endif

                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="{{ $isStaleUsage ? 'amber' : 'emerald' }}"
                                    >
                                        {{ $isStaleUsage ? __('Stale') : __('admin.app_settings.locale.current') }}
                                    </flux:badge>

                                    @if ($usageFunction !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ $usageFunction }}
                                        </flux:badge>
                                    @endif

                                    @if ($usageReason !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="amber"
                                        >
                                            {{ $usageReason }}
                                        </flux:badge>
                                    @endif
                                </div>

                                @if ($usageHasRawDetails)
                                    <div class="shrink-0">
                                        <x-ui.button.show-hide
                                            size="xs"
                                            width="min-w-16"
                                            state="showUsageRaw"
                                        />
                                    </div>
                                @endif
                            </div>

                            <code class="wrap-anywhere block text-xs">
                                {{ $usage['full_key'] ?? '—' }}
                            </code>

                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="font-semibold">{{ __('admin.translation_list.modal.path') }}:</span>
                                    <code class="wrap-anywhere whitespace-normal px-3 text-xs">
                                        {{ $usagePath !== '' ? $usagePath : '—' }}
                                    </code>
                                </div>

                                @if ($usageLine > 0)
                                    <flux:badge
                                        class="shrink-0 tabular-nums"
                                        size="sm"
                                        variant="subtle"
                                        color="zinc"
                                    >
                                        {{ __('admin.translation_list.modal.line') }} {{ $usageLine }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($usageContext !== '')
                        <div
                            class="wrap-anywhere mt-3 rounded-md bg-zinc-50 p-2 text-xs text-zinc-600 dark:bg-zinc-900/50 dark:text-zinc-300">
                            {{ $usageContext }}
                        </div>
                    @endif

                    @if ($usageHasRawDetails)
                        <div
                            class="mt-3 space-y-3"
                            x-show="showUsageRaw"
                            x-collapse
                        >
                            @if ($usageRaw !== '')
                                <x-ui.text.copyable-field
                                    :title="__('admin.translation_list.modal.current_raw')"
                                    :value="$usageRaw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif

                            @if ($usageOriginalDiffersRaw)
                                <x-ui.text.copyable-field
                                    :title="__('admin.translation_list.modal.original_raw')"
                                    :value="$usageOriginalRaw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('No usage locations available.') }}
                </div>
            @endforelse
        </div>
    </div>
</flux:callout>
