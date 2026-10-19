@if ($storedPublicationComparison ?? null)
    <flux:callout>
        <flux:callout.heading>{{ __('Stored and published values') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Read-only snapshot of stored values and language files. Unsaved input and runtime fallbacks are not included. Refresh this comparison after changes. Nothing is approved or published.') }}</flux:callout.text>
        <flux:text class="break-all font-mono">{{ implode(', ', $storedPublicationComparison['keys']) }}</flux:text>
        <flux:text>{{ __('Unchanged values: :count', ['count' => $storedPublicationComparison['values_unchanged']]) }}</flux:text>
        @forelse ($storedPublicationComparison['plans'] as $plan)
            <flux:heading class="mt-3 break-all font-mono" size="sm">{{ $plan['path'] }}</flux:heading>
            @foreach ($plan['changes'] as $change)
                <flux:text class="break-all font-mono">{{ $change['translation_key'] }}</flux:text>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <flux:heading size="sm">{{ __('Published value') }}</flux:heading>
                        <flux:text class="whitespace-pre-wrap break-words">{{ ($change['state'] ?? null) === 'new' && $change['old_value'] === null ? __('Not present') : (is_string($change['old_value']) ? $change['old_value'] : json_encode($change['old_value'], JSON_UNESCAPED_UNICODE)) }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm">{{ __('Stored value') }}</flux:heading>
                        <flux:text class="whitespace-pre-wrap break-words">{{ is_string($change['new_value']) ? $change['new_value'] : json_encode($change['new_value'], JSON_UNESCAPED_UNICODE) }}</flux:text>
                    </div>
                </div>
            @endforeach
            @foreach ($plan['conflicts'] as $conflict)
                <flux:text class="text-red-600!">{{ __('Structure conflict') }}: {{ $conflict['translation_key'] }}</flux:text>
            @endforeach
            @foreach ($plan['pruned'] as $removed)
                <flux:text class="text-red-600!">{{ __('Deletion requires separate review; nothing is removed.') }} {{ $removed['translation_key'] }}</flux:text>
            @endforeach
        @empty
            <flux:text>{{ __('No eligible stored values or language-file entries found for this selection.') }}</flux:text>
        @endforelse
    </flux:callout>
@endif
