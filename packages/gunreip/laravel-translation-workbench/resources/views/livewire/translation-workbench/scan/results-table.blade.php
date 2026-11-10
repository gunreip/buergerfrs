@php($showScanSharedStatus = !in_array($scanCategory, ['review_files', 'usage_decisions'], true))
<flux:callout
    color="sky"
    icon="bring-to-front"
    heading="{{ __('Select scan results overview') }}"
>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <flux:field class="min-w-0">
            <flux:label>
                <flux:callout.heading>
                    {{ __('Result category') }}
                </flux:callout.heading>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.file-chart-column class="size-5" />
                </flux:input.group.prefix>
                <flux:select
                    variant="listbox"
                    wire:model.live="scanCategory"
                >
                    <flux:select.group :label="__('Findings')">
                        <flux:select.option
                            value="missing_immutable"
                            icon="clipboard-document-list"
                        >
                            {{ __('Missing immutable information') }} (<x-ui.number :value="$scanCategoryCounts['missing_immutable'] ?? 0" />)
                        </flux:select.option>
                        <flux:select.option
                            value="created"
                            icon="search-check"
                        >
                            {{ __('New findings') }} (<x-ui.number :value="$scanCategoryCounts['created'] ?? 0" />)
                        </flux:select.option>
                        <flux:select.option
                            value="confirmed"
                            icon="check"
                        >
                            {{ __('Unchanged confirmed findings') }} (<x-ui.number :value="$scanCategoryCounts['confirmed'] ?? 0" />)
                        </flux:select.option>
                        <flux:select.option
                            value="moved"
                            icon="separator-horizontal"
                        >
                            {{ __('Moved findings') }} (<x-ui.number :value="$scanCategoryCounts['moved'] ?? 0" />)
                        </flux:select.option>
                        <flux:select.option
                            value="removed"
                            icon="eraser"
                        >
                            {{ __('Archived findings') }} (<x-ui.number :value="$scanCategoryCounts['removed'] ?? 0" />)
                        </flux:select.option>
                    </flux:select.group>
                    <flux:select.group :label="__('Files and usage decisions')">
                        <flux:select.option
                            value="review_files"
                            icon="eye"
                        >
                            {{ __('Files requiring review') }} (<x-ui.number :value="$scanCategoryCounts['review_files'] ?? 0" />)
                        </flux:select.option>
                        <flux:select.option
                            value="usage_decisions"
                            icon="check"
                        >
                            {{ __('Usage decisions considered') }} (<x-ui.number :value="$scanCategoryCounts['usage_decisions'] ?? 0" />)
                        </flux:select.option>
                    </flux:select.group>
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field class="min-w-0">
            <flux:label>
                <flux:callout.heading>
                    {{ __('Search results') }}
                </flux:callout.heading>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass class="size-5" />
                </flux:input.group.prefix>
                <flux:input
                    wire:model.live.debounce.350ms="scanSearch"
                    clearable
                    copyable
                    :placeholder="in_array($scanCategory, ['review_files', 'usage_decisions'], true) ?
                        __('Source path') : __('Finding ID, source path or translation key')"
                />
            </flux:input.group>
        </flux:field>
    </div>
    <flux:callout.text>
        {{ __('Matching results') }}: <x-ui.number :value="$scanRows->total()" />
        · {{ __('Category total') }}: <x-ui.number :value="$scanCategoryCounts[$scanCategory] ?? 0" />
    </flux:callout.text>

    <flux:callout.text>
        {{ $scanCategory === 'missing_immutable'
            ? __(
                'Current completion needs for findings confirmed or preserved by this scan. The historical scan result remains unchanged.',
            )
            : __('Scan results are historical. Open a finding to inspect its current state and history.') }}
    </flux:callout.text>
</flux:callout>

@if ($scanCategory === 'missing_immutable')
    <flux:callout
        color="amber"
        icon="information-circle"
        :heading="__('Missing reconstructed source text')"
        :text="__('Only active findings with a captured translation key and neither an original literal nor a reconstructed source text are listed. An empty translation key for an ordinary literal is not an error.')"
    >
        <flux:callout.text>
            {{ __('Missing SuggestedKeys are initialized by a saved regular scan for active findings within its scope. No existing SuggestedKey is overwritten. Reconstructed text must be recorded separately with its evidence; it must not replace captured source data.') }}
        </flux:callout.text>
    </flux:callout>
@endif

@if ($scanCategory === 'review_files')
    <flux:callout
        color="amber"
        heading="{{ __('These files were preserved for review. This list does not resolve or approve them.') }}"
        icon="info"
    />
@endif

<flux:callout
    icon="radar"
    color="sky"
>
    <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
        <flux:callout.heading>
            {{ __('Scan results for category »:category«', [
                'category' => match ($scanCategory) {
                    'created' => __('New findings'),
                    'confirmed' => __('Unchanged confirmed findings'),
                    'moved' => __('Moved findings'),
                    'removed' => __('Archived findings'),
                    'review_files' => __('Files requiring review'),
                    'usage_decisions' => __('Usage decisions considered'),
                    'missing_immutable' => __('Missing immutable information'),
                    default => __('All results'),
                },
            ]) }}
        </flux:callout.heading>

        <div class="ml-auto flex items-end gap-2">
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:callout.text>
                        {{ __('Per page') }}
                    </flux:callout.text>
                </flux:input.group.prefix>
                <flux:select
                    class="w-32"
                    {{-- size="xs" --}}
                    variant="listbox"
                    options:class="min-w-48"
                    wire:model.live="scanPerPage"
                    :aria-label="__('Per page')"
                >
                    @foreach ([10, 25, 50, 100] as $pageSize)
                        <flux:select.option
                            :value="$pageSize"
                            icon="list"
                        >{{ $pageSize }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

    </div>
    {{-- Table --}}
    <flux:table container:class="max-h-120 overflow-auto rounded-t-lg">
        {{-- Table Header --}}
        <flux:table.columns
            class="bg-white dark:bg-zinc-800"
            sticky
        >
            {{-- Table Header Column Source --}}
            <flux:table.column class="w-full">
                <flux:callout.heading class="ml-2">
                    {{ __('Source') }}
                </flux:callout.heading>
            </flux:table.column>
            @if ($showScanSharedStatus)
                <flux:table.column>
                    <flux:tooltip>
                        <flux:callout.heading>{{ __('Current shared status') }}</flux:callout.heading>
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Current shared status')"
                            :text="__(
                                'Current shared candidates and key assignments across all active findings, including findings from earlier scans. This is not a historical status recorded by this scan.',
                            )"
                        />
                    </flux:tooltip>
                </flux:table.column>
            @endif
            {{-- Table Header Column Dexision /Finding  --}}
            <flux:table.column class="w-px whitespace-nowrap">
                <flux:callout.heading class="mr-3">
                    {{ $scanCategory === 'usage_decisions' ? __('Decision') : __('Finding') }}
                </flux:callout.heading>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Body --}}
        <flux:table.rows>
            @forelse ($scanRows as $scanRow)
                {{-- Table Row --}}
                <flux:table.row>
                    {{-- Table Row Column Finding --}}
                    <flux:table.cell class="whitespace-normal break-all">
                        <flux:callout.text class="ml-2 font-mono">
                            {{ $scanRow['path'] }}{{ $scanRow['source_line'] ? ':' . $scanRow['source_line'] : '' }}
                        </flux:callout.text>
                        @if ($scanCategory === 'missing_immutable')
                            <x-translation-workbench::ui.common.text-preview
                                :text="$scanRow['captured_key']"
                                :mono="true"
                                :heading="__('Captured translation key')"
                            />
                            <flux:badge
                                color="amber"
                                size="sm"
                            >{{ __('Reconstructed source text missing') }}</flux:badge>
                        @endif
                        @if ($scanCategory === 'review_files')
                            @if ($scanRow['preserved_count'] !== null)
                                <flux:callout.text class="ml-2">
                                    <flux:callout.heading class="inline">
                                        {{ __('Existing findings preserved') }}:
                                    </flux:callout.heading>
                                    {{ __(':count', ['count' => $scanRow['preserved_count']]) }}
                                </flux:callout.text>
                            @endif
                            <ul class="[scrollbar-gutter-stable] mt-2 max-h-48 space-y-2 overflow-auto">
                                @foreach ($scanRow['issues'] as $issue)
                                    <li class="ml-2">
                                        <flux:callout.text
                                            class="grid grid-cols-[minmax(0,6rem)_minmax(0,1fr)] gap-x-2">
                                            @if (isset($issue['line']))
                                                <span class="whitespace-nowrap font-mono">
                                                    {{ $issue['line'] }}:{{ $issue['column'] ?? '—' }} ·
                                                </span>
                                            @else
                                                <span></span>
                                            @endif
                                            <flux:callout.text class="wrap-break-word min-w-0">
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
                                            </flux:callout.text>
                                        </flux:callout.text>
                                        <code
                                            class="col-span-full block text-xs text-zinc-500">{{ $issue['reason'] }}</code>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </flux:table.cell>
                    @if ($showScanSharedStatus)
                        <flux:table.cell>
                            @include(
                                'translation-workbench::livewire.translation-workbench.shared.finding-candidates',
                                [
                                    'candidateFinding' => $scanRow['id'],
                                    'showCandidateCount' => false,
                                ]
                            )
                        </flux:table.cell>
                    @endif
                    {{-- Table Row Column Actions --}}
                    <flux:table.cell class="w-px whitespace-nowrap">
                        <div class="mr-3 flex flex-col items-start gap-2">
                            @if ($scanCategory === 'usage_decisions')
                                {{ $scanRow['usage_decision'] === 'database_value' ? __('DatabaseValue') : __('Translate') }}
                                · {{ __('Decision') }} #{{ $scanRow['decision_event_id'] }}
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openConsideredScanUsage({{ $scanRow['decision_event_id'] }})"
                                    >
                                        <flux:icon.gavel size="4" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openConsideredScanUsage({{ $scanRow['decision_event_id'] }})"
                                    >
                                        {{ __('Review source and current decision') }}
                                    </flux:button>
                                </flux:button.group>
                            @endif
                            @if ($scanCategory === 'review_files')
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openScanCaptures({{ \Illuminate\Support\Js::from($scanRow['path']) }})"
                                    >
                                        <flux:icon.file-code-corner size="4" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openScanCaptures({{ \Illuminate\Support\Js::from($scanRow['path']) }})"
                                    >
                                        {{ __('Inspect captured source') }}
                                    </flux:button>
                                </flux:button.group>
                            @endif
                            @if ($scanCategory === 'review_files' && ($scanRow['preserved_count'] ?? 0) > 0)
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openPreservedFindings({{ \Illuminate\Support\Js::from($scanRow['path']) }})"
                                    >
                                        <flux:icon.search-code size="4" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openPreservedFindings({{ \Illuminate\Support\Js::from($scanRow['path']) }})"
                                    >
                                        {{ __('Open preserved findings') }}
                                    </flux:button>
                                </flux:button.group>
                            @endif
                            @if ($scanRow['predecessor'])
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="open({{ $scanRow['predecessor'] }})"
                                    >
                                        <flux:icon.arrow-left-to-line size="4" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="open({{ $scanRow['predecessor'] }})"
                                    >
                                        #{{ $scanRow['predecessor'] }}
                                    </flux:button>
                                </flux:button.group>
                            @endif
                            @if ($scanRow['id'])
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="open({{ $scanRow['id'] }})"
                                    >
                                        <flux:icon.scan-search size="4" />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="open({{ $scanRow['id'] }})"
                                    >
                                        #{{ $scanRow['id'] }}
                                    </flux:button>
                                </flux:button.group>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Row Empty --}}
                <flux:table.row>
                    {{-- Table Row Column Empty --}}
                    <flux:table.cell :colspan="$showScanSharedStatus ? 3 : 2">
                        <flux:callout.text class="ml-2">
                            {{ trim($scanSearch) !== '' ? __('No results match this search in the selected category.') : __('No results in this category.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</flux:callout>

<flux:pagination :paginator="$scanRows" />
