@php
    $workSteps = [
        'translations' => __('Edit translations'),
        'review' => __('Review translations'),
        'publication' => __('Publish translations'),
        'source' => __('Check source replacement'),
        'scan' => __('Confirm source by scan'),
        'complete' => __('Completed according to saved records'),
        'archived' => __('No active linked findings'),
    ];
@endphp

{{-- Table --}}
<flux:table container:class="max-h-120 overflow-auto rounded-t-lg">
    {{-- Table Header Row --}}
    <flux:table.columns
        class="bg-white dark:bg-zinc-800"
        sticky
    >
        {{-- Table Header Column Key ID --}}
        <flux:table.column>
            <flux:callout.heading class="ml-2">
                {{ __('Key ID') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Table Header Column Translation Key --}}
        <flux:table.column>
            <flux:callout.heading>
                {{ __('Translation key') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Table Header Column Findings --}}
        <flux:table.column>
            <flux:callout.heading>
                {{ __('Findings') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Table Header Column Languages --}}
        <flux:table.column>
            <flux:callout.heading>
                {{ __('Languages') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Table Header Column Next Step --}}
        <flux:table.column>
            <flux:callout.heading>
                {{ __('Next step') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Table Header Column Last Manual Edit --}}
        <flux:table.column>
            <flux:callout.heading>
                {{ __('Last manual edit') }}
            </flux:callout.heading>
        </flux:table.column>
        {{-- Table Header Column Actions --}}
        <flux:table.column>
            <flux:callout.heading class="mr-3">
                {{ __('Actions') }}
            </flux:callout.heading>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Body Rows --}}
    <flux:table.rows>
        @forelse ($workRows as $work)
            {{-- Table Body Row --}}
            <flux:table.row :key="'work-'.$work['key_id']">
                {{-- Table Body Column Key ID --}}
                <flux:table.cell>
                    <flux:callout.text class="ml-2">
                        #{{ $work['key_id'] }}
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Table Body Column Translation Key --}}
                <flux:table.cell>
                    <x-translation-workbench::ui.common.text-preview
                        :text="($work['namespace'] !== '' ? $work['namespace'].'::' : '').$work['full_name']"
                        :heading="__('Translation key')"
                        :length="55"
                        :mono="true"
                    />
                </flux:table.cell>
                {{-- Table Body Column Finding --}}
                <flux:table.cell>
                    <div class="flex flex-col items-start gap-1">
                        <flux:callout.text>
                            {{ __(':count active', ['count' => $work['finding_count']]) }}
                        </flux:callout.text>
                        @if ($work['finding_count'] > 1)
                            <flux:badge
                                color="sky"
                                size="sm"
                            >{{ __('Shared key') }}</flux:badge>
                        @endif
                        @if ($work['finding_id'])
                            <flux:callout.text class="text-xs">
                                {{ __('Continue with finding #:id', ['id' => $work['finding_id']]) }}
                            </flux:callout.text>
                        @endif
                    </div>
                </flux:table.cell>
                {{-- Table Body Column Language --}}
                <flux:table.cell>
                    <div class="flex flex-wrap items-center gap-1">
                        @foreach (array_filter(array_map('trim', explode(',', $work['locales'] ?? ''))) as $workLocale)
                            <flux:tooltip toggleable>
                                <flux:button
                                    type="button"
                                    square
                                    variant="ghost"
                                    size="xs"
                                    :aria-label="$workLocale"
                                >
                                    <flux:flag
                                        size="xs"
                                        circle
                                        :country="$translationFlagCountries[$workLocale] ?? null"
                                    />
                                </flux:button>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Language')"
                                    :text="$workLocale"
                                />
                            </flux:tooltip>
                        @endforeach
                    </div>
                </flux:table.cell>
                {{-- Table Body Column NextStep --}}
                <flux:table.cell>
                    <flux:badge
                        :color="$work['step'] === 'complete' ? 'green' : ($work['step'] === 'archived' ? 'zinc' : 'amber')"
                        size="sm"
                    >
                        {{ !empty($work['open_publication']) ? __('Resume publication') : (!empty($work['open_source']) ? __('Resume source replacement') : $workSteps[$work['step']]) }}
                    </flux:badge>
                    @if ($work['source_pending'] > 0)
                        <flux:callout.text class="mt-1 text-xs">
                            {{ __('Source replacement pending: :count findings', ['count' => $work['source_pending']]) }}
                        </flux:callout.text>
                    @endif
                </flux:table.cell>
                {{-- Table Body ColumnLast Manual Edit --}}
                <flux:table.cell>
                    <div class="flex flex-col gap-1">
                        <x-ui.date-time.date-time :value="$work['occurred_at']" />
                        <flux:callout.text>
                            {{ $workActors[$work['actor_id']]['name'] ?? '' ?: __('User #:id', ['id' => $work['actor_id']]) }}
                        </flux:callout.text>
                    </div>
                </flux:table.cell>
                {{-- Table Body Column Actions --}}
                <flux:table.cell>
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:tooltip>
                            <flux:button
                                size="sm"
                                variant="outline"
                                square
                                wire:click="openWorkflowStatus({{ (int) $work['finding_id'] }})"
                                wire:loading.attr="disabled"
                                :disabled="!$work['finding_id']"
                                :aria-label="__('Workflow status')"
                            >
                                <flux:icon.queue-list size="5" />
                            </flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Workflow status')"
                                :text="__(
                                    'View completed and outstanding steps for this finding and its assigned translation key.',
                                )"
                            />
                        </flux:tooltip>
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="green"
                                square
                                wire:click="resumeWork({{ $work['key_id'] }})"
                                wire:loading.attr="disabled"
                                wire:target="resumeWork"
                                :disabled="!$work['finding_id']"
                            >
                                <flux:icon.arrow-right class="size-5" />
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="filled"
                                color="green"
                                wire:click="resumeWork({{ $work['key_id'] }})"
                                wire:loading.attr="disabled"
                                wire:target="resumeWork"
                                :disabled="!$work['finding_id']"
                            >
                                {{ empty($work['open_publication']) && empty($work['open_source']) && in_array($work['step'], ['complete', 'archived'], true) ? __('Workflow status') : __('Continue working') }}
                            </flux:button>
                        </flux:button.group>
                    </div>
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Table Body Row Empty --}}
            <flux:table.row>
                {{-- Table Body Column Empty --}}
                <flux:table.cell colspan="7">
                    <flux:callout.text class="ml-2">
                        {{ __('No manually edited work items match this selection.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
