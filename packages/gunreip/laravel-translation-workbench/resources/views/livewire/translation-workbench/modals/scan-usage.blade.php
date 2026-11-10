<flux:modal
    class="w-full max-w-4xl"
    name="workbench-scan-usage"
    wire:key="workbench-scan-usage"
    wire:model.live.self="scanUsageOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="saveScanUsage, openWorkflowReason"
    />
    @if ($scanUsageOpen && $scanUsageContext)
        <div class="space-y-4">
            <flux:heading
                class="flex items-center justify-start gap-2"
                size="xl"
            >
                <flux:icon.landmark class="size-6" />
                {{ __('Classify source usage') }}
            </flux:heading>
            <flux:callout.text class="text-zinc-400">
                {{ __('Choose a usage classification to see suggestions. Select an existing reason or create your own. Reasons are saved with the confirmed decision.') }}
            </flux:callout.text>

            <flux:callout
                color="sky"
                icon="code-bracket"
                heading="Source usage context"
            >
                <x-translation-workbench::ui.common.details label-width="14rem">
                    <x-translation-workbench::ui.common.details.item :label="__('Source file')">
                        <x-translation-workbench::ui.common.path-preview
                            :path="$scanUsageContext['path']"
                            :length="100"
                            side="center"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    @if (isset($scanUsageContext['capture']['source_line']))
                        <x-translation-workbench::ui.common.details.item
                            :label="__('Position')"
                            :item="__('Line :line, column :column', [
                                'line' => $scanUsageContext['capture']['source_line'],
                                'column' => $scanUsageContext['capture']['source_column'] ?? '—',
                            ])"
                        />
                    @endif
                    <x-translation-workbench::ui.common.details.item :label="__('Selected source expression')">
                        <flux:callout.text><span
                                class="whitespace-pre-wrap break-all font-mono">{{ $scanUsageContext['capture']['raw_expression'] }}</span>
                        </flux:callout.text>
                    </x-translation-workbench::ui.common.details.item>
                </x-translation-workbench::ui.common.details>
                <flux:callout.text>
                    {{ __('DatabaseValue excludes this unchanged dynamic usage from translation capture in subsequent scans. Translate keeps it open for translation work; it does not mean translated or approved.') }}
                </flux:callout.text>

                @if ($scanUsageContext['decision'])
                    <flux:callout.text>{{ __('Last decision') }}:
                        {{ $scanUsageContext['decision']['decision'] === 'database_value' ? __('DatabaseValue') : __('Translate') }}
                        · #{{ $scanUsageContext['decision']['actor_id'] }} · <x-ui.date-time.date-time
                            :value="$scanUsageContext['decision']['occurred_at']" />
                    </flux:callout.text>
                @endif
            </flux:callout>

            <flux:callout
                color="amber"
                icon="list"
            >
                <flux:field>
                    <flux:label>
                        <flux:callout.text>
                            {{ __('Usage') }}
                        </flux:callout.text>
                    </flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.mouse-pointer-click class="size-6" />
                        </flux:input.group.prefix>
                        <flux:select
                            variant="listbox"
                            clearable
                            wire:model.live="scanUsageChoice"
                        >
                            <flux:select.option
                                value=""
                                icon="shapes"
                            >
                                {{ __('Choose a classification') }}
                            </flux:select.option>
                            <flux:select.option
                                value="database_value"
                                icon="file-database"
                            >
                                {{ __('DatabaseValue') }}
                            </flux:select.option>
                            <flux:select.option
                                value="translate"
                                icon="language"
                            >
                                {{ __('Translate') }}
                            </flux:select.option>
                        </flux:select>
                    </flux:input.group>
                    <flux:error name="scanUsageChoice" />
                </flux:field>
            </flux:callout>

            <div class="">
                @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                    'reasonField' => 'scanUsageReason',
                    'reasonLabel' => __('Reason'),
                ])
            </div>

            <flux:error name="scanUsage" />

            <div class="mt-3 flex justify-end gap-2">
                <flux:button.group>
                    <flux:button
                        variant="primary"
                        color="green"
                        size="sm"
                        wire:click="saveScanUsage"
                    >
                        <flux:icon.save class="size-6" />
                    </flux:button>
                    <flux:button
                        variant="primary"
                        color="green"
                        size="sm"
                        wire:click="saveScanUsage"
                    >
                        {{ __('Confirm usage decision') }}
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
    @endif
</flux:modal>
