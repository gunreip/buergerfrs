<flux:modal
    class="max-w-9/10 w-full"
    name="generation-finding-editor"
    wire:model="editorOpen"
    x-data
    x-init="const clear = () => $dispatch('buergerfrs:clear-notices', { scope: 'finding-editor:' + $wire.$id });
    $watch('$wire.selected', clear);
    $watch('$wire.editorOpen', open => { if (!open) clear(); });
    ['confirmOpen', 'sharedOpen', 'sourcePreviewOpen', 'reviewOpen', 'publicationOpen', 'changeReasonOpen', 'assignmentReasonOpen', 'translationKeyOpen', 'workflowReasonOpen']
    .forEach(property => $watch('$wire.' + property, open => { if (open) clear(); }));"
    x-on:workbench-editor-error-focus.window="
        if ($event.detail.component !== $wire.$id || $event.detail.finding !== $wire.selected || !$wire.editorOpen) return;
        const targetName = $event.detail.target;
        if ($wire.confirmOpen) await $wire.$set('confirmOpen', false);
        await $nextTick();
        const target = Array.from($el.querySelectorAll('[data-editor-error-target]'))
            .find(element => element.dataset.editorErrorTarget === targetName);
        if (!target) return;
        target.scrollIntoView({ block: 'center', behavior: 'smooth' });
        if (target.matches('[data-flux-select]')) {
            const trigger = target.querySelector('[data-flux-select-button]');
            if (!trigger || trigger.disabled) return;
            trigger.focus({ preventScroll: true });
            if (!target.hasAttribute('data-open')) trigger.click();
            requestAnimationFrame(() => {
                const search = target.querySelector('[data-flux-select-search] input, input[data-flux-select-search]');
                if (search) search.focus({ preventScroll: true });
            });
            return;
        }
        const section = ['translationValues'].includes(targetName);
        const control = section ? null : target.matches('input, textarea, select, button') ? target
            : target.querySelector('input:not([disabled]), textarea:not([disabled]), select:not([disabled]), button:not([disabled])');
        if (control) control.focus({ preventScroll: true });
        else { target.setAttribute('tabindex', '-1'); target.focus({ preventScroll: true }); }
    "
>
    <x-translation-workbench::ui.common.modal-loading
        target="open, openWorkflowStatus, previewSource, confirmAssignment, openShared, copySourceToTarget, copyTargetLanguageToAllSelectedSubLanguages, copyPublished, openPublication, saveDraft, confirmReview, openPublicationSelection, submitForReview, selectedSubLanguages, openTranslationKey, openAssignmentReason, openChangeReason"
    />
    @if ($editorOpen && $context)
        @php
            $finding = $context['finding'];
            $assignedKey = !empty($finding['key_id'])
                ? (($finding['namespace'] ?? '') !== '' ? $finding['namespace'] . '::' : '') . $finding['full_name']
                : null;
            $assignmentUnchanged = $assignedKey !== null && trim($assignmentKey) === $assignedKey;
        @endphp
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.square-pen class="inline size-8" />
                    {{ __('Edit finding') }} #{{ $selected }}
                </flux:heading>
                <flux:button.group>
                    <flux:button
                        size="sm"
                        variant="outline"
                        wire:click="openWorkflowStatus({{ $selected }})"
                    >
                        <flux:icon.queue-list class="size-5" />
                    </flux:button>
                    <flux:button
                        class="mr-8"
                        size="sm"
                        variant="outline"
                        wire:click="openWorkflowStatus({{ $selected }})"
                    >
                        {{ __('Workflow status') }}
                    </flux:button>
                </flux:button.group>
            </div>
            @if ($finding['status'] !== 'active')
                <flux:callout
                    class="flex-1"
                    color="amber"
                    icon="archive"
                    heading="{{ __('Archived source occurrence. It cannot be edited or reactivated.') }}"
                    text="{{ $finding['retired_reason'] }}"
                />
            @endif

            <flux:callout
                icon="key"
                heading="{{ __('Translation key') }}"
                text="{{ __('The translation key associated with this finding.') }}"
            >
                <div class="grid items-stretch gap-4 lg:grid-cols-2">
                    <div class="flex min-w-0 flex-col gap-4">
                        <flux:callout
                            class="grow"
                            color="sky"
                            icon="code"
                            :heading="__('Finding in source')"
                        >
                            <x-translation-workbench::ui.common.details>
                                <x-translation-workbench::ui.common.details.item
                                    class="break-all"
                                    :label="__('Source file')"
                                >
                                    <x-translation-workbench::ui.common.path-preview
                                        :path="$finding['path']"
                                        :length="60"
                                        side="center"
                                        :line="$finding['source_line']"
                                        :column="$finding['source_column']"
                                        :heading="__('Source file')"
                                    />
                                </x-translation-workbench::ui.common.details.item>
                                <x-translation-workbench::ui.common.details.item :label="__('Captured source expression')">
                                    <x-translation-workbench::ui.common.text-preview
                                        :mono="true"
                                        :text="$finding['raw_expression']"
                                        side="center"
                                        :heading="__('Captured source expression')"
                                    />
                                </x-translation-workbench::ui.common.details.item>
                            </x-translation-workbench::ui.common.details>

                            <div class="mt-auto flex flex-wrap items-center justify-end gap-3 pt-3">
                                {{-- Predecessor --}}
                                @if ($finding['predecessor_id'])
                                    <flux:button.group>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="fuchsia"
                                            wire:click="open({{ $finding['predecessor_id'] }})"
                                        >
                                            <flux:icon.git-branch-minus />
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="fuchsia"
                                            wire:click="open({{ $finding['predecessor_id'] }})"
                                        >
                                            {{ __('Predecessor') }}
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="fuchsia"
                                            wire:click="open({{ $finding['predecessor_id'] }})"
                                        >
                                            #{{ $finding['predecessor_id'] }}
                                        </flux:button>
                                    </flux:button.group>
                                @endif
                                {{-- Successor --}}
                                @if ($context['successor'])
                                    <flux:button.group>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="sky"
                                            wire:click="open({{ $context['successor'] }})"
                                        >
                                            <flux:icon.git-branch-plus />
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="sky"
                                            wire:click="open({{ $context['successor'] }})"
                                        >
                                            {{ __('Successor') }}
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="sky"
                                            wire:click="open({{ $context['successor'] }})"
                                        >
                                            #{{ $context['successor'] }}
                                        </flux:button>
                                    </flux:button.group>
                                @endif
                                {{-- Inspect source replacement --}}
                                @if ($finding['status'] === 'active' && $finding['key_id'])
                                    <flux:button.group>
                                        <flux:button
                                            data-editor-error-target="sourcePreview"
                                            size="sm"
                                            variant="filled"
                                            color="orange"
                                            wire:click="previewSource"
                                            disabled
                                        >
                                            <flux:icon.code-xml />
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="orange"
                                            wire:click="previewSource"
                                            disabled
                                        >
                                            {{ __('Inspect source replacement') }}
                                        </flux:button>
                                    </flux:button.group>
                                @endif
                            </div>

                        </flux:callout>

                        <flux:callout
                            class="grow"
                            color="sky"
                            icon="germ-off"
                            :heading="__('Immutable capture')"
                        >
                            <x-translation-workbench::ui.common.details>
                                @foreach (['raw_source_code_literal' => 'Raw source literal / key', 'raw_literal' => 'Raw literal', 'suggested_literal' => 'Suggested literal', 'suggested_key' => 'Suggested key'] as $field => $label)
                                    <x-translation-workbench::ui.common.details.item :label="__($label)">
                                        <x-translation-workbench::ui.common.text-preview
                                            :mono="$field === 'suggested_key' || ($field === 'raw_source_code_literal' && ($finding['raw_literal'] ?? null) === null)"
                                            :text="$finding[$field] ?? 'NULL'"
                                            side="center"
                                            :heading="__($label)"
                                        />
                                    </x-translation-workbench::ui.common.details.item>
                                @endforeach
                            </x-translation-workbench::ui.common.details>
                        </flux:callout>
                    </div>

                    <flux:callout
                        class="min-w-0 [&>div]:min-w-0"
                        id="workbench-key-assignment"
                        :color="$finding['status'] === 'active' ? 'green' : 'amber'"
                        icon="key"
                    >
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <flux:callout.heading>
                                {{ __('Translation key assignment') }}
                            </flux:callout.heading>
                            @include(
                                'translation-workbench::livewire.translation-workbench.shared.finding-candidates',
                                ['candidateFinding' => $selected]
                            )
                        </div>
                        <flux:callout.text>
                            {{ __('Assign this source occurrence to a key. Other findings retain their identity. Changing the assignment reloads the draft fields.') }}
                        </flux:callout.text>
                        <x-translation-workbench::ui.common.details>
                            <x-translation-workbench::ui.common.details.item
                                class="tabular-nums"
                                :label="__('Current key ID')"
                                :item="$finding['key_id'] ? '#' . $finding['key_id'] : __('Not assigned')"
                            />
                            <x-translation-workbench::ui.common.details.item :label="__('Current key')">
                                @if ($finding['key_id'])
                                    <x-translation-workbench::ui.common.text-preview
                                        :mono="true"
                                        :text="$finding['full_name']"
                                        :length="80"
                                        side="center"
                                        :heading="__('Current key')"
                                    />
                                @else
                                    <flux:callout.text>
                                        {{ __('Not assigned') }}
                                    </flux:callout.text>
                                @endif
                            </x-translation-workbench::ui.common.details.item>
                        </x-translation-workbench::ui.common.details>
                        {{-- Assignment action --}}
                        @if ($finding['status'] === 'active')
                            <div class="space-y-3">
                                <flux:field class="mt-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <flux:label>
                                            <flux:callout.heading>
                                                {{ __('Translation key') }}
                                            </flux:callout.heading>
                                        </flux:label>
                                        @if ($assignmentUnchanged)
                                            <flux:badge
                                                class="-mt-1 shrink-0"
                                                size="sm"
                                                color="green"
                                            >
                                                {{ __('Assigned key') }}
                                            </flux:badge>
                                        @elseif (trim($assignmentKey) !== '' &&
                                                !\Gunreip\TranslationWorkbench\Generation\TranslationKeySegments::configured()->allows(trim($assignmentKey)))
                                            <flux:badge
                                                class="-mt-1 shrink-0"
                                                size="sm"
                                                color="amber"
                                            >
                                                {{ __('Incomplete translation key') }}
                                            </flux:badge>
                                        @elseif (trim($assignmentKey) !== '')
                                            <flux:badge
                                                class="-mt-1 shrink-0"
                                                size="sm"
                                                :color="trim($assignmentKey) === ($finding['suggested_key'] ?? null) ? 'amber' :
                                                    'green'"
                                            >
                                                {{ trim($assignmentKey) === ($finding['suggested_key'] ?? null) ? __('Suggested key') : __('New translation key') }}
                                            </flux:badge>
                                        @else
                                            <flux:badge
                                                class="-mt-1 shrink-0"
                                                size="sm"
                                                color="red"
                                            >
                                                {{ __('No translation key selected') }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                    @include('translation-workbench::livewire.translation-workbench.shared.key-segment-selector')

                                    @if (empty($finding['suggested_key']))
                                        <flux:error>
                                            {{ __('Capture inconsistency: this finding has no suggested key.') }}
                                        </flux:error>
                                    @endif
                                </flux:field>

                                {{-- Reason for assignment --}}
                                <flux:field class="mb-6">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <flux:label>
                                            <flux:callout.heading>
                                                {{ __('Reason for translation key assignment') }}
                                            </flux:callout.heading>
                                        </flux:label>
                                        @if ($assignmentUnchanged)
                                            <flux:tooltip>
                                                <flux:badge
                                                    tabindex="0"
                                                    size="sm"
                                                    color="green"
                                                >
                                                    {{ __('Already assigned') }}
                                                </flux:badge>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Already assigned')"
                                                    :text="__(
                                                        'This finding already uses the selected translation key. No new assignment or reason is needed. Choose a different key to enable the reason field and review the changed assignment.',
                                                    )"
                                                />
                                            </flux:tooltip>
                                        @endif
                                    </div>
                                    <flux:input.group>
                                        <flux:input.group.prefix>
                                            <flux:icon.tube-lotion />
                                        </flux:input.group.prefix>
                                        <flux:select
                                            data-editor-error-target="assignmentReason"
                                            wire:model="assignmentReason"
                                            :disabled="$assignmentUnchanged"
                                            x-data="{ reasonSearch: '' }"
                                            variant="listbox"
                                            searchable
                                            clearable
                                            :placeholder="__('Choose a translation key reason')"
                                        >
                                            <x-slot name="search">
                                                <flux:select.search x-model="reasonSearch" />
                                            </x-slot>
                                            <flux:select.option.create
                                                x-on:click="$wire.openAssignmentReason(reasonSearch)"
                                            >
                                                {{ __('Create a translation key reason') }}
                                            </flux:select.option.create>
                                            @foreach ($assignmentReasonOptions as $assignmentReasonOption)
                                                <flux:select.option
                                                    icon="tube-lotion"
                                                    :value="$assignmentReasonOption"
                                                >
                                                    <span class="wrap-break-word whitespace-normal">
                                                        {{ $assignmentReasonOption }}
                                                    </span>
                                                </flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    </flux:input.group>
                                </flux:field>

                                <div class="flex justify-end gap-3">
                                    <flux:button.group>
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="emerald"
                                            icon="check"
                                            wire:click="confirmAssignment"
                                            :disabled="$assignmentUnchanged"
                                        />
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            color="emerald"
                                            wire:click="confirmAssignment"
                                            :disabled="$assignmentUnchanged"
                                        >
                                            {{ __('Review key assignment') }}
                                        </flux:button>
                                    </flux:button.group>

                                    @if (($sharedSummaries[$selected]['candidates'] ?? 0) >= 2 || ($sharedSummaries[$selected]['shared'] ?? 0) >= 2)
                                        <flux:button.group>
                                            <flux:button
                                                size="sm"
                                                variant="filled"
                                                color="sky"
                                                icon="square-text"
                                                wire:click="openShared({{ $selected }})"
                                            />
                                            <flux:button
                                                size="sm"
                                                variant="filled"
                                                color="sky"
                                                wire:click="openShared({{ $selected }})"
                                            >
                                                {{ __('View matching findings') }}
                                            </flux:button>
                                        </flux:button.group>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </flux:callout>
                </div>
            </flux:callout>

            @if ($finding['key_id'])
                <flux:callout
                    id="workbench-translation-values"
                    data-editor-error-target="translationValues"
                    color="zinc"
                    icon="language"
                >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <x-translation-workbench::ui.common.text-preview
                                :prefix-key="__('Translations for key #:id', ['id' => $finding['key_id']])"
                                :text="$assignedKey"
                                :mono="true"
                                side="center"
                                :heading="__('Assigned translation key')"
                            />
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            @if ($context['usageCount'] > 1)
                                <flux:badge
                                    color="sky"
                                    size="sm"
                                >
                                    {{ __('Shared key · :count findings', ['count' => $context['usageCount']]) }}
                                </flux:badge>
                            @endif
                            <x-translation-workbench::ui.common.spellcheck-info />
                        </div>
                    </div>

                    @if ($context['usageCount'] > 1)
                        <flux:callout
                            color="red"
                            icon="triangle-alert"
                            heading="{{ __('Shared key information') }}"
                            text="{{ __('This finding uses the translations of the shared key. Changes apply to all :count linked findings; they do not need separate translations.', ['count' => $context['usageCount']]) }}"
                        />
                    @endif

                    <flux:callout
                        color="amber"
                        icon="info"
                        heading="Intentional translation"
                        text="{{ __('An empty string or 0 can be an intentional translation. Saving creates a draft revision; it does not publish or rewrite source code.') }}"
                    />

                    <div class="grid items-stretch gap-4 lg:grid-cols-2">
                        <div class="*:data-flux-callout:grow flex min-w-0 flex-col gap-3">
                            <flux:callout color="green">
                                <flux:field>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <flux:tooltip>
                                            <flux:label>
                                                <flux:callout.text class="inline-flex items-center gap-2">
                                                    <flux:flag
                                                        :country="$translationFlagCountries['en']"
                                                        size="xs"
                                                        circle
                                                    />
                                                    {{ __('Source language') }} · en
                                                </flux:callout.text>
                                            </flux:label>
                                            <x-translation-workbench::ui.common.tooltip-content
                                                :heading="__('Source language')"
                                                :text="__('The language of the original source text.')"
                                            />
                                        </flux:tooltip>
                                        <div class="flex items-center gap-2">
                                            @if (($context['sourceText'] ?? ($finding['raw_literal'] ?? ($finding['suggested_literal'] ?? null))) !== null)
                                                <flux:tooltip
                                                    :content="($context['sourceText'] ?? null) !== null ? __(
                                                            'Current English source text.') :
                                                        (($finding['raw_literal'] ?? null) !== null ? __(
                                                            'Original captured literal.') : __(
                                                            'Source text derived from the captured translation evidence.'
                                                        ))"
                                                >
                                                    <flux:badge
                                                        tabindex="0"
                                                        size="sm"
                                                        :color="($context['sourceText'] ?? null) !== null ? 'green' : ((
                                                                $finding[
                                                                    'raw_literal'] ?? null) !== null ? 'amber' :
                                                            'pink')"
                                                    >
                                                        {{ __('Captured text') }}
                                                    </flux:badge>
                                                </flux:tooltip>
                                            @endif
                                            <flux:button
                                                type="button"
                                                size="xs"
                                                variant="ghost"
                                                color="green"
                                                icon="clipboard-document"
                                                wire:click="copySourceToTarget"
                                                :disabled="$finding['status'] !== 'active'"
                                                :tooltip="__('Copy the current source text into this translation draft, replacing its contents. This does not save changes.')"
                                                :aria-label="__('Copy source text to translation draft')"
                                            />
                                        </div>
                                    </div>
                                    <flux:textarea
                                        class="field-sizing-content max-h-80 min-h-12 overflow-x-auto"
                                        data-editor-error-target="sourceLanguageValue"
                                        lang="en"
                                        spellcheck="true"
                                        autocorrect="on"
                                        rows="3"
                                        wire:model="sourceLanguageValue"
                                        :placeholder="__('No English source text captured.')"
                                    />
                                </flux:field>

                                <flux:field>
                                    <div class="flex items-center justify-between gap-3">
                                        <flux:tooltip>
                                            <flux:label>
                                                <flux:callout.text class="inline-flex items-center gap-2">
                                                    <flux:flag
                                                        :country="$translationFlagCountries[$locale] ?? null"
                                                        size="xs"
                                                        circle
                                                    />
                                                    {{ __('Active target language') }} · {{ $locale }}
                                                </flux:callout.text>
                                            </flux:label>
                                            <x-translation-workbench::ui.common.tooltip-content
                                                :heading="__('Active target language')"
                                                :text="__(
                                                    'Translation targets follow the active language in Settings and its enabled sublanguages.',
                                                )"
                                            />
                                        </flux:tooltip>
                                        <div
                                            class="flex items-center gap-2"
                                            x-data
                                        >
                                            <span
                                                x-cloak
                                                x-show="($wire.value ?? '') === ''"
                                            >
                                                <flux:tooltip
                                                    :content="__('The input for :locale on key #:key is empty. This describes the current input, not whether translations exist in published files. An empty translation can also be intentional.',
                                                        ['locale' => $locale, 'key' => $finding['key_id']])"
                                                >
                                                    <flux:badge
                                                        tabindex="0"
                                                        color="red"
                                                        size="sm"
                                                    >
                                                        {{ __('Input empty') }}
                                                    </flux:badge>
                                                </flux:tooltip>
                                            </span>
                                            <span
                                                x-cloak
                                                x-show="($wire.value ?? '') !== '' && $wire.sourceLanguageValue === $wire.value"
                                            >
                                                <flux:tooltip
                                                    :content="__('Source and target text are identical. The target text may not have been translated yet; identical wording can also be intentional.')"
                                                >
                                                    <flux:badge
                                                        tabindex="0"
                                                        color="amber"
                                                        size="sm"
                                                    >
                                                        {{ __('Equal literal') }}
                                                    </flux:badge>
                                                </flux:tooltip>
                                            </span>
                                            <flux:button
                                                type="button"
                                                size="xs"
                                                variant="ghost"
                                                color="sky"
                                                icon="clipboard-document"
                                                wire:click="copyTargetLanguageToAllSelectedSubLanguages"
                                                :disabled="$finding['status'] !== 'active' || count($selectedSubLanguages) === 0"
                                                x-bind:disabled="{{ \Illuminate\Support\Js::from($finding['status'] !== 'active') }} ||
                                                    $wire.selectedSubLanguages.length === 0 || $wire.value
                                                    .trim() === ''"
                                                :tooltip="__('Copy the current main-language text into all selected language variants, replacing their contents. This does not save changes.')"
                                                :aria-label="__('Copy main-language text to all selected language variants')"
                                            />
                                        </div>
                                    </div>
                                    <flux:textarea
                                        class="field-sizing-content max-h-80 min-h-12 overflow-x-auto"
                                        data-editor-error-target="value"
                                        wire:model="value"
                                        x-on:workbench-select-translation-draft.window="$nextTick(() => { $el.focus(); $el.select(); })"
                                        :lang="$locale"
                                        spellcheck="true"
                                        rows="3"
                                        :disabled="$finding['status'] !== 'active'"
                                    />

                                </flux:field>
                            </flux:callout>

                            <flux:callout
                                color="amber"
                                icon="circle-dashed-check"
                            >
                                <flux:callout.heading>
                                    {{ __('Published file evidence') }}
                                </flux:callout.heading>
                                @forelse ($context['published'] as $evidence)
                                    <flux:textarea
                                        class="min-h-12"
                                        readonly
                                        rows="3"
                                        :label="$evidence['path']"
                                    >
                                        {{ $evidence['value'] }}
                                    </flux:textarea>
                                    @if (!$evidence['current'])
                                        <flux:badge color="amber">
                                            {{ __('File changed since capture') }}
                                        </flux:badge>
                                    @endif
                                @empty
                                    <flux:callout.text>
                                        {{ __('No published value captured for this exact locale and key.') }}
                                    </flux:callout.text>
                                @endforelse
                                @if (count($context['published']) === 1 && $context['published'][0]['current'] && $finding['status'] === 'active')
                                    <flux:tooltip>
                                        <div class="inline-flex">
                                            <flux:button
                                                size="sm"
                                                variant="outline"
                                                wire:click="copyPublished"
                                            >{{ __('Copy published value into draft') }}</flux:button>
                                        </div>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('Copy published value into draft')"
                                            :text="__(
                                                'Replace the current translation draft with the captured published value for this language. Save the draft to keep this change.',
                                            )"
                                        />
                                    </flux:tooltip>
                                @endif
                            </flux:callout>
                        </div>

                        <div class="min-w-0">
                            <flux:callout
                                class="h-full"
                                color="indigo"
                            >
                                <flux:field class="mb-3">
                                    <flux:tooltip>
                                        <flux:label>
                                            <flux:callout.heading>
                                                {{ __('Language variants for :language', ['language' => $locale ?? null]) }}
                                                <flux:flag
                                                    :country="$translationFlagCountries[$locale] ?? null"
                                                    size="xs"
                                                    circle
                                                />
                                            </flux:callout.heading>
                                        </flux:label>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('Language variants for :language', [
                                                'language' => $locale ?? null,
                                            ])"
                                            :text="__(
                                                'Select the appropriate language variants for language :language.',
                                                [
                                                    'language' => $locale ?? null,
                                                ],
                                            )"
                                        />
                                    </flux:tooltip>
                                    <div class="grid grid-cols-[minmax(0,18rem)_minmax(0,1fr)] items-center gap-3">
                                        <flux:input.group class="min-w-0">
                                            <flux:input.group.prefix>
                                                <flux:icon.language />
                                            </flux:input.group.prefix>
                                            <flux:select
                                                class="min-w-0"
                                                data-editor-error-target="selectedSubLanguages"
                                                variant="listbox"
                                                multiple
                                                clearable
                                                searchable
                                                :placeholder="__('Select language variants…')"
                                                :disabled="$finding['status'] !== 'active' || count($subLanguages) === 0"
                                                x-bind:disabled="{{ \Illuminate\Support\Js::from($finding['status'] !== 'active' || count($subLanguages) === 0) }}
                                                    ||
                                                    $wire.value.trim() === ''"
                                                wire:model.live="selectedSubLanguages"
                                            >
                                                @foreach ($subLanguages as $code)
                                                    <flux:select.option :value="$code">
                                                        <span class="inline-flex items-center gap-2">
                                                            <flux:flag
                                                                :country="$translationFlagCountries[$code]"
                                                                size="xs"
                                                                circle
                                                            />
                                                            <span class="font-mono">
                                                                {{ $code }}
                                                            </span>
                                                        </span>
                                                    </flux:select.option>
                                                @endforeach
                                            </flux:select>
                                        </flux:input.group>
                                        <flux:text
                                            size="sm"
                                            x-cloak
                                            x-show="$wire.value.trim() === ''"
                                        >
                                            {{ __('Enter the main target-language translation before editing language variants. Existing variant texts are retained.') }}
                                        </flux:text>
                                        <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
                                            @foreach ($selectedSubLanguages as $code)
                                                @if (in_array($code, $subLanguages, true))
                                                    <flux:tooltip
                                                        :content="$code"
                                                        wire:key="finding-{{ $selected }}-selected-flag-{{ $code }}"
                                                    >
                                                        <span
                                                            class="inline-flex"
                                                            aria-label="{{ $code }}"
                                                            tabindex="0"
                                                        >
                                                            <flux:flag
                                                                :country="$translationFlagCountries[$code] ?? null"
                                                                size="sm"
                                                                circle
                                                            />
                                                        </span>
                                                    </flux:tooltip>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @if (count($subLanguages) === 0)
                                        <flux:description>
                                            {{ __('No language variants are activated for the active main language :language.', [
                                                'language' => $locale ?? null,
                                            ]) }}
                                        </flux:description>
                                    @endif

                                </flux:field>

                                @foreach ($selectedSubLanguages as $code)
                                    @if (in_array($code, $subLanguages, true))
                                        <flux:field
                                            wire:key="finding-{{ $selected }}-variant-{{ $code }}"
                                        >
                                            <div
                                                class="flex flex-wrap items-center justify-between gap-2"
                                                x-data
                                            >
                                                <flux:label>
                                                    <span class="inline-flex items-center gap-2">
                                                        <flux:flag
                                                            :country="$translationFlagCountries[$code] ?? null"
                                                            size="xs"
                                                            circle
                                                        />
                                                        <flux:callout.heading>
                                                            {{ __('Language variant') }} · {{ $code }}
                                                        </flux:callout.heading>
                                                    </span>
                                                </flux:label>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        x-cloak
                                                        x-show="($wire.subLanguageValues[@js($code)] ?? '') === ''"
                                                    >
                                                        <flux:tooltip
                                                            :content="__('The input for :locale on key #:key is empty. This describes the current input, not whether translations exist in published files. An empty translation can also be intentional.',
                                                                ['locale' => $code, 'key' => $finding['key_id']])"
                                                        >
                                                            <flux:badge
                                                                tabindex="0"
                                                                color="red"
                                                                size="sm"
                                                            >
                                                                {{ __('Input empty') }}
                                                            </flux:badge>
                                                        </flux:tooltip>
                                                    </span>
                                                    <span
                                                        x-cloak
                                                        x-show="($wire.subLanguageValues[@js($code)] ?? '') !== '' && $wire.value === $wire.subLanguageValues[@js($code)]"
                                                    >
                                                        <flux:tooltip
                                                            :content="__('Main-language and variant text are identical. The variant may not have been adapted yet; identical wording can also be intentional.')"
                                                        >
                                                            <flux:badge
                                                                tabindex="0"
                                                                color="amber"
                                                                size="sm"
                                                            >
                                                                {{ __('Equal literal') }}
                                                            </flux:badge>
                                                        </flux:tooltip>
                                                    </span>
                                                    <flux:button
                                                        type="button"
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="x-mark"
                                                        color="red"
                                                        x-on:click="$wire.$set({{ \Illuminate\Support\Js::from('subLanguageValues.' . $code) }}, '', false)"
                                                        :disabled="$finding['status'] !== 'active'"
                                                        :tooltip="__('Clear this language variant. This does not save changes.')"
                                                        :aria-label="__('Clear language variant :locale', ['locale' => $code])"
                                                    />
                                                </div>
                                            </div>
                                            <flux:textarea
                                                class="field-sizing-content max-h-80 min-h-12 overflow-x-auto"
                                                data-editor-error-target="subLanguageValues.{{ $code }}"
                                                wire:model="subLanguageValues.{{ $code }}"
                                                x-bind:readonly="$wire.value.trim() === ''"
                                                :lang="$code"
                                                spellcheck="true"
                                                copyable
                                                rows="3"
                                                :disabled="$finding['status'] !== 'active'"
                                            />

                                        </flux:field>
                                    @endif
                                @endforeach

                                <flux:callout.text class="tabular-nums">
                                    {{ __('Revision') }}:
                                    {{ $context['revision']['id'] ?? __('No saved draft') }}
                                </flux:callout.text>

                                @if ($finding['status'] === 'active')
                                    <flux:field>
                                        <flux:label>
                                            <flux:tooltip>
                                                <flux:callout.heading>
                                                    {{ __('Reason for translation change') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Reason for translation change')"
                                                    :text="__(
                                                        'Please provide a reason for the translation change.',
                                                    )"
                                                />
                                            </flux:tooltip>
                                        </flux:label>
                                        <flux:button.group>
                                            <flux:button>
                                                <flux:icon.circle-dot />
                                            </flux:button>
                                            <flux:select
                                                data-editor-error-target="reason"
                                                wire:model="reason"
                                                variant="listbox"
                                                searchable
                                                clearable
                                                x-data="{ changeReasonSearch: '' }"
                                                :placeholder="__('Choose a reason')"
                                            >
                                                <x-slot name="search">
                                                    <flux:select.search x-model="changeReasonSearch" />
                                                </x-slot>
                                                <flux:select.option.create
                                                    x-on:click="$wire.openChangeReason(changeReasonSearch)"
                                                >
                                                    {{ __('Create a translation reason') }}
                                                </flux:select.option.create>
                                                @foreach ($changeReasonOptions as $changeReasonOption)
                                                    <flux:select.option
                                                        :value="$changeReasonOption"
                                                        icon="rocket"
                                                    >
                                                        <span
                                                            class="wrap-break-word whitespace-normal">{{ $changeReasonOption }}</span>
                                                    </flux:select.option>
                                                @endforeach
                                            </flux:select>
                                        </flux:button.group>

                                    </flux:field>
                                @endif
                            </flux:callout>
                        </div>

                        <div class="flex min-w-0 flex-wrap items-center justify-between gap-3 lg:col-span-2">
                            @if ($context['publication'] ?? null)
                                <flux:tooltip>
                                    <div class="inline-flex">
                                        <flux:button.group>
                                            <flux:button
                                                size="sm"
                                                variant="filled"
                                                color="green"
                                                wire:click="openPublication('{{ $context['publication']['id'] }}')"
                                            >
                                                <flux:icon.check-check />
                                            </flux:button>
                                            <flux:button
                                                size="sm"
                                                variant="filled"
                                                color="green"
                                                wire:click="openPublication('{{ $context['publication']['id'] }}')"
                                            >
                                                {{ __('Latest publication for this key and locale') }} ·
                                                {{ __($context['publication']['state']) }}
                                            </flux:button>
                                        </flux:button.group>
                                    </div>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Latest publication')"
                                        :text="__(
                                            'Inspect the latest publication for this translation key and language, including its file results and status.',
                                        )"
                                    />
                                </flux:tooltip>
                            @endif
                            @if ($finding['status'] === 'active')
                                <div class="ml-auto flex flex-wrap items-center justify-end gap-3">
                                    <flux:tooltip>
                                        <div class="inline-flex">
                                            <flux:button.group>
                                                <flux:button
                                                    icon="save"
                                                    size="sm"
                                                    variant="filled"
                                                    color="teal"
                                                    wire:click="saveDraft"
                                                />
                                                <flux:button
                                                    size="sm"
                                                    variant="filled"
                                                    color="teal"
                                                    wire:click="saveDraft"
                                                >
                                                    {{ __('Save draft') }}
                                                </flux:button>
                                            </flux:button.group>
                                        </div>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('Save draft')"
                                            :text="__(
                                                'Save the source text, main-language translation and selected language variants as drafts. A reason is required. This does not submit them for review or publish them.',
                                            )"
                                        />
                                    </flux:tooltip>

                                    @include('translation-workbench::livewire.translation-workbench.shared.submit-drafts')
                                </div>
                            @endif
                        </div>
                    </div>
                </flux:callout>

                @if ($context['review'] ?? null)
                    <flux:callout
                        icon="eye"
                        variant="outline"
                        :color="$context['review']['approved'] ? 'green' : 'amber'"
                    >
                        <flux:callout.heading>
                            {{ __('Revision review') }} #{{ $context['revision']['id'] }} ·
                            {{ $locale }}
                        </flux:callout.heading>
                        <div class="grid items-start gap-x-6 gap-y-3 lg:grid-cols-[minmax(0,1fr)_auto]">
                            <div class="min-w-0 space-y-2">
                                <flux:callout.text>
                                    {{ $context['review']['approved'] ? __('Approved for the current key and active source links.') : __('No current approval. Review is required before publication.') }}
                                </flux:callout.text>
                                <flux:callout.text>
                                    {{ __('Approval applies to this saved revision and all active findings using its key. A changed value, key name or set of active links requires a new review. Approval alone does not publish.') }}
                                </flux:callout.text>
                                @if ($context['review']['latest'])
                                    <x-translation-workbench::ui.common.details class="my-3">
                                        <x-translation-workbench::ui.common.details.item
                                            :label="__('Last decision')"
                                            :item="__($context['review']['latest']['decision'])"
                                        />
                                        <x-translation-workbench::ui.common.details.item
                                            class="tabular-nums"
                                            :label="__('Reviewer ID')"
                                            :item="'#' . $context['review']['latest']['actor_id']"
                                        />
                                        <x-translation-workbench::ui.common.details.item :label="__('Decision date')">
                                            <x-ui.date-time.date-time :value="$context['review']['latest']['occurred_at']" />
                                        </x-translation-workbench::ui.common.details.item>
                                        <x-translation-workbench::ui.common.details.item :label="__('Reason')">
                                            <x-translation-workbench::ui.common.text-preview
                                                :text="$context['review']['latest']['reason']"
                                                :heading="__('Reason')"
                                            />
                                        </x-translation-workbench::ui.common.details.item>
                                    </x-translation-workbench::ui.common.details>
                                @endif
                                @if ($finding['status'] === 'active')
                                    <flux:callout.text>
                                        {{ __('Preparing reserves the affected language file. No file is written until confirmation. Close unused runs to release their reservations.') }}
                                    </flux:callout.text>
                                @endif
                            </div>
                            <div class="grid items-start gap-2 justify-self-start lg:justify-self-end">
                                @if ($finding['status'] === 'active')
                                    <flux:tooltip class="w-full">
                                        <div class="flex w-full">
                                            <flux:button.group class="w-full">
                                                <flux:button
                                                    size="sm"
                                                    variant="filled"
                                                    color="sky"
                                                    wire:click="confirmReview(true)"
                                                >
                                                    <flux:icon
                                                        name="check"
                                                        size="5"
                                                    />
                                                </flux:button>
                                                <flux:button
                                                    class="flex-1"
                                                    size="sm"
                                                    variant="filled"
                                                    color="sky"
                                                    wire:click="confirmReview(true)"
                                                >
                                                    {{ __('Review and approve languages') }}
                                                </flux:button>
                                            </flux:button.group>
                                        </div>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('Review and approve languages')"
                                            :text="__(
                                                'Choose saved language revisions to review and approve. Approval is required before publication; it does not write language files.',
                                            )"
                                        />
                                    </flux:tooltip>
                                    @if (($context['review']['latest']['decision'] ?? null) === 'approved')
                                        <flux:tooltip class="w-full">
                                            <div class="flex w-full">
                                                <flux:button.group class="w-full">
                                                    <flux:button
                                                        size="sm"
                                                        variant="filled"
                                                        color="red"
                                                        wire:click="confirmReview(false)"
                                                    >
                                                        <flux:icon.x-mark size="5" />
                                                    </flux:button>
                                                    <flux:button
                                                        size="sm"
                                                        variant="filled"
                                                        color="red"
                                                        wire:click="confirmReview(false)"
                                                    >
                                                        {{ __('Revoke approval') }}
                                                    </flux:button>
                                                </flux:button.group>
                                            </div>
                                            <x-translation-workbench::ui.common.tooltip-content
                                                :heading="__('Revoke approval')"
                                                :text="__(
                                                    'Review the current approval and confirm its revocation with a reason. This does not remove translations already written to language files.',
                                                )"
                                            />
                                        </flux:tooltip>
                                    @endif
                                @endif
                                @if ($finding['status'] === 'active')
                                    <div>
                                        <flux:tooltip class="w-full">
                                            <div class="flex w-full">
                                                <flux:button.group class="w-full">
                                                    <flux:button
                                                        size="sm"
                                                        variant="filled"
                                                        color="fuchsia"
                                                        wire:click="openPublicationSelection"
                                                    >
                                                        <flux:icon.language size="5" />
                                                    </flux:button>
                                                    <flux:button
                                                        class="flex-1"
                                                        size="sm"
                                                        variant="filled"
                                                        color="fuchsia"
                                                        wire:click="openPublicationSelection"
                                                    >
                                                        {{ __('Select languages for publication') }}
                                                    </flux:button>
                                                </flux:button.group>
                                            </div>
                                            <x-translation-workbench::ui.common.tooltip-content
                                                :heading="__('Select languages for publication')"
                                                :text="__(
                                                    'Choose approved language revisions and prepare a publication preview. Language files are written only after confirmation.',
                                                )"
                                            />
                                        </flux:tooltip>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </flux:callout>
                @endif
            @endif

            <flux:callout
                icon="timeline"
                heading="{{ __('Timeline') }}"
                :text="__('A chronological view of the translation key\'s history and related activities.')"
            >
                <flux:callout
                    class="mb-3"
                    color="green"
                    icon="link"
                >
                    <flux:accordion>
                        {{-- Linked Findings --}}
                        <flux:accordion.item transition>
                            <flux:accordion.heading>
                                <flux:tooltip>
                                    <flux:callout.heading>
                                        {{ __('Linked findings') }} ({{ $context['usageCount'] }})
                                    </flux:callout.heading>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Linked findings')"
                                        :text="__(
                                            'Findings linked to the same translation key. This shows shared usage, not a chronological history.',
                                        )"
                                    />
                                </flux:tooltip>
                            </flux:accordion.heading>

                            <flux:accordion.content>
                                {{-- Table --}}
                                <flux:table container:class="max-h-80 overflow-x-auto rounded-t-lg mt-2">
                                    {{-- Table Header --}}
                                    <flux:table.columns
                                        class="bg-white dark:bg-zinc-800"
                                        sticky
                                    >
                                        {{-- Finding ID --}}
                                        <flux:table.column class="w-32">
                                            <flux:tooltip>
                                                <flux:callout.heading class="ml-2">
                                                    {{ __('Finding ID') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Finding ID')"
                                                    :text="__('The unique identifier of the finding.')"
                                                />
                                            </flux:tooltip>
                                        </flux:table.column>
                                        {{-- Source --}}
                                        <flux:table.column>
                                            <flux:tooltip>
                                                <flux:callout.heading>
                                                    {{ __('Source') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Source')"
                                                    :text="__('The source file and line number of the finding.')"
                                                />
                                            </flux:tooltip>
                                        </flux:table.column>
                                        {{-- Actions --}}
                                        <flux:table.column class="w-28">
                                            <flux:tooltip>
                                                <flux:callout.heading>
                                                    {{ __('Actions') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Actions')"
                                                    :text="__('Available actions for the finding.')"
                                                />
                                            </flux:tooltip>
                                        </flux:table.column>
                                    </flux:table.columns>
                                    {{-- Table Rows --}}
                                    <flux:table.rows>
                                        @foreach ($context['usages'] as $usage)
                                            <flux:table.row :key="'usage-'.$usage['id']">
                                                {{-- Finding ID --}}
                                                <flux:table.cell>
                                                    <flux:callout.text class="ml-2">
                                                        #{{ $usage['id'] }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Source --}}
                                                <flux:table.cell class="whitespace-normal break-all">
                                                    <flux:callout.text>
                                                        {{ $usage['path'] }}:{{ $usage['source_line'] }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Actions --}}
                                                <flux:table.cell>
                                                    <flux:button.group>
                                                        <flux:button
                                                            size="sm"
                                                            variant="outline"
                                                            icon="folder-open"
                                                            wire:click="open({{ $usage['id'] }})"
                                                        />
                                                        <flux:button
                                                            size="sm"
                                                            variant="outline"
                                                            wire:click="open({{ $usage['id'] }})"
                                                        >
                                                            {{ __('Open') }}
                                                        </flux:button>
                                                    </flux:button.group>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                                @if ($context['usageCount'] > 100)
                                    <flux:callout.text>
                                        {{ __('Showing the first 100 linked findings.') }}
                                    </flux:callout.text>
                                @endif
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:callout>

                {{-- Recent Working History --}}
                <flux:callout
                    color="green"
                    icon="timeline"
                >
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>
                                <flux:tooltip>
                                    <flux:callout.heading>
                                        {{ __('Recent working history') }}
                                        ({{ __('Latest 50 events for this finding and its current key') }})
                                    </flux:callout.heading>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Recent working history')"
                                        :text="__(
                                            'The latest recorded work events for this finding and its current key: what changed, when, by whom and why, where recorded. This is a recent activity list, not the complete history.',
                                        )"
                                    />
                                </flux:tooltip>
                            </flux:accordion.heading>

                            <flux:accordion.content>
                                <flux:table container:class="mt-2 max-h-80 overflow-x-auto rounded-t-lg">
                                    <flux:table.columns
                                        class="bg-white dark:bg-zinc-800"
                                        sticky
                                    >
                                        {{-- Table Header Column Predecessor --}}
                                        <flux:table.column>
                                            <flux:tooltip>
                                                <flux:callout.heading class="ml-2">
                                                    {{ __('Predecessor') }}
                                                </flux:callout.heading>
                                            </flux:tooltip>
                                            <x-translation-workbench::ui.common.tooltip-content
                                                :heading="__('Predecessor')"
                                                :text="__('The predecessor of this finding.')"
                                            />
                                        </flux:table.column>
                                        {{-- Table Header Column Event --}}
                                        <flux:table.column>
                                            <flux:tooltip>
                                                <flux:callout.heading class="ml-2">
                                                    {{ __('Event') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Event')"
                                                    :text="__(
                                                        'The type of event that occurred for this finding and its current key.',
                                                    )"
                                                />
                                            </flux:tooltip>
                                        </flux:table.column>
                                        {{-- Table Header Column Successor --}}
                                        <flux:table.column>
                                            <flux:tooltip>
                                                <flux:callout.heading>
                                                    {{ __('Successor') }}
                                                </flux:callout.heading>
                                            </flux:tooltip>
                                            <x-translation-workbench::ui.common.tooltip-content
                                                :heading="__('Successor')"
                                                :text="__('The Successor of this finding.')"
                                            />
                                        </flux:table.column>
                                        {{-- Table Header Column When / who --}}
                                        <flux:table.column>
                                            <flux:tooltip>
                                                <flux:callout.heading>
                                                    {{ __('When / who') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('When / who')"
                                                    :text="__(
                                                        'The timestamp and user responsible for this finding event.',
                                                    )"
                                                />
                                            </flux:tooltip>
                                        </flux:table.column>
                                        {{-- Table Header Column Reason --}}
                                        <flux:table.column>
                                            <flux:tooltip>
                                                <flux:callout.heading>
                                                    {{ __('Reason') }}
                                                </flux:callout.heading>
                                                <x-translation-workbench::ui.common.tooltip-content
                                                    :heading="__('Reason')"
                                                    :text="__('The reason for this finding event.')"
                                                />
                                            </flux:tooltip>
                                        </flux:table.column>
                                    </flux:table.columns>
                                    {{-- Table Rows --}}
                                    <flux:table.rows>
                                        @forelse ($context['history'] as $event)
                                            <flux:table.row :key="'event-'.$event['id']">
                                                {{-- Table Body Column Predecessor --}}
                                                <flux:table.cell>
                                                    @foreach ($event['successors'] ?? [] as $pair)
                                                        <flux:button.group class="ml-2">
                                                            <flux:button
                                                                size="xs"
                                                                variant="filled"
                                                                color="fuchsia"
                                                                wire:click="open({{ (int) $pair['predecessor'] }})"
                                                                :tooltip="__('Open predecessor finding')"
                                                            >
                                                                #{{ $pair['predecessor'] }}
                                                            </flux:button>
                                                            <flux:button
                                                                size="xs"
                                                                variant="filled"
                                                                color="fuchsia"
                                                                wire:click="open({{ (int) $pair['predecessor'] }})"
                                                                :tooltip="__('Open predecessor finding')"
                                                            >
                                                                <flux:icon.corner-down-left />
                                                            </flux:button>
                                                        </flux:button.group>
                                                    @endforeach
                                                </flux:table.cell>
                                                {{-- Table Body Column Event --}}
                                                <flux:table.cell>
                                                    <x-translation-workbench::ui.common.details
                                                        class="gap-y-1 whitespace-normal"
                                                        label-width="7rem"
                                                    >
                                                        <x-translation-workbench::ui.common.details.item
                                                            :label="__('Event')"
                                                            :item="__(
                                                                [
                                                                    'source_rewrite_reconciled' =>
                                                                        'Source replacement recorded',
                                                                    'source_movement_reconciled' =>
                                                                        'Source movement recorded',
                                                                    'scan_finding_moved' => 'Scanner: finding moved',
                                                                    'scan_finding_created' =>
                                                                        'Scanner: finding captured',
                                                                    'scan_finding_removed' =>
                                                                        'Scanner: finding archived',
                                                                ][$event['event_type']] ?? $event['event_type'],
                                                            )"
                                                        />
                                                        <x-translation-workbench::ui.common.details.item
                                                            class="break-all tabular-nums"
                                                            :label="str_starts_with(
                                                                $event['event_type'],
                                                                'scan_finding_',
                                                            )
                                                                ? __('Scan run ID')
                                                                : __('Event ID')"
                                                            :item="$event['id']"
                                                        />
                                                    </x-translation-workbench::ui.common.details>
                                                </flux:table.cell>
                                                {{-- Table Body Column Successor --}}
                                                <flux:table.cell>
                                                    @foreach ($event['successors'] ?? [] as $pair)
                                                        <flux:button.group>
                                                            <flux:button
                                                                size="xs"
                                                                variant="filled"
                                                                color="sky"
                                                                wire:click="open({{ (int) $pair['successor'] }})"
                                                                :tooltip="__('Open successor finding')"
                                                            >
                                                                <flux:icon.corner-up-right />
                                                            </flux:button>
                                                            <flux:button
                                                                size="xs"
                                                                variant="filled"
                                                                color="sky"
                                                                wire:click="open({{ (int) $pair['successor'] }})"
                                                                :tooltip="__('Open successor finding')"
                                                            >
                                                                #{{ $pair['successor'] }}
                                                            </flux:button>
                                                        </flux:button.group>
                                                    @endforeach
                                                </flux:table.cell>
                                                {{-- Table Body Column When / Who --}}
                                                <flux:table.cell>
                                                    <flux:callout.text>
                                                        <x-ui.date-time.date-time :value="$event['occurred_at']" /> ·
                                                        {{ $event['actor_id'] ? '#' . $event['actor_id'] : __('Scanner') }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                                {{-- Reason --}}
                                                <flux:table.cell class="whitespace-normal">
                                                    <flux:callout.text>
                                                        {{ $event['actor_id'] === null ? __($event['reason']) : $event['reason'] }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @empty
                                            <flux:table.row>
                                                <flux:table.cell colspan="3">
                                                    <flux:callout.text>
                                                        {{ __('No working events yet.') }}
                                                    </flux:callout.text>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforelse
                                    </flux:table.rows>
                                </flux:table>
                                {{-- </div> --}}
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:callout>
            </flux:callout>
        </div>
    @endif
</flux:modal>
