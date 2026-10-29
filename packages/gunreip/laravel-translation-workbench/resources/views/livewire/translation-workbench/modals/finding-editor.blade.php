<flux:modal
    class="max-w-9/10 w-full"
    name="generation-finding-editor"
    wire:model="editorOpen"
>
    @if ($editorOpen && $context)
        @php($finding = $context['finding'])
        <div class="space-y-4">
            <flux:heading size="xl">
                {{ __('Edit finding') }} #{{ $selected }}
            </flux:heading>
            <flux:button size="sm" variant="outline" icon="queue-list" wire:click="openWorkflowStatus({{ $selected }})">{{ __('Workflow status') }}</flux:button>
            @if ($notice)
                <flux:callout color="green">
                    {{ $notice }}
                </flux:callout>
            @endif
            @if ($finding['status'] !== 'active')
                <flux:callout
                    class="flex-1"
                    color="amber"
                >
                    {{ __('Archived source occurrence. It cannot be edited or reactivated.') }}
                    {{ $finding['retired_reason'] }}
                </flux:callout>
            @endif
            <div class="grid items-stretch gap-4 lg:grid-cols-2">

                <div class="flex flex-col gap-4">
                    <flux:callout
                        color="sky"
                        icon="code"
                        :heading="__('Finding in source')"
                    >
                        <dl
                            class="grid grid-cols-[minmax(0,12rem)_minmax(0,1fr)] items-baseline gap-x-3 gap-y-2 text-sm">
                            <dt class="font-medium">
                                <flux:callout.heading>
                                    {{ __('Source file') }}:
                                </flux:callout.heading>
                            </dt>
                            <dd class="min-w-0 break-all">
                                <x-translation-workbench::ui.common.path-preview
                                    :path="$finding['path']"
                                    :length="60"
                                    side="center"
                                    :line="$finding['source_line']"
                                    :column="$finding['source_column']"
                                    :heading="__('Source file')"
                                />
                            </dd>
                            <dt class="font-medium">
                                <flux:callout.heading>
                                    {{ __('Captured source expression') }}:
                                </flux:callout.heading>
                            </dt>
                            <dd class="wrap-break-word min-w-0">
                                <x-translation-workbench::ui.common.text-preview
                                    :text="$finding['raw_expression']"
                                    side="center"
                                    :heading="__('Captured source expression')"
                                />
                            </dd>
                        </dl>

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
                                        size="sm"
                                        variant="filled"
                                        color="orange"
                                        wire:click="previewSource"
                                    >
                                        <flux:icon.code-xml />
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="filled"
                                        color="orange"
                                        wire:click="previewSource"
                                    >
                                        {{ __('Inspect source replacement') }}
                                    </flux:button>
                                </flux:button.group>
                            @endif
                        </div>

                        <flux:error name="sourcePreview" />

                    </flux:callout>

                    <flux:callout
                        color="sky"
                        icon="germ-off"
                        :heading="__('Immutable capture')"
                    >
                        <dl
                            class="grid grid-cols-[minmax(0,12rem)_minmax(0,1fr)] items-baseline gap-x-3 gap-y-2 text-sm">
                            @foreach (['raw_source_code_literal' => 'Raw source literal / key', 'raw_literal' => 'Raw literal', 'suggested_literal' => 'Suggested literal', 'suggested_key' => 'Suggested key'] as $field => $label)
                                <dt class="font-medium">
                                    <flux:callout.heading>
                                        {{ __($label) }}:
                                    </flux:callout.heading>
                                </dt>
                                <dd class="wrap-break-word min-w-0">
                                    <x-translation-workbench::ui.common.text-preview
                                        :text="$finding[$field] ?? 'NULL'"
                                        side="center"
                                        :heading="__($label)"
                                    />
                                </dd>
                            @endforeach
                        </dl>
                    </flux:callout>
                </div>

                <flux:callout
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
                    <flux:callout.heading class="break-all">
                        {{ __('Current key') }}:
                        <flux:callout.text>
                            {{ $finding['key_id'] ? '#' . $finding['key_id'] . ' · ' . $finding['full_name'] : __('Not assigned') }}
                        </flux:callout.text>
                    </flux:callout.heading>
                    {{-- Assignment action --}}
                    @if ($finding['status'] === 'active')
                        <div class="space-y-3">
                            <flux:field class="mt-4">
                                <div class="flex items-start justify-between gap-3">
                                    <flux:label>
                                        <flux:callout.heading>
                                            {{ __('Translation key') }}
                                        </flux:callout.heading>
                                    </flux:label>
                                    @if (trim($assignmentKey) !== '' &&
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

                                <flux:error name="assignmentKey" />

                                @if (empty($finding['suggested_key']))
                                    <flux:error>
                                        {{ __('Capture inconsistency: this finding has no suggested key.') }}
                                    </flux:error>
                                @endif
                            </flux:field>

                            {{-- Reason for assignment --}}
                            <flux:field class="mb-6">
                                <flux:label>
                                    <flux:callout.heading>
                                        {{ __('Reason for translation key assignment') }}
                                    </flux:callout.heading>
                                </flux:label>
                                <flux:input.group>
                                    <flux:input.group.prefix>
                                        <flux:icon.tube-lotion />
                                    </flux:input.group.prefix>
                                    <flux:select
                                        wire:model="assignmentReason"
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
                                            x-on:click="$wire.openAssignmentReason(reasonSearch)">
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

                            <flux:error name="assignment" />

                            <div class="flex justify-end gap-3">
                                <flux:button.group>
                                    <flux:button
                                        size="sm"
                                        variant="filled"
                                        color="emerald"
                                        icon="check"
                                        wire:click="confirmAssignment"
                                    />
                                    <flux:button
                                        size="sm"
                                        variant="filled"
                                        color="emerald"
                                        wire:click="confirmAssignment"
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
            @if ($finding['key_id'])
                <flux:callout color="zinc" id="workbench-translation-values">
                    <div class="flex items-center justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('Translation values') }} · {{ __('Key') }} #{{ $finding['key_id'] }}
                        </flux:callout.heading>
                        <x-translation-workbench::ui.common.spellcheck-info />
                    </div>
                    <flux:callout.text>
                        {{ __('An empty string or 0 can be an intentional translation. Saving creates a draft revision; it does not publish or rewrite source code.') }}
                    </flux:callout.text>
                    @if ($context['publication'] ?? null)
                        <flux:button
                            class="mt-3"
                            variant="outline"
                            size="sm"
                            wire:click="openPublication('{{ $context['publication']['id'] }}')"
                        >
                            {{ __('Latest publication for this key and locale') }} ·
                            {{ __($context['publication']['state']) }}
                        </flux:button>
                    @endif
                    <div class="my-4 grid items-stretch gap-4 lg:grid-cols-2">
                        <div class="*:data-flux-callout:grow flex min-w-0 flex-col gap-3">
                            <flux:callout color="green">
                                <flux:field>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <flux:label>
                                            <span class="inline-flex items-center gap-2">
                                                <flux:flag
                                                    :country="$translationFlagCountries['en']"
                                                    size="xs"
                                                    circle
                                                />
                                                {{ __('Source language') }} · en
                                            </span>
                                        </flux:label>
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
                                                    >{{ __('Captured text') }}</flux:badge>
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
                                        lang="en"
                                        spellcheck="true"
                                        autocorrect="on"
                                        rows="3"
                                        wire:model="sourceLanguageValue"
                                        :placeholder="__('No English source text captured.')"
                                    />
                                    <flux:error name="sourceLanguageValue" />
                                </flux:field>

                                <flux:field>
                                    <div class="flex items-center justify-between gap-3">
                                        <flux:tooltip
                                            :content="__('Translation targets follow the active language in Settings and its enabled sublanguages.')"
                                        >
                                            <flux:label>
                                                <span class="inline-flex items-center gap-2">
                                                    <flux:flag
                                                        :country="$translationFlagCountries[$locale] ?? null"
                                                        size="xs"
                                                        circle
                                                    />
                                                    {{ __('Active target language') }} · {{ $locale }}
                                                </span>
                                            </flux:label>
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
                                                    :content="__('This translation field is empty. An empty translation can also be intentional.')"
                                                >
                                                    <flux:badge
                                                        tabindex="0"
                                                        color="red"
                                                        size="sm"
                                                    >
                                                        {{ __('Still empty') }}
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
                                                :tooltip="__('Copy the current main-language text into all selected language variants, replacing their contents. This does not save changes.')"
                                                :aria-label="__('Copy main-language text to all selected language variants')"
                                            />
                                        </div>
                                    </div>
                                    <flux:textarea
                                        class="field-sizing-content max-h-80 min-h-12 overflow-x-auto"
                                        wire:model="value"
                                        x-on:workbench-select-translation-draft.window="$nextTick(() => { $el.focus(); $el.select(); })"
                                        :lang="$locale"
                                        spellcheck="true"
                                        rows="3"
                                        :disabled="$finding['status'] !== 'active'"
                                    />
                                    <flux:error name="value" />
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
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="copyPublished"
                                    >{{ __('Copy published value into draft') }}</flux:button>
                                @endif
                            </flux:callout>
                        </div>

                        <div class="min-w-0">
                            <flux:callout
                                class="h-full"
                                color="indigo"
                            >
                                <flux:field class="mb-3">
                                    <flux:label>
                                        <flux:callout.heading>
                                            {{ __('Language variants') }}
                                        </flux:callout.heading>
                                    </flux:label>
                                    <div class="grid grid-cols-[minmax(0,18rem)_minmax(0,1fr)] items-center gap-3">
                                        <flux:input.group class="min-w-0">
                                            <flux:input.group.prefix>
                                                <flux:icon.language />
                                            </flux:input.group.prefix>
                                            <flux:select
                                                class="min-w-0"
                                                variant="listbox"
                                                multiple
                                                clearable
                                                searchable
                                                :placeholder="__('Select language variants…')"
                                                :disabled="$finding['status'] !== 'active' || count($subLanguages) === 0"
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
                                            {{ __('No language variants are activated for the active main language.') }}
                                        </flux:description>
                                    @endif

                                    <flux:error name="selectedSubLanguages" />

                                    <flux:error name="selectedSubLanguages.*" />

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
                                                            :content="__('This translation field is empty. An empty translation can also be intentional.')"
                                                        >
                                                            <flux:badge
                                                                tabindex="0"
                                                                color="red"
                                                                size="sm"
                                                            >
                                                                {{ __('Still empty') }}
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
                                                wire:model="subLanguageValues.{{ $code }}"
                                                :lang="$code"
                                                spellcheck="true"
                                                copyable
                                                rows="3"
                                                :disabled="$finding['status'] !== 'active'"
                                            />
                                            <flux:error :name="'subLanguageValues.'.$code" />
                                        </flux:field>
                                    @endif
                                @endforeach

                                <flux:callout.text>
                                    {{ __('Revision') }}:
                                    {{ $context['revision']['id'] ?? __('No saved draft') }}
                                </flux:callout.text>
                                @if ($finding['status'] === 'active')
                                    <flux:field>
                                        <flux:label>
                                            <flux:callout.heading>
                                                {{ __('Reason for translation change') }}
                                            </flux:callout.heading>
                                        </flux:label>
                                        <flux:button.group>
                                            <flux:button>
                                                <flux:icon.circle-dot />
                                            </flux:button>
                                            <flux:select
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

                                        <flux:error name="reason" />

                                    </flux:field>
                                @endif
                            </flux:callout>
                        </div>

                        <div class="min-w-0 lg:col-start-2">
                            @if ($finding['status'] === 'active')
                                <flux:callout color="red">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <flux:button
                                            variant="outline"
                                            icon="paper-airplane"
                                            wire:click="submitForReview"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ __('Submit saved drafts for review') }}
                                        </flux:button>
                                        <flux:error name="submission" />
                                        <flux:button.group>
                                            <flux:button
                                                size="sm"
                                                wire:click="saveDraft"
                                                variant="primary"
                                                icon="save"
                                            />
                                            <flux:button
                                                size="sm"
                                                wire:click="saveDraft"
                                                variant="primary"
                                            >
                                                {{ __('Save draft') }}
                                            </flux:button>
                                        </flux:button.group>
                                    </div>
                                </flux:callout>
                            @endif
                        </div>
                    </div>
                </flux:callout>
                @if ($context['review'] ?? null)
                    <flux:callout :color="$context['review']['approved'] ? 'green' : 'amber'">
                        <flux:callout.heading>{{ __('Revision review') }} #{{ $context['revision']['id'] }} ·
                            {{ $locale }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ $context['review']['approved'] ? __('Approved for the current key and active source links.') : __('No current approval. Review is required before publication.') }}
                        </flux:callout.text>
                        <flux:callout.text>
                            {{ __('Approval applies to this saved revision and all active findings using its key. A changed value, key name or set of active links requires a new review. Approval alone does not publish.') }}
                        </flux:callout.text>
                        @if ($context['review']['latest'])
                            <flux:text class="my-3">{{ __('Last decision') }}:
                                {{ __($context['review']['latest']['decision']) }} ·
                                #{{ $context['review']['latest']['actor_id'] }} ·
                                <x-ui.date-time.date-time
                                    :value="$context['review']['latest']['occurred_at']" /><br>{{ $context['review']['latest']['reason'] }}
                            </flux:text>
                        @endif
                        <flux:error name="review" />
                        @if ($finding['status'] === 'active')
                            <div class="mt-3 flex flex-wrap gap-3">
                                <flux:button
                                    variant="outline"
                                    wire:click="confirmReview(true)"
                                >{{ __('Review and approve languages') }}</flux:button>
                                @if (($context['review']['latest']['decision'] ?? null) === 'approved')
                                    <flux:button
                                        variant="outline"
                                        wire:click="confirmReview(false)"
                                    >{{ __('Revoke approval') }}</flux:button>
                                @endif
                            </div>
                        @endif
                        @if ($finding['status'] === 'active')
                            <div class="mt-4 space-y-3">
                                <flux:button
                                    variant="outline"
                                    wire:click="openPublicationSelection"
                                >{{ __('Select languages for publication') }}</flux:button>
                                <flux:text>
                                    {{ __('Preparing reserves the affected language file. No file is written until confirmation. Close unused runs to release their reservations.') }}
                                </flux:text>
                                <flux:error name="publication" />
                            </div>
                        @endif
                    </flux:callout>
                @endif
            @endif

            <flux:callout
                color="green"
                {{-- icon="timeline" --}}
            >
                <flux:accordion>
                    {{-- Linked Findings --}}
                    <flux:accordion.item>
                        <flux:accordion.heading>
                            <flux:callout
                                class="py-0"
                                color="sky"
                                icon="link"
                            >
                                <flux:tooltip>
                                    <flux:callout.heading>
                                        {{ __('Linked findings') }} ({{ $context['usageCount'] }})
                                    </flux:callout.heading>
                                    <flux:tooltip.content class="max-w-[20rem] space-y-2 hyphens-auto text-justify">
                                        {{ __('Findings linked to the same translation key. This shows shared usage, not a chronological history.') }}
                                    </flux:tooltip.content>
                                </flux:tooltip>
                            </flux:callout>
                        </flux:accordion.heading>

                        <flux:accordion.content>
                            {{-- Table --}}
                            <flux:table container:class="max-h-[60vh] overflow-x-auto rounded-t-lg">
                                {{-- Table Header --}}
                                <flux:table.columns
                                    class="bg-white dark:bg-zinc-800"
                                    sticky
                                >
                                    {{-- Finding ID --}}
                                    <flux:table.column class="w-32">
                                        <flux:callout.heading class="ml-2">
                                            {{ __('Finding ID') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- Source --}}
                                    <flux:table.column>
                                        <flux:callout.heading>
                                            {{ __('Source') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- Actions --}}
                                    <flux:table.column class="w-28">
                                        <flux:callout.heading>
                                            {{ __('Actions') }}
                                        </flux:callout.heading>
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

                    {{-- Recent Working History --}}
                    <flux:accordion.item>
                        <flux:accordion.heading>
                            <flux:callout
                                class="py-0"
                                color="indigo"
                                icon="link"
                            >
                                <flux:tooltip>
                                    <flux:callout.heading>
                                        {{ __('Recent working history') }}
                                        ({{ __('Latest 50 events for this finding and its current key') }})
                                    </flux:callout.heading>
                                    <flux:tooltip.content class="max-w-[20rem] space-y-2 hyphens-auto text-justify">
                                        {{ __('The latest recorded work events for this finding and its current key: what changed, when, by whom and why, where recorded. This is a recent activity list, not the complete history.') }}
                                    </flux:tooltip.content>
                                </flux:tooltip>
                            </flux:callout>
                        </flux:accordion.heading>

                        <flux:accordion.content>
                            <flux:table container:class="max-h-[60vh] overflow-x-auto rounded-t-lg">
                                <flux:table.columns
                                    class="bg-white dark:bg-zinc-800"
                                    sticky
                                >
                                    {{-- Event --}}
                                    <flux:table.column>
                                        <flux:callout.heading class="ml-2">
                                            {{ __('Event') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- When / who --}}
                                    <flux:table.column>
                                        <flux:callout.heading>
                                            {{ __('When / who') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                    {{-- Reason --}}
                                    <flux:table.column>
                                        <flux:callout.heading>
                                            {{ __('Reason') }}
                                        </flux:callout.heading>
                                    </flux:table.column>
                                </flux:table.columns>
                                {{-- Table Rows --}}
                                <flux:table.rows>
                                    @forelse ($context['history'] as $event)
                                        <flux:table.row :key="'event-'.$event['id']">
                                            <flux:table.cell>
                                                <flux:callout.text>
                                                    {{ __(['source_rewrite_reconciled' => 'Source replacement recorded', 'source_movement_reconciled' => 'Source movement recorded', 'scan_finding_moved' => 'Scanner: finding moved', 'scan_finding_created' => 'Scanner: finding captured', 'scan_finding_removed' => 'Scanner: finding archived'][$event['event_type']] ?? $event['event_type']) }}
                                                    #{{ $event['id'] }}
                                                </flux:callout.text>
                                                @foreach ($event['successors'] ?? [] as $pair)
                                                    <div class="flex items-center gap-1">
                                                        <flux:button.group>
                                                            <flux:button
                                                                size="xs"
                                                                icon="skip-back"
                                                                variant="outline"
                                                                wire:click="open({{ (int) $pair['predecessor'] }})"
                                                                :tooltip="__('Open predecessor finding')"
                                                            />
                                                            <flux:button
                                                                size="xs"
                                                                variant="outline"
                                                                wire:click="open({{ (int) $pair['predecessor'] }})"
                                                                :tooltip="__('Open predecessor finding')"
                                                            >
                                                                #{{ $pair['predecessor'] }}
                                                            </flux:button>
                                                        </flux:button.group>
                                                        <span aria-hidden="true">
                                                            <flux:callout.text inline>
                                                                →
                                                            </flux:callout.text>
                                                        </span>
                                                        <flux:button.group>
                                                            <flux:button
                                                                size="xs"
                                                                variant="outline"
                                                                icon="skip-forward"
                                                                wire:click="open({{ (int) $pair['successor'] }})"
                                                                :tooltip="__('Open successor finding')"
                                                            />
                                                            <flux:button
                                                                size="xs"
                                                                variant="outline"
                                                                wire:click="open({{ (int) $pair['successor'] }})"
                                                                :tooltip="__('Open successor finding')"
                                                            >
                                                                #{{ $pair['successor'] }}
                                                            </flux:button>
                                                        </flux:button.group>
                                                    </div>
                                                @endforeach
                                            </flux:table.cell>
                                            {{-- When / Who --}}
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
        </div>
    @endif
</flux:modal>
