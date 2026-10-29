<flux:modal
    class="w-full max-w-6xl"
    name="generation-source-preview"
    wire:model="sourcePreviewOpen"
>
    @if ($sourcePreview)
        <div class="space-y-4">
            <flux:heading size="xl">
                {{ __('Source replacement preview') }} · {{ __('Finding') }}
                #{{ $sourcePreview['finding'] }}
            </flux:heading>
            <flux:callout
                color="sky"
                icon="circle-dashed-check"
                heading="{{ __('Source replacements peview') }}"
                text="{{ __('This preview shows the exact source occurrence that will be replaced. The proposed replacement is based on the published, approved source-language value and the assigned translation key.') }}"
            >
                <dl class="grid grid-cols-[minmax(0,12rem)_minmax(0,1fr)] items-baseline gap-x-3 gap-y-2 text-sm">
                    <dt class="font-medium">
                        <flux:callout.heading>
                            {{ __('Source file') }}:
                        </flux:callout.heading>
                    </dt>
                    <dd class="min-w-0">
                        <x-translation-workbench::ui.common.path-preview
                            :path="$sourcePreview['path']"
                            :length="60"
                            side="center"
                            :line="$sourcePreview['line']"
                            :column="$sourcePreview['column']"
                            :heading="__('Source file')"
                        />
                    </dd>
                    <dt class="font-medium">
                        <flux:callout.heading>
                            {{ __('Assigned key') }}:
                        </flux:callout.heading>
                    </dt>
                    <dd class="flex min-w-0 items-baseline gap-2">
                        <span class="shrink-0 tabular-nums">
                            <flux:callout.text class="ml-1">
                                #{{ $sourcePreview['key_id'] }} →
                            </flux:callout.text>
                        </span>
                        <x-translation-workbench::ui.common.text-preview
                            :text="$sourcePreview['key']"
                            side="center"
                            :heading="__('Assigned key')"
                        />
                    </dd>
                </dl>
            </flux:callout>

            <flux:callout
                :heading="__('Current source call')"
                icon="code"
                color="teal"
            >
                <flux:callout.text>
                    <pre class="min-w-0 whitespace-pre-wrap break-all"><code>{{ $sourcePreview['before'] }}</code></pre>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                :heading="__('Proposed source call')"
                icon="code"
                color="teal"
            >
                <flux:callout.text>
                    <pre class="min-w-0 whitespace-pre-wrap break-all"><code>{{ $sourcePreview['after'] }}</code></pre>
                </flux:callout.text>
            </flux:callout>

            @include('translation-workbench::livewire.translation-workbench.shared.source-preview-status', [
                'previewStatus' => $sourcePreview,
            ])
            @if ($sourcePreview['language_path'])
                <flux:text><code>lang/{{ $sourcePreview['language_path'] }}</code> ·
                    {{ __('Source language') }}: {{ $sourcePreview['source_locale'] }}</flux:text>
                <flux:textarea
                    class="min-h-12"
                    readonly
                    rows="3"
                    :label="__('Published source-language value')"
                    :placeholder="__('No scalar value found')"
                >
                    {{ $sourcePreview['published_value'] }}
                </flux:textarea>
            @endif

            <flux:callout
                color="amber"
                heading="{{ __('Exact source preview') }}"
                icon="circle-alert"
            >
                <flux:callout.text>
                    {{ __('Only this exact occurrence is inspected. Identical text elsewhere is not replaced. Arguments, placeholders and surrounding source code are preserved.') }}
                </flux:callout.text>
                <flux:callout.text>
                    {{ __('Preparation records a plan without writing the file. Confirmation replaces this call and creates a successor for the archived original finding. Moved findings are reconciled together and retain their own key assignments. Overlapping changed calls require separate review.') }}
                </flux:callout.text>
            </flux:callout>

            @if ($sourcePreview['ready'])
                @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                    'reasonField' => 'sourceReason',
                    'reasonLabel' => __('Reason for source replacement'),
                ])
                <flux:button
                    variant="outline"
                    wire:click="prepareSource"
                >{{ __('Prepare source replacement') }}</flux:button>
            @endif
            <flux:error name="sourcePreview" />
        </div>
    @endif
</flux:modal>
