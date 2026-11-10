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
    <flux:table container:class="max-h-96 overflow-auto rounded-t-lg">
        {{-- Table Header Row --}}
        <flux:table.columns
            class="bg-white dark:bg-zinc-800"
            sticky
        >
            {{-- Table Header Column Finding --}}
            <flux:table.column>
                <flux:callout.heading>
                    {{ __('Finding') }}
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
        <flux:table.rows>
            @foreach (collect($sourcePreview['items'])->groupBy('path') as $path => $items)
                <flux:table.row>
                    <flux:table.cell colspan="4"><x-translation-workbench::ui.common.path-preview
                            :path="$path"
                            :length="90"
                            side="center"
                        /></flux:table.cell>
                </flux:table.row>
                @foreach ($items as $item)
                    <flux:table.row :key="'batch-preview-'.$item['finding']">
                        <flux:table.cell>#{{ $item['finding'] }}</flux:table.cell>
                        <flux:table.cell><x-translation-workbench::ui.common.text-preview
                                :mono="true"
                                :text="$item['before']"
                            /></flux:table.cell>
                        <flux:table.cell><x-translation-workbench::ui.common.text-preview
                                :mono="true"
                                :text="$item['after']"
                            /></flux:table.cell>
                        <flux:table.cell>
                            <flux:tooltip>
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                >
                                    <flux:badge
                                        size="sm"
                                        :color="$item['already_set'] ? 'sky' : ($item['ready'] ? 'green' : (!empty($item[
                                            'scan_required']) ? 'amber' : 'red'))"
                                    >
                                        {{ $item['already_set'] ? __('No change required') : ($item['ready'] ? __('Ready') : (!empty($item['scan_required']) ? __('New scan required') : __('Blocked'))) }}
                                    </flux:badge>
                                </flux:button>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Source check')"
                                    :text="__($item['blocker'] ?? 'This finding is ready for source replacement.')"
                                />
                            </flux:tooltip>
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
<flux:callout :color="$sourcePreview['ready'] ? 'green' : ($sourcePreview['blocked'] ? 'red' : 'sky')">
    <flux:callout.text>
        {{ $sourcePreview['ready'] ? __('All required replacements can be prepared together. One shared reason applies to this group.') : ($sourcePreview['blocked'] ? __('Resolve the blocked findings or open file operations first. No partial selection will be prepared.') : __('All selected calls already use their assigned key. No replacement is required.')) }}
    </flux:callout.text>
</flux:callout>
