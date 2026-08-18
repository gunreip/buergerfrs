{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/lang-cleanup/filters.blade.php --}}

<div class="space-y-3">
    <flux:separator text="{{ __('Cleanup filters') }}" />

    <div class="grid w-full grid-cols-10 gap-3">
        <flux:field class="col-span-4 md:col-span-4">
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Search') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Cleanup search')"
                        :text="__(
                            'Searches translation key, namespace, group and key type inside the cleanup inventory. The listed rows remain limited to lang cleanup candidates.',
                        )"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass />
                </flux:input.group.prefix>
                <flux:input
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="langCleanupSearch"
                    placeholder="{{ __('Translation key, namespace, group or type') }}"
                />
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-10 md:col-span-2">
            <flux:label>{{ __('Namespace') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.folder-tree />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="langCleanupNamespace"
                    variant="listbox"
                    searchable
                >
                    <x-ui.input.select-option
                        value="all"
                        icon="asterisk"
                        icon-class="text-sky-400"
                    >
                        {{ __('All namespaces') }}
                    </x-ui.input.select-option>
                    @foreach ($langCleanupNamespaceOptions as $option)
                        <x-ui.input.select-option
                            value="{{ $option }}"
                            icon="folder-tree"
                        >
                            {{ $option === 'NULL' ? __('No namespace') : $option }}
                        </x-ui.input.select-option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-10 md:col-span-2">
            <flux:label>{{ __('Group') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.folder />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="langCleanupGroup"
                    variant="listbox"
                    searchable
                >
                    <x-ui.input.select-option
                        value="all"
                        icon="asterisk"
                        icon-class="text-sky-400"
                    >
                        {{ __('All groups') }}
                    </x-ui.input.select-option>
                    @foreach ($langCleanupGroupOptions as $option)
                        <x-ui.input.select-option
                            value="{{ $option }}"
                            icon="folder"
                        >
                            {{ $option === 'NULL' ? __('No group') : $option }}
                        </x-ui.input.select-option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-10 md:col-span-2">
            <flux:label>{{ __('Key type') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tags />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="langCleanupKeyType"
                    variant="listbox"
                    searchable
                >
                    <x-ui.input.select-option
                        value="all"
                        icon="asterisk"
                        icon-class="text-sky-400"
                    >
                        {{ __('All key types') }}
                    </x-ui.input.select-option>
                    @foreach ($langCleanupKeyTypeOptions as $option)
                        <x-ui.input.select-option
                            value="{{ $option }}"
                            icon="tag"
                        >
                            {{ $option === 'NULL' ? __('No key type') : $option }}
                        </x-ui.input.select-option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-10 md:col-span-2">
            <flux:label>{{ __('Context') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.list-filter />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="langCleanupContext"
                    variant="listbox"
                >
                    <x-ui.input.select-option
                        value="all"
                        icon="asterisk"
                        icon-class="text-sky-400"
                    >{{ __('All context') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="orphaned"
                        icon="unlink"
                    >{{ __('Orphaned') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="shared"
                        icon="combine"
                    >{{ __('Shared') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="ui"
                        icon="panel-top"
                    >{{ __('UI') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="dynamic"
                        icon="zap"
                    >{{ __('Dynamic') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="dynamic_multi"
                        icon="list-checks"
                    >{{ __('Dynamic multi') }}</x-ui.input.select-option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-10 md:col-span-3">
            <flux:label>{{ __('Usage') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.activity />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="langCleanupUsage"
                    variant="listbox"
                >
                    <x-ui.input.select-option
                        value="all"
                        icon="asterisk"
                        icon-class="text-sky-400"
                    >{{ __('All usage states') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="no_active"
                        icon="check"
                    >{{ __('No active code usage') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="commented"
                        icon="message-square-text"
                    >{{ __('Commented-out usage') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="obsolete"
                        icon="archive"
                    >{{ __('Obsolete code usage') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="relations"
                        icon="link"
                    >{{ __('Active key relations') }}</x-ui.input.select-option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="col-span-10 md:col-span-3">
            <flux:label>{{ __('Values') }}</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.languages />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="langCleanupValueState"
                    variant="listbox"
                >
                    <x-ui.input.select-option
                        value="all"
                        icon="asterisk"
                        icon-class="text-sky-400"
                    >{{ __('All value states') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="lang_values"
                        icon="languages"
                    >{{ __('Has lang values') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="source_missing"
                        icon="circle-alert"
                    >{{ __('Source value missing') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="target_missing"
                        icon="circle-alert"
                    >{{ __('Target value missing') }}</x-ui.input.select-option>
                    <x-ui.input.select-option
                        value="source_target_available"
                        icon="badge-check"
                    >{{ __('Source and target available') }}</x-ui.input.select-option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        <div class="col-span-10 flex items-end justify-end md:col-span-2">
            <x-ui.table.per-page-selector
                id="translation-workbench-lang-cleanup-per-page"
                name="translation-workbench-lang-cleanup-per-page"
                label="{{ __('Per page') }}"
            />
        </div>
    </div>
</div>
