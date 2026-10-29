@if ($baselineFiles ?? null)
    <flux:callout :color="$baselineFiles['matches'] ? 'green' : 'amber'">
        <flux:callout.heading>
            {{ $baselineFiles['matches'] ? __('Referenced files match the snapshot') : __('Current files need verification') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Unchanged files') }}: {{ $baselineFiles['counts']['unchanged'] ?? 0 }} /
            {{ count($baselineFiles['files']) }}.
            {{ __('Each referenced file is checked once, even when it contains several findings.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('A changed checksum means that the file changed, not necessarily the literal. Missing or changed files require a new source check before a correction can be applied. This comparison does not check the current database, discover new usages or approve a correction.') }}
        </flux:callout.text>
        <flux:accordion class="mt-3">
            <flux:accordion.item>
                <flux:accordion.heading>
                    <flux:callout.heading>
                        {{ __('Current file comparison') }}
                    </flux:callout.heading>
                </flux:accordion.heading>
                <flux:accordion.content>
                    @if ($baselineFiles['files'])
                        <div class="overflow-x-auto rounded rounded-t-lg">
                            <flux:table container:class="max-h-80">
                                {{-- Table Header --}}
                                <flux:table.columns
                                    class="rounded rounded-t-lg bg-white dark:bg-zinc-800"
                                    sticky
                                >
                                    {{-- Table Header Seaqunce Nuber --}}
                                    <flux:table.column>
                                        <flux:callout.heading class="w-8">
                                            <flux:icon.tally-5 class="ml-2" />
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- Table Header Kind --}}
                                    <flux:table.column>
                                        <flux:callout.heading>
                                            {{ __('Kind') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- Table Header File --}}
                                    <flux:table.column>
                                        <flux:callout.heading>
                                            {{ __('File') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- Table Header Status --}}
                                    <flux:table.column>
                                        <flux:callout.heading>
                                            {{ __('Status') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows class="[&_td]:align-top">
                                    @foreach ($baselineFiles['files'] as $file)
                                        <flux:table.row
                                            :key="'freshness-'.$file['kind'].
                                            '-'.$file['path']"
                                        >
                                            {{-- Sequence Number --}}
                                            <flux:table.cell align="right">
                                                <flux:callout.text>
                                                    {{ $loop->index + 1 }}
                                                </flux:callout.text>
                                            </flux:table.cell>
                                            {{-- Kind --}}
                                            <flux:table.cell>
                                                <flux:callout.text>
                                                    {{ $file['kind'] === 'source' ? __('Source') : __('Language file') }}
                                                </flux:callout.text>
                                            </flux:table.cell>
                                            {{-- Files --}}
                                            <flux:table.cell class="whitespace-normal break-all font-mono">
                                                <flux:callout.text>
                                                    {{ $file['path'] }}
                                                </flux:callout.text>
                                            </flux:table.cell>

                                            <flux:table.cell>
                                                <flux:badge
                                                    size="sm"
                                                    :color="$file['status'] === 'unchanged' ? 'green' : 'amber'"
                                                >
                                                    {{ match ($file['status']) {
                                                        'unchanged' => __('Unchanged'),
                                                        'changed' => __('Changed'),
                                                        'missing' => __('Missing file'),
                                                        'unreadable' => __('Unreadable file'),
                                                        'outside_root' => __('Outside project scope'),
                                                        'conflicting_evidence' => __('Conflicting snapshot checksums'),
                                                        default => __('Missing snapshot checksum'),
                                                    } }}
                                                </flux:badge>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        </div>
                    @else
                        <flux:callout.text>{{ __('No file evidence available for comparison.') }}</flux:callout.text>
                    @endif
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
@endif
