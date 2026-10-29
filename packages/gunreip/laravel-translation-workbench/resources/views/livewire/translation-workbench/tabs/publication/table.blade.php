{{-- Table Publications --}}
<flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
    {{-- Table Header --}}
    <flux:table.columns class="bg-white dark:bg-zinc-800">
        {{-- Column Key --}}
        <flux:table.column
            class="tabular-nums"
            sortable
            :sorted="$publicationSort === 'key'"
            :direction="$publicationDirection"
            wire:click="sortPublication('key')"
        >
            <flux:tooltip content="{{ __('Sort by Key') }}">
                <flux:callout.heading class="ml-2">
                    {{ __('Key') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Language --}}
        <flux:table.column sortable
            :sorted="$publicationSort === 'language'"
            :direction="$publicationDirection"
            wire:click="sortPublication('language')">
            <flux:tooltip content="{{ __('Sort by language') }}">
                <flux:callout.heading>
                    {{ __('Language') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Actions --}}
        <flux:table.column>
            <flux:tooltip content="{{ __('Actions') }}">
                <flux:callout.heading>
                    {{ __('Actions') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
    </flux:table.columns>
    {{-- Table Rows --}}
    <flux:table.rows>
        @forelse ($publicationTasks as $task)
            {{-- Table Row --}}
            <flux:table.row :key="'publication-task-'.$task['revision']">
                {{-- Column Key --}}
                <flux:table.cell class="whitespace-normal tabular-nums">
                    <flux:callout.text>
                        <code>#{{ $task['key_id'] }} ·
                            {{ $task['namespace'] !== '' ? $task['namespace'] . '::' : '' }}{{ $task['full_name'] }}</code>
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Column Language --}}
                <flux:table.cell>
                    <flux:callout.text>
                        {{ $task['locale'] }} · #{{ $task['revision'] }}
                    </flux:callout.text>
                </flux:table.cell>
                {{-- Column Actions --}}
                <flux:table.cell>
                    <flux:button size="sm" variant="outline" icon="queue-list" wire:click="openKeyWorkflowStatus({{ $task['key_id'] }})" :tooltip="__('Workflow status')" :aria-label="__('Workflow status')" />
                    <flux:button.group>
                        <flux:button
                            size="sm"
                            variant="outline"
                            icon="eye"
                            wire:click="openPublicationTask({{ $task['revision'] }})"
                        />
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="openPublicationTask({{ $task['revision'] }})"
                        >
                            {{ __('Prepare publication') }}
                        </flux:button>
                    </flux:button.group>
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Table Row Empty --}}
            <flux:table.row>
                {{-- Column Empty --}}
                <flux:table.cell colspan="3">
                    <flux:callout.text>
                        {{ __('No approved revisions waiting for publication.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>

<flux:pagination :paginator="$publicationTasks" />
