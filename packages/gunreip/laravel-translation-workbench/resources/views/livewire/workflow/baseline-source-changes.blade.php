<flux:callout color="sky" icon="code-bracket">
    <flux:callout.heading>{{ __('Recorded source changes') }}</flux:callout.heading>
    <flux:callout.text>{{ __('These changes belong to the synthetic rehearsal, independently of the selected translation key. The production source files have not been changed. Finding IDs refer to the isolated generation.') }}</flux:callout.text>
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading>{{ __('Inspect changes by source file') }}</flux:accordion.heading>
            <flux:accordion.content>
                @if ($baselineSourceChanges['snapshot'])
                    @php
                        $sourceSnapshot = $baselineSourceChanges['snapshot'];
                    @endphp
                    <div class="space-y-4">
                        <flux:text><code>{{ $sourceSnapshot['source_key'] }}</code> → <code>{{ $sourceSnapshot['synthetic_target'] }}</code> · {{ $sourceSnapshot['recorded_at'] }}</flux:text>
                        @if (($baselinePreview['snapshot']['input_dump_sha256'] ?? null) !== $sourceSnapshot['input_dump_sha256'])
                            <flux:callout color="amber"><flux:callout.text>{{ __('This report cannot be matched to the displayed baseline snapshot.') }}</flux:callout.text></flux:callout>
                        @endif
                        <flux:button variant="outline" wire:click="checkBaselineSourceSet" wire:loading.attr="disabled">
                            {{ __('Check all recorded source files') }}
                            <flux:icon.loading class="size-4" wire:loading wire:target="checkBaselineSourceSet" />
                        </flux:button>
                        @if ($baselineSourceSet)
                            <flux:callout :color="$baselineSourceSet['all_match_before'] ? 'sky' : 'amber'">
                                <flux:callout.heading>{{ __('Complete source file comparison') }}</flux:callout.heading>
                                <flux:callout.text>
                                    {{ __('Files checked') }}: {{ $baselineSourceSet['total'] }} ·
                                    {{ __('Matches starting point') }}: {{ $baselineSourceSet['counts']['matches_before'] ?? 0 }} ·
                                    {{ __('Needs inspection') }}: {{ $baselineSourceSet['total'] - ($baselineSourceSet['counts']['matches_before'] ?? 0) }}
                                </flux:callout.text>
                                <flux:callout.text>
                                    {{ __('Checked at') }}: {{ $baselineSourceSet['checked_at'] }} ·
                                    {{ __('This compares recorded source files only. It does not verify current database assignments or language files and does not authorize applying changes.') }}
                                </flux:callout.text>
                                @if ($baselineSourceSet['total'] === 0)
                                    <flux:callout.text>{{ __('No recorded source files are available to verify.') }}</flux:callout.text>
                                @elseif (! $baselineSourceSet['all_match_before'])
                                    <div class="max-h-64 overflow-auto">
                                        <flux:table>
                                            <flux:table.columns>
                                                <flux:table.column>{{ __('Source file') }}</flux:table.column>
                                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                                                <flux:table.column>{{ __('Inspect') }}</flux:table.column>
                                            </flux:table.columns>
                                            <flux:table.rows>
                                                @foreach ($baselineSourceSet['files'] as $checkedFile)
                                                    @if ($checkedFile['status'] !== 'matches_before')
                                                        <flux:table.row :key="'source-check-'.$checkedFile['path']">
                                                            <flux:table.cell class="whitespace-normal break-all font-mono">{{ $checkedFile['path'] }}</flux:table.cell>
                                                            <flux:table.cell>
                                                                {{ match ($checkedFile['status']) {
                                                                    'matches_after' => __('Matches proposed result; application not proven'),
                                                                    'diverged' => __('Changed since recording'),
                                                                    'missing' => __('Missing file'),
                                                                    'unreadable' => __('Unreadable file'),
                                                                    'outside_root' => __('Outside project root'),
                                                                    'conflicting_evidence' => __('Duplicate path in recorded evidence'),
                                                                    default => __('Missing checksum evidence'),
                                                                } }}
                                                            </flux:table.cell>
                                                            <flux:table.cell>
                                                                <flux:button size="sm" variant="outline" wire:click="$set('baselineSourceFile', {{ Illuminate\Support\Js::from($checkedFile['path']) }})">{{ __('Inspect') }}</flux:button>
                                                            </flux:table.cell>
                                                        </flux:table.row>
                                                    @endif
                                                @endforeach
                                            </flux:table.rows>
                                        </flux:table>
                                    </div>
                                @endif
                            </flux:callout>
                        @endif
                        <flux:select variant="listbox" searchable wire:model.live="baselineSourceFile" :label="__('Source file')" :placeholder="__('Select a source file')">
                            @foreach ($sourceSnapshot['files'] as $sourceFile)
                                <flux:select.option :value="$sourceFile['path']">{{ $sourceFile['path'] }} ({{ count($sourceFile['edits']) }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @if ($baselineSourceChanges['file'])
                            <flux:text class="break-all font-mono">{{ $baselineSourceChanges['file']['path'] }}</flux:text>
                            @if ($baselineSourceCurrent)
                                @php
                                    $currentState = match ($baselineSourceCurrent['status']) {
                                        'matches_before' => ['sky', __('Current file matches the saved starting point.')],
                                        'matches_after' => ['sky', __('Current file matches the proposed result. This does not prove that a Workbench operation applied it.')],
                                        'diverged' => ['amber', __('Current file differs from both recorded versions. A new scan and change preview are required.')],
                                        'missing' => ['red', __('The source file is missing. This does not authorize deleting a finding.')],
                                        'unreadable' => ['red', __('The source file cannot be read.')],
                                        'outside_root' => ['red', __('The recorded path is outside the permitted project root.')],
                                        default => ['amber', __('The snapshot lacks sufficient checksum evidence for this comparison.')],
                                    };
                                @endphp
                                <flux:callout :color="$currentState[0]">
                                    <flux:callout.heading>{{ __('Current source file') }}</flux:callout.heading>
                                    <flux:callout.text>{{ $currentState[1] }}</flux:callout.text>
                                    <flux:callout.text>{{ __('Checked at') }}: {{ $baselineSourceCurrent['checked_at'] }} · {{ __('Read-only comparison; not an approval to apply changes.') }}</flux:callout.text>
                                    <flux:button size="sm" variant="outline" wire:click="$refresh" wire:loading.attr="disabled">{{ __('Check again') }}</flux:button>
                                </flux:callout>
                            @endif
                            <div class="max-h-96 overflow-auto">
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Finding') }}</flux:table.column>
                                        <flux:table.column>{{ __('Original line / byte column') }}</flux:table.column>
                                        <flux:table.column>{{ __('Before') }}</flux:table.column>
                                        <flux:table.column>{{ __('After') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        @foreach ($baselineSourceChanges['file']['edits'] as $edit)
                                            <flux:table.row :key="'source-change-'.$edit['finding']">
                                                <flux:table.cell>#{{ $edit['finding'] }}</flux:table.cell>
                                                <flux:table.cell>{{ $edit['line'] }} : {{ $edit['column'] }}</flux:table.cell>
                                                <flux:table.cell><code>{{ $edit['before'] }}</code></flux:table.cell>
                                                <flux:table.cell><code>{{ $edit['after'] }}</code></flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                            @if ($baselineSourceChanges['movements'])
                                <flux:heading>{{ __('Unchanged calls with shifted positions') }}</flux:heading>
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Expression') }}</flux:table.column>
                                        <flux:table.column>{{ __('Before: line / byte column') }}</flux:table.column>
                                        <flux:table.column>{{ __('After: line / byte column') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        @foreach ($baselineSourceChanges['movements'] as $movement)
                                            <flux:table.row>
                                                <flux:table.cell><code>{{ $movement['expression'] }}</code></flux:table.cell>
                                                <flux:table.cell>{{ $movement['before_line'] }} : {{ $movement['before_column'] }}</flux:table.cell>
                                                <flux:table.cell>{{ $movement['after_line'] }} : {{ $movement['after_column'] }}</flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @endif
                        @else
                            <flux:text>{{ __('Select a file contained in the recorded preview.') }}</flux:text>
                        @endif
                    </div>
                @else
                    <flux:text>{{ __('No readable source-change preview is available. Opening this section does not run a scan or change any files.') }}</flux:text>
                @endif
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:callout>
