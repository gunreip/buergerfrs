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
                                @php($decision = $capture['usage_decision'] ?? null)
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
                                        <code>{{ $capture['reason'] }}</code>
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
                                            @php($actor = $decisionActors[$decision['actor_id']] ?? null)
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
