{{-- resources/views/components/admin/partials/translation-usage-audit/⚡modal-edit.blade.php --}}

{{-- Modal translation-usage-audit editieren --}}
<flux:modal
    class="scrollbar-gutter-stable w-full max-w-6xl"
    wire:model="usageAuditEditModalOpen"
>
    @if ($selectedItem)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card header mit Titel, Beschreibung und Badges für Audit-Typ und Entscheidungstyp --}}
                <x-ui.headers.card
                    :title="__('Usage audit decision')"
                    :description="__('Create and edit the workflow decision for the selected source-language literal.')"
                />

                <div class="mr-8 flex flex-wrap items-center gap-2">
                    {{-- Badges für Audit-Typ und Entscheidungstyp --}}
                    <flux:badge
                        variant="subtle"
                        color="{{ $selectedAuditType === 'duplicate' ? 'amber' : 'sky' }}"
                    >
                        {{ $selectedAuditType }}
                    </flux:badge>

                    @if ($editDecisionId)
                        {{-- Badge mit der Entscheidung --}}
                        <flux:badge
                            variant="subtle"
                            color="emerald"
                        >
                            #{{ $editDecisionId }}
                        </flux:badge>
                    @else
                        {{-- Badge für neue Entscheidung --}}
                        <flux:badge
                            variant="subtle"
                            color="zinc"
                        >
                            {{ __('New decision') }}
                        </flux:badge>
                    @endif
                </div>
            </div>

            <div class="scrollbar-gutter-auto -mr-4 flex flex-col gap-6 overflow-y-auto pr-4">

                {{-- Callout mit Quellwert und Normalisierung --}}
                <flux:callout
                    icon="languages"
                    color="pink"
                    stroke-width="1"
                >
                    <flux:callout.heading>
                        {{ __('Source value') }}
                    </flux:callout.heading>

                    <flux:callout.text class="wrap-anywhere mt-2">
                        {{ $selectedItem['value'] ?? '—' }}
                    </flux:callout.text>

                    <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                        <span class="font-semibold">{{ __('Normalized') }}:</span>
                        <code class="wrap-anywhere ml-1">
                            {{ $selectedItem['normalized_value'] ?? '—' }}
                        </code>
                    </div>
                </flux:callout>

                @php
                    $hasTargetTranslationKeyOptions = count($editTargetTranslationKeyOptions) > 0;
                @endphp

                {{-- Callout mit Entscheidungsoptionen und Liste der betroffenen Verwendungsstellen --}}
                <flux:callout
                    icon="workflow"
                    color="emerald"
                    stroke-width="1"
                >
                    <div class="flex items-start justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('Decision') }}
                        </flux:callout.heading>

                        @if (!$hasTargetTranslationKeyOptions)
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <div class="relative">
                                    <flux:button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        color="amber"
                                        wire:click="openCreateTranslationKeyPanel"
                                        wire:loading.attr="disabled"
                                        wire:target="openCreateTranslationKeyPanel"
                                    >
                                        {{ __('Create new translation key') }}
                                    </flux:button>

                                    @if ($createTranslationKeyPanelOpen)
                                        <div
                                            class="w-124 absolute right-0 z-50 mt-2 rounded-xl border border-zinc-200 bg-white p-4 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
                                            wire:keydown.escape.window="closeCreateTranslationKeyPanel"
                                        >
                                            <div class="space-y-4">
                                                <flux:callout
                                                    title="{{ __('Create new translation key') }}"
                                                    icon="key"
                                                    color="amber"
                                                    stroke-width="1"
                                                    text="{{ __('Enter the new translation key that should be created for this usage-audit item.') }}"
                                                />

                                                <flux:field>
                                                    <flux:label>{{ __('New translation key') }}</flux:label>

                                                    <flux:input.group>
                                                        <flux:input.group.prefix>
                                                            <flux:icon.key stroke-width="1" />
                                                        </flux:input.group.prefix>

                                                        <flux:input
                                                            type="text"
                                                            wire:model.live.debounce.300ms="createTranslationKeyInput"
                                                            wire:keydown.enter="createTranslationKeyFromUsageAudit"
                                                            placeholder="{{ __('admin.common.ui.example_admin_actions_save') }}"
                                                        />
                                                    </flux:input.group>

                                                    @error('createTranslationKeyInput')
                                                        <flux:error>{{ $message }}</flux:error>
                                                    @enderror
                                                </flux:field>

                                                <div class="flex justify-end gap-2">
                                                    <x-ui.button.cancel
                                                        type="button"
                                                        size="sm"
                                                        wire:click="closeCreateTranslationKeyPanel"
                                                        wire:loading.attr="disabled"
                                                        wire:target="closeCreateTranslationKeyPanel"
                                                    />

                                                    <x-ui.button.save
                                                        type="button"
                                                        size="sm"
                                                        :label="__('admin.roles.actions.create')"
                                                        :disabled="trim($createTranslationKeyInput) === ''"
                                                        wire:click="createTranslationKeyFromUsageAudit"
                                                        wire:loading.attr="disabled"
                                                        wire:target="createTranslationKeyFromUsageAudit"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <x-ui.tooltip.trigger
                                    :title="__('Missing translation key')"
                                    :text="__(
                                        'Unify is disabled because no existing target translation key is available. Use Needs new key to keep this item as a follow-up.',
                                    )"
                                >
                                    <flux:badge
                                        variant="subtle"
                                        color="amber"
                                    >
                                        {{ __('Missing translation key') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-3">
                        <flux:field>
                            <flux:label>
                                {{ __('Action') }}
                            </flux:label>

                            {{-- Input für die Auswahl der Entscheidungsaktion (Unify, Skip, Needs new key) --}}
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <flux:icon.activity stroke-width="1" />
                                </flux:input.group.prefix>

                                <flux:select wire:model.live="editDecisionAction">
                                    <flux:select.option value="undecided">
                                        {{ __('Undecided') }}
                                    </flux:select.option>
                                    @if ($hasTargetTranslationKeyOptions)
                                        <flux:select.option value="unify_to_target_key">
                                            {{ __('Unify to target key') }}
                                        </flux:select.option>
                                    @endif
                                    <flux:select.option value="skip">
                                        {{ __('Skip') }}
                                    </flux:select.option>
                                    <flux:select.option value="create_new_key">
                                        {{ __('Needs new key') }}
                                    </flux:select.option>
                                </flux:select>
                            </flux:input.group>
                        </flux:field>

                        <flux:field class="col-span-2">
                            <flux:label>{{ __('Target translation key') }}</flux:label>

                            {{-- Input für die Auswahl des Ziel-Übersetzungsschlüssels --}}
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <flux:icon.key stroke-width="1" />
                                </flux:input.group.prefix>

                                <flux:select
                                    wire:model.live="editTargetTranslationKey"
                                    :disabled="$editDecisionAction !== 'unify_to_target_key' || !$hasTargetTranslationKeyOptions"
                                >
                                    <flux:select.option
                                        value=""
                                        disabled
                                    >
                                        {{ __('Select target translation key ...') }}
                                    </flux:select.option>

                                    @forelse ($editTargetTranslationKeyOptions as $targetTranslationKeyOption)
                                        <flux:select.option value="{{ $targetTranslationKeyOption }}">
                                            {{ $targetTranslationKeyOption }}
                                        </flux:select.option>
                                    @empty
                                        <flux:select.option
                                            value=""
                                            disabled
                                        >
                                            {{ __('No target key available') }}
                                        </flux:select.option>
                                    @endforelse
                                </flux:select>
                            </flux:input.group>

                            @error('editTargetTranslationKey')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>

                    <div class="mt-4">
                        <flux:field>
                            <flux:label>{{ __('Review note') }}</flux:label>

                            {{-- Textarea-Input für eine optionale Anmerkung zur Entscheidungsbegründung --}}
                            <flux:input.group>
                                <flux:input.group.prefix>
                                    <flux:icon.pen-line stroke-width="1" />
                                </flux:input.group.prefix>

                                <flux:textarea
                                    class="rounded-l-none"
                                    rows="2"
                                    wire:model.live.debounce.500ms="editReviewNote"
                                    placeholder="{{ __('Optional review note for traceability') }}"
                                />
                            </flux:input.group>
                        </flux:field>
                    </div>
                </flux:callout>

                {{-- Callout mit Liste der betroffenen Verwendungsstellen --}}
                <flux:callout
                    icon="radar"
                    color="orange"
                    stroke-width="1"
                >
                    <div class="scrollbar-gutter-auto -mr-4 flex items-center justify-between gap-3 pr-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:callout.heading>
                                    {{ __('Change usages') }}
                                </flux:callout.heading>

                                @if ($editDecisionAction === 'create_new_key')
                                    <x-ui.tooltip.trigger
                                        title="{{ $this->computedUsageAuditDecisionStatusLabel() }}"
                                        text="{{ __('Usage replacements are disabled because this item needs a new translation key first.') }}"
                                    >

                                        <flux:badge
                                            variant="subtle"
                                            color="{{ $this->computedUsageAuditDecisionStatusColor() }}"
                                        >
                                            {{ $this->computedUsageAuditDecisionStatusLabel() }}
                                        </flux:badge>
                                    </x-ui.tooltip.trigger>
                                @elseif ($editDecisionAction === 'skip')
                                    <x-ui.tooltip.trigger
                                        title="{{ $this->computedUsageAuditDecisionStatusLabel() }}"
                                        text="{{ __('Usage replacements are disabled because this item needs a new translation key first.') }}"
                                    >

                                        <flux:badge
                                            variant="subtle"
                                            color="{{ $this->computedUsageAuditDecisionStatusColor() }}"
                                        >
                                            {{ $this->computedUsageAuditDecisionStatusLabel() }}
                                        </flux:badge>
                                    </x-ui.tooltip.trigger>
                                @endif

                            </div>

                        </div>

                        <flux:badge
                            variant="subtle"
                            color="sky"
                        >
                            {{ count($editUsageRows) }}
                        </flux:badge>
                    </div>

                    <div class="scrollbar-gutter-auto -mr-4 mt-4 max-h-80 space-y-2 overflow-y-auto pr-3">
                        @forelse ($editUsageRows as $usageIndex => $usageRow)
                            <div
                                class="rounded-lg border border-zinc-200 bg-white/60 p-3 text-sm dark:border-zinc-700 dark:bg-zinc-950/20">
                                <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(18rem,24rem)]">
                                    <div class="min-w-0 space-y-2 overflow-hidden">
                                        <div class="flex flex-wrap items-center gap-2">

                                            {{-- Badge ID --}}
                                            <flux:badge
                                                class="tabular-nums"
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                #{{ $usageRow['translation_key_id'] ?? '—' }}
                                            </flux:badge>

                                            {{-- Badge mit der Verwendungsstelle --}}
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ (bool) ($usageRow['is_stale'] ?? false) ? 'amber' : 'emerald' }}"
                                            >
                                                {{ (bool) ($usageRow['is_stale'] ?? false) ? __('Stale') : __('admin.app_settings.locale.current') }}
                                            </flux:badge>

                                            @if (trim((string) ($usageRow['detected_function'] ?? '')) !== '')
                                                <flux:badge
                                                    size="sm"
                                                    variant="subtle"
                                                    color="zinc"
                                                >
                                                    {{ $usageRow['detected_function'] }}
                                                </flux:badge>
                                            @endif

                                            @if (trim((string) ($usageRow['classification'] ?? '')) !== '')
                                                <flux:badge
                                                    size="sm"
                                                    variant="subtle"
                                                    color="sky"
                                                >
                                                    {{ $usageRow['classification'] }}
                                                </flux:badge>
                                            @endif
                                        </div>

                                        <code class="wrap-anywhere block text-xs">
                                            {{ $usageRow['current_translation_key'] ?? '—' }}
                                        </code>

                                        <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-2">
                                            <span class="shrink-0 font-semibold">
                                                {{ __('admin.translation_list.modal.path') }}:
                                            </span>

                                            <code class="wrap-anywhere min-w-0 max-w-full whitespace-normal text-xs">
                                                {{ $usageRow['file'] ?? '—' }}
                                            </code>

                                            @if ((int) ($usageRow['line'] ?? 0) > 0)
                                                <flux:badge
                                                    class="shrink-0 tabular-nums"
                                                    size="sm"
                                                    variant="subtle"
                                                    color="zinc"
                                                >
                                                    {{ __('admin.translation_list.modal.line') }}
                                                    {{ (int) ($usageRow['line'] ?? 0) }}
                                                </flux:badge>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="h-full min-w-0 border-l border-zinc-200 pl-4 xl:min-w-72 dark:border-zinc-700">
                                        <div class="flex flex-col items-stretch justify-start gap-3">
                                            <flux:field variant="inline">
                                                <flux:switch
                                                    class="switch-colored mr-3 hover:cursor-pointer"
                                                    wire:model="editUsageRows.{{ $usageIndex }}.include_in_change"
                                                    :disabled="$editDecisionAction !== 'unify_to_target_key' || trim(
                                                        $editTargetTranslationKey) === '' || ($usageRow[
                                                        'change_status'] ?? null) === 'already_target'"
                                                />

                                                <flux:label @class([
                                                    'text-sm opacity-70',
                                                    'hover:cursor-pointer' =>
                                                        ($usageRow['change_status'] ?? null) !== 'already_target',
                                                    'cursor-not-allowed' =>
                                                        ($usageRow['change_status'] ?? null) === 'already_target',
                                                ])>
                                                    {{ ($usageRow['change_status'] ?? null) === 'already_target' ? __('Already target') : __('Include in change') }}
                                                </flux:label>
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>{{ __('Change status') }}</flux:label>

                                                <flux:input.group>
                                                    <flux:input.group.prefix>
                                                        <flux:icon.scale stroke-width="1" />
                                                    </flux:input.group.prefix>

                                                    <flux:select
                                                        wire:model="editUsageRows.{{ $usageIndex }}.change_status"
                                                        :disabled="$editDecisionAction !== 'unify_to_target_key' || trim(
                                                            $editTargetTranslationKey) === '' || (
                                                            $usageRow[
                                                                'change_status'] ?? null) === 'already_target'"
                                                    >
                                                        <flux:select.option value="pending">
                                                            {{ __('Pending') }}
                                                        </flux:select.option>
                                                        <flux:select.option value="ready">
                                                            {{ __('Ready') }}
                                                        </flux:select.option>
                                                        <flux:select.option value="skipped">
                                                            {{ __('Skipped') }}
                                                        </flux:select.option>
                                                        <flux:select.option value="applied">
                                                            {{ __('Applied') }}
                                                        </flux:select.option>
                                                        <flux:select.option value="already_target">
                                                            {{ __('Already target') }}
                                                        </flux:select.option>
                                                        <flux:select.option value="needs_key">
                                                            {{ __('Needs key') }}
                                                        </flux:select.option>
                                                    </flux:select>
                                                </flux:input.group>
                                            </flux:field>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No usage locations available.') }}
                            </div>
                        @endforelse
                    </div>
                </flux:callout>
            </div>

            <div class="shrink-0 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <div class="flex justify-end gap-3">
                    <x-ui.button.close wire:click="closeUsageAuditEditModal" />

                    <x-ui.button.save
                        variant="primary"
                        :label="__('Save decision')"
                        :disabled="!$this->canSaveUsageAuditDecision()"
                        wire:click="saveUsageAuditDecision"
                        wire:loading.attr="disabled"
                        wire:target="saveUsageAuditDecision"
                    />
                </div>
            </div>
        </div>
    @endif
</flux:modal>
