<flux:callout
    class="my-4"
    icon="gavel"
    color="amber"
>

    <flux:field>
        @isset($reasonLocaleField)
            <div class="flex items-center justify-between gap-3">
                <flux:label>
                    <flux:callout.text>
                        {{ $reasonLabel }}
                    </flux:callout.text>
                </flux:label>
                <x-translation-workbench::ui.common.input-language-selector
                    class="shrink-0"
                    wire:model.live="{{ $reasonLocaleField }}"
                    :target="$this->reasonTargetLocale()"
                    :countries="$translationFlagCountries ?? []"
                    :focus="$reasonSearchId ?? null"
                />
            </div>
        @else
            <flux:label>
                <flux:callout.text>
                    {{ $reasonLabel }}
                </flux:callout.text>
            </flux:label>
        @endisset
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.list />
            </flux:input.group.prefix>
            <flux:select
                variant="listbox"
                searchable
                clearable
                wire:model="{{ $reasonField }}"
                x-data="{ reasonSearch: '' }"
                :placeholder="__('Choose a reason')"
            >
                <x-slot name="search">
                    <flux:select.search
                        x-model="reasonSearch"
                        :id="$reasonSearchId ?? null"
                        :lang="$reasonInputLocale ?? null"
                        :spellcheck="isset($reasonInputLocale) ? 'true' : null"
                        wire:key="reason-search-{{ $reasonField }}-{{ $reasonInputLocale ?? 'default' }}"
                    />
                </x-slot>
                <flux:select.option.create
                    x-on:click="$wire.openWorkflowReason({{ \Illuminate\Support\Js::from($reasonField) }}, reasonSearch)"
                >
                    {{ __('Create a reason') }}
                </flux:select.option.create>
                @foreach ($this->workflowReasonOptions($reasonField) as $reasonOption)
                    <flux:select.option :value="$reasonOption">
                        <span class="wrap-break-word whitespace-normal">{{ $reasonOption }}</span>
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>

        <flux:callout.text>
            {{ $reasonHint ?? __('This decision is recorded with your user and reason. It does not write language files or source code.') }}
        </flux:callout.text>

        <flux:error :name="$reasonField" />

    </flux:field>
</flux:callout>
