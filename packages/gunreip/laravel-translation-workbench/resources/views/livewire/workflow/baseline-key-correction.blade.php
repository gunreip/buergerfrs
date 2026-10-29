<div class="mt-4 space-y-3">
    <form
        class="flex flex-wrap items-end gap-3"
        wire:submit="checkBaselineProposal"
    >
        <flux:field>
            <flux:label>
                <flux:callout.heading>
                    {{ __('Proposed replacement key') }}
                </flux:callout.heading>
            </flux:label>
            <flux:callout.text>
                {{ __('Enter your own target key. This check does not save or publish changes.') }}
            </flux:callout.text>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.circle-dashed-check />
                </flux:input.group.prefix>
                <flux:input
                    class="min-w-96 flex-1"
                    clearable
                    wire:model.live.debounce.300ms="baselineProposedKey"
                />
                <flux:button
                    type="submit"
                    variant="outline"
                    icon="magnifying-glass"
                >
                    {{ __('Check proposal') }}
                </flux:button>
            </flux:input.group>
        </flux:field>
    </form>
    @if ($baselineCorrection ?? null)
        <flux:callout :color="$baselineCorrection['status'] === 'no_snapshot_conflict' ? 'green' : 'amber'">
            <flux:callout.heading>
                {{ match ($baselineCorrection['status']) {
                    'invalid' => __('Invalid target key structure'),
                    'unchanged' => __('The key is unchanged'),
                    'conflict' => __('The target conflicts with exported keys'),
                    'no_snapshot_conflict' => __('No conflict found in this snapshot'),
                    default => __('No correction preview available'),
                } }}
            </flux:callout.heading>
            <flux:callout.text>
                <code class="break-all">{{ $baselineCorrection['source'] }}</code>
                → <code class="break-all">{{ $baselineCorrection['target'] }}</code>
            </flux:callout.text>
            @if ($baselineCorrection['status'] === 'invalid')
                <flux:callout.text>
                    {{ __('Use a file group and at least two nested key segments. Each segment may contain letters, digits, underscores or hyphens.') }}
                </flux:callout.text>
            @elseif ($baselineCorrection['status'] === 'unchanged')
                <flux:callout.text>{{ __('The existing structural block remains unresolved.') }}</flux:callout.text>
            @endif
            @if ($baselineCorrection['conflicts'])
                <div class="overflow-x-auto rounded rounded-t-lg">
                    <flux:table container:class="max-h-64">
                        <flux:table.columns
                            class="rounded rounded-t-lg bg-white dark:bg-zinc-800"
                            sticky
                        >
                            <flux:table.column>{{ __('Existing key') }}</flux:table.column>
                            <flux:table.column>{{ __('Conflict') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($baselineCorrection['conflicts'] as $conflict)
                                <flux:table.row :key="'proposal-conflict-'.$conflict['key']">
                                    <flux:table.cell class="whitespace-normal break-all">
                                        <code>{{ $conflict['key'] }}</code>
                                    </flux:table.cell>
                                    <flux:table.cell class="whitespace-normal">
                                        {{ match ($conflict['relation']) {
                                            'occupied' => __('The target key already exists; assignment or consolidation needs a separate decision.'),
                                            'scalar_parent' => __('An existing scalar value occupies a parent path of the target.'),
                                            'array_parent' => __('The target would replace an existing array path with a scalar value.'),
                                        } }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            @endif
            <flux:callout.text>
                {{ __('Captured source occurrences') }}:
                {{ $baselineCorrection['source_count'] ?? __('Not included in this snapshot') }} ·
                {{ __('Published locales included in the preview') }}:
                {{ implode(', ', $baselineCorrection['locales']) ?: '—' }}
            </flux:callout.text>
            <flux:callout.text>
                {{ __('This is a proposal against exported evidence only. The current source files, language files and database state must be checked before applying any change. Raw evidence, findings, key assignments and published values remain unchanged.') }}
            </flux:callout.text>
        </flux:callout>
        @include('translation-workbench::livewire.workflow.baseline-file-freshness')
        @if ($baselineCorrection['status'] === 'no_snapshot_conflict')
            @include('translation-workbench::livewire.workflow.baseline-proposed-source-changes')
        @endif
    @endif
</div>
