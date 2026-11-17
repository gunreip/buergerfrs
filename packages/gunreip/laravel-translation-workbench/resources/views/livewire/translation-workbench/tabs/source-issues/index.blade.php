<flux:callout
    icon="exclamation-triangle"
    :color="$sourceIssueTotal > 0 ? 'red' : 'sky'"
    :heading="__('Source issues')"
    text="{{ __('Open finding positions from the latest saved scan and blocked source replacements.') }}"
>
    @php
        $observationLabels = [
            'reformatted' => __('Reformatted call'),
            'changed' => __('Changed call'),
            'moved' => __('Possible move'),
            'ambiguous' => __('Multiple candidates'),
            'occupied' => __('Position occupied'),
            'expression_missing' => __('No matching call'),
            'source_unavailable' => __('Source unavailable'),
            'source_changed' => __('Source changed during check'),
            'duplicate' => __('Duplicate finding'),
        ];
    @endphp
    @include('translation-workbench::livewire.translation-workbench.tabs.source-issues.filters')
    {{-- Table --}}
    <flux:table container:class="mt-3 max-h120 rounded-t-lg overflow-auto">
        {{-- Table Header Columns --}}
        <flux:table.columns class="bg-white dark:bg-zinc-800">
            {{-- Table Header Column Cause --}}
            <flux:table.column>
                <flux:callout.text class="ml-2">
                    {{ __('Cause') }}
                </flux:callout.text>
            </flux:table.column>
            {{-- Table Header Column Finding and captured position --}}
            <flux:table.column>
                <flux:callout.text>
                    {{ __('Finding and captured position') }}
                </flux:callout.text>
            </flux:table.column>
            {{-- Table Header Column Blocked replacements --}}
            <flux:table.column>
                <flux:callout.text>
                    {{ __('Blocked replacements') }}
                </flux:callout.text>
            </flux:table.column>
            {{-- Table Header Column Source observations --}}
            <flux:table.column>
                <flux:callout.text>{{ __('Source observations') }}</flux:callout.text>
            </flux:table.column>
            {{-- Table Header Column Actions --}}
            <flux:table.column>
                <flux:callout.text class="mr-3">
                    {{ __('Actions') }}
                </flux:callout.text>
            </flux:table.column>
        </flux:table.columns>
        {{-- Table Body Rows --}}
        <flux:table.rows>
            @forelse ($sourceIssues as $issue)
                {{-- Table Body Row --}}
                <flux:table.row :key="$issue['id']">
                    {{-- Table Body Row Column Cause --}}
                    <flux:table.cell class="max-w-sm whitespace-normal">
                        <flux:callout.text class="ml-2">
                            {{ match ($issue['reason']) {
                                'ambiguous' => __('Several matching occurrences'),
                                'no_match' => __('Stored occurrence could not be matched'),
                                'occupied_destination' => __('Matching position is occupied'),
                                'source_usage' => __('Source usage needs review'),
                                default => $issue['blocker'] ?? __('Unresolved scan position'),
                            } }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Table Body Row Column Finding and captured position --}}
                    <flux:table.cell class="max-w-sm whitespace-normal">
                        <x-translation-workbench::ui.common.details
                            label-width="6rem"
                            row-gap="0.125rem"
                        >
                            <x-translation-workbench::ui.common.details.item
                                class="tabular-nums"
                                :label="__('Finding ID')"
                                :item="'#' . $issue['id']"
                            />
                            <x-translation-workbench::ui.common.details.item :label="__('Expression')">
                                <x-translation-workbench::ui.common.text-preview
                                    :text="$issue['raw_expression']"
                                    :length="56"
                                    :mono="true"
                                    :heading="__('Captured source expression')"
                                />
                            </x-translation-workbench::ui.common.details.item>
                            <x-translation-workbench::ui.common.details.item :label="__('Captured in')">
                                <x-translation-workbench::ui.common.path-preview
                                    :path="$issue['path']"
                                    :line="$issue['source_line']"
                                    :column="$issue['source_column']"
                                />
                            </x-translation-workbench::ui.common.details.item>
                        </x-translation-workbench::ui.common.details>
                    </flux:table.cell>
                    {{-- Table Body Row Column Blocked replacements --}}
                    <flux:table.cell class="max-w-xs whitespace-normal">
                        @forelse ($issue['affected'] ?? [] as $finding => $key)
                            <x-translation-workbench::ui.common.details
                                class="mt-2 first:mt-0"
                                label-width="6rem"
                                row-gap="0.125rem"
                            >
                                <x-translation-workbench::ui.common.details.item
                                    class="tabular-nums"
                                    :label="__('Finding ID')"
                                    :item="'#' . $finding"
                                />
                                <x-translation-workbench::ui.common.details.item
                                    class="tabular-nums"
                                    :label="__('Key ID')"
                                    :item="'#' . $key"
                                />
                            </x-translation-workbench::ui.common.details>
                        @empty
                            —
                        @endforelse
                    </flux:table.cell>
                    {{-- Table Body Row Column Source observations --}}
                    <flux:table.cell>
                        <div class="flex max-w-xs flex-wrap gap-1">
                            @if (!empty($issue['retained']))
                                @include('translation-workbench::livewire.translation-workbench.shared.duplicate-finding-badge', ['retained' => $issue['retained'], 'findingId' => $issue['id']])
                            @endif
                            @foreach ($issue['observations'] as $kind => $count)
                                @php
                                    [$label, $color, $detail] = match ($kind) {
                                        'reformatted' => [$observationLabels['reformatted'], 'sky', __('The call has the same parsed structure as the captured expression.')],
                                        'changed' => [$observationLabels['changed'], 'amber', __('The function and first argument match, but the call structure differs.')],
                                        'moved' => [$observationLabels['moved'], 'sky', __('A candidate occurs at a different line or column.')],
                                        'ambiguous' => [$observationLabels['ambiguous'], 'amber', __('Compare the candidates to identify the original occurrence.')],
                                        'occupied' => [$observationLabels['occupied'], 'red', __('Another active finding is recorded at a candidate position. Compare source shows its finding ID.')],
                                        'expression_missing' => [$observationLabels['expression_missing'], 'amber', __('No candidate was found in the current file.')],
                                        'source_unavailable' => [$observationLabels['source_unavailable'], 'red', __('The source file could not be read.')],
                                        'source_changed' => [$observationLabels['source_changed'], 'amber', __('The file changed while its positions were being checked.')],
                                    };
                                @endphp
                                <flux:tooltip>
                                    <flux:badge size="sm" :color="$color" tabindex="0">
                                        {{ $label }}@if ($count > 1) · {{ $count }}@endif
                                    </flux:badge>
                                    <x-translation-workbench::ui.common.tooltip-content :heading="$label" :text="$detail" />
                                </flux:tooltip>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    {{-- Table Body Row Column Actions --}}
                    <flux:table.cell>
                        @if (isset($issue['pending_run']))
                            <flux:button.group>
                                <flux:button
                                    size="sm"
                                    wire:click="openSourceRun('{{ $issue['pending_run'] }}')"
                                >
                                    <flux:icon.package-open class="size-5" />
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    wire:click="openSourceRun('{{ $issue['pending_run'] }}')"
                                >
                                    {{ __('Open prepared operation') }}
                                </flux:button>
                            </flux:button.group>
                        @endif
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                square
                                wire:click="openSourceIssue({{ $issue['id'] }})"
                            >
                                <flux:icon.scale class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                wire:click="openSourceIssue({{ $issue['id'] }})"
                            >
                                {{ __('Compare source') }}
                            </flux:button>
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">{{ $sourceIssueTotal ? __('No source issues match these filters.') : __('No open source issues.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <flux:pagination :paginator="$sourceIssues" />
</flux:callout>
