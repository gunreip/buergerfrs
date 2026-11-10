<flux:modal
    class="relative w-full max-w-6xl"
    name="workbench-scan-results"
    wire:key="workbench-scan-results"
    wire:model="scanOpen"
    {{-- :closable="true" --}}
>
    <x-translation-workbench::ui.common.modal-loading
        target="scanCategory, scanSearch, scanPerPage, gotoPage, nextPage, previousPage, setPage, openScanCaptures, openPreservedFindings, closePreservedFindings, openScanUsage, openConsideredScanUsage, open, openWorkflowStatus"
    />
    @if ($scanRows || $preservedRows || $captureRows)
        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-2 pe-10">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.radar class="size-5 shrink-0" />
                    {{ $captureRows ? __('Captured source occurrences') : ($preservedRows ? __('Preserved findings') : ($scanCategory === 'missing_immutable' ? __('Missing immutable information') : __('Scan results'))) }}
                </flux:heading>
                @if ($captureRows)
                    <flux:tooltip toggleable>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="information-circle"
                            :aria-label="__('About captured source occurrences')"
                        />
                        <x-translation-workbench::ui.common.tooltip-content
                            :heading="__('Captured source occurrences')"
                            :text="__(
                                'These are source occurrences recorded during this scan, not assigned Finding IDs. The source may have changed since then. Usage classification is available for confirmed dynamic calls; it does not approve a translation.',
                            )"
                        />
                    </flux:tooltip>
                @endif

                @if ($preservedRows || $captureRows)
                    <div class="ms-auto shrink-0">
                        <flux:button.group>
                            <flux:button
                                variant="outline"
                                size="sm"
                                wire:click="closePreservedFindings"
                            >
                                <flux:icon.arrow-left-from-line class="size-6" />
                            </flux:button>
                            <flux:button
                                variant="outline"
                                size="sm"
                                wire:click="closePreservedFindings"
                            >
                                {{ __('Back to scan results') }}
                            </flux:button>
                        </flux:button.group>
                    </div>
                @endif
            </div>

            <flux:callout
                color="sky"
                icon="database-search"
                :heading="__('Scan context')"
            >
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item
                        class="break-all tabular-nums"
                        :label="__('Scan run ID (UUID)')"
                        :item="$selectedScan ?? '—'"
                    />
                    @if ($preservedRows || $captureRows)
                        <x-translation-workbench::ui.common.details.item :label="__('Source file')">
                            <x-translation-workbench::ui.common.path-preview
                                :path="$scanReviewPath"
                                :length="100"
                                side="center"
                            />
                        </x-translation-workbench::ui.common.details.item>
                    @endif
                </x-translation-workbench::ui.common.details>
                @if ($fileHash = $captureRows?->first()['source_sha256'] ?? null)
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>
                                {{ __('Source evidence') }}
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:callout.text class="break-all">SHA-256: {{ $fileHash }}</flux:callout.text>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                @endif
                @if ($captureRows)
                    <div class="mt-4 space-y-4">

                        <flux:error name="scanReview" />

                        @include('translation-workbench::livewire.translation-workbench.scan.captured-source')
                    </div>
                @endif
            </flux:callout>

            @if (!$captureRows)
                <flux:error name="scanReview" />
            @endif

            @if ($preservedRows && !$captureRows)
                @include('translation-workbench::livewire.translation-workbench.scan.preserved-findings')
            @elseif (!$captureRows)
                @include('translation-workbench::livewire.translation-workbench.scan.results-table')
            @endif
        </div>
    @endif
</flux:modal>
