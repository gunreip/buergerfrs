{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-scalar-transform-translation-key.blade.php --}}

@php
    $scalarTransformRows = collect($scalarTransformContext['rows'] ?? []);
    $invalidCount = (int) ($scalarTransformContext['invalid_count'] ?? 0);
    $duplicateTargetCount = (int) ($scalarTransformContext['duplicate_target_count'] ?? 0);
    $conflictCount = (int) ($scalarTransformContext['conflict_count'] ?? 0);
    $includedCount = (int) ($scalarTransformContext['included_count'] ?? 0);
    $targetPath = $scalarTransformContext['target_path'] ?? null;
    $canTransform = $includedCount > 0 && $invalidCount === 0 && $duplicateTargetCount === 0 && $conflictCount === 0;
    $transformStateText = match (true) {
        $canTransform => __('Ready to transform included scalar keys.'),
        $includedCount === 0 => __('Include at least one selected scalar key before transforming.'),
        default => __('Resolve invalid targets, duplicate targets or conflicts first.'),
    };
@endphp

{{-- Modal Transform Scalar Keys --}}
<flux:modal
    class="w-full max-w-7xl"
    name="translation-workbench-scalar-transform-translation-key"
    wire:model.self="scalarTransformModalOpen"
>
    <div class="flex h-[calc(100vh-8rem)] max-h-[calc(100vh-8rem)] flex-col gap-4 overflow-hidden">
        {{-- Card Transform Scalar Keys --}}
        <x-ui.headers.card
            :title="__('Transform scalar keys')"
            :description="__('Review selected scalar translation keys and move them to explicit array-style keys.')"
        >
            <div class="mr-8 flex flex-wrap items-center gap-2">
                <flux:badge
                    size="sm"
                    color="{{ $scalarTransformRows->isNotEmpty() ? 'amber' : 'zinc' }}"
                >
                    {{ __('Selected') }}: {{ number_format($scalarTransformRows->count()) }}
                </flux:badge>

                <flux:badge
                    size="sm"
                    color="{{ $includedCount > 0 ? 'sky' : 'red' }}"
                >
                    {{ __('Included') }}: {{ number_format($includedCount) }}
                </flux:badge>

                @if ($invalidCount > 0)
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Invalid targets') }}: {{ number_format($invalidCount) }}
                    </flux:badge>
                @endif

                @if ($duplicateTargetCount > 0)
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Duplicate targets') }}: {{ number_format($duplicateTargetCount) }}
                    </flux:badge>
                @endif

                @if ($conflictCount > 0)
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Conflicts') }}: {{ number_format($conflictCount) }}
                    </flux:badge>
                @endif
            </div>
        </x-ui.headers.card>

        <div class="grid shrink-0 grid-cols-2 gap-3">
            <flux:callout
                color="sky"
                icon="key-round"
                heading="{{ __('Current scalar keys') }}"
                text="{{ __('The current keys are scalar lang-file entries. They can block deeper array keys with the same path prefix.') }}"
            />

            <flux:callout
                color="{{ $canTransform ? 'green' : 'red' }}"
                icon="{{ $canTransform ? 'circle-check' : 'circle-alert' }}"
                heading="{{ __('Transform state') }}"
                text="{{ $transformStateText }}"
            >
                <flux:callout.heading>{{ __('Current target path') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="wrap-anywhere font-mono">
                        {{ $targetPath ?: __('Empty') }}
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="amber"
                icon="git-branch"
                heading="{{ __('Target array keys') }}"
            >
                <flux:callout.text>
                    <div class="space-y-3">
                        <div>
                            {{ __('Enter the target path once. The final target key is built from target path plus the last segment of each current key.') }}
                        </div>
                        <flux:field>
                            <flux:label>{{ __('Target path') }}</flux:label>
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <flux:icon.folder-tree />
                                </flux:input.group.prefix>
                                <flux:input
                                    class="font-mono text-xs"
                                    clearable
                                    copyable
                                    wire:model.live.debounce.300ms="scalarTransformTargetPath"
                                    placeholder="e.g. ui.states"
                                />
                            </flux:input.group>
                        </flux:field>
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>

        <div class="min-h-0 flex-1">
            <flux:table container:class="max-h-full overflow-auto">
                <flux:table.columns
                    class="rounded-t-lg bg-white dark:bg-zinc-800"
                    sticky
                >
                    <flux:table.column class="w-20 rounded-tl-lg">{{ __('Include') }}</flux:table.column>
                    <flux:table.column class="w-24">{{ __('ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Current key') }}</flux:table.column>
                    <flux:table.column>{{ __('Target key') }}</flux:table.column>
                    <flux:table.column>{{ __('Values') }}</flux:table.column>
                    <flux:table.column class="w-44 rounded-tr-lg">{{ __('State') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($scalarTransformRows as $row)
                        @php
                            $isIncluded = (bool) ($row['is_included'] ?? false);
                        @endphp

                        <flux:table.row
                            class="{{ $isIncluded ? '' : 'opacity-60' }}"
                            wire:key="translation-workbench-scalar-transform-row-{{ $row['id'] }}"
                        >
                            <flux:table.cell>
                                <flux:checkbox
                                    value="{{ $row['id'] }}"
                                    wire:model.live="scalarTransformIncludedLangValueIds"
                                />
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm">LV#{{ $row['id'] }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="space-y-1">
                                    <div class="wrap-anywhere text-wrap font-mono text-xs">
                                        {{ $row['translation_key'] }}
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <flux:badge size="sm">{{ $row['namespace'] ?: __('No namespace') }}
                                        </flux:badge>
                                        <flux:badge
                                            size="sm"
                                            color="{{ $row['risk'] === 'high' ? 'amber' : 'zinc' }}"
                                        >
                                            {{ $row['risk'] === 'high' ? __('High risk') : __('Medium risk') }}
                                        </flux:badge>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="space-y-1">
                                    <div class="wrap-anywhere text-wrap font-mono text-xs">
                                        {{ $row['target_translation_key'] ?: __('No target key') }}
                                    </div>
                                    <div class="wrap-anywhere text-wrap text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ __('Last segment') }}: <span
                                            class="font-mono">{{ $row['leaf'] ?: __('None') }}</span>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="space-y-1">
                                    <div class="flex items-start gap-2">
                                        <x-ui.locale.flag
                                            :locale="$row['source_locale']"
                                            size="sm"
                                        />
                                        <span class="wrap-anywhere max-w-md text-wrap text-sm">
                                            {{ str($row['source_value'])->limit(90) }}
                                        </span>
                                    </div>

                                    @if ($row['target_locale'] !== '')
                                        <div class="flex items-start gap-2 text-zinc-500 dark:text-zinc-400">
                                            <x-ui.locale.flag
                                                :locale="$row['target_locale']"
                                                size="sm"
                                            />
                                            <span class="wrap-anywhere max-w-md text-wrap text-sm">
                                                {{ str($row['target_value'])->limit(90) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-col items-start gap-1">
                                    @if (!$isIncluded)
                                        <flux:badge
                                            size="sm"
                                            color="zinc"
                                        >
                                            {{ __('Excluded') }}
                                        </flux:badge>
                                    @elseif ($row['target_is_valid'])
                                        <flux:badge
                                            size="sm"
                                            color="green"
                                        >
                                            {{ __('Valid target') }}
                                        </flux:badge>
                                    @else
                                        <x-ui.tooltip.simple
                                            :title="__('Invalid target key')"
                                            :text="__(
                                                'Target keys must be dot-separated, lowercase translation keys with at least three segments.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                color="red"
                                            >
                                                {{ __('Invalid target') }}
                                            </flux:badge>
                                        </x-ui.tooltip.simple>
                                    @endif

                                    @if ($isIncluded && $row['target_is_duplicate'])
                                        <flux:badge
                                            size="sm"
                                            color="red"
                                        >
                                            {{ __('Duplicate target') }}
                                        </flux:badge>
                                    @endif

                                    @if ($isIncluded && count($row['target_conflicts'] ?? []) > 0)
                                        <x-ui.tooltip.simple :title="__('Existing target values')">
                                            <flux:badge
                                                size="sm"
                                                color="red"
                                            >
                                                {{ __('Conflict') }}: {{ count($row['target_conflicts']) }}
                                            </flux:badge>

                                            <x-slot:content>
                                                <div class="space-y-1">
                                                    @foreach ($row['target_conflicts'] as $conflict)
                                                        <div class="grid grid-cols-[auto_1fr] gap-2 text-xs">
                                                            <x-ui.locale.flag
                                                                :locale="$conflict['locale']"
                                                                size="sm"
                                                            />
                                                            <span class="wrap-anywhere">
                                                                <span class="font-mono">#{{ $conflict['id'] }}</span>
                                                                <span
                                                                    class="text-zinc-400">({{ $conflict['status'] }})</span>
                                                                {{ $conflict['value'] }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </x-slot:content>
                                        </x-ui.tooltip.simple>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <flux:text class="text-sm text-zinc-500">
                                    {{ __('No scalar keys selected.') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="flex shrink-0 items-center justify-end gap-2">
            <flux:button
                type="button"
                variant="subtle"
                color="zinc"
                wire:click="closeScalarTransformModal"
            >
                {{ __('ui.button.cancel') }}
            </flux:button>

            <flux:button
                type="button"
                variant="primary"
                color="amber"
                icon="git-branch"
                :disabled="!$canTransform"
                wire:click="confirmScalarTransformToArray"
            >
                {{ __('Transform keys') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
