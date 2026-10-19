@if ($assignment = $keyConflictInspection['assignment'] ?? null)
    <flux:callout
        class="inline-flex min-w-0 self-stretch"
        color="red"
        icon="swords"
    >
        <flux:callout.heading>{{ __('Choose the key record for finding #:id', ['id' => $assignment['finding_id']]) }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Keep one existing assignment for this finding. Other assignments of this finding are archived. No key records are merged and no translation values are changed.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Review evidence below refers to this finding. A bulk review documents a shared operation, not an exclusive assignment to one key.') }}
        </flux:callout.text>

        @php
            $assignmentTextLabel =
                ($assignment['text_source'] ?? 'literal') === 'literal'
                    ? __('Literal text')
                    : __('Source translation (:locale)', ['locale' => $assignment['source_locale']]);
            $assignmentTextTooltip =
                ($assignment['text_source'] ?? 'literal') === 'literal'
                    ? __(
                        'The original literal text stored for this finding. Shown read-only; this is not the current translated value.',
                    )
                    : __(
                        'No original literal text is stored for this finding. This field shows the active stored value for the same translation key in the configured source language. It does not reconstruct or change the original literal.',
                    );
        @endphp
        <flux:field>
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>
                        <flux:callout.heading>{{ $assignmentTextLabel }}</flux:callout.heading>
                    </span>
                    <x-ui.tooltip.simple
                        :header="$assignmentTextLabel"
                        :text="$assignmentTextTooltip"
                    />
                </span>
            </flux:label>
            <flux:textarea
                :placeholder="($assignment['text_source'] ?? '') === 'conflict'
                    ?
                    __('Conflicting source values. Review Stored scalar values.') :
                    __('No stored literal text or active source translation.')"
                rows="3"
                readonly
            >
                {{ $assignment['display_text'] ?? ($assignment['literal_text'] ?? '') }}
            </flux:textarea>
        </flux:field>

        <flux:field>
            <flux:label>
                <span class="inline-flex items-center gap-1">
                    <span>
                        <flux:callout.heading>{{ __('Key record') }}</flux:callout.heading>
                    </span>
                    <x-ui.tooltip.simple
                        :header="__('Key record')"
                        :text="__(
                            'Search the linked key records by ID or suggested key. Only confirmation saves your choice: the selected link is retained and the other links of this finding are archived. Key records and translation values are retained.',
                        )"
                    />
                </span>
            </flux:label>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.binoculars />
                </flux:input.group.prefix>
                <flux:select
                    variant="listbox"
                    searchable
                    wire:model.live="selectedAssignmentKeyId"
                    :placeholder="__('Select a key record')"
                >
                    @foreach ($assignment['keys'] as $choice)
                        <flux:select.option
                            icon="key"
                            :value="$choice['id']"
                        >
                            <span class="flex min-w-0 items-start gap-1 whitespace-normal">
                                <span class="shrink-0">#{{ $choice['id'] }} ·</span>
                                <span class="wrap-anywhere min-w-0">{{ $choice['suggested_key'] }}</span>
                            </span>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
            <flux:error name="selectedAssignmentKeyId" />
        </flux:field>

        <flux:button
            class="mt-3"
            type="button"
            variant="outline"
            color="green"
            wire:click="openKeyAssignmentConfirmation"
            wire:loading.attr="disabled"
            wire:target="openKeyAssignmentConfirmation,selectedAssignmentKeyId"
            :disabled="empty($selectedAssignmentKeyId)"
        >
            {{ __('Confirm key assignment') }}
        </flux:button>
        <flux:error name="translationKeyValue" />

        <div class="mt-3 flex min-w-0 flex-col gap-2">
            @foreach ($assignment['keys'] as $choice)
                <div class="min-w-0 space-y-2 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <flux:callout.heading class="break-all">
                        #{{ $choice['id'] }} · {{ $choice['suggested_key'] }}
                    </flux:callout.heading>
                    @if (empty($choice['review_evidence']))
                        <flux:callout.text>
                            {{ __('No recorded review for this finding on this key. Reviews of other findings are not shown here.') }}
                        </flux:callout.text>
                    @else
                        <flux:accordion>
                            <flux:accordion.item>
                                <flux:accordion.heading>
                                    {{ __('Review evidence (:count operations)', ['count' => count($choice['review_evidence'])]) }}
                                </flux:accordion.heading>
                                <flux:accordion.content>
                                    <flux:table container:class="max-h-64 overflow-auto">
                                        <flux:table.columns>
                                            <flux:table.column>{{ __('Review') }}</flux:table.column>
                                            <flux:table.column>{{ __('Scope') }}</flux:table.column>
                                            <flux:table.column>{{ __('Decision') }}</flux:table.column>
                                            <flux:table.column>{{ __('Translation key') }}</flux:table.column>
                                            <flux:table.column>{{ __('Reviewed at') }}</flux:table.column>
                                        </flux:table.columns>
                                        <flux:table.rows>
                                            @foreach ($choice['review_evidence'] as $evidence)
                                                <flux:table.row>
                                                    <flux:table.cell class="whitespace-normal">
                                                        {{ implode(', ', array_map(fn($id) => '#' . $id, $evidence['review_ids'])) }}
                                                    </flux:table.cell>
                                                    <flux:table.cell class="whitespace-normal">
                                                        {{ $evidence['direct'] ? __('This finding') : __('Included in bulk selection') }}
                                                        @if ($evidence['bulk'])
                                                            <flux:badge
                                                                size="sm"
                                                                color="zinc"
                                                            >{{ __('Bulk review') }}</flux:badge>
                                                        @endif
                                                    </flux:table.cell>
                                                    <flux:table.cell class="whitespace-normal">
                                                        {{ __($evidence['decision']) }}</flux:table.cell>
                                                    <flux:table.cell class="whitespace-normal break-all font-mono">
                                                        {{ $evidence['translation_key'] ?? '—' }}</flux:table.cell>
                                                    <flux:table.cell>{{ $evidence['reviewed_at'] ?? '—' }}
                                                    </flux:table.cell>
                                                </flux:table.row>
                                            @endforeach
                                        </flux:table.rows>
                                    </flux:table>
                                </flux:accordion.content>
                            </flux:accordion.item>
                        </flux:accordion>
                    @endif
                </div>
            @endforeach
        </div>
    </flux:callout>
@endif
