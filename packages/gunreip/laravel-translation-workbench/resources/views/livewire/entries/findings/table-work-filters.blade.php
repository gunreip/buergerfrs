{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-filters.blade.php --}}

@php
    $sourceLiteralLocaleLabel = strtoupper((string) ($sourceMainLocale ?? 'en'));
    $targetLiteralLocaleLabel = strtoupper((string) ($targetMainLocale ?? app()->getLocale()));
@endphp

{{-- Search Filters for Findings --}}
    <div class="grid w-full grid-cols-5 gap-3">
        {{-- Search Field --}}
        <flux:field class="col-span-2">
            {{-- Search Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Search') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Search')"
                        :text="__('Searches visible text context such as source path, scanned literal, suggested keys and translation keys. IDs are filtered with dedicated ID filters where available.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass />
                </flux:input.group.prefix>
                <flux:input
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="findingSearch"
                    placeholder="{{ __('Path, literal or key') }}"
                />
                <flux:input.group.suffix>
                    <div class="flex items-center gap-1">
                        <x-ui.tooltip.simple
                            :title="__('Remove first key segment')"
                            :text="__('Removes the first dot-separated segment from the current search text. Useful when export-report keys differ from work-finding keys in their leading namespace/path segments.')"
                        >
                            <flux:button
                                class="h-6 w-6"
                                type="button"
                                size="sm"
                                variant="subtle"
                                color="zinc"
                                icon="minus"
                                :disabled="!str_contains($findingSearch, '.')"
                                :aria-label="__('Remove first key segment')"
                                wire:click="reduceFindingSearchFirstSegment"
                            />
                        </x-ui.tooltip.simple>

                        <x-ui.tooltip.simple
                            :title="__('Exact search')"
                            :text="$findingSearchExact
                                ? __('Search must match the complete value.')
                                : __('Search matches values that contain the entered text.')"
                        >
                            <flux:button
                                class="h-6 w-6"
                                type="button"
                                size="sm"
                                variant="{{ $findingSearchExact ? 'primary' : 'subtle' }}"
                                color="{{ $findingSearchExact ? 'green' : 'red' }}"
                                icon="circle-dot"
                                :aria-label="__('Toggle exact search')"
                                wire:click="toggleFindingSearchExact"
                            />
                        </x-ui.tooltip.simple>

                        <x-ui.tooltip.simple
                            :title="__('Case-sensitive search')"
                            :text="$findingSearchCaseSensitive
                                ? __('Uppercase and lowercase letters must match exactly.')
                                : __('Uppercase and lowercase letters are ignored.')"
                        >
                            <flux:button
                                class="h-6 w-6"
                                type="button"
                                size="sm"
                                variant="{{ $findingSearchCaseSensitive ? 'primary' : 'subtle' }}"
                                color="{{ $findingSearchCaseSensitive ? 'green' : 'red' }}"
                                icon="case-sensitive"
                                :aria-label="__('Toggle case-sensitive search')"
                                wire:click="toggleFindingSearchCaseSensitive"
                            />
                        </x-ui.tooltip.simple>
                    </div>
                </flux:input.group.suffix>
            </flux:input.group>
        </flux:field>

        {{-- Status Field --}}
        <flux:field class="col-span-1">
            {{-- Status Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Status') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Finding status')"
                        :text="__('Filters scanner lifecycle state: active findings are still present, obsolete findings remain visible for audit and history when explicitly selected.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.pencil-sparkles />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingStatus"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.pencil-sparkles
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingStatusOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.pencil-sparkles
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Kind Field --}}
        <flux:field class="col-span-1">
            {{-- Kind Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Kind') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Scanner kind')"
                        :text="__('Technical scanner classification of the code occurrence, for example literal, key, dynamic values or numeric dynamic.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingKind"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.tag
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingKindOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.tag
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Candidate Field --}}
        <flux:field class="col-span-1">
            {{-- Candidate Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.candidate') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Candidate state')"
                        :text="__('Filters scanner suggestions and reviewed states such as UI candidate, dynamic values candidate, confirmed UI, dynamic values or dynamic option lists.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.shield-cog />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingCandidateType"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.shield-cog
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingCandidateTypeOptions as $option)
                        <flux:select.option value="{{ $option['value'] }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.shield-cog
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option['label'] }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Namespace Filter --}}
        <flux:field class="col-span-1">
            {{-- Namespace Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Namespace') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation namespace')"
                        :text="__('Top-level lang file namespace derived from the suggested or reviewed translation key, for example management, ui or dynamic.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.folder-tree />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingNamespace"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.folder-tree
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingNamespaceOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.folder-tree
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Group Filter --}}
        <flux:field class="col-span-1">
            {{-- Group Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Group') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation group')"
                        :text="__('Second key segment below the namespace. Group options are scoped by the selected namespace when possible.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingGroup"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.tag
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    @foreach ($findingGroupOptions as $option)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.tag
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $option }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Translation Key Field --}}
        <flux:field class="col-span-1">
            {{-- Translation Key Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Translation key') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation key state')"
                        :text="__('Filters whether a reviewed workbench translation key is set. Shared candidates keeps repeated literals visible for the bulk equalize workflow, even when some entries already have a translation key.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.key />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingKeyRelation"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.key
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="linked">
                        <div class="flex items-center gap-2">
                            <flux:icon.link
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Set') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="missing">
                        <div class="flex items-center gap-2">
                            <flux:icon.key
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Missing') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="shared_candidates">
                        <div class="flex items-center gap-2">
                            <flux:icon.git-merge
                                class="text-amber-400"
                                variant="mini"
                            />
                            {{ __('Shared candidates all') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="shared_candidates_open">
                        <div class="flex items-center gap-2">
                            <flux:icon.git-merge
                                class="text-amber-400"
                                variant="mini"
                            />
                            {{ __('Shared candidates open') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="shared_candidates_done">
                        <div class="flex items-center gap-2">
                            <flux:icon.git-merge
                                class="text-green-400"
                                variant="mini"
                            />
                            {{ __('Shared candidates done') }}
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Literal State Field --}}
        <flux:field class="col-span-1">
            {{-- Literal State Filter --}}
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Literal state') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Language literal state')"
                        :text="__('Filters stored language values for the source language and active target language, for example Source EN literal missing or Target DE literal available.')"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.languages />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="findingLiteralState"
                    variant="listbox"
                    searchable
                >
                    <flux:select.option value="all">
                        <div class="flex items-center gap-2">
                            <flux:icon.languages
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('ui.states.all') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="source_available">
                        <div class="flex items-center gap-2">
                            <flux:icon.check-circle
                                class="text-green-500"
                                variant="mini"
                            />
                            {{ __('Source :locale literal available', ['locale' => $sourceLiteralLocaleLabel]) }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="source_missing">
                        <div class="flex items-center gap-2">
                            <flux:icon.triangle-alert
                                class="text-red-500"
                                variant="mini"
                            />
                            {{ __('Source :locale literal missing', ['locale' => $sourceLiteralLocaleLabel]) }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="target_available">
                        <div class="flex items-center gap-2">
                            <flux:icon.check-circle
                                class="text-green-500"
                                variant="mini"
                            />
                            {{ __('Target :locale literal available', ['locale' => $targetLiteralLocaleLabel]) }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="target_missing">
                        <div class="flex items-center gap-2">
                            <flux:icon.triangle-alert
                                class="text-red-500"
                                variant="mini"
                            />
                            {{ __('Target :locale literal missing', ['locale' => $targetLiteralLocaleLabel]) }}
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- PerPage Selector Buttons --}}
        <x-ui.table.per-page-selector
            id="translation-workbench-findings-per-page"
            name="translation-workbench-findings-per-page"
            model="perPage"
        />
    </div>
