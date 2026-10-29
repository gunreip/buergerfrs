<flux:modal
    class="w-full max-w-7xl"
    name="generation-publication-confirmation"
    wire:model="publicationOpen"
>
    @if ($publication)
        @php
            [$publicationStatusLabel, $publicationStatusColor] = match ($publication['state']) {
                'prepared' => [__('Prepared'), 'sky'],
                'writing' => [__('Publishing'), 'blue'],
                'interrupted' => [__('Interrupted'), 'red'],
                'complete' => [__('Complete'), 'green'],
                'closed' => [__('Closed'), 'amber'],
                default => [__('Unknown status'), 'zinc'],
            };
            $publicationStatusDetails = [
                ['label' => __('Status'), 'value' => $publicationStatusLabel],
                ['label' => __('Publication run ID'), 'value' => $publication['run']],
            ];
            if ($publication['error']) {
                $publicationStatusDetails[] = ['label' => __('Error'), 'value' => $publication['error']];
            }
        @endphp
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-3 pe-6">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.workflow
                        class="shrink-0"
                        size="sm"
                    />
                    {{ __('Publication preview and result') }}
                </flux:heading>
                <flux:tooltip toggleable>
                    <flux:badge
                        class="shrink-0"
                        as="button"
                        :color="$publicationStatusColor"
                        :aria-label="__('Show publication status details')"
                    >
                        {{ $publicationStatusLabel }}
                    </flux:badge>
                    <x-translation-workbench::ui.common.tooltip-content-tabular
                        :heading="__('Publication status')"
                        :rows="$publicationStatusDetails"
                    />
                </flux:tooltip>
            </div>
            <flux:text>
                {{ $publication['reason'] }}
            </flux:text>
            <flux:callout
                color="sky"
                icon="user"
            >
                <flux:callout.heading>{{ __('Prepared by user') }}</flux:callout.heading>
                <flux:tooltip toggleable>
                    <flux:button
                        variant="ghost"
                        size="sm"
                        :aria-label="__('Show preparing user details')"
                    >
                        <flux:callout.text>
                            {{-- {{ __('User ID: #:id', ['id' => $publication['actor']]) }} --}}
                            @if ($publicationActor)
                                {{ $publicationActor['name'] ?: __('User ID #:id', ['id' => $publication['actor']]) }}
                                @if ($publicationActor['email'] !== '')
                                    · {{ $publicationActor['email'] }}
                                @endif
                            @else
                                {{ __('User #:id · Account no longer available', ['id' => $publication['actor']]) }}
                            @endif
                        </flux:callout.text>
                    </flux:button>
                    <x-translation-workbench::ui.common.tooltip-content-tabular
                        :heading="__('Current user information')"
                        :rows="[
                            ['label' => __('User ID'), 'value' => '#' . $publication['actor']],
                            ['label' => __('Name'), 'value' => $publicationActor['name'] ?? __('Unavailable')],
                            ['label' => __('Email'), 'value' => $publicationActor['email'] ?? __('Unavailable')],
                            [
                                'label' => __('Current roles'),
                                'value' => $publicationActor
                                    ? (implode(', ', $publicationActor['roles']) ?:
                                    __('No roles assigned'))
                                    : __('Unavailable'),
                            ],
                        ]"
                    />
                </flux:tooltip>
            </flux:callout>

            <flux:callout
                class="mt-4"
                color="sky"
                icon="eye"
                heading="{{ __('Publication review and results') }}"
            >
                {{-- Table --}}
                <flux:table container:class="max-h-[96vh] overflow-x-auto rounded-t-lg">
                    {{-- Table Header Columns --}}
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-800"
                        sticky
                    >
                        {{-- Column Key --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading class="mx-2">
                                    {{ __('Key') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Translation key ID')"
                                    :text="__(
                                        'Database ID of the translation key record whose language revision is being published. Multiple findings and languages can use the same key record. This is not a finding ID or a revision ID.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Language --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading class="mx-2">
                                    {{ __('Language') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Language')"
                                    :text="__(
                                        'Display of the language code for the language and/or language variants translation revision being published. This is the locale code of the language file being written.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Open in VSC --}}
                        <flux:table.column align="center">
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('VSC') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Open language file in VS Code')"
                                    :text="__(
                                        'This link will open the corresponding language/language variant file. If there are currently no entries in the lines:column section, the language file will open at line 1, column 1—that is, at the beginning of the file.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column File / Translation key --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('File / Translation key') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('File / Translation key')"
                                    :text="__(
                                        'Destination language file and the translation key written to it. Select the shortened path or key to view it in full.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Before preparation --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Translation before') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Translation before')"
                                    :text="__(
                                        'Value in the language file when this publication was prepared. This snapshot remains unchanged after publication. An absent entry and an empty string are shown separately.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Planned value --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Translation') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Translation')"
                                    :text="__(
                                        'Approved translation revision selected for this publication. This is the planned value; the column alone does not confirm that it has already been written.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Column Status --}}
                        <flux:table.column align="center">
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Status') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Status')"
                                    :text="__(
                                        'Compares the entire current language file with this publication plan. Green: matches the planned result. Sky: still matches the initial version. Red: differs from both versions. Select the status icon for details.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                    </flux:table.columns>
                    {{-- Table Rows --}}
                    <flux:table.rows>
                        @foreach ($publication['changes'] as $change)
                            @php
                                $file = $publication['files'][$change['path']];
                                $matchesResult = $file['current'] === $file['after'];
                                $matchesInitial = $file['current'] === $file['before'];
                                $statusText = $matchesResult
                                    ? __('File matches the publication result.')
                                    : ($matchesInitial
                                        ? __('File still matches the initial version.')
                                        : __('File changed outside this publication. Recheck before continuing.'));
                                $statusIcon = $matchesResult
                                    ? 'check-circle'
                                    : ($matchesInitial
                                        ? 'clock'
                                        : 'exclamation-triangle');
                                $statusColor = $matchesResult ? 'green' : ($matchesInitial ? 'sky' : 'red');
                            @endphp
                            {{-- Table Row --}}
                            <flux:table.row
                                wire:key="publication-change-{{ $publication['run'] }}-{{ $change['revision'] }}"
                            >
                                {{-- Column Key --}}
                                <flux:table.cell align="end">
                                    <flux:callout.text class="ml-2 tabular-nums">
                                        #{{ $change['key_id'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                {{-- Column Locale --}}
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:flag
                                            circle
                                            size="xs"
                                            :country="$translationFlagCountries[$change['locale']] ?? null"
                                        />
                                        <flux:callout.text>
                                            {{ $change['locale'] }}
                                        </flux:callout.text>
                                    </div>
                                </flux:table.cell>
                                {{-- Column Open in VSC --}}
                                <flux:table.cell align="center">
                                    @php
                                        $languageRoot =
                                            $testToken !== ''
                                                ? app(
                                                        \Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment::class,
                                                    )->root() . '/lang'
                                                : lang_path();
                                        $languageFile = $languageRoot . '/' . $change['path'];
                                        $editorPath = str_replace(
                                            '%2F',
                                            '/',
                                            rawurlencode(str_replace('\\', '/', $languageFile)),
                                        );
                                        $editorUrl =
                                            'vscode://vscode-remote/wsl+' .
                                            rawurlencode(
                                                (string) config('translation-workbench.editor.vscode_wsl_distro'),
                                            ) .
                                            $editorPath .
                                            ':1:1';
                                    @endphp
                                    <flux:tooltip
                                        wire:key="publication-vsc-{{ $publication['run'] }}-{{ $change['revision'] }}"
                                    >
                                        <flux:button
                                            type="button"
                                            size="xs"
                                            color="sky"
                                            variant="ghost"
                                            icon="external-link"
                                            :href="$editorUrl"
                                            :aria-label="__('Open language file in VS Code')"
                                        />
                                        <x-translation-workbench::ui.common.tooltip-content :heading="__('Open language file in VS Code')">
                                            <x-translation-workbench::ui.common.path-preview
                                                :path="$languageFile"
                                                :tooltip="false"
                                                :length="60"
                                                side="center"
                                            />
                                        </x-translation-workbench::ui.common.tooltip-content>
                                    </flux:tooltip>
                                </flux:table.cell>
                                {{-- Column File / Translation Key --}}
                                <flux:table.cell>
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1">
                                            <flux:icon.document-text
                                                class="size-4 shrink-0"
                                                aria-hidden="true"
                                            />
                                            <x-translation-workbench::ui.common.path-preview
                                                :path="'lang/' . $change['path']"
                                                :length="36"
                                                side="center"
                                            />
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <flux:icon.key
                                                class="size-4 shrink-0"
                                                aria-hidden="true"
                                            />
                                            <x-translation-workbench::ui.common.text-preview
                                                :text="$change['key']"
                                                :length="36"
                                                :heading="__('Translation key')"
                                            />
                                        </div>
                                    </div>
                                </flux:table.cell>
                                {{-- Column Before Preperation --}}
                                <flux:table.cell>
                                    @if ($change['before'] === null)
                                        <flux:badge
                                            color="orange"
                                            icon="message-circle-warning"
                                            label="{{ __('Entry does not exist') }}"
                                        />
                                    @elseif ($change['before'] === '')
                                        <span class="text-fuchsia-500">
                                            {{ __('Empty string') }}
                                        </span>
                                    @else
                                        <x-translation-workbench::ui.common.text-preview
                                            :text="$change['before']"
                                            :length="45"
                                            :heading="__('Translation before')"
                                        />
                                    @endif
                                </flux:table.cell>
                                {{-- Column Planned Value --}}
                                <flux:table.cell>
                                    @if ($change['after'] === '')
                                        {{ __('Empty string') }}
                                    @else
                                        <x-translation-workbench::ui.common.text-preview
                                            :text="$change['after']"
                                            :length="45"
                                            :heading="__('Translation')"
                                        />
                                    @endif
                                </flux:table.cell>
                                {{-- Column Status --}}
                                <flux:table.cell align="center">
                                    <flux:tooltip toggleable>
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            :icon="$statusIcon"
                                            :color="$statusColor"
                                            :aria-label="$statusText"
                                        />
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('File comparison')"
                                            :text="[
                                                $statusText,
                                                __('Revision #:revision · :locale', [
                                                    'revision' => $change['revision'],
                                                    'locale' => $change['locale'],
                                                ]),
                                            ]"
                                        />
                                    </flux:tooltip>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:callout>

            @if (in_array($publication['state'], ['prepared', 'writing', 'interrupted'], true))
                <flux:text>
                    {{ __('Confirmation writes the planned language files. Current approval and file hashes are checked again. Source code is not rewritten.') }}
                </flux:text>
                <div class="flex flex-wrap gap-3">
                    @if ((int) $publication['actor'] === (int) auth()->id())
                        <flux:button
                            variant="primary"
                            wire:click="publish"
                        >
                            {{ __('Confirm / resume publication') }}
                        </flux:button>
                    @else
                        <flux:text>
                            {{ __('The preparing user must confirm or resume this run.') }}
                        </flux:text>
                    @endif
                    <flux:button
                        variant="outline"
                        wire:click="previewClosure"
                    >
                        {{ __('Review closure without further writes') }}
                    </flux:button>
                </div>
                @if ($closure)
                    <flux:callout color="amber">
                        <flux:callout.heading>
                            {{ __('Close this run without further writes?') }}
                        </flux:callout.heading>
                        <flux:callout.text>
                            {{ __('Already written files remain as they are. Closing does not roll them back. The current file state is recorded and reservations are released.') }}
                        </flux:callout.text>
                        @include(
                            'translation-workbench::livewire.translation-workbench.shared.reason-select',
                            ['reasonField' => 'closureReason', 'reasonLabel' => __('Reason for closure')]
                        )
                        <flux:button
                            variant="outline"
                            wire:click="closePublication"
                        >
                            {{ __('Confirm closure') }}
                        </flux:button>
                    </flux:callout>
                @endif
            @elseif ($publication['state'] === 'complete')
                <flux:callout
                    color="green"
                    icon="check"
                    heading="{{ __('Publication Complete') }}"
                    text="{{ __('Publication was completed. The file comparison above shows whether that result still matches the files now.') }}"
                />
            @else
                <flux:callout
                    color="amber"
                    icon="x-mark"
                    heading="{{ __('Run Closed') }}"
                    text="{{ __('This run is closed. It cannot write files again.') }}"
                />
            @endif
        </div>
    @endif
</flux:modal>
