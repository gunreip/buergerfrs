<flux:modal
    class="w-full max-w-7xl"
    name="generation-source-preview"
    wire:model="sourcePreviewOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="openKeyWorkflowStatus, openWorkflowStatus, openSourceRun, prepareSource, openWorkflowReason"
    />
    @if ($sourcePreview)
        <div class="space-y-4">
            <div class="flex items-start justify-between gap-3">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.replace-all class="size-8 shrink-0" />
                    {{ __('Source replacement preview') }} ·
                    @if ($sourcePreview['batch'] ?? false)
                        {{ __('Key #:key · :count findings', ['key' => $sourcePreview['key_id'], 'count' => count($sourcePreview['items'])]) }}
                    @else
                        {{ __('Finding') }} #{{ $sourcePreview['finding'] }}
                    @endif
                </flux:heading>
                <flux:tooltip>
                    <flux:button.group class="mr-8 shrink-0">
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="{{ $sourcePreview['batch'] ?? false ? 'openKeyWorkflowStatus(' . $sourcePreview['key_id'] . ')' : 'openWorkflowStatus(' . $sourcePreview['finding'] . ')' }}"
                            wire:loading.attr="disabled"
                        >
                            <flux:icon.workflow class="size-5" />
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="{{ $sourcePreview['batch'] ?? false ? 'openKeyWorkflowStatus(' . $sourcePreview['key_id'] . ')' : 'openWorkflowStatus(' . $sourcePreview['finding'] . ')' }}"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Workflow status') }}
                        </flux:button>
                    </flux:button.group>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Workflow status')"
                        :text="__(
                            'View completed and outstanding steps for finding #:finding and its assigned translation key. Use Next step there to continue at the required workflow step.',
                            ['finding' => $sourcePreview['finding']],
                        )"
                    />
                </flux:tooltip>
            </div>

            @if ($sourcePreview['batch'] ?? false)
                @include('translation-workbench::livewire.translation-workbench.shared.source-batch-preview')
            @else
                <flux:callout
                    class="min-w-0 [&>div]:min-w-0"
                    color="sky"
                    icon="circle-dashed-check"
                    heading="{{ __('Source replacement preview') }}"
                    text="{{ __('This preview shows the exact source occurrence that will be replaced. The proposed replacement is based on the published, approved source-language value and the assigned translation key.') }}"
                >
                    <x-translation-workbench::ui.common.details>
                        <x-translation-workbench::ui.common.details.item :label="__('Source file')">
                            <x-translation-workbench::ui.common.path-preview
                                :path="$sourcePreview['path']"
                                :length="60"
                                side="center"
                                :line="$sourcePreview['line']"
                                :column="$sourcePreview['column']"
                                :heading="__('Source file')"
                            />
                        </x-translation-workbench::ui.common.details.item>

                        <x-translation-workbench::ui.common.details.item
                            class="tabular-nums"
                            :label="__('Assigned key ID')"
                            :item="'#' . $sourcePreview['key_id']"
                        />
                        <x-translation-workbench::ui.common.details.item :label="__('Assigned translation key')">
                            <x-translation-workbench::ui.common.text-preview
                                :mono="true"
                                :text="$sourcePreview['key']"
                                side="center"
                                :heading="__('Assigned translation key')"
                            />
                        </x-translation-workbench::ui.common.details.item>
                        <x-translation-workbench::ui.common.details.item :label="__('Current source call')">
                            <x-translation-workbench::ui.common.source-code :text="$sourcePreview['before']" />
                        </x-translation-workbench::ui.common.details.item>
                        <x-translation-workbench::ui.common.details.item :label="__('Proposed source call')">
                            <x-translation-workbench::ui.common.source-code :text="$sourcePreview['after']" />
                        </x-translation-workbench::ui.common.details.item>
                    </x-translation-workbench::ui.common.details>
                </flux:callout>

                @if (!empty($sourcePreview['scan_required']))
                    @include(
                        'translation-workbench::livewire.translation-workbench.shared.source-scan-required',
                        ['scanRequired' => $sourcePreview['scan_required']]
                    )
                @else
                    @include(
                        'translation-workbench::livewire.translation-workbench.shared.source-preview-status',
                        [
                            'previewStatus' => $sourcePreview,
                        ]
                    )
                @endif
            @endif
            <flux:callout
                color="sky"
                icon="languages"
                :heading="__('Translation files')"
            >
                <flux:callout.text>
                    {{ __('This source occurrence uses the assigned key for all these languages. Source replacement does not publish language files. Paths marked Expected are not confirmed readable files.') }}
                </flux:callout.text>

                {{-- Table Of Translation Files --}}
                <flux:table container:class="max-h-90 overflow-auto rounded-t-lg">
                    {{-- Table Header Columns --}}
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-800"
                        sticky
                    >
                        {{-- Table Header Column Language --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading class="ml-2">
                                    {{ __('Language') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Language')"
                                    :text="__(
                                        'English source language and the active target languages or variants with a saved translation, a publication record or a file entry for this key.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Table Header Column Translation File --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Translation file') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Translation file')"
                                    :text="__(
                                        'The language file containing this translation key. Expected marks a path that has not been confirmed as a readable file. Source replacement does not write language files.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Table Header Column Status --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Status') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Status')"
                                    :text="__(
                                        'Compares the current file entry with completed publication records and the saved revision. File presence alone does not mean Published. Hover over a status for its specific explanation.',
                                    )"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                    </flux:table.columns>
                    {{-- Table Rows --}}
                    <flux:table.rows>
                        @foreach ($sourcePreview['translation_files'] ?? [] as $file)
                            {{-- Table Row Columns --}}
                            <flux:table.row :key="'source-language-file-'.$file['locale']">
                                {{-- Table Row Column Language --}}
                                <flux:table.cell>
                                    <span class="inline-flex items-center gap-2">
                                        <flux:flag
                                            class="ml-2"
                                            circle
                                            :country="$translationFlagCountries[$file['locale']] ?? null"
                                        />
                                        <flux:callout.text>
                                            {{ $file['locale'] }}
                                        </flux:callout.text>
                                    </span>
                                </flux:table.cell>
                                {{-- Table Row Column Translation File --}}
                                <flux:table.cell>
                                    <x-translation-workbench::ui.common.path-preview
                                        :path="'lang/' . $file['path']"
                                        :length="60"
                                        side="center"
                                    />
                                    @if (!$file['actual'])
                                        <flux:badge
                                            size="sm"
                                            color="amber"
                                        >{{ __('Expected') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                                {{-- Table Row Column Status --}}
                                <flux:table.cell>
                                    <flux:tooltip>
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                        >
                                            <flux:badge
                                                size="sm"
                                                :color="match($file[
                                                    'state']) {
                                                    'published' => 'green', 'present' => 'sky', 'key_missing',
                                                    'unavailable', 'changed' => 'red',
                                                    default => 'amber'
                                                }"
                                            >
                                                {{ match ($file['state']) {
                                                    'published' => __('Published'),
                                                    'published_previous' => __('Earlier revision published'),
                                                    'present' => __('Present in file'),
                                                    'changed' => __('Differs from publication'),
                                                    'key_missing' => __('Key missing'),
                                                    default => __('Missing or unavailable'),
                                                } }}
                                            </flux:badge>
                                        </flux:button>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('Translation file status')"
                                            :text="__($file['detail'])"
                                        />
                                    </flux:tooltip>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:callout>

            <flux:callout
                color="amber"
                heading="{{ __('Exact source preview') }}"
                icon="circle-alert"
            >
                <flux:callout.text>
                    {{ $sourcePreview['batch'] ?? false ? __('All listed findings belong to this selection. Calls already using the assigned key need no replacement. Blocking entries prevent preparation of the entire group.') : __('Only this exact occurrence is inspected. Identical text elsewhere is not replaced. Arguments, placeholders and surrounding source code are preserved.') }}
                </flux:callout.text>
                <flux:callout.text>
                    {{ __('Preparation records a plan without writing the file. Confirmation replaces this call and creates a successor for the archived original finding. Moved findings are reconciled together and retain their own key assignments. Overlapping changed calls require separate review.') }}
                </flux:callout.text>
            </flux:callout>

            @if ($sourcePreview['ready'])
                @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                    'reasonField' => 'sourceReason',
                    'reasonLabel' => __('Reason for source replacement'),
                ])
            @endif

            <flux:error name="sourcePreview" />

            <div class="flex items-center justify-end gap-3">

                @if ($sourcePreview['ready'])
                    <flux:button.group>
                        <flux:tooltip>
                            <flux:button
                                size="sm"
                                color="green"
                                variant="filled"
                                wire:click="prepareSource"
                            >
                                <flux:icon.tool-case class="size-5" />
                            </flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Prepare source replacement')"
                                :text="__(
                                    'Prepare the listed source replacements. This records a plan without writing source files.',
                                    ['finding' => $sourcePreview['finding']],
                                )"
                            />
                        </flux:tooltip>
                        <flux:tooltip>
                            <flux:button
                                size="sm"
                                color="green"
                                variant="filled"
                                wire:click="prepareSource"
                            >
                                {{ __('Prepare source replacement') }}
                            </flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Prepare source replacement')"
                                :text="__(
                                    'Prepare the listed source replacements. This records a plan without writing source files.',
                                    ['finding' => $sourcePreview['finding']],
                                )"
                            />
                        </flux:tooltip>
                    </flux:button.group>
                @endif
            </div>
        </div>
    @endif
</flux:modal>
