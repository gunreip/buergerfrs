<flux:field>
    <flux:label>{{ $reasonLabel }}</flux:label>
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
        <flux:select.option.create x-on:click="$wire.openWorkflowReason({{ \Illuminate\Support\Js::from($reasonField) }}, reasonSearch)">
            {{ __('Create a reason') }}
        </flux:select.option.create>
        @foreach ($this->workflowReasonOptions($reasonField) as $reasonOption)
            <flux:select.option :value="$reasonOption">
                <span class="wrap-break-word whitespace-normal">{{ $reasonOption }}</span>
            </flux:select.option>
        @endforeach
    </flux:select>
    <flux:error :name="$reasonField" />
</flux:field>
