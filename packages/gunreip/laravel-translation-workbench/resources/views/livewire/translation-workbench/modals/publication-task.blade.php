<flux:modal
    class="w-full max-w-6xl"
    name="workbench-publication-task"
    wire:model="publicationTaskOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="preparePublicationTask, openWorkflowReason"
    />
    @if ($publicationTask)
        <div class="space-y-4">
            <flux:heading
                class="flex items-center justify-start gap-2"
                size="xl"
                text="{{ __('Preparation shows the planned language file changes. Files are written only after confirmation in the preview.') }}"
            >
                <flux:icon.book-plus class="size-6" />
                {{ __('Prepare publication') }}
            </flux:heading>

            <flux:text>
                {{ __('Preparation shows the planned language file changes. Files are written only after confirmation in the preview.') }}
            </flux:text>

            <flux:callout
                color="green"
                icon="book-plus"
                :heading="__('Prepare publication · :count revisions', ['count' => count($publicationBatch ? : [
                    $publicationTask
                ])])"
            >

                {{-- Table --}}
                <flux:table container:class="max-h-96 overflow-auto rounded-t-lg mt-3">
                    {{-- Table Header Row --}}
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-800"
                        sticky
                    >
                        {{-- Table Header Row Column Revision --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading class="ml-2">
                                    {{ __('Revision') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Revision')"
                                    :text="__('Database ID of the saved, approved translation revision selected for publication. This identifies a specific text version, not the finding or translation key.')"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Table Header Row Column Translation Key --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Translation key') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Translation key')"
                                    :text="__('The assigned translation key under which this revision will be published. Its first segment is the translationGroup and determines the language file, for example ui.button.save uses ui.php.')"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Table Header Row Column Language --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading>
                                    {{ __('Language') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Language')"
                                    :text="__('The language or regional variant of this saved revision. Its locale determines the target directory within lang; each row publishes only this language.')"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                        {{-- Table Header Row Column Saved Text --}}
                        <flux:table.column>
                            <flux:tooltip>
                                <flux:callout.heading class="mr-3">
                                    {{ __('Saved text') }}
                                </flux:callout.heading>
                                <x-translation-workbench::ui.common.tooltip-content
                                    :heading="__('Saved text')"
                                    :text="__('The saved, approved text that will be written under this translation key for the displayed language. Preparing publication creates a preview; files are written only after confirmation.')"
                                />
                            </flux:tooltip>
                        </flux:table.column>
                    </flux:table.columns>
                    {{-- Table Body Rows --}}
                    <flux:table.rows>
                        @foreach ($publicationBatch ?: [$publicationTask] as $task)
                            {{-- Table Body Rows --}}
                            <flux:table.row wire:key="publication-task-{{ $task['context']['revision_id'] }}">
                                {{-- Table Body Row Column Revision --}}
                                <flux:table.cell>
                                    <flux:callout.text class="ml-2 tabular-nums">
                                        #{{ $task['context']['revision_id'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                {{-- Table Body Row Column Translation Key --}}
                                <flux:table.cell class="max-w-xs whitespace-normal">
                                    <x-translation-workbench::ui.common.text-preview
                                        :mono="true"
                                        :text="$task['context']['full_name']"
                                        :length="60"
                                        side="center"
                                        :heading="__('Translation key')"
                                    />
                                </flux:table.cell>
                                {{-- Table Body Row Column Language --}}
                                <flux:table.cell>
                                    <flux:callout.text>
                                        <span class="inline-flex items-center gap-2">
                                            <flux:flag
                                                :country="$translationFlagCountries[$task['context']['locale']] ?? null"
                                                size="xs"
                                                circle
                                            />
                                            <span>{{ $task['context']['locale'] }}</span>
                                        </span>
                                    </flux:callout.text>
                                </flux:table.cell>
                                {{-- Table Body Row Column Saved Text --}}
                                <flux:table.cell class="max-w-lg whitespace-normal">
                                    <x-translation-workbench::ui.common.text-preview
                                        :text="$task['context']['value']"
                                        :lang="str_replace('_', '-', $task['context']['locale'])"
                                        :length="160"
                                        :heading="__('Saved text')"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:callout>

            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                'reasonField' => 'publicationReason',
                'reasonLabel' => __('Reason for publication'),
                'reasonInputLocale' => $publicationReasonLocale,
                'reasonLocaleField' => 'publicationReasonLocale',
                'reasonSearchId' => 'publication-task-reason-' . $this->getId(),
            ])

            <flux:error name="publicationTask" />

            <div class="flex justify-end gap-3">
                <flux:button.group>
                    <flux:button
                        variant="filled"
                        color="sky"
                        size="sm"
                        wire:click="preparePublicationTask"
                    >
                        <flux:icon.bell-check class="size-5" />
                    </flux:button>
                    <flux:button
                        variant="filled"
                        color="sky"
                        size="sm"
                        wire:click="preparePublicationTask"
                    >
                        {{ __('Prepare file preview') }}
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
    @endif
</flux:modal>
