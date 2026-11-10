<flux:error name="sourceRun" />

@if ($sourceRuns)
    <flux:callout
        color="sky"
        heading="{{ __('Source replacement runs') }}"
        icon="replace-all"
    >
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>
                    <flux:callout.heading icon="list-restart">
                        {{ __('Open or resume source replacement') }}
                    </flux:callout.heading>
                </flux:accordion.heading>
                <flux:accordion.content>
                    {{-- Table --}}
                    <flux:table container:class="mt-2 max-h-64 overflow-auto rounded-t-lg">
                        {{-- Tabler Header Row --}}
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-800"
                            sticky
                        >
                            {{-- Table Header Column Source / Key --}}
                            <flux:table.column>
                                <flux:tooltip>
                                    <flux:callout.heading class="ml-2">
                                        {{ __('Source / key') }}
                                    </flux:callout.heading>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Source / key')"
                                        :text="__(
                                            'The source file for an individual replacement, or the translation key ID for a group of replacements.',
                                        )"
                                    />
                                </flux:tooltip>
                            </flux:table.column>
                            {{-- Table Header Column Status --}}
                            <flux:table.column>
                                <flux:tooltip>
                                    <flux:callout.heading>{{ __('Status') }}</flux:callout.heading>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Status')"
                                        :text="__(
                                            'The current state of the source replacement run. Open the run to inspect its result or continue an unfinished replacement.',
                                        )"
                                    />
                                </flux:tooltip>
                            </flux:table.column>
                            {{-- Table Header Column User ID --}}
                            <flux:table.column>
                                <flux:tooltip>
                                    <flux:callout.heading>{{ __('User ID') }}</flux:callout.heading>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('User ID')"
                                        :text="__(
                                            'The database ID of the user who prepared this source replacement.',
                                        )"
                                    />
                                </flux:tooltip>
                            </flux:table.column>
                            {{-- Table Header Column Open --}}
                            <flux:table.column>
                                <flux:tooltip>
                                    <flux:callout.heading>{{ __('Open') }}</flux:callout.heading>
                                    <x-translation-workbench::ui.common.tooltip-content
                                        :heading="__('Open')"
                                        :text="__(
                                            'Open the source replacement run to inspect its details and available next steps.',
                                        )"
                                    />
                                </flux:tooltip>
                            </flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($sourceRuns as $run)
                                <flux:table.row wire:key="source-run-{{ $run['id'] }}">
                                    <flux:table.cell class="max-w-lg whitespace-normal">
                                        @if ($run['kind'] === 'source_replacement_batch_prepared')
                                            <flux:callout.text>{{ $run['path'] }}</flux:callout.text>
                                        @else
                                            <x-translation-workbench::ui.common.path-preview
                                                :path="$run['path']"
                                                :length="70"
                                                side="center"
                                                :heading="__('Source file')"
                                            />
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:callout.text>{{ __($run['state']) }}</flux:callout.text>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:callout.text class="tabular-nums">#{{ $run['actor'] }}</flux:callout.text>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button.group>
                                            <flux:button
                                                size="xs"
                                                variant="filled"
                                                color="sky"
                                                wire:click="openSourceRun('{{ $run['id'] }}')"
                                            >
                                                <flux:icon.package-open class="size-5" />
                                            </flux:button>
                                            <flux:button
                                                size="xs"
                                                variant="filled"
                                                color="sky"
                                                wire:click="openSourceRun('{{ $run['id'] }}')"
                                            >
                                                {{ __('Open') }}
                                            </flux:button>
                                        </flux:button.group>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
@endif
