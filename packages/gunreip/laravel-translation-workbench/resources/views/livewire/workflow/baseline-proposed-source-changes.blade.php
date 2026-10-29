<flux:accordion>
    <flux:accordion.item>
        <flux:accordion.heading>{{ __('Source changes for your proposed key') }}</flux:accordion.heading>
        <flux:accordion.content>
            <div class="space-y-3">
                <flux:callout.text>{{ __('Select a recorded file to preview your entered key against its current contents. This does not save files, change findings or approve publication.') }}</flux:callout.text>
                <flux:button variant="outline" wire:click="checkBaselineProposedChanges" wire:loading.attr="disabled">
                    {{ __('Check all proposed changes') }}
                    <flux:icon.loading class="size-4" wire:loading wire:target="checkBaselineProposedChanges" />
                </flux:button>
                @if ($baselineProposedChanges)
                    <flux:callout :color="$baselineProposedChanges['status'] === 'preview' ? 'sky' : 'amber'">
                        <flux:callout.heading>
                            {{ $baselineProposedChanges['status'] === 'preview'
                                ? __('Complete source preview verified')
                                : __('The complete proposal could not be verified') }}
                        </flux:callout.heading>
                        <flux:callout.text>
                            {{ __('Verified source files') }}: {{ count($baselineProposedChanges['files']) }} ·
                            {{ __('Verified source occurrences') }}: {{ $baselineProposedChanges['finding_count'] }} ·
                            {{ __('Checked at') }}: {{ $baselineProposedChanges['checked_at'] }}
                        </flux:callout.text>
                        <flux:callout.text>{{ __('This is the recorded check result, not a live approval. Database assignments have not been checked. Files and assignments must be verified again before any application.') }}</flux:callout.text>
                        @if ($baselineProposedChanges['fingerprint'])
                            <flux:callout.text>{{ __('Proposal fingerprint') }}: <code class="break-all">{{ $baselineProposedChanges['fingerprint'] }}</code></flux:callout.text>
                        @elseif ($baselineProposedChanges['status'] === 'source_evidence_incomplete')
                            <flux:callout.text>{{ __('Source evidence is missing or contains duplicate finding IDs. No complete proposal was produced.') }}</flux:callout.text>
                        @elseif ($baselineProposedChanges['status'] === 'proposal_required')
                            <flux:callout.text>{{ __('Check a valid, conflict-free proposal first.') }}</flux:callout.text>
                        @endif
                        @if ($baselineProposedChanges['issues'])
                            <div class="max-h-64 overflow-auto">
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Kind') }}</flux:table.column>
                                        <flux:table.column>{{ __('File') }}</flux:table.column>
                                        <flux:table.column>{{ __('Reason') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        @foreach ($baselineProposedChanges['issues'] as $proposalIssue)
                                            <flux:table.row>
                                                <flux:table.cell>{{ $proposalIssue['kind'] === 'source' ? __('Source') : __('Language file') }}</flux:table.cell>
                                                <flux:table.cell class="whitespace-normal break-all font-mono">{{ $proposalIssue['path'] }}</flux:table.cell>
                                                <flux:table.cell class="whitespace-normal">
                                                    {{ match ($proposalIssue['status']) {
                                                        'changed' => __('Changed since recording'),
                                                        'missing' => __('Missing file'),
                                                        'unreadable' => __('Unreadable file'),
                                                        'outside_root' => __('Outside project root'),
                                                        'conflicting_evidence' => __('Conflicting checksum evidence'),
                                                        'missing_evidence' => __('Missing checksum evidence'),
                                                        default => __('Captured source occurrence could not be verified'),
                                                    } }}
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                        @endif
                    </flux:callout>
                @endif
                <flux:select variant="listbox" searchable wire:model.live="baselineProposedSourceFile" :label="__('Source file')" :placeholder="__('Select a source file')">
                    @foreach (array_unique(array_column($selected['blocked_sources'] ?? [], 'path')) as $proposedPath)
                        <flux:select.option :value="$proposedPath">{{ $proposedPath }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:icon.loading class="size-4" wire:loading wire:target="baselineProposedSourceFile" />
                @if ($baselineProposedSourceChanges)
                    @if ($baselineProposedSourceChanges['status'] === 'preview')
                        <flux:callout color="sky">
                            <flux:callout.heading>{{ __('Proposed key arguments') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('Only the captured key arguments would change. Other arguments and surrounding code are preserved. Finding IDs refer to the saved baseline.') }}</flux:callout.text>
                            <div class="max-h-80 overflow-auto">
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Finding') }}</flux:table.column>
                                        <flux:table.column>{{ __('Line / byte column') }}</flux:table.column>
                                        <flux:table.column>{{ __('Before') }}</flux:table.column>
                                        <flux:table.column>{{ __('After') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        @foreach ($baselineProposedSourceChanges['edits'] as $proposedEdit)
                                            <flux:table.row :key="'proposed-edit-'.$proposedEdit['finding']">
                                                <flux:table.cell>#{{ $proposedEdit['finding'] }}</flux:table.cell>
                                                <flux:table.cell>{{ $proposedEdit['line'] }} / {{ $proposedEdit['column'] }}</flux:table.cell>
                                                <flux:table.cell><code>{{ $proposedEdit['before'] }}</code></flux:table.cell>
                                                <flux:table.cell><code>{{ $proposedEdit['after'] }}</code></flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                        </flux:callout>
                    @else
                        <flux:callout color="amber">
                            <flux:callout.text>
                                {{ match ($baselineProposedSourceChanges['status']) {
                                    'select_file' => __('Select a file belonging to this key in the snapshot.'),
                                    'proposal_required' => __('Check a valid, conflict-free proposal first.'),
                                    default => __('The current file or its captured occurrences could not be verified. Refresh the source evidence before preparing changes.'),
                                } }}
                            </flux:callout.text>
                        </flux:callout>
                    @endif
                @endif
            </div>
        </flux:accordion.content>
    </flux:accordion.item>
</flux:accordion>
