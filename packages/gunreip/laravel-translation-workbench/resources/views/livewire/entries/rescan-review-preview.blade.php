<flux:callout
    class="mb-4"
    color="amber"
>
    <flux:callout.heading icon="information-circle">{{ __('Isolated rescan review') }}</flux:callout.heading>
    <flux:callout.text>
        {{ __('Read-only evidence from the isolated generation. These are not changes to the regular Workbench database. A file awaiting review keeps its previous findings; this does not mean that every finding in that file is incorrect.') }}
    </flux:callout.text>
    @if ($rescan === null)
        <flux:callout.text>{{ __('No verified rescan review export is available.') }}</flux:callout.text>
    @else
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('Inspect preserved findings and scan questions') }}
                    ({{ count($rescan['files']) }})</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Source scan') }}: <code>{{ $rescan['source_run'] }}</code></flux:text>
                    <flux:text>{{ __('Generation') }}: <code>{{ $rescan['generation'] }}</code> ·
                        {{ __('Exported at') }}: {{ $rescan['exported_at'] }}</flux:text>
                    <div class="my-3 flex flex-wrap gap-2">
                        <flux:badge color="green">{{ __('Confirmed findings') }}:
                            {{ $rescan['summary']['confirmed'] }}</flux:badge>
                        <flux:badge color="amber">{{ __('Preserved pending file review') }}:
                            {{ $rescan['summary']['preserved_for_review'] }}</flux:badge>
                    </div>
                    <flux:input
                        wire:model.live.debounce.300ms="rescanSearch"
                        :label="__('Search source path')"
                    />
                    <flux:table container:class="mt-3 overflow-auto">
                        <flux:table.columns>
                            <flux:table.column>{{ __('Source file') }}</flux:table.column>
                            <flux:table.column>{{ __('Questions') }}</flux:table.column>
                            <flux:table.column>{{ __('Preserved findings') }}</flux:table.column>
                            <flux:table.column>{{ __('Details') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($rescanFiles as $file)
                                <flux:table.row wire:key="rescan-file-{{ $file['id'] }}">
                                    <flux:table.cell class="whitespace-normal break-all">
                                        <code>{{ $file['path'] }}</code></flux:table.cell>
                                    <flux:table.cell>{{ $file['issues'] }}</flux:table.cell>
                                    <flux:table.cell>{{ $file['preserved'] }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button
                                            size="sm"
                                            variant="outline"
                                            wire:click="$set('rescanSelected', '{{ $file['id'] }}')"
                                        >{{ __('Inspect') }}</flux:button>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4">{{ __('No matching source files.') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                    <flux:pagination :paginator="$rescanFiles" />
                    @if ($rescanSelected !== '' && $rescanDetail === null)
                        <flux:text>
                            {{ __('The selected evidence is unavailable or has changed. Reload the verified export before continuing.') }}
                        </flux:text>
                    @elseif ($rescanDetail !== null)
                        <flux:callout
                            class="mt-4"
                            color="zinc"
                        >
                            <flux:callout.heading class="break-all">{{ $rescanDetail['path'] }}</flux:callout.heading>
                            <flux:callout.text>
                                {{ __('These finding IDs belong to the isolated generation and must not be used as IDs of the existing Workbench database.') }}
                            </flux:callout.text>
                            <flux:table container:class="mt-3 overflow-auto">
                                <flux:table.columns>
                                    <flux:table.column>{{ __('Line / Column') }}</flux:table.column>
                                    <flux:table.column>{{ __('Raw expression') }}</flux:table.column>
                                    <flux:table.column>{{ __('Review reason') }}</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @foreach ($rescanIssues as $issue)
                                        @php($capture = collect($rescanDetail['captures'])->first(fn($row) => $row['source_line'] === ($issue['line'] ?? null) && $row['source_column'] === ($issue['column'] ?? null)))
                                        <flux:table.row>
                                            <flux:table.cell>{{ $issue['line'] ?? '—' }}:{{ $issue['column'] ?? '—' }}
                                            </flux:table.cell>
                                            <flux:table.cell class="max-w-lg whitespace-pre-wrap break-words">
                                                <code>{{ $capture['raw_expression'] ?? '—' }}</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal">
                                                {{ match ($issue['reason']) {
                                                    'dynamic_or_nonliteral_argument' => __('The argument cannot be resolved to a literal without further context.'),
                                                    'key_like_argument_without_lang_evidence' => __('No exact source-language value confirms this key.'),
                                                    'ambiguous_movement' => __('Several occurrences could be predecessors. No relationship was guessed.'),
                                                    default => __('Inspect the captured expression and its source context.'),
                                                } }}
                                                <code class="block text-xs">{{ $issue['reason'] }}</code>
                                                @if (isset($capture['option_source']))
                                                    <flux:text class="mt-2">
                                                        {{ __('Option group identified. The concrete option value and its source still need evidence; the group key is not a scalar translation.') }}
                                                    </flux:text>
                                                    <dl
                                                        class="mt-2 grid grid-cols-[auto_minmax(0,1fr)] gap-x-3 gap-y-2">
                                                        <dt>{{ __('Option group key') }}</dt>
                                                        <dd class="break-all">
                                                            <code>{{ $capture['option_source']['owner_key'] }}</code>
                                                        </dd>
                                                        <dt>{{ __('Option value expression') }}</dt>
                                                        <dd class="break-words">
                                                            <code>{{ $capture['option_source']['value_expression'] }}</code>
                                                        </dd>
                                                        <dt>{{ __('Fallback expression') }}</dt>
                                                        <dd class="break-words">
                                                            <code>{{ $capture['option_source']['fallback_expression'] }}</code>
                                                        </dd>
                                                        <dt>{{ __('Locale expression') }}</dt>
                                                        <dd class="break-words">
                                                            <code>{{ $capture['option_source']['locale_expression'] ?? __('Current application locale') }}</code>
                                                        </dd>
                                                    </dl>
                                                    @php($listEvidence = $capture['option_list_review'] ?? null)
                                                    @if (($listEvidence['state'] ?? null) === 'reviewed_default_source')
                                                        <flux:accordion class="mt-3">
                                                            <flux:accordion.item>
                                                                <flux:accordion.heading>
                                                                    {{ __('Default list evidence') }}
                                                                    ({{ count($listEvidence['entries']) }})
                                                                </flux:accordion.heading>
                                                                <flux:accordion.content>
                                                                    <flux:text>
                                                                        {{ __('Reviewed source relationship only. These defaults do not prove every runtime value and do not resolve the finding or mark it as DatabaseValue.') }}
                                                                    </flux:text>
                                                                    <flux:text class="break-all">
                                                                        <code>{{ $listEvidence['path'] }}:{{ $listEvidence['line'] }}</code>
                                                                    </flux:text>
                                                                    <div class="mt-2 max-h-64 overflow-auto">
                                                                        <flux:table>
                                                                            <flux:table.columns>
                                                                                <flux:table.column>
                                                                                    {{ __('Option key') }}
                                                                                </flux:table.column>
                                                                                <flux:table.column>
                                                                                    {{ __('Default value') }}
                                                                                </flux:table.column>
                                                                                <flux:table.column>{{ __('Source') }}
                                                                                </flux:table.column>
                                                                            </flux:table.columns>
                                                                            <flux:table.rows>
                                                                                @foreach ($listEvidence['entries'] as $entry)
                                                                                    <flux:table.row>
                                                                                        <flux:table.cell
                                                                                            class="whitespace-normal break-words"
                                                                                        ><code>{{ $entry['key'] ?? '—' }}</code>
                                                                                        </flux:table.cell>
                                                                                        <flux:table.cell
                                                                                            class="whitespace-pre-wrap break-words"
                                                                                        >{{ $entry['value'] ?? __('Unresolved expression') }}
                                                                                        </flux:table.cell>
                                                                                        <flux:table.cell
                                                                                            class="whitespace-normal break-all"
                                                                                        >
                                                                                            {{ __('Line') }}
                                                                                            {{ $entry['line'] }}
                                                                                            @if ($entry['key_dependency'] ?? null)
                                                                                                <code
                                                                                                    class="block">{{ $entry['key_dependency']['class'] }}::{{ $entry['key_dependency']['constant'] }}</code>
                                                                                                <code
                                                                                                    class="block">{{ $entry['key_dependency']['path'] }}:{{ $entry['key_dependency']['line'] }}</code>
                                                                                            @endif
                                                                                        </flux:table.cell>
                                                                                    </flux:table.row>
                                                                                @endforeach
                                                                            </flux:table.rows>
                                                                        </flux:table>
                                                                    </div>
                                                                </flux:accordion.content>
                                                            </flux:accordion.item>
                                                        </flux:accordion>
                                                    @elseif ($listEvidence !== null)
                                                        <flux:text class="mt-2 text-amber-600">
                                                            {{ __('The source evidence changed or the list is ambiguous. No default values are attached; rebuild and review the evidence.') }}
                                                        </flux:text>
                                                    @endif
                                                @endif
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                            <flux:pagination :paginator="$rescanIssues" />
                            <flux:accordion class="mt-3">
                                <flux:accordion.item>
                                    <flux:accordion.heading>{{ __('Preserved finding IDs') }}
                                        ({{ count($rescanDetail['preserved']) }})</flux:accordion.heading>
                                    <flux:accordion.content>
                                        <div class="max-h-48 overflow-auto break-words">
                                            <code>{{ implode(', ', array_map(fn($id) => '#' . $id, $rescanDetail['preserved'])) }}</code>
                                        </div>
                                    </flux:accordion.content>
                                </flux:accordion.item>
                            </flux:accordion>
                            @foreach ($rescanDetail['relocations'] as $relocation)
                                <flux:text>{{ __('Possible relocation; decision required') }}:
                                    #{{ $relocation['finding'] }} · {{ $relocation['source'] }}</flux:text>
                                @foreach ($relocation['candidates'] as $candidate)
                                    <flux:text>
                                        <code>{{ $candidate['path'] }}:{{ $candidate['line'] }}:{{ $candidate['column'] }}</code>
                                    </flux:text>
                                @endforeach
                            @endforeach
                        </flux:callout>
                    @endif
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    @endif
</flux:callout>
