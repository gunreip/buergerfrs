{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit-dynamic.blade.php --}}

{{-- Modal Edit Dynamic --}}
<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    id="translation-workbench-entry-edit-dynamic"
    name="translation-workbench-entry-edit-dynamic"
    wire:model="dynamicEditModalOpen"
>
    @if ($entry)
        @php
            $editLocales = $editLocales ?? [
                'source' => 'en',
                'active' => app()->getLocale(),
                'sub' => [],
            ];
            $sourceLocale = (string) ($editLocales['source'] ?? 'en');
            $activeLocale = (string) ($editLocales['active'] ?? app()->getLocale());
            $dynamicRows = collect($dynamicEditRows ?? []);
            $missingSourceCount = $dynamicRows
                ->filter(static fn(array $row): bool => blank($row['source'] ?? null))
                ->count();
            $missingTargetCount = $dynamicRows
                ->filter(static fn(array $row): bool => blank($row['target'] ?? null))
                ->count();
            $identicalCount = $dynamicRows
                ->filter(
                    static fn(array $row): bool => filled($row['source'] ?? null) &&
                        filled($row['target'] ?? null) &&
                        trim((string) ($row['source'] ?? '')) === trim((string) ($row['target'] ?? '')),
                )
                ->count();
            $dynamicStorageAvailable = \Illuminate\Support\Facades\Schema::hasTable(
                'translation_workbench_dynamic_values',
            );
            $sourceAbsolutePath = str_replace('\\', '/', base_path($entry->source_path));
            $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
            $sourceEditorLine = $entry->source_line ? ':' . $entry->source_line : '';
            $sourceEditorUrl =
                'vscode://vscode-remote/wsl+' .
                rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                $sourceEditorPath .
                $sourceEditorLine;
        @endphp

        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">
                <x-ui.headers.card
                    :title="__('Dynamic multi edit')"
                    :description="__('Edit multiple dynamic option values for the selected workbench entry.')"
                />

                <div class="mr-8 mt-2 flex items-center gap-2">
                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                        color="violet"
                    >
                        #{{ $entry->id }}
                    </flux:badge>

                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="save"
                        :aria-label="__('Save dynamic values')"
                        wire:click="saveDynamicTranslationValues"
                    />

                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-right"
                        :disabled="$nextDynamicTranslationEntryId === null"
                        :aria-label="__('Next dynamic multi entry')"
                        wire:click="openNextDynamicTranslationEntry"
                    />
                </div>
            </div>

            <div class="min-h-0 overflow-y-auto pr-2">
                <div class="grid gap-3 md:grid-cols-5">
                    <flux:callout
                        color="violet"
                        icon="braces"
                    >
                        <flux:callout.heading>{{ __('Type') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            <div class="flex flex-wrap gap-1.5">
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('Kind') }}: {{ str($entry->kind)->headline() }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('Is dynamic') }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="violet"
                                >
                                    {{ __('Is dynamic multi') }}
                                </flux:badge>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $entry->candidate_reason ?: __('No candidate reason') }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>

                    <flux:callout
                        color="emerald"
                        icon="activity"
                    >
                        <flux:callout.heading>{{ __('Status') }}</flux:callout.heading>
                        <flux:callout.text>
                            <flux:badge
                                size="sm"
                                :color="$entry->status === 'obsolete' ? 'zinc' : 'emerald'"
                            >
                                {{ str($entry->status)->headline() }}
                            </flux:badge>
                        </flux:callout.text>
                    </flux:callout>

                    <flux:callout
                        color="lime"
                        icon="badge-check"
                    >
                        <flux:callout.heading>{{ __('Review') }}</flux:callout.heading>
                        <flux:callout.text class="flex flex-wrap gap-1.5">
                            <flux:badge
                                size="sm"
                                :color="match ($entry->review_status) {
                                    'reviewed', 'approved' => 'emerald',
                                    'needs_attention' => 'amber',
                                    default => 'zinc',
                                }"
                            >
                                {{ str($entry->review_status)->headline() }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="violet"
                            >
                                {{ number_format($dynamicRows->count()) }} {{ __('values') }}
                            </flux:badge>
                        </flux:callout.text>
                    </flux:callout>

                    <flux:callout
                        color="violet"
                        icon="folder"
                    >
                        <flux:callout.heading>{{ __('Namespace') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-1 text-sm">
                            <div class="truncate font-mono text-xs">{{ $entry->namespace ?: '—' }}</div>
                            <div class="truncate font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $entry->group ?: '—' }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>

                    <flux:callout
                        color="amber"
                        icon="map-pin"
                    >
                        <flux:callout.heading>
                            <span class="flex w-full items-center justify-between gap-2">
                                <span>{{ __('Source') }}</span>
                                <flux:button
                                    class="h-5 w-5 shrink-0"
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    icon="external-link"
                                    icon:class="text-red-500 dark:text-red-400"
                                    :href="$sourceEditorUrl"
                                    :aria-label="__('Open source in VS Code')"
                                />
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="space-y-1 text-sm">
                            <div
                                class="wrap-anywhere text-wrap font-mono text-xs"
                                title="{{ $entry->source_path }}{{ $entry->source_line ? ':' . $entry->source_line : '' }}"
                            >
                                {{ $entry->source_path }}
                                {{ $entry->source_line ? ':' . $entry->source_line : '' }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ __('Scans') }} {{ number_format($entry->scan_count ?? 0) }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    <flux:card>
                        <x-ui.headers.card
                            :title="__('Translation key')"
                            :description="__('Read-only dynamic translation key metadata.')"
                        >
                            <flux:badge
                                size="sm"
                                color="violet"
                            >
                                {{ __('Dynamic multi') }}
                            </flux:badge>
                        </x-ui.headers.card>

                        <div class="break-all font-mono text-xs">{{ $entry->translation_key ?: '—' }}</div>
                    </flux:card>

                    <flux:card>
                        <x-ui.headers.card
                            :title="__('Warnings')"
                            :description="__('Non-blocking indicators for the current dynamic values.')"
                        />

                        <div class="flex flex-wrap gap-2">
                            @if ($dynamicRows->isEmpty())
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('No dynamic values') }}
                                </flux:badge>
                            @endif

                            @if (! $dynamicStorageAvailable)
                                <flux:badge
                                    size="sm"
                                    color="red"
                                >
                                    {{ __('Storage table missing') }}
                                </flux:badge>
                            @endif

                            @if ($missingSourceCount > 0)
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('Source empty') }}: {{ $missingSourceCount }}
                                </flux:badge>
                            @endif

                            @if ($missingTargetCount > 0)
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('Target empty') }}: {{ $missingTargetCount }}
                                </flux:badge>
                            @endif

                            @if ($identicalCount > 0)
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('Source equals target') }}: {{ $identicalCount }}
                                </flux:badge>
                            @endif

                            @if ($dynamicRows->isNotEmpty() && $missingSourceCount === 0 && $missingTargetCount === 0)
                                <flux:badge
                                    size="sm"
                                    color="emerald"
                                >
                                    {{ __('Complete') }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:card>
                </div>

                <flux:card class="mt-6">
                    <x-ui.headers.card
                        :title="__('Dynamic values')"
                        :description="__('Each option key can have its own source and target language value.')"
                    >
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded border border-zinc-700/60 px-1.5 py-1 dark:border-zinc-200/60">
                                <x-ui.locale.flag
                                    :locale="$sourceLocale"
                                    size="md"
                                    :title="strtoupper($sourceLocale)"
                                />
                                <span class="font-mono text-sm uppercase">{{ $sourceLocale }}</span>
                            </span>

                            <span class="h-4 w-px bg-zinc-200 dark:bg-zinc-700"></span>

                            <span class="inline-flex items-center gap-1 rounded border border-zinc-700/60 px-1.5 py-1 dark:border-zinc-200/60">
                                <x-ui.locale.flag
                                    :locale="$activeLocale"
                                    size="md"
                                    :title="strtoupper($activeLocale)"
                                />
                                <span class="font-mono text-sm uppercase">{{ $activeLocale }}</span>
                            </span>

                            <flux:button
                                class="ms-2 h-7 w-7"
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="plus"
                                :aria-label="__('Add dynamic value')"
                                wire:click="addDynamicValueRow"
                            />
                        </div>
                    </x-ui.headers.card>

                    <div class="space-y-3">
                        @forelse ($dynamicRows as $index => $row)
                            <div class="grid gap-3 rounded border border-zinc-200 p-3 dark:border-zinc-700 xl:grid-cols-[minmax(12rem,18rem)_1fr_1fr_auto]">
                                <flux:field>
                                    <flux:label>{{ __('Option key') }}</flux:label>
                                    <flux:input
                                        wire:model="dynamicValueKeys.{{ $index }}"
                                        placeholder="{{ __('value_key') }}"
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>
                                        <span class="inline-flex items-center gap-2">
                                            <x-ui.locale.flag
                                                :locale="$sourceLocale"
                                                size="md"
                                                :title="strtoupper($sourceLocale)"
                                            />
                                            <span>{{ __('Source') }}</span>
                                        </span>
                                    </flux:label>
                                    <flux:textarea
                                        rows="2"
                                        wire:model="dynamicSourceValues.{{ $index }}"
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>
                                        <span class="flex w-full items-center gap-2">
                                            <span class="inline-flex items-center gap-2">
                                                <x-ui.locale.flag
                                                    :locale="$activeLocale"
                                                    size="md"
                                                    :title="strtoupper($activeLocale)"
                                                />
                                                <span>{{ __('Target') }}</span>
                                            </span>

                                            <flux:button
                                                class="ms-auto h-6 w-6"
                                                type="button"
                                                size="xs"
                                                variant="ghost"
                                                icon="copy"
                                                :disabled="blank($dynamicSourceValues[$index] ?? null)"
                                                :aria-label="__('Copy source to target')"
                                                wire:click="copyDynamicSourceToTargetValue({{ $index }})"
                                            />
                                        </span>
                                    </flux:label>
                                    <flux:textarea
                                        id="translation-workbench-dynamic-target-{{ $index }}"
                                        rows="2"
                                        wire:model="dynamicTargetValues.{{ $index }}"
                                    />
                                </flux:field>

                                <div class="flex items-end justify-end">
                                    <flux:button
                                        class="h-8 w-8"
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        icon="trash"
                                        :aria-label="__('Remove dynamic value')"
                                        wire:click="removeDynamicValueRow({{ $index }})"
                                    />
                                </div>
                            </div>
                        @empty
                            <div class="rounded border border-dashed border-amber-300 p-6 text-center text-sm text-zinc-500 dark:border-amber-700 dark:text-zinc-400">
                                {{ __('No dynamic values are known for this entry yet.') }}
                            </div>
                        @endforelse
                    </div>
                </flux:card>
            </div>
        </div>
    @endif
</flux:modal>
