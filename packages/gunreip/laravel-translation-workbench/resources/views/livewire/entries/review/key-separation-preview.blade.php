<flux:callout color="amber" class="mb-3">
    <flux:callout.heading>{{ __('Separate translation task') }}</flux:callout.heading>
    <flux:callout.text>{{ __('Key #:id would become an independent task. This affects all its active finding assignments, not only the opened finding.', ['id' => $separation['key_id']]) }}</flux:callout.text>
    <dl class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-[auto_1fr]">
        <dt>{{ __('Saved translation key') }}</dt><dd class="break-all font-mono">{{ $separation['source'] }}</dd>
        <dt>{{ __('Entered translation key') }}</dt><dd class="break-all font-mono">{{ $separation['target'] }}</dd>
        <dt>{{ __('Keys remaining on the saved translation key') }}</dt>
        <dd>{{ implode(', ', array_map(fn ($id) => '#'.$id, $separation['remaining_owner_ids'])) }}</dd>
    </dl>
    <flux:table container:class="mt-3 max-h-64 overflow-auto">
        <flux:table.columns>
            <flux:table.column>{{ __('Finding') }}</flux:table.column>
            <flux:table.column>{{ __('Source') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($separation['findings'] as $row)
                <flux:table.row>
                    <flux:table.cell>#{{ $row['id'] }}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal break-all">{{ $row['path'] }}:{{ $row['source_line'] }}</flux:table.cell>
                    <flux:table.cell>{{ __($row['status']) }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <flux:callout.text class="mt-3">{{ __('Stored values on the saved key: :count. They are shared by key text and must remain available to the other tasks. Copying them to the new task would require a separate decision and review.', ['count' => count($separation['shared_values'])]) }}</flux:callout.text>
    @foreach ($separation['issues'] as $issue)
        <flux:callout.text class="text-red-600 dark:text-red-400">{{ $issue }}</flux:callout.text>
    @endforeach
    <flux:callout.text>{{ __('Only the selected key record and its assignments are separated. Shared values remain at the old key; no values are copied, no files are changed and nothing is published. The new task needs its own translations and review.') }}</flux:callout.text>
    @if ($separation['proof'] ?? null)
        <flux:button class="mt-3" variant="outline" wire:click="separateTranslationTask" wire:loading.attr="disabled" wire:target="separateTranslationTask"
            wire:confirm="{{ __('Separate key #:id as :target without copying shared translation values?', ['id' => $separation['key_id'], 'target' => $separation['target']]) }}">
            {{ __('Separate without copying values') }}
        </flux:button>
    @endif
    <flux:error name="translationKeyValue" />
</flux:callout>
