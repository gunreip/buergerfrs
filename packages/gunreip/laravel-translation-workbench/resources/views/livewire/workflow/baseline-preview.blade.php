<div
    class="space-y-4"
    data-baseline-preview
>
    <flux:callout
        color="amber"
        icon="beaker"
    >
        <flux:callout.heading>{{ __('Read-only rehearsal snapshot') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('These values come from the isolated test generation, not the current Workbench database. Selecting a key does not edit, approve or publish anything. Draft and review fields show only what existed at export time.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Current stage: the new data model has been populated and tested in isolated PostgreSQL databases. Those test clusters are stopped after verification. This page reads exported JSON evidence; the regular Workbench still uses its existing database. No production migration or switch has taken place.') }}
        </flux:callout.text>
        @if ($baselinePreview['snapshot'])
            <flux:callout.text>{{ __('Exported at') }}: {{ $baselinePreview['snapshot']['exported_at'] }} ·
                {{ __('Keys') }}: {{ count($baselinePreview['snapshot']['items']) }}</flux:callout.text>
        @endif
    </flux:callout>

    @if (!$baselinePreview['snapshot'])
        <flux:callout
            color="zinc"
            icon="information-circle"
        >
            <flux:card.heading>{{ __('No baseline preview available') }}</flux:card.heading>
            <flux:callout.text>
                {{ __('An exported rehearsal snapshot is required. The preview never starts or connects to the test database automatically.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:callout>
            <flux:heading
                size="xl"
                level="3"
            >
                {{ __('Findings') }}
            </flux:heading>

            <flux:field>
                <flux:label>
                    <flux:callout.heading>
                        {{ __('Assignment status') }}
                    </flux:callout.heading>
                </flux:label>
                <flux:callout.text>
                    {{ __('Counts refer to keys across all exported locales, independently of the selected fallback chain. Groups may overlap when locale states differ.') }}
                </flux:callout.text>
                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.search />
                    </flux:input.group.prefix>
                    <flux:select
                        variant="listbox"
                        wire:model.live="baselineFilter"
                        clearable
                        searchable
                        {{-- :label="__('Assignment status')"
                    :description="__('Counts refer to keys across all exported locales, independently of the selected fallback chain. Groups may overlap when locale states differ.')" --}}
                    >
                        @foreach ([
        'all' => __('All keys'),
        'linked' => __('Linked'),
        'blocked_structure' => __('Blocked key structure'),
        'option_source_evidenced' => __('Unassigned · option source identified'),
        'source_assignment_open' => __('Unassigned · source assignment open'),
    ] as $filter => $label)
                            <flux:select.option
                                :value="$filter"
                                icon="search"
                            >
                                {{ $label }} ({{ count($baselinePreview['groups'][$filter]) }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>
            </flux:field>

            <div class="grid grid-cols-3 gap-4">
                <flux:field class="col-span-2 min-w-0">
                    <flux:label>
                        {{ __('Translation key') }}
                    </flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.key />
                        </flux:input.group.prefix>
                        <flux:select
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="baselineKey"
                            {{-- :label="__('Translation key')" --}}
                            :placeholder="__('Select a key')"
                        >
                            @foreach ($baselinePreview['keys'] as $key)
                                <flux:select.option
                                    :value="$key"
                                    icon="key"
                                >
                                    {{ $key }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>
                </flux:field>

                <flux:field class="col-span-1 min-w-0">
                    <flux:label>
                        {{ __('Explicit preview fallback chain') }}
                    </flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.language />
                        </flux:input.group.prefix>
                        <flux:select
                            variant="listbox"
                            wire:model.live="baselineChain"
                        >
                            @foreach ($baselinePreview['snapshot']['chains'] as $name => $chain)
                                <flux:select.option
                                    :value="$name"
                                    icon="language"
                                >
                                    {{ implode(' → ', $chain) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>
                </flux:field>
            </div>
            <div
                role="status"
                wire:loading
                wire:target="baselineKey,baselineChain,baselineFilter,baselineAllLocales"
            >
                <span class="inline-flex items-center gap-2">
                    <flux:icon.loading class="size-4" />{{ __('Loading preview…') }}
                </span>
            </div>
        </flux:callout>

        @if ($baselinePreview['selected'] && $baselinePreview['chain'])
            @php
                $selected = $baselinePreview['selected'];
            @endphp
            <flux:callout
                icon="key"
                heading="{{ $selected['key'] }}"
                color="{{ $baselinePreview['result'] && $baselinePreview['result']['state'] === 'linked' ? 'sky' : 'amber' }}"
            >
                <flux:callout.heading>
                    {{ __('Test key ID') }}: {{ $selected['key_id'] ?? '—' }}
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('Published value used by this preview') }}:
                    @if ($baselinePreview['result'])
                        <flux:badge size="sm">{{ $baselinePreview['result']['locale'] }}</flux:badge>
                        <flux:badge
                            size="sm"
                            :color="$baselinePreview['result']['fallback'] ? 'amber' : 'green'"
                        >
                            {{ $baselinePreview['result']['fallback'] ? __('Fallback') : __('Exact locale') }}
                        </flux:badge>
                    @else
                        <flux:callout.text>
                            {{ __('No published value') }}
                        </flux:callout.text>
                    @endif
                </flux:callout.text>
                @if ($baselinePreview['result'])
                    <flux:textarea
                        class="min-h-12"
                        rows="1"
                        readonly
                    >
                        {{ $baselinePreview['result']['value'] === '' ? __('Empty string') : $baselinePreview['result']['value'] }}
                    </flux:textarea>
                @endif
            </flux:callout>
            @if (in_array($selected['key'], $baselinePreview['groups']['blocked_structure'], true))
                <flux:callout
                    color="red"
                    icon="exclamation-triangle"
                >
                    <flux:callout.heading>{{ __('Key structure needs a decision') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('The Workbench requires a file group followed by at least two nested key segments, for example group.section.name. A published file value can still be displayed, but it does not remove this structural block.') }}
                    </flux:callout.text>
                    <flux:callout.text>
                        {{ __('No replacement key is chosen automatically. Source occurrences remain separate and raw evidence stays unchanged.') }}
                    </flux:callout.text>
                    @include('translation-workbench::livewire.workflow.baseline-key-correction')
                    <flux:accordion class="mt-3">
                        <flux:accordion.item>
                            <flux:accordion.heading>
                                <flux:callout.heading>
                                    {{ __('Affected source occurrences') }}
                                    ({{ count($selected['blocked_sources'] ?? []) }})
                                </flux:callout.heading>
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:callout.text class="mb-3">
                                    {{ __('Test finding IDs and source positions refer to the saved baseline, not the current production database or current source files.') }}
                                </flux:callout.text>
                                @if ($selected['blocked_sources'] ?? [])
                                    <div class="overflow-x-auto rounded rounded-t-lg">
                                        <flux:table container:class="max-h-80 overflow-auto rounded rounded-t-lg">
                                            {{-- Table Header --}}
                                            <flux:table.columns
                                                class="bg-white dark:bg-zinc-800"
                                                sticky
                                            >
                                                {{-- Sequence Number --}}
                                                <flux:table.column class="w-8">
                                                    <flux:callout.text>
                                                        <flux:icon.tally-5 class="ml-2" />
                                                    </flux:callout.text>
                                                </flux:table.column>
                                                {{-- Test Finding     --}}
                                                <flux:table.column>
                                                    <flux:callout.heading>
                                                        {{ __('Test finding') }}
                                                    </flux:callout.heading>
                                                </flux:table.column>
                                                {{-- Source --}}
                                                <flux:table.column>
                                                    <flux:callout.heading>
                                                        {{ __('Source') }}
                                                    </flux:callout.heading>
                                                </flux:table.column>
                                                {{-- Line / Column --}}
                                                <flux:table.column>
                                                    <flux:callout.heading>
                                                        {{ __('Line / column') }}
                                                    </flux:callout.heading>
                                                </flux:table.column>
                                                {{-- Captured Expression --}}
                                                <flux:table.column>
                                                    <flux:callout.heading>
                                                        {{ __('Captured expression') }}
                                                    </flux:callout.heading>
                                                </flux:table.column>
                                            </flux:table.columns>
                                            {{-- Table Rows --}}
                                            <flux:table.rows class="[&_td]:align-top">
                                                @foreach ($selected['blocked_sources'] as $source)
                                                    {{-- Table Row --}}
                                                    <flux:table.row :key="'blocked-source-'.$source['finding_id']">
                                                        {{-- Sequence Number --}}
                                                        <flux:table.cell align="right">
                                                            <flux:callout.text>
                                                                {{ $loop->index + 1 }}
                                                            </flux:callout.text>
                                                        </flux:table.cell>
                                                        {{-- Finding ID --}}
                                                        <flux:table.cell>
                                                            <flux:callout.text>
                                                                #{{ $source['finding_id'] }}
                                                            </flux:callout.text>
                                                        </flux:table.cell>
                                                        {{-- Source --}}
                                                        <flux:table.cell class="whitespace-normal break-all font-mono">
                                                            <flux:callout.text>
                                                                {{ $source['path'] }}
                                                            </flux:callout.text>
                                                        </flux:table.cell>
                                                        {{-- Source Line/Column --}}
                                                        <flux:table.cell>
                                                            <flux:callout.text>
                                                                {{ $source['line'] }} / {{ $source['column'] }}
                                                            </flux:callout.text>
                                                        </flux:table.cell>
                                                        {{-- Captured Expression --}}
                                                        <flux:table.cell class="whitespace-normal break-all">
                                                            <flux:callout.text>
                                                                <code>{{ $source['expression'] }}</code>
                                                            </flux:callout.text>
                                                        </flux:table.cell>
                                                    </flux:table.row>
                                                @endforeach
                                            </flux:table.rows>
                                        </flux:table>
                                    </div>
                                @else
                                    <flux:callout.text>
                                        {{ __('Source occurrence details are not included in this snapshot. This does not mean there are no usages.') }}
                                    </flux:callout.text>
                                @endif
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:callout>
            @endif
            @if ($selected['unassigned_context'] ?? null)
                @php
                    $context = $selected['unassigned_context'];
                @endphp
                <flux:callout
                    color="{{ $context['kind'] === 'option_source_evidenced' ? 'sky' : 'amber' }}"
                    icon="information-circle"
                >
                    <flux:callout.heading>
                        {{ $context['kind'] === 'option_source_evidenced' ? __('Option source identified') : __('Source assignment remains open') }}
                    </flux:callout.heading>
                    <flux:callout.text>
                        {{ $context['kind'] === 'option_source_evidenced'
                            ? __(
                                'A scanned option call references this option group. This does not prove that this individual option is used at runtime, and does not create a scalar key assignment.',
                            )
                            : __(
                                'No unambiguous option source was established for this value. This does not mean that the translation is unused or may be deleted.',
                            ) }}
                    </flux:callout.text>
                    @if ($context['kind'] === 'option_source_evidenced')
                        <dl class="mt-3 grid gap-x-4 gap-y-2 md:grid-cols-[auto_1fr]">
                            <dt>
                                <flux:callout.heading>
                                    {{ __('Option group key') }}
                                </flux:callout.heading>
                            </dt>
                            <dd class="break-all font-mono">
                                <flux:callout.text>
                                    {{ $context['owner'] }}
                                </flux:callout.text>
                            </dd>
                            <dt>
                                <flux:callout.heading>
                                    {{ __('Option identity') }}
                                </flux:callout.heading>
                            </dt>
                            <dd class="break-all font-mono">
                                <flux:callout.text>
                                    {{ $context['identity'] }}
                                </flux:callout.text>
                            </dd>
                        </dl>
                        <flux:accordion class="mt-3">
                            <flux:accordion.item>
                                <flux:accordion.heading>
                                    <flux:callout.heading>
                                        {{ __('Source calls') }} ({{ count($context['sources']) }})
                                    </flux:callout.heading>
                                </flux:accordion.heading>
                                <flux:accordion.content>
                                    <div class="max-h-80 space-y-4 overflow-auto pr-3">
                                        @foreach ($context['sources'] as $source)
                                            <div class="space-y-1">
                                                <div class="break-all font-mono">
                                                    <flux:callout.text>
                                                        {{ $loop->index + 1 }})
                                                        {{ $source['path'] }}:{{ $source['line'] }}
                                                    </flux:callout.text>
                                                </div>
                                                <div>
                                                    <flux:callout.heading>
                                                        {{ __('Option expression') }}:
                                                    </flux:callout.heading>
                                                    <flux:callout.text>
                                                        <code class="break-all">
                                                            {{ $source['identity_expression'] }}
                                                        </code>
                                                    </flux:callout.text>
                                                </div>
                                                <div>
                                                    <flux:callout.heading>
                                                        {{ __('Fallback expression') }}:
                                                    </flux:callout.heading>
                                                    <flux:callout.text>
                                                        <code class="break-all">
                                                            {{ $source['fallback_expression'] }}
                                                        </code>
                                                    </flux:callout.text>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </flux:accordion.content>
                            </flux:accordion.item>
                        </flux:accordion>
                    @endif
                </flux:callout>
            @endif

            <flux:callout
                color="teal"
                icon="book-plus"
            >
                <flux:accordion>
                    <flux:accordion.item expanded>
                        <flux:accordion.heading>
                            <flux:callout.heading>
                                {{ __('Published values, drafts and reviews') }}
                            </flux:callout.heading>
                        </flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:callout.text class="mb-4">
                                {{ __('The table shows exact values per locale. The effective value above still follows the selected fallback chain.') }}
                                <flux:toggle
                                    class="ml-3 inline hover:cursor-pointer"
                                    wire:model.live="baselineAllLocales"
                                    :on:label="__('All exported locales')"
                                    :off:label="__('Selected fallback chain')"
                                    :aria-label="__('Locale scope of the table')"
                                >
                                    <x-slot:icon>
                                        <flux:icon.plus
                                            class="group-data-checked:block hidden size-4"
                                            variant="outline"
                                        />
                                        <flux:icon.ban
                                            class="group-data-checked:hidden size-4"
                                            variant="outline"
                                        />
                                    </x-slot:icon>
                                </flux:toggle>
                            </flux:callout.text>
                            <div class="overflow-x-auto rounded rounded-t-lg">
                                <flux:table container:class="max-h-120">
                                    {{-- Header/Columns --}}
                                    <flux:table.columns
                                        class="bg-white dark:bg-zinc-800"
                                        sticky
                                    >
                                        {{-- Sequence Number --}}
                                        <flux:table.column class="w-8">
                                            <flux:callout.heading>
                                                <flux:icon.tally-5 class="ml-2" />
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Locale --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('Locale') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Source --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('Source') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Chksum --}}
                                        <flux:table.column align="center">
                                            <flux:callout.heading>
                                                {{ __('Chksum') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Published File Value --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('Published file value') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- State --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('State') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Current Draft --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('Current draft') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                        {{-- Review --}}
                                        <flux:table.column>
                                            <flux:callout.heading>
                                                {{ __('Review') }}
                                            </flux:callout.heading>
                                        </flux:table.column>
                                    </flux:table.columns>
                                    {{-- Table Rows --}}
                                    <flux:table.rows class="[&_td]:align-top">
                                        @foreach ($baselineAllLocales ? array_keys($selected['locales']) : $baselinePreview['chain'] as $locale)
                                            @php
                                                $published = $selected['locales'][$locale]['published'] ?? null;
                                                $editing = $selected['locales'][$locale]['editing'] ?? null;
                                            @endphp
                                            {{-- Table Row --}}
                                            <flux:table.row
                                                :key="$selected['key'].
                                                ':'.$locale"
                                            >
                                                {{-- Sequence Number --}}
                                                <flux:table.cell class="text-right">
                                                    <flux:callout.text>
                                                        {{ $loop->index + 1 }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Locale --}}
                                                <flux:table.cell class="whitespace-normal">
                                                    <x-ui.locale.flag locale="{{ $locale }}" />
                                                    <flux:badge
                                                        class="ml-1"
                                                        size="sm"
                                                    >
                                                        {{ $locale }}
                                                    </flux:badge>
                                                </flux:table.cell>
                                                {{-- Published File --}}
                                                <flux:table.cell class="whitespace-normal">
                                                    @if ($published)
                                                        <flux:callout.text class="break-all font-mono">
                                                            {{ $published['path'] }}
                                                        </flux:callout.text>
                                                    @else
                                                        <flux:callout.text>
                                                            {{ __('No published value evidence') }}
                                                        </flux:callout.text>
                                                    @endif
                                                </flux:table.cell>
                                                {{-- Chksum --}}
                                                <flux:table.cell class="whitespace-normal text-center">
                                                    @if ($published)
                                                        <flux:tooltip
                                                            toggleable
                                                            :content="$published['sha256']"
                                                        >
                                                            <flux:button
                                                                variant="ghost"
                                                                size="xs"
                                                                :aria-label="__('File checksum')"
                                                            >
                                                                <flux:icon.info class="size-4" />
                                                            </flux:button>
                                                        </flux:tooltip>
                                                    @else
                                                        <flux:callout.text>
                                                            —
                                                        </flux:callout.text>
                                                    @endif
                                                </flux:table.cell>
                                                {{-- Published File Value --}}
                                                <flux:table.cell class="whitespace-normal">
                                                    @if ($published)
                                                        <flux:callout.text class="wrap-break-word whitespace-pre-wrap">
                                                            {{ $published['value'] === '' ? __('Empty string') : $published['value'] }}
                                                        </flux:callout.text>
                                                    @else
                                                        <flux:callout.text>
                                                            {{ __('No own translation; fallback may apply.') }}
                                                        </flux:callout.text>
                                                    @endif
                                                </flux:table.cell>
                                                {{-- State --}}
                                                <flux:table.cell class="whitespace-normal">
                                                    @if ($published)
                                                        <flux:badge
                                                            size="sm"
                                                            :color="$published['state'] === 'linked' ? 'green' : 'amber'"
                                                        >
                                                            {{ match ($published['state']) {'linked' => __('Linked'),'blocked_structure' => __('Blocked key structure'),default => __('Unassigned key')} }}
                                                        </flux:badge>
                                                        @if (
                                                            $published['state'] === 'unassigned_key' &&
                                                                ($selected['unassigned_context']['kind'] ?? null) === 'option_source_evidenced')
                                                            <flux:badge
                                                                size="sm"
                                                                color="sky"
                                                            >
                                                                {{ __('Option source identified') }}
                                                            </flux:badge>
                                                        @endif
                                                    @else
                                                        <flux:callout.text>
                                                            {{ __('No own translation; fallback may apply.') }}
                                                        </flux:callout.text>
                                                    @endif
                                                </flux:table.cell>
                                                {{-- Current Draft --}}
                                                <flux:table.cell
                                                    class="whitespace-normal"
                                                    align="start"
                                                >
                                                    @if ($editing)
                                                        <flux:callout.text class="wrap-break-word whitespace-pre-wrap">
                                                            {{ $editing['value'] === '' ? __('Empty string') : $editing['value'] }}
                                                        </flux:callout.text>
                                                        <flux:callout.text>
                                                            {{ __('Revision') }} #{{ $editing['revision_id'] }}
                                                        </flux:callout.text>
                                                    @else
                                                        <flux:callout.text>
                                                            {{ __('No draft in this snapshot') }}
                                                        </flux:callout.text>
                                                    @endif
                                                </flux:table.cell>
                                                {{-- Review --}}
                                                <flux:table.cell class="whitespace-normal">
                                                    <flux:callout.text>
                                                        {{ $editing? match ($editing['approval']) {'approved' => __('Approved, not yet published by this preview'),'revoked' => __('Revoked'),'stale' => __('Review context changed'),default => __('Unreviewed')}: __('No review; file evidence is not an approval') }}
                                                    </flux:callout.text>
                                                    @if ($editing && $editing['recorded_decision'])
                                                        <flux:callout.text>
                                                            {{ __('Recorded decision') }}:
                                                            {{ $editing['recorded_decision'] === 'approved' ? __('Approved') : __('Revoked') }}
                                                        </flux:callout.text>
                                                    @endif
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
            </flux:callout>
        @else
            <flux:callout color="amber">
                {{ $baselinePreview['keys'] ? __('Select an available key and locale chain.') : __('No keys match this assignment status in the snapshot.') }}
            </flux:callout>
        @endif
    @endif
    <flux:callout>
        <flux:callout.heading>{{ __('Current source scan') }}</flux:callout.heading>
        <flux:callout.text>{{ __('The fresh source scan and its current findings are available in the Workbench.') }}</flux:callout.text>
        <flux:button :href="route('admin.translation-workbench', ['tab' => 'source-usage'])">{{ __('Open Workbench review') }}</flux:button>
    </flux:callout>

    @include('translation-workbench::livewire.workflow.baseline-rehearsal-report')
    @include('translation-workbench::livewire.workflow.baseline-source-changes')
</div>
