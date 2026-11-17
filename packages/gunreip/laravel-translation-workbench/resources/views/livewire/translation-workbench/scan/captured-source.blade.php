                        @unless ($scanUsageOpen)
                            <flux:error name="scanUsage" />
                        @endunless

                        @if ($scanUsageNotice)
                            <flux:callout
                                color="green"
                                icon="robot-vacuum"
                                :heading="__('Scan usage information')"
                                :text="$scanUsageNotice"
                            />
                        @endif

                        @if ($scanDecisionReturnRun !== '')
                            <flux:callout
                                color="sky"
                                icon="origami"
                                text="{{ __('You are viewing the original captured source and its latest usage decision. The decision recorded by the selected scan remains unchanged. Back to scan results returns to that scan.') }}"
                            />
                        @endif

                        @forelse ($captureRows as $capture)
                            <flux:callout
                                icon="magnifying-glass"
                                color="sky"
                            >
                                @php
                                    $decision = $capture['usage_decision'] ?? null;
                                @endphp
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:callout.heading>
                                        {{ __('Line :line, column :column', ['line' => $capture['source_line'], 'column' => $capture['source_column']]) }}
                                    </flux:callout.heading>
                                    @if ($capture['can_classify'] ?? false)
                                        <div class="ms-auto flex flex-wrap items-center justify-end gap-2">
                                            @if ($decision)
                                                <flux:badge
                                                    :color="$decision['decision'] === 'database_value' ? 'sky' : 'amber'"
                                                >
                                                    {{ $decision['decision'] === 'database_value' ? __('DatabaseValue · decision saved') : __('Translate · work remains open') }}
                                                </flux:badge>
                                            @else
                                                <flux:badge color="amber">
                                                    {{ __('No usage decision saved') }}
                                                </flux:badge>
                                            @endif
                                            <flux:button.group>
                                                <flux:button
                                                    size="sm"
                                                    variant="outline"
                                                    wire:click="openScanUsage({{ $capture['capture_index'] }})"
                                                >
                                                    <flux:icon.glasses class="size-5" />
                                                </flux:button>
                                                <flux:button
                                                    size="sm"
                                                    variant="outline"
                                                    wire:click="openScanUsage({{ $capture['capture_index'] }})"
                                                >
                                                    {{ $decision ? __('Review usage decision') : __('Classify source usage') }}
                                                </flux:button>
                                            </flux:button.group>
                                        </div>
                                    @endif
                                </div>
                                <x-translation-workbench::ui.common.details
                                    class="mt-3"
                                    label-width="14rem"
                                >
                                    <x-translation-workbench::ui.common.details.item :label="__('Captured source expression')">
                                        <flux:callout.text><span
                                                class="whitespace-pre-wrap break-all font-mono">{{ $capture['raw_expression'] }}</span>
                                        </flux:callout.text>
                                    </x-translation-workbench::ui.common.details.item>
                                    <x-translation-workbench::ui.common.details.item :label="__('Literal from source')">
                                        <flux:callout.text><span
                                                class="wrap-break-word whitespace-pre-wrap">{{ $capture['raw_literal'] ?? 'NULL' }}</span>
                                        </flux:callout.text>
                                    </x-translation-workbench::ui.common.details.item>
                                    <x-translation-workbench::ui.common.details.item
                                        class="break-all"
                                        :label="__('Translation key found')"
                                        :item="$capture['found_translation_key'] ?? 'NULL'"
                                    />
                                    <x-translation-workbench::ui.common.details.item
                                        class="break-all"
                                        :label="__('Capture reason')"
                                    >
                                        @php
                                            $explanation = match ($capture['reason']) {
                                                'literal_candidate' => __('The first argument is text written directly in the translation call. The scanner captured it as the source literal.'),
                                                'key_with_exact_lang_evidence' => __('The first argument was classified as a translation key. The scan found an exact entry for it in the source-language data.'),
                                                'key_like_argument_without_lang_evidence' => __('The argument looks like a translation key, but the scan found no matching source-language entry. It may also be ordinary text.'),
                                                'dynamic_or_nonliteral_argument' => __('The first argument is a variable or another expression. The scanner could not read a fixed string from it.'),
                                                'incomplete_expression' => __('The scanner located the start of the call but could not capture the complete expression.'),
                                                'special_call_requires_context' => __('The meaning of the captured string depends on how this particular call uses its arguments.'),
                                                'commented_source' => __('The expression occurs inside a comment, so the scanner excluded it.'),
                                                'context_excluded_string' => __('The apparent call is part of a quoted string, so the scanner excluded it.'),
                                                'context_excluded_verbatim' => __('The expression is inside a Blade verbatim block and is rendered as text.'),
                                                'context_excluded_escaped_blade' => __('The Blade expression is escaped and is rendered as text.'),
                                                'context_unresolved' => __('The scanner could not confirm an active translation call at this position.'),
                                                default => null,
                                            };
                                        @endphp
                                        @if ($explanation !== null)
                                            <flux:tooltip>
                                                <code tabindex="0" class="cursor-help">{{ $capture['reason'] }}</code>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Capture reason')"
                                                    :text="$explanation"
                                                />
                                            </flux:tooltip>
                                        @else
                                            <code>{{ $capture['reason'] }}</code>
                                        @endif
                                    </x-translation-workbench::ui.common.details.item>
                                </x-translation-workbench::ui.common.details>
                                @if (($capture['can_classify'] ?? false) && $decision)
                                    <flux:callout
                                        class="mt-3"
                                        color="sky"
                                        icon="gavel"
                                        heading="{{ __('Decision applied') }}"
                                    >
                                        <div class="mt-3 space-y-2">
                                            <flux:callout.text class="wrap-break-word">
                                                <span class="whitespace-pre-wrap">{{ $decision['reason'] }}</span>
                                            </flux:callout.text>
                                            @php
                                                $actor = $decisionActors[$decision['actor_id']] ?? null;
                                            @endphp
                                            <div class="flex flex-wrap items-center gap-2">
                                                <flux:avatar
                                                    icon:variant="outline"
                                                    size="sm"
                                                    circle
                                                    :src="$actor['avatar_url'] ?? null"
                                                    :name="$actor['name'] ?? null"
                                                />
                                                <flux:callout.text>
                                                    {{ __('Decided by') }}
                                                    {{ $actor['name'] ?? '' ?: ($actor['email'] ?? '' ?: __('User')) }}
                                                    #{{ $decision['actor_id'] }}
                                                </flux:callout.text>
                                                <span aria-hidden="true">·</span>
                                                <x-ui.date-time.date-time :value="$decision['occurred_at']" />
                                            </div>
                                            <flux:callout.text>
                                                {{ __('This is the latest decision for the exact captured source. It does not change this historical scan result. A subsequent scan checks whether it can be applied.') }}
                                            </flux:callout.text>
                                        </div>
                                    </flux:callout>
                                @endif
                            </flux:callout>
                        @empty
                            <flux:text>
                                {{ __('This scan did not retain individual source captures for this file. Review the recorded reasons and preserved findings.') }}
                            </flux:text>
                        @endforelse
                        <flux:pagination :paginator="$captureRows" />
