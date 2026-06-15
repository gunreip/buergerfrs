{{-- resources/views/components/admin/partials/translation-usage-audit/modal/⚡translation-keys.blade.php --}}

{{-- Translations Keys --}}
<flux:callout
    icon="language"
    color="amber"
    stroke-width="1"
    x-data="{ showTranslationKeys: false }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <flux:callout.heading>
                {{ __('Translation keys') }}
            </flux:callout.heading>
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <flux:badge
                variant="subtle"
                color="amber"
            >
                {{ $selectedKeys->count() }}
            </flux:badge>

            <x-ui.button.show-hide
                size="xs"
                state="showTranslationKeys"
            />
        </div>
    </div>

    <div
        {{-- class="max-h-54 -mr-3 mt-3 space-y-2 overflow-y-auto pr-2" --}}
        x-show="showTranslationKeys"
        x-collapse
    >
        <div
            class="max-h-54 -mr-3 mt-3 space-y-2 overflow-y-auto pr-2"
            {{-- x-show="showTranslationKeys" --}}
            {{-- x-collapse --}}
        >
            @forelse ($selectedKeys as $key)
                @php
                    $keyUsageTotal = (int) ($key['usage_count_total'] ?? ($key['usage_count'] ?? 0));
                    $keyUsageCurrent = (int) ($key['usage_count_current'] ?? 0);
                    $keyUsageStale = (int) ($key['usage_count_stale'] ?? 0);
                @endphp

                <div
                    class="rounded-lg border border-zinc-200 bg-white/60 p-3 text-sm dark:border-zinc-700 dark:bg-zinc-950/20">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:badge
                                    class="tabular-nums"
                                    size="sm"
                                    variant="subtle"
                                    color="zinc"
                                >
                                    #{{ $key['translation_key_id'] ?? '—' }}
                                </flux:badge>

                                @if ((bool) ($key['is_ui_key'] ?? false))
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="emerald"
                                    >
                                        ui
                                    </flux:badge>
                                @endif

                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                    color="sky"
                                >
                                    {{ $key['classification'] ?? '—' }}
                                </flux:badge>

                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                    color="amber"
                                >
                                    {{ $key['status'] ?? '—' }}
                                </flux:badge>
                            </div>

                            <code class="wrap-anywhere mt-2 block text-xs">
                                {{ $key['full_key'] ?? ($key['key'] ?? '—') }}
                            </code>

                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <span class="font-semibold">{{ __('admin.translation_list.modal.namespace') }}:</span>
                                {{ $key['namespace'] ?? '—' }}

                                <span class="ml-3 font-semibold">{{ __('admin.translation_list.modal.group') }}:</span>
                                {{ $key['group'] ?? '—' }}
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-1.5">
                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="zinc"
                            >
                                {{ __('Total') }} {{ $keyUsageTotal }}
                            </flux:badge>

                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="emerald"
                            >
                                {{ __('admin.app_settings.locale.current') }} {{ $keyUsageCurrent }}
                            </flux:badge>

                            <flux:badge
                                size="sm"
                                variant="subtle"
                                color="amber"
                            >
                                {{ __('Stale') }} {{ $keyUsageStale }}
                            </flux:badge>
                        </div>
                    </div>

                    @if (trim((string) ($key['native_text'] ?? '')) !== '')
                        <div
                            class="wrap-anywhere mt-3 rounded-md bg-zinc-50 p-2 text-xs text-zinc-600 dark:bg-zinc-900/50 dark:text-zinc-300">
                            {{ $key['native_text'] }}
                        </div>
                    @endif
                </div>
            @empty
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('No translation keys available.') }}
                </flux:text>
            @endforelse
        </div>
    </div>
</flux:callout>
