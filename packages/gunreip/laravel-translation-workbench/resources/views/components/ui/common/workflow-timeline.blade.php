@props(['steps', 'countries' => [], 'testMode' => false, 'navigable' => false, 'sharedKey' => null])
@php
    $timelineEntries = [];
    $translationSteps = collect($steps)
        ->filter(fn($step) => in_array($step['id'], ['draft', 'review', 'publication'], true))
        ->values()
        ->all();
    $groupAdded = false;
    foreach ($steps as $entry) {
        if ($sharedKey && in_array($entry['id'], ['draft', 'review', 'publication'], true)) {
            if (!$groupAdded) {
                $timelineEntries[] = ['translation_group' => $translationSteps];
                $groupAdded = true;
            }
        } else {
            $timelineEntries[] = $entry;
        }
    }
@endphp

<flux:timeline align="start">
    @foreach ($timelineEntries as $step)
        @if (isset($step['translation_group']))
            <flux:timeline.item wire:key="workflow-shared-block-{{ $sharedKey['id'] }}">
                <flux:timeline.block>
                    <flux:callout
                        color="green"
                        icon="waypoints"
                        :heading="__('Shared key #:id', ['id' => $sharedKey['id']])"
                        :text="$sharedKey['name']"
                    >
                        <flux:timeline>
                            @foreach ($step['translation_group'] as $translationStep)
                                <flux:timeline.item
                                    wire:key="workflow-shared-{{ $sharedKey['id'] }}-{{ $translationStep['id'] }}"
                                >
                                    <flux:timeline.indicator :color="$translationStep['color']">
                                        <flux:icon
                                            :name="$translationStep['icon']"
                                            variant="micro"
                                        />
                                    </flux:timeline.indicator>
                                    <flux:timeline.content>
                                        <flux:heading>
                                            @if (($translationStep['description'] ?? null) || ($translationStep['hint'] ?? null))
                                                <flux:tooltip toggleable>
                                                    <button type="button" class="cursor-help text-left">{{ $translationStep['label'] }}</button>
                                                    <x-translation-workbench::ui.common.tooltip-content
                                                        :heading="$translationStep['label']"
                                                        :text="array_filter([
                                                            $translationStep['description'] ?? '',
                                                            $translationStep['hint'] ?? '',
                                                            $translationStep['action'] ?? '',
                                                        ])"
                                                    />
                                                </flux:tooltip>
                                            @else
                                                {{ $translationStep['label'] }}
                                            @endif
                                        </flux:heading>
                                        @if ($navigable && $translationStep['status'] === 'current')
                                            <flux:tooltip
                                                :content="__('Continue the translation workflow for this shared key.')"
                                            >
                                                <flux:button
                                                    size="xs"
                                                    variant="filled"
                                                    color="sky"
                                                    icon:trailing="arrow-right"
                                                    wire:click="continueWorkflow"
                                                    wire:loading.attr="disabled"
                                                    wire:target="continueWorkflow"
                                                >
                                                    <flux:icon.arrow-right-from-line />
                                                </flux:button>
                                                <flux:button
                                                    size="xs"
                                                    variant="filled"
                                                    color="sky"
                                                    icon:trailing="arrow-right"
                                                    wire:click="continueWorkflow"
                                                    wire:loading.attr="disabled"
                                                    wire:target="continueWorkflow"
                                                >
                                                    {{ __('Next step') }}
                                                </flux:button>
                                            </flux:tooltip>
                                        @else
                                            <flux:text>
                                                {{ $translationStep['summary'] ?? ($translationStep['done'] ? __('Complete') : ($translationStep['status'] === 'current' ? __('Next step') : __('Pending'))) }}
                                            </flux:text>
                                        @endif
                                        @foreach (collect($translationStep['languages'])->groupBy(fn($language) => strtolower(explode('-', str_replace('_', '-', $language['locale']))[0])) as $languages)
                                            <flux:callout.text>
                                                @foreach ($languages->sortBy(fn($language) => str_contains(str_replace('_', '-', $language['locale']), '-') ? 1 : 0) as $language)
                                                    @php
                                                        $languageState = match ($language['state']) {
                                                            'existing_publication' => __(
                                                                'Existing publication verified in the language file',
                                                            ),
                                                            'published' => __('Published'),
                                                            'approved' => __('Approved'),
                                                            'review' => __('Waiting for review'),
                                                            'draft' => __('Draft'),
                                                            default => __('Not saved'),
                                                        };
                                                        $languageDetails =
                                                            $language['locale'] .
                                                            ' · ' .
                                                            $languageState .
                                                            ($language['revision']
                                                                ? ' · #' . $language['revision']
                                                                : '') .
                                                            ' — ' .
                                                            ($language['review_unrecorded'] ?? false
                                                                ? __('No review was recorded for this imported value.')
                                                                : ($language['done']
                                                                    ? __('This step is complete for this language.')
                                                                    : __(
                                                                        'This step is still pending for this language.',
                                                                    )));
                                                    @endphp
                                                    <flux:tooltip toggleable>
                                                        <flux:button
                                                            size="xs"
                                                            variant="ghost"
                                                            :aria-label="$language['locale'].
                                                            ' · '.$languageState"
                                                        >
                                                            @if ($countries[$language['locale']] ?? null)
                                                                <flux:flag
                                                                    :country="$countries[$language['locale']]"
                                                                    size="xs"
                                                                />
                                                            @endif
                                                            {{ $language['locale'] }}
                                                            <flux:icon
                                                                :name="($language['review_unrecorded'] ?? false) ?
                                                                'information-circle' : ($language['done'] ? 'check' :
                                                                    'x-mark')"
                                                                variant="micro"
                                                            />
                                                        </flux:button>
                                                        <x-translation-workbench::ui.common.tooltip-content
                                                            :heading="__('Language')"
                                                            :text="$languageDetails"
                                                        >
                                                            @if ($translationStep['id'] === 'publication' && !empty($language['file']))
                                                                <p class="mt-2 break-all">
                                                                    {{ $language['file']['actual'] ? __('Language file') : __('Expected language file') }}:
                                                                    lang/{{ $language['file']['path'] }}</p>
                                                                <p class="mt-2">{{ __($language['file']['detail']) }}
                                                                </p>
                                                            @endif
                                                        </x-translation-workbench::ui.common.tooltip-content>
                                                    </flux:tooltip>
                                                @endforeach
                                            </flux:callout.text>
                                        @endforeach
                                    </flux:timeline.content>
                                </flux:timeline.item>
                            @endforeach
                        </flux:timeline>
                    </flux:callout>
                </flux:timeline.block>
            </flux:timeline.item>
        @else
            <flux:timeline.item
                wire:key="workflow-step-{{ $step['id'] }}"
                :status="$step['status']"
            >
                {{-- Explicit colors take precedence over Flux's item-status selectors. --}}
                <flux:timeline.indicator
                    :color="$step['color']"
                    :style="'background-color: var(--color-'.$step['color'].
                    '-500); border-color: var(--color-'.$step['color'].
                    '-500); color: '.($step['color'] === 'amber' ? 'var(--color-zinc-950)' : 'var(--color-white)').
                    ';'"
                >
                    <flux:icon
                        :name="$step['icon']"
                        variant="micro"
                    />
                </flux:timeline.indicator>
                <flux:timeline.content>
                    <div class="flex flex-nowrap items-start gap-4">
                        <div class="w-48 min-w-0 flex-none">
                            <flux:callout.heading>
                            @if (($step['description'] ?? null) || ($step['hint'] ?? null))
                                <flux:tooltip toggleable>
                                    <button type="button" class="cursor-help text-left">{{ $step['label'] }}</button>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="$step['label']"
                                        :text="array_filter([
                                            $step['description'] ?? '',
                                            $step['hint'] ?? '',
                                            $step['action'] ?? '',
                                        ])"
                                    >
                                        @if ($step['scan_action'] ?? false)
                                            @if ($testMode)
                                                {{ __('The Artisan scan operates on the working database, not on these UI test cases.') }}
                                            @else
                                                @foreach ([['label' => __('Preview scan (read-only)'), 'command' => 'php artisan translation-workbench:generation-scan'], ['label' => __('Save scan results'), 'command' => 'php artisan translation-workbench:generation-scan --apply']] as $commandItem)
                                                    <span class="mt-2 block">{{ $commandItem['label'] }}:</span>
                                                    <span
                                                        class="flex items-start gap-2"
                                                        x-data="{ copied: false, failed: false }"
                                                    >
                                                        <code
                                                            class="min-w-0 break-all">{{ $commandItem['command'] }}</code>
                                                        <flux:button
                                                            class="shrink-0"
                                                            type="button"
                                                            size="xs"
                                                            variant="ghost"
                                                            :aria-label="__('Copy command')"
                                                            :title="__('Copy command')"
                                                            x-on:click.stop="try { await navigator.clipboard.writeText({{ \Illuminate\Support\Js::from($commandItem['command']) }}); copied = true; failed = false; setTimeout(() => copied = false, 2000); } catch { copied = false; failed = true; }"
                                                        >
                                                            <flux:icon.clipboard-document
                                                                class="size-4"
                                                                x-show="!copied"
                                                            />
                                                            <flux:icon.check
                                                                class="size-4 text-green-500"
                                                                x-show="copied"
                                                                x-cloak
                                                            />
                                                        </flux:button>
                                                        <span
                                                            class="text-red-500"
                                                            role="status"
                                                            x-show="failed"
                                                            x-cloak
                                                        >
                                                            {{ __('Could not copy command.') }}
                                                        </span>
                                                    </span>
                                                @endforeach
                                            @endif
                                        @endif
                                    </x-translation-workbench::ui.common.tooltip-content>
                                </flux:tooltip>
                            @else
                                {{ $step['label'] }}
                            @endif
                            </flux:callout.heading>
                            @if ($navigable && $step['status'] === 'current')
                                <flux:tooltip>
                                    <flux:button.group>
                                        <flux:button
                                            size="xs"
                                            variant="filled"
                                            color="sky"
                                            wire:click="continueWorkflow"
                                            wire:loading.attr="disabled"
                                            wire:target="continueWorkflow"
                                        >
                                            {{ __('Next step') }}
                                        </flux:button>
                                        <flux:button
                                            size="xs"
                                            variant="filled"
                                            color="sky"
                                            wire:click="continueWorkflow"
                                            wire:loading.attr="disabled"
                                            wire:target="continueWorkflow"
                                        >
                                            <flux:icon.arrow-right class="size-4" />
                                        </flux:button>
                                    </flux:button.group>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Continue this workflow')"
                                        :text="match ($step['id']) {
                                            'assignment' => __('Open the translation key assignment for this finding.'),
                                            'draft' => __(
                                                'Open Translation values for this finding. Existing unsaved input is retained when the editor is already open.',
                                            ),
                                            'review' => collect($step['languages'])->contains('state', 'review')
                                                ? __('Show pending reviews for this translation key in all languages.')
                                                : __('Open Translation values to submit or review the saved drafts.'),
                                            'publication' => __(
                                                'Show approved revisions awaiting publication for this translation key in all languages.',
                                            ),
                                            'source' => __(
                                                'Open the read-only Source replacement tab for this finding. Replacement actions are currently disabled.',
                                            ),
                                            'scan' => __(
                                                'Go to Scan results and the scan commands. This button does not run a scan.',
                                            ),
                                            default => __('Open the next workflow step.'),
                                        }"
                                    />
                                </flux:tooltip>
                            @else
                                <flux:callout.text size="sm">
                                    {{ $step['summary'] ?? ($step['done'] ? __('Complete') : ($step['status'] === 'current' ? __('Next step') : __('Pending'))) }}
                                </flux:callout.text>
                            @endif

                        </div>
                        @if ($step['languages'])
                            <div class="min-w-0 flex-1 space-y-2">
                                @foreach (collect($step['languages'])->groupBy(fn($item) => strtolower(explode('-', str_replace('_', '-', $item['locale']))[0])) as $languages)
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        @foreach ($languages->sortBy(fn($item) => str_contains(str_replace('_', '-', $item['locale']), '-') ? 1 : 0) as $language)
                                            <div class="flex shrink-0 items-center">
                                                @php
                                                    $stateLabel = match ($language['state']) {
                                                        'existing_publication' => __(
                                                            'Existing publication verified in the language file',
                                                        ),
                                                        'published' => __('Published'),
                                                        'approved' => __('Approved'),
                                                        'review' => __('Waiting for review'),
                                                        'draft' => __('Draft'),
                                                        default => __('Not saved'),
                                                    };
                                                    $stepExplanation = $language['done']
                                                        ? __('This step is complete for this language.')
                                                        : match ($step['id']) {
                                                            'draft' => __(
                                                                'No translation revision has been saved for this language yet.',
                                                            ),
                                                            'review' => $language['state'] === 'missing'
                                                                ? __(
                                                                    'Save a translation for this language before submitting it for review.',
                                                                )
                                                                : ($language['state'] === 'review'
                                                                    ? __(
                                                                        'This translation has been submitted and is waiting for approval.',
                                                                    )
                                                                    : __(
                                                                        'The current translation revision does not have a valid approval. Submit it for review and approve it.',
                                                                    )),
                                                            'publication' => $language['state'] === 'missing'
                                                                ? __(
                                                                    'No translation has been saved for this language yet. Save, review and publish it.',
                                                                )
                                                                : ($language['state'] === 'approved'
                                                                    ? __(
                                                                        'The current revision is approved but has not been published to the language file yet.',
                                                                    )
                                                                    : __(
                                                                        'The current revision is not approved and published. An older translation may already exist in the language file.',
                                                                    )),
                                                            default => __(
                                                                'This step is still pending for this language.',
                                                            ),
                                                        };
                                                    if ($language['review_unrecorded'] ?? false) {
                                                        $stepExplanation = __(
                                                            'No review was recorded for this imported value.',
                                                        );
                                                    }
                                                @endphp
                                                <flux:tooltip toggleable>
                                                    <flux:button
                                                        size="xs"
                                                        variant="ghost"
                                                        :aria-label="$language['locale'].
                                                        ' · '.$stateLabel"
                                                    >
                                                        @if ($countries[$language['locale']] ?? null)
                                                            <flux:flag
                                                                :country="$countries[$language['locale']]"
                                                                size="xs"
                                                            />
                                                        @endif
                                                        <flux:callout.heading>
                                                            {{ $language['locale'] }}
                                                        </flux:callout.heading>
                                                        <flux:icon
                                                            :name="($language['review_unrecorded'] ?? false) ?
                                                            'information-circle' : ($language['done'] ? 'check' :
                                                                'x-mark')"
                                                            variant="micro"
                                                            :class="($language['review_unrecorded'] ?? false) ?
                                                            'text-zinc-500' : ($language['done'] ? 'text-green-500' :
                                                                'text-red-500')"
                                                        />
                                                    </flux:button>
                                                    <x-translation-workbench::ui.common.tooltip-content
                                                        :heading="__('Language')"
                                                        :text="$language['locale'] .
                                                            ' · ' .
                                                            $stateLabel .
                                                            ($language['revision']
                                                                ? ' · #' . $language['revision']
                                                                : '') .
                                                            ' — ' .
                                                            $stepExplanation"
                                                    >
                                                        @if ($step['id'] === 'publication' && !empty($language['file']))
                                                            <p class="mt-2 break-all">
                                                                {{ $language['file']['actual'] ? __('Language file') : __('Expected language file') }}:
                                                                lang/{{ $language['file']['path'] }}</p>
                                                            <p class="mt-2">{{ __($language['file']['detail']) }}</p>
                                                        @endif
                                                    </x-translation-workbench::ui.common.tooltip-content>
                                                </flux:tooltip>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </flux:timeline.content>
            </flux:timeline.item>
        @endif
    @endforeach
</flux:timeline>
