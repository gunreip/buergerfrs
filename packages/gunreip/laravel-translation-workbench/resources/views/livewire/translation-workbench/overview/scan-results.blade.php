<flux:error name="scan" />

{{-- Scan Results --}}
<flux:callout
    class="relative"
    id="workbench-scan-results"
    color="sky"
    icon="radar"
    inline
    text="{{ __('Latest 20 saved scans. Counts describe the result at scan time. Unchanged findings are confirmed; files requiring review are preserved.') }}"
>
    {{-- Don't change this slot-code --}}
    <x-slot name="heading">
        <span class="block md:min-h-8 md:pe-64">{{ __('Scan results') }}</span>
    </x-slot>
    <x-slot:controls
        class="inset-e-4 absolute top-4 hidden md:block"
    >
        @include('translation-workbench::livewire.translation-workbench.shared.scan-refresh-button')
    </x-slot:controls>

    <div class="md:hidden">
        @include('translation-workbench::livewire.translation-workbench.shared.scan-refresh-button')
    </div>

    {{-- Run Another Scan --}}
    <flux:accordion class="mt-4">
        <flux:accordion.item>
            <flux:accordion.heading>
                <flux:callout.heading icon="repeat">
                    {{ __('Run another scan') }}
                </flux:callout.heading>
            </flux:accordion.heading>
            <flux:accordion.content>
                <flux:callout.text>
                    {{ __('Run these commands in the project terminal. The configured source paths determine the scan scope.') }}
                </flux:callout.text>
                <flux:callout.text class="mt-3">
                    {{ __('Preview does not save a scan. Scan and save performs a fresh check, records finding changes and considers saved usage decisions. It does not publish translations or rewrite source files. Afterwards, refresh the results below.') }}
                </flux:callout.text>
                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    <flux:field>
                        <flux:label>
                            <flux:callout.heading>
                                {{ __('Preview only') }}
                            </flux:callout.heading>
                        </flux:label>
                        <flux:input
                            class="bg-zinc-800 font-mono"
                            value="php artisan translation-workbench:generation-scan"
                            copyable
                            readonly
                        />
                    </flux:field>
                    <flux:field>
                        <flux:label>
                            <flux:callout.heading>
                                {{ __('Scan and save results') }}
                            </flux:callout.heading>

                        </flux:label>
                        <flux:input
                            class="bg-zinc-800 font-mono"
                            Value="php artisan translation-workbench:generation-scan --apply"
                            copyable
                            readonly
                        />
                    </flux:field>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>

    <x-ui.loading.lazy-indicator
        class="absolute inset-0 z-40"
        role="status"
        box-class="h-full w-full rounded-lg bg-white/75 dark:bg-zinc-900/75"
        target="$refresh"
        :text="__('Refreshing scan results…')"
    />

    {{-- Show Scan Results --}}
    <flux:accordion class="space-y-4">
        <flux:accordion.item>
            <flux:accordion.heading>
                <flux:callout.heading icon="eye">
                    {{ __('Show scan results') }}
                </flux:callout.heading>
            </flux:accordion.heading>
            <flux:accordion.content>
                {{-- Table --}}
                <flux:table
                    class="pr-10"
                    container:class="max-h-[100vh] overflow-x-auto rounded-t-lg"
                >
                    {{-- Table Header --}}
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-800"
                        sticky
                    >
                        {{-- Column Completed --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading class="ml-2">
                                    {{ __('Completed') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Completed')"
                                    :text="__(
                                        'Date and time when this scan result was saved. The counts describe that scan, not the current state of all findings.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Confirmed --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Confirmed') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Confirmed')"
                                    :text="__(
                                        'Existing findings recognized again without a change to their source occurrence. They were confirmed rather than captured as new findings.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Captured --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Captured') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Captured')"
                                    :text="__(
                                        'New findings created by this scan. Successor findings for recognized moves are counted separately under Moved.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Moved --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Moved') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Moved')"
                                    :text="__(
                                        'Recognized source relocations. Each move links an archived predecessor finding to a new successor finding, preserving the assignment when present.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Archived --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Archived') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Archived')"
                                    :text="__(
                                        'Findings retired because their source occurrence was no longer present in the checked scan scope. Their history remains available. Recognized moves are counted separately.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Files requiring review --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Files requiring review') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Files requiring review')"
                                    :text="__(
                                        'Files whose scan results could not be reconciled safely. Existing findings were preserved for review instead of automatically replaced or archived. This counts files, not findings.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Usage decisions considered --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Usage decisions considered') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Usage decisions considered')"
                                    :text="__(
                                        'Previously saved source usage decisions applied during this scan. Select the count to inspect the affected source locations and decisions.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Open --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Open') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Open')"
                                    :text="__(
                                        'Open the saved scan results to inspect captured, moved or archived findings, files requiring review, and considered usage decisions.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                    </flux:table.columns>
                    {{-- Table Rows --}}
                    <flux:table.rows>
                        @forelse ($scanRuns as $scan)
                            {{-- Table Row --}}
                            <flux:table.row :key="'scan-'.$scan['id']">
                                {{-- Column Completed --}}
                                <flux:table.cell>
                                    <x-ui.date-time.date-time
                                        class="ml-2"
                                        :value="$scan['completed_at']"
                                        :title="$scan['id']"
                                    />
                                </flux:table.cell>
                                @foreach (['confirmed', 'created', 'moved', 'removed', 'review_files'] as $count)
                                    {{-- Column Moved  --}}
                                    <flux:table.cell>
                                        <flux:callout.text>
                                            {{ number_format($scan[$count]) }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                @endforeach
                                {{-- Column Archived --}}
                                <flux:table.cell>
                                    @if ($scan['usage_decisions'] > 0)
                                        <flux:button.group>
                                            <flux:button
                                                size="sm"
                                                variant="ghost"
                                                wire:click="openScan('{{ $scan['id'] }}', 'usage_decisions')"
                                                :aria-label="__('Open considered usage decisions')"
                                            >
                                                <flux:icon.list-ordered size="4" />
                                            </flux:button>
                                            <flux:button
                                                size="sm"
                                                variant="ghost"
                                                wire:click="openScan('{{ $scan['id'] }}', 'usage_decisions')"
                                                :aria-label="__('Open considered usage decisions')"
                                            >
                                                {{ number_format($scan['usage_decisions']) }}
                                            </flux:button>
                                        </flux:button.group>
                                    @else
                                        <flux:callout.text>
                                            0
                                        </flux:callout.text>
                                    @endif
                                </flux:table.cell>
                                {{-- Column Actions --}}
                                <flux:table.cell>
                                    <flux:button.group>
                                        <flux:button
                                            size="sm"
                                            variant="outline"
                                            wire:click="openScan('{{ $scan['id'] }}')"
                                        >
                                            <flux:icon.package-open class="size-5" />
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="outline"
                                            wire:click="openScan('{{ $scan['id'] }}')"
                                        >
                                            {{ __('Open') }}
                                        </flux:button>
                                    </flux:button.group>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            {{-- Table Row --}}
                            <flux:table.row>
                                {{-- Column Empty --}}
                                <flux:table.cell colspan="8">
                                    <flux:callout.text class="ml-2">
                                        {{ __('No saved scans yet.') }}
                                    </flux:callout.text>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:callout>
