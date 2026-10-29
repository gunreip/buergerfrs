@props(['steps', 'countries' => [], 'testMode' => false, 'navigable' => false])
<flux:timeline align="start">
    @foreach ($steps as $step)
        <flux:timeline.item :status="$step['status']">
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
                            {{ $step['label'] }}
                        </flux:callout.heading>
                        @if ($navigable && $step['status'] === 'current')
                            <flux:tooltip>
                                <flux:button size="xs" variant="outline" icon:trailing="arrow-right" wire:click="continueWorkflow" wire:loading.attr="disabled" wire:target="continueWorkflow">
                                    {{ __('Next step') }}
                                </flux:button>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Continue this workflow')"
                                    :text="match ($step['id']) {
                                        'assignment' => __('Open the translation key assignment for this finding.'),
                                        'draft' => __('Open Translation values for this finding. Existing unsaved input is retained when the editor is already open.'),
                                        'review' => collect($step['languages'])->contains('state', 'review')
                                            ? __('Show pending reviews for this translation key in all languages.')
                                            : __('Open Translation values to submit or review the saved drafts.'),
                                        'publication' => __('Show approved revisions awaiting publication for this translation key in all languages.'),
                                        'source' => __('Open the source replacement preview for this finding. No source code is changed by opening the preview.'),
                                        'scan' => __('Go to Scan results and the scan commands. This button does not run a scan.'),
                                        default => __('Open the next workflow step.'),
                                    }"
                                />
                            </flux:tooltip>
                        @else
                            <flux:callout.text size="sm">
                                {{ $step['done'] ? __('Complete') : ($step['status'] === 'current' ? __('Next step') : __('Pending')) }}
                            </flux:callout.text>
                        @endif
                        @if ($step['hint'] ?? null)
                            <flux:tooltip toggleable>
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    icon="information-circle"
                                    :aria-label="__('Details')"
                                />
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="$step['label']"
                                    :text="[__($step['hint']), $step['action'] ?? '']"
                                >
                                    @if ($step['scan_action'] ?? false)
                                        @if ($testMode)
                                            {{ __('The Artisan scan operates on the working database, not on these UI test cases.') }}
                                        @else
                                            @foreach ([
                                                ['label' => __('Preview scan (read-only)'), 'command' => 'php artisan translation-workbench:generation-scan'],
                                                ['label' => __('Save scan results'), 'command' => 'php artisan translation-workbench:generation-scan --apply'],
                                            ] as $commandItem)
                                                <span class="mt-2 block">{{ $commandItem['label'] }}:</span>
                                                <span class="flex items-start gap-2" x-data="{ copied: false, failed: false }">
                                                    <code class="min-w-0 break-all">{{ $commandItem['command'] }}</code>
                                                    <flux:button
                                                        type="button"
                                                        size="xs"
                                                        variant="ghost"
                                                        class="shrink-0"
                                                        :aria-label="__('Copy command')"
                                                        :title="__('Copy command')"
                                                        x-on:click.stop="try { await navigator.clipboard.writeText({{ \Illuminate\Support\Js::from($commandItem['command']) }}); copied = true; failed = false; setTimeout(() => copied = false, 2000); } catch { copied = false; failed = true; }"
                                                    >
                                                        <flux:icon.clipboard-document class="size-4" x-show="!copied" />
                                                        <flux:icon.check class="size-4 text-green-500" x-show="copied" x-cloak />
                                                    </flux:button>
                                                    <span class="text-red-500" role="status" x-show="failed" x-cloak>{{ __('Could not copy command.') }}</span>
                                                </span>
                                            @endforeach
                                        @endif
                                    @endif
                                </x-translation-workbench::ui.common.tooltip-content>
                            </flux:tooltip>
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
                                                        default => __('This step is still pending for this language.'),
                                                    };
                                            @endphp
                                            <flux:tooltip
                                                toggleable
                                                :content="$language['locale'].
                                                ' · '.$stateLabel.($language['revision'] ? ' · #'.$language[
                                                    'revision'] : '').
                                                ' — '.$stepExplanation"
                                            >
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
                                                        :name="$language['done'] ? 'check' : 'x-mark'"
                                                        variant="micro"
                                                        :class="$language['done'] ? 'text-green-500' : 'text-red-500'"
                                                    />
                                                </flux:button>
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
    @endforeach
</flux:timeline>
