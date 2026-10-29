<flux:table container:class="max-h-96 overflow-auto">
    <flux:table.columns>
        <flux:table.column>{{ __('Select') }}</flux:table.column>
        <flux:table.column>{{ __('Language / Revision') }}</flux:table.column>
        <flux:table.column>{{ __('Saved text') }}</flux:table.column>
        <flux:table.column>{{ __('Status') }}</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
        @foreach ($languageChoices as $code => $choice)
            <flux:table.row :key="$languageSelection.'-'.$code">
                <flux:table.cell>
                    <flux:checkbox wire:model.live="{{ $languageSelection }}" :value="$code"
                        :disabled="! $choice['unchanged'] || ($languageApprovalRequired && ! $choice['approved'])"
                        :aria-label="__('Select :locale', ['locale' => $code])" />
                </flux:table.cell>
                <flux:table.cell>
                    <div class="flex items-center gap-2">
                        @if ($translationFlagCountries[$code] ?? null)
                            <flux:flag :country="$translationFlagCountries[$code]" size="xs" circle />
                        @endif
                        {{ $code }} · #{{ $choice['revision'] }}
                    </div>
                </flux:table.cell>
                <flux:table.cell class="max-w-lg whitespace-pre-wrap break-words">{{ $choice['value'] }}</flux:table.cell>
                <flux:table.cell>
                    <flux:badge size="sm" :color="$choice['unchanged'] ? ($choice['approved'] ? 'green' : 'amber') : 'red'">
                        {{ ! $choice['unchanged'] ? __('Unsaved changes or changed revision') : ($choice['approved'] ? __('Approved') : __('Draft')) }}
                    </flux:badge>
                </flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
