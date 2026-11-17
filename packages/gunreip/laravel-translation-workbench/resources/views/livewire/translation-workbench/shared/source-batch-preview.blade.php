<flux:callout
    color="sky"
    icon="replace-all"
    :heading="__('Selected findings')"
>
    <x-translation-workbench::ui.common.details>
        <x-translation-workbench::ui.common.details.item
            :label="__('Assigned key ID')"
            :item="'#' . $sourcePreview['key_id']"
        />
        <x-translation-workbench::ui.common.details.item :label="__('Assigned translation key')">
            <x-translation-workbench::ui.common.text-preview
                :mono="true"
                :text="$sourcePreview['key']"
            />
        </x-translation-workbench::ui.common.details.item>
    </x-translation-workbench::ui.common.details>

    {{-- Table of selected findings --}}
    <flux:table container:class="mt-3 max-h-96 overflow-auto rounded-t-lg">
        {{-- Table Header Row --}}
        <flux:table.columns
            class="bg-white dark:bg-zinc-800"
            sticky
        >
            {{-- Table Header Column Finding --}}
            <flux:table.column>
                <flux:callout.heading class="ml-2">
                    {{ __('Finding') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Line/Position --}}
            <flux:table.column align="center">
                <flux:callout.heading>
                    {{ __('Position') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column VSC --}}
            <flux:table.column align="center">
                <flux:callout.heading>
                    {{ __('VSC') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Current Source Call --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Current source call') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Proposed Source Call --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Proposed source call') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Proposed Source Call --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Status') }}
                </flux:callout.heading>
            </flux:table.column>
            {{-- Table Header Column Status --}}
        </flux:table.columns>
        {{-- Table Body Rows --}}
        <flux:table.rows>
            @foreach (collect($sourcePreview['items'])->groupBy('path') as $path => $items)
                {{-- Table Body Row Column (Sub-Header) --}}
                <flux:table.row>
                    {{-- Table Body Column Path  --}}
                    <flux:table.cell
                        class="bg-white dark:bg-zinc-800"
                        colspan="6"
                    >
                        <x-translation-workbench::ui.common.path-preview
                            class="ml-2"
                            :path="$path"
                            :vsc="false"
                            :length="90"
                            side="center"
                        />
                    </flux:table.cell>
                </flux:table.row>
                @foreach ($items as $item)
                    {{-- Table Body Row Column Finding --}}
                    <flux:table.row :key="'batch-preview-'.$item['finding']">
                        <flux:table.cell>
                            <flux:callout.text class="ml-2">
                                #{{ $item['finding'] }}
                            </flux:callout.text>
                        </flux:table.cell>
                        {{-- Table Body Row Column Line/Position --}}
                        <flux:table.cell align="center">
                            <div class="inline-flex items-center gap-1 whitespace-nowrap tabular-nums">
                                <flux:callout.text>
                                    {{ isset($item['line'], $item['column']) ? $item['line'] . ':' . $item['column'] : '—' }}
                                </flux:callout.text>
                                @if (isset($item['scan_position']))
                                    @php
                                        $position = $item['scan_position'];
                                        $scanText = match ($position['state']) {
                                            'unchanged' => __('Scan confirmed #:id at :position', [
                                                'id' => $item['finding'],
                                                'position' => $position['from'],
                                            ]),
                                            'unique_move' => __('Move detected') . ' → ' . $position['to'],
                                            'reformatted' => __('Call reformatted at the same position'),
                                            default => __('Position requires review'),
                                        };
                                    @endphp
                                    <flux:tooltip>
                                        <span
                                            class="inline-flex"
                                            role="img"
                                            aria-label="{{ $scanText }}"
                                            tabindex="0"
                                        >
                                            @if ($position['state'] === 'unchanged')
                                                <flux:icon.check class="size-4 text-green-600 dark:text-green-400" />
                                            @else
                                                <flux:icon.information-circle
                                                    class="size-4 text-amber-600 dark:text-amber-400"
                                                />
                                            @endif
                                        </span>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            class="whitespace-normal"
                                            :heading="__('Scan position')"
                                            :text="$scanText"
                                        />
                                    </flux:tooltip>
                                @endif
                            </div>
                        </flux:table.cell>
                        {{-- Table Body Row Column VSC Link --}}
                        <flux:table.cell align="center">
                            @if (($item['source_link_available'] ?? true) && $path !== '' && isset($item['line'], $item['column']))
                                <x-translation-workbench::ui.common.vsc-link
                                    :path="$path"
                                    :line="$item['line']"
                                    :column="$item['column']"
                                />
                            @endif
                        </flux:table.cell>
                        {{-- Table Body Row Column Current Source Call --}}
                        <flux:table.cell>
                            <x-translation-workbench::ui.common.text-preview
                                :mono="true"
                                :text="$item['before']"
                            />
                        </flux:table.cell>
                        {{-- Table Body Row Column Proposed Source Call --}}
                        <flux:table.cell><x-translation-workbench::ui.common.text-preview
                                :mono="true"
                                :text="$item['after']"
                            />
                        </flux:table.cell>
                        {{-- Table Body Row Column Status --}}
                        <flux:table.cell class="flex items-center gap-1">
                            <flux:tooltip>
                                <flux:badge
                                    tabindex="0"
                                    size="sm"
                                    :color="$item['already_set'] ? 'sky' : ($item['ready'] ? 'green' : (!empty($item[
                                        'scan_required']) ? 'amber' : 'red'))"
                                >
                                    {{ $item['already_set'] ? __('No change required') : ($item['ready'] ? __('Ready') : (!empty($item['scan_required']) ? __('translation-workbench.buttons.new_scan_required') : __('Blocked'))) }}
                                </flux:badge>
                                <x-translation-workbench::ui.common.tooltip-content
                                    class="whitespace-normal [overflow-wrap:anywhere]"
                                    :heading="__('Source check')"
                                    :text="__($item['blocker'] ?? 'This finding is ready for source replacement.')"
                                >
                                    @if (isset($item['blocking_finding']))
                                        @php
                                            $blocking = $item['blocking_finding'];
                                            $storedPosition = $blocking['line'] . ':' . $blocking['column'];
                                        @endphp
                                        <p>{{ __('Stored call') }}: <code>{{ $blocking['expression'] }}</code></p>
                                        <p>{{ __('translation-workbench.literal.source_file') }}: {{ $blocking['path'] }}:{{ $storedPosition }}</p>
                                        @if ($blocking['to'] && $blocking['to'] !== $storedPosition)
                                            <p>{{ __('Possible position') }}: {{ $blocking['to'] }}</p>
                                        @endif
                                    @endif
                                </x-translation-workbench::ui.common.tooltip-content>
                            </flux:tooltip>
                            @if (isset($item['blocking_finding']))
                                <x-translation-workbench::ui.common.vsc-link
                                    :path="$item['blocking_finding']['path']"
                                    :line="$item['blocking_finding']['line']"
                                    :column="$item['blocking_finding']['column']"
                                />
                                <flux:button
                                    size="sm"
                                    icon="scale"
                                    wire:click="openSourceIssue({{ $item['blocking_finding']['finding'] }})"
                                >
                                    {{ __('Compare finding #:id', ['id' => $item['blocking_finding']['finding']]) }}
                                </flux:button>
                            @elseif (isset($item['scan_position']) && !$item['ready'] && !$item['already_set'])
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="openSourceComparison({{ $item['finding'] }})"
                                >
                                    {{ __('Compare source') }}
                                </flux:button>
                            @endif
                            @if (!empty($item['pending_run']))
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="openSourceRun('{{ $item['pending_run'] }}')"
                                >{{ __('Open prepared operation') }}</flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:callout>
@foreach (collect($sourcePreview['items'])->pluck('scan_required')->filter()->unique('finding') as $scanRequired)
    @include('translation-workbench::livewire.translation-workbench.shared.source-scan-required', [
        'scanRequired' => $scanRequired,
    ])
@endforeach
<flux:callout
    :color="$sourcePreview['ready'] ? 'green' : ($sourcePreview['blocked'] ? 'red' : 'sky')"
    icon="triangle-exclamation-point"
    heading="{{ $sourcePreview['ready'] ? __('Ready') : ($sourcePreview['blocked'] ? __('Blocked') : __('Pending')) }}"
    text="{{ $sourcePreview['ready'] ? __('All required replacements can be prepared together. One shared reason applies to this group.') : ($sourcePreview['blocked'] ? __('Resolve the blocked findings or open file operations first. No partial selection will be prepared.') : __('All selected calls already use their assigned key. No replacement is required.')) }}"
/>
{{-- <flux:callout.text>
        {{ $sourcePreview['ready'] ? __('All required replacements can be prepared together. One shared reason applies to this group.') : ($sourcePreview['blocked'] ? __('Resolve the blocked findings or open file operations first. No partial selection will be prepared.') : __('All selected calls already use their assigned key. No replacement is required.')) }}
    </flux:callout.text>
</flux:callout> --}}
