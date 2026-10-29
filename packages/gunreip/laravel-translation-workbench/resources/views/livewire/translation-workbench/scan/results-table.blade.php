<flux:select
    wire:model.live="scanCategory"
    :label="__('Result category')"
>
    <flux:select.option value="created">{{ __('Captured findings') }}</flux:select.option>
    <flux:select.option value="moved">{{ __('Moved findings') }}</flux:select.option>
    <flux:select.option value="removed">{{ __('Archived findings') }}</flux:select.option>
    <flux:select.option value="review_files">{{ __('Files requiring review') }}
    </flux:select.option>
    <flux:select.option value="usage_decisions">{{ __('Usage decisions considered') }}
    </flux:select.option>
</flux:select>
<flux:text>
    {{ __('Scan results are historical. Open a finding to inspect its current state and history.') }}
</flux:text>
@if ($scanCategory === 'review_files')
    <flux:callout color="amber">
        {{ __('These files were preserved for review. This list does not resolve or approve them.') }}
    </flux:callout>
@endif
<flux:table>
    <flux:table.columns>
        <flux:table.column>{{ __('Source') }}</flux:table.column>
        <flux:table.column>
            {{ $scanCategory === 'usage_decisions' ? __('Decision') : __('Finding') }}
        </flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
        @forelse ($scanRows as $scanRow)
            <flux:table.row>
                <flux:table.cell class="whitespace-normal break-all">
                    {{ $scanRow['path'] }}{{ $scanRow['source_line'] ? ':' . $scanRow['source_line'] : '' }}
                    @if ($scanCategory === 'review_files')
                        @if ($scanRow['preserved_count'] !== null)
                            <flux:text>
                                {{ __('Existing findings preserved: :count', ['count' => $scanRow['preserved_count']]) }}
                            </flux:text>
                        @endif
                        <ul
                            class="mt-2 max-h-48 space-y-2 overflow-auto [scrollbar-gutter:stable]">
                            @foreach ($scanRow['issues'] as $issue)
                                <li>
                                    @if (isset($issue['line']))
                                        <span
                                            class="font-mono">{{ $issue['line'] }}:{{ $issue['column'] ?? '—' }}</span>
                                        ·
                                    @endif
                                    {{ match ($issue['reason']) {
                                        'key_like_argument_without_lang_evidence' => __(
                                            'Translation key found, but no verified source-language value is available for this scan.',
                                        ),
                                        'dynamic_or_nonliteral_argument' => __('The translation argument is dynamic or is not a literal string.'),
                                        'translation_work_required' => __('This dynamic usage was marked for translation work and remains open.'),
                                        'database_value_finding_conflict' => __(
                                            'DatabaseValue was selected, but an existing finding needs review before this usage can be excluded.',
                                        ),
                                        'ambiguous_movement' => __('Several identical occurrences prevent an unambiguous position match.'),
                                        'relocation_review' => __(
                                            'An occurrence from a missing file also appears elsewhere. A file move must be distinguished from a copy.',
                                        ),
                                        'incomplete_expression' => __('The translation expression could not be captured completely.'),
                                        'special_call_requires_context' => __('This call requires an explicit context decision.'),
                                        'review_details_unavailable' => __('This scan did not record a detailed review reason.'),
                                        default => __('The scanner could not confirm this occurrence automatically.'),
                                    } }}
                                    <code
                                        class="block text-xs text-zinc-500">{{ $issue['reason'] }}</code>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </flux:table.cell>
                <flux:table.cell>
                    @if ($scanCategory === 'usage_decisions')
                        {{ $scanRow['usage_decision'] === 'database_value' ? __('DatabaseValue') : __('Translate') }}
                        · {{ __('Decision') }} #{{ $scanRow['decision_event_id'] }}
                        <flux:button
                            class="mt-2"
                            size="sm"
                            variant="outline"
                            wire:click="openConsideredScanUsage({{ $scanRow['decision_event_id'] }})"
                        >{{ __('Review source and current decision') }}</flux:button>
                    @endif
                    @if ($scanCategory === 'review_files')
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="openScanCaptures({{ \Illuminate\Support\Js::from($scanRow['path']) }})"
                        >{{ __('Inspect captured source') }}</flux:button>
                    @endif
                    @if ($scanCategory === 'review_files' && ($scanRow['preserved_count'] ?? 0) > 0)
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="openPreservedFindings({{ \Illuminate\Support\Js::from($scanRow['path']) }})"
                        >{{ __('Open preserved findings') }}</flux:button>
                    @endif
                    @if ($scanRow['predecessor'])
                        <flux:button
                            size="sm"
                            variant="ghost"
                            wire:click="open({{ $scanRow['predecessor'] }})"
                        >#{{ $scanRow['predecessor'] }}</flux:button> →
                    @endif
                    @if ($scanRow['id'])
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="open({{ $scanRow['id'] }})"
                        >#{{ $scanRow['id'] }}</flux:button>
                    @endif
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="2">{{ __('No results in this category.') }}
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
<flux:pagination :paginator="$scanRows" />
