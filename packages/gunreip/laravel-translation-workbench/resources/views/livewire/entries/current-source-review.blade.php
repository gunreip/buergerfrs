<div id="current-source-review">
    @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.observed-values')
    @include('translation-workbench::livewire.entries.rescan-review-preview')
    <flux:callout icon="magnifying-glass" color="sky">
        <flux:callout.heading>{{ __('Current source scan — restart baseline') }}</flux:callout.heading>
        <flux:callout.text>{{ __('This completed scan describes the current source files and published translations at the time shown. Historical database rows are not imported. Reusable means an evidence-based transfer candidate, not an applied import or approval.') }}</flux:callout.text>
        @if ($snapshot === null)
            <flux:callout.text>{{ __('No verified completed scan is available.') }}</flux:callout.text>
        @else
            <flux:callout.text>{{ $snapshot['recorded_at'] }} · {{ __('Scanned files') }}: {{ $snapshot['files'] }} · {{ __('Source locale') }}: {{ $snapshot['source_locale'] }}</flux:callout.text>
            <div class="my-3 flex flex-wrap gap-2">
                <flux:badge color="green">{{ __('Reusable key findings') }}: {{ $snapshot['counts']['reusable_key'] ?? 0 }}</flux:badge>
                <flux:badge color="sky">{{ __('Literal findings') }}: {{ $snapshot['counts']['literal_new'] ?? 0 }}</flux:badge>
                <flux:badge color="amber">{{ __('Key findings to review') }}: {{ $snapshot['counts']['key_review'] ?? 0 }}</flux:badge>
                <flux:badge color="amber">{{ __('Expressions to review') }}: {{ $snapshot['counts']['expression_review'] ?? 0 }}</flux:badge>
                <flux:badge>{{ __('Excluded calls') }}: {{ $snapshot['counts']['excluded'] ?? 0 }}</flux:badge>
            </div>
            <flux:callout.text>{{ __('Counts refer to source occurrences. The table groups exact existing keys; literal occurrences remain separate. Missing target locales do not fabricate translations or trigger a fallback import.') }}</flux:callout.text>
            <flux:accordion>
                <flux:accordion.item>
                    <flux:accordion.heading>{{ __('Scan scope and limitations') }}</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:text>{{ __('Included roots') }}: {{ implode(', ', $snapshot['scope']['paths']) }}</flux:text>
                        <flux:text>{{ __('Excluded paths') }}: {{ implode(', ', $snapshot['scope']['exclude_paths']) }}</flux:text>
                        <flux:text>{{ __('Excluded directories') }}: {{ implode(', ', $snapshot['scope']['exclude_directory_names']) }}</flux:text>
                        <flux:text>{{ __('Configured translation calls only. Unmarked text, runtime values and JSON/vendor translation resolution are not covered. No database reconciliation or retirement is performed.') }}</flux:text>
                        @foreach ($snapshot['language_issues'] as $issue)
                            <flux:callout color="amber"><flux:callout.text><code>{{ $issue['path'] }}</code>: {{ $issue['reason'] }}</flux:callout.text></flux:callout>
                        @endforeach
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <div class="my-4 grid gap-3 md:grid-cols-2">
                <flux:input wire:model.live.debounce.300ms="search" :label="__('Search key or source text')" />
                <flux:select wire:model.live="filter" :label="__('Scan result')">
                    <flux:select.option value="review">{{ __('Current problems') }}</flux:select.option>
                    <flux:select.option value="reusable_key">{{ __('Reusable keys') }}</flux:select.option>
                    <flux:select.option value="literal_new">{{ __('Literals to capture') }}</flux:select.option>
                    <flux:select.option value="all">{{ __('All current findings') }}</flux:select.option>
                </flux:select>
            </div>
            <div wire:loading.delay role="status"><flux:icon.loading class="size-4" />{{ __('Loading scan evidence') }}</div>
            <flux:table>
                <flux:table.columns><flux:table.column>{{ __('Key / Source text') }}</flux:table.column><flux:table.column>{{ __('Result') }}</flux:table.column><flux:table.column>{{ __('Occurrences') }}</flux:table.column><flux:table.column>{{ __('Published locales') }}</flux:table.column><flux:table.column>{{ __('Details') }}</flux:table.column></flux:table.columns>
                <flux:table.rows>
                    @forelse ($groups as $group)
                        <flux:table.row wire:key="current-scan-{{ $group['id'] }}">
                            <flux:table.cell class="max-w-lg whitespace-normal break-all">{{ $group['title'] }}</flux:table.cell>
                            <flux:table.cell>{{ match ($group['state']) {'reusable_key'=>__('Reusable key'), 'literal_new'=>__('Literal to capture'), 'key_review'=>__('Key needs review'), default=>__('Expression needs review')} }}</flux:table.cell>
                            <flux:table.cell>{{ $group['findings'] }}</flux:table.cell>
                            <flux:table.cell><div class="flex flex-wrap gap-2">@foreach ($group['locales'] as $locale)<span class="inline-flex items-center gap-1"><x-ui.locale.flag :locale="$locale" size="xs" />{{ $locale }}</span>@endforeach</div></flux:table.cell>
                            <flux:table.cell><flux:button size="sm" variant="outline" wire:click="$set('selected', '{{ $group['id'] }}')">{{ __('Inspect') }}</flux:button></flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row><flux:table.cell colspan="5">{{ __('No matching current findings.') }}</flux:table.cell></flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            <flux:pagination :paginator="$groups" />
            @if ($detail !== null)
                <flux:callout class="mt-4" color="zinc">
                    <flux:callout.heading>{{ $detail['title'] }}</flux:callout.heading>
                    <flux:callout.text>{{ match($detail['state']) {
                        'reusable_key'=>__('Exact source-language value exists and the key has the required structure. Existing locale values can be proposed for transfer; nothing has been imported.'),
                        'literal_new'=>__('Capture this literal with its immutable source text and suggested key. Matching text elsewhere does not assign a shared key.'),
                        'key_review'=>__('Clarify key structure or missing exact source-language evidence. Do not rename or create a translation automatically.'),
                        default=>__('Resolve the expression or its supplied values before creating a translation task. No literal is guessed.'),
                    } }}</flux:callout.text>
                    <flux:table container:class="mt-3 overflow-auto">
                        <flux:table.columns><flux:table.column>{{ __('Source location') }}</flux:table.column><flux:table.column>{{ __('Raw expression') }}</flux:table.column><flux:table.column>{{ __('Evidence / Suggested key') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>@foreach ($sources as $source)<flux:table.row>
                            <flux:table.cell class="whitespace-normal break-all"><code>{{ $source['source_path'] }}:{{ $source['source_line'] }}:{{ $source['source_column'] }}</code></flux:table.cell>
                            <flux:table.cell class="max-w-lg whitespace-pre-wrap break-words"><code>{{ $source['raw_expression'] }}</code></flux:table.cell>
                            <flux:table.cell class="whitespace-normal break-all">{{ $source['reason'] }}<br><code>{{ $source['suggested_key'] }}</code></flux:table.cell>
                        </flux:table.row>@endforeach</flux:table.rows>
                    </flux:table>
                    <flux:pagination :paginator="$sources" />
                    <flux:table container:class="mt-3 overflow-auto">
                        <flux:table.columns><flux:table.column>{{ __('Locale') }}</flux:table.column><flux:table.column>{{ __('Language file') }}</flux:table.column><flux:table.column>{{ __('Published value') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>@forelse ($detail['values'] as $locale=>$value)<flux:table.row>
                            <flux:table.cell><span class="inline-flex items-center gap-2"><x-ui.locale.flag :locale="$locale" />{{ $locale }}</span></flux:table.cell><flux:table.cell><code>{{ $value['path'] }}</code></flux:table.cell><flux:table.cell class="whitespace-pre-wrap break-words">{{ $value['value'] }}</flux:table.cell>
                        </flux:table.row>@empty<flux:table.row><flux:table.cell colspan="3">{{ __('No exact published values in the checked files.') }}</flux:table.cell></flux:table.row>@endforelse</flux:table.rows>
                    </flux:table>
                </flux:callout>
            @endif
        @endif
    </flux:callout>
</div>
