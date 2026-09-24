@php
    $textSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-text-label',
    );
@endphp
<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Text Label') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Seven examples demonstrate four text widths: half (6rem), default (12rem), half-long (18rem), and long (24rem), with different colors and one to four text lines. All labels share the same X coordinate. These widths describe the text area; the badge adds its own padding. The text primitive draws the label only.') }}
        </flux:callout.text>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Complete preview: seven text labels') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $textSource->example('source-text-preview') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Changed props for the other examples') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Only text props that differ from the two-line default label in the complete preview are listed below, including content, color, and maxLines. anchor-y places the examples on separate rows; the width flags select the text width independently of side and align.') }}
        </flux:text>
        <flux:heading
            class="mt-4"
            size="sm"
        >half · 6rem · two lines</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">{{ $textSource->changedProps('text-half', 'text-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >default · 12rem · one line</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">{{ $textSource->changedProps('text-default-one-line', 'text-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >default · 12rem · four lines</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">{{ $textSource->changedProps('text-default-four-lines', 'text-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >half-long · 18rem · two lines</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">{{ $textSource->changedProps('text-half-long', 'text-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >long · 24rem · two lines</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">{{ $textSource->changedProps('text-long', 'text-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >long · 24rem · three lines</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">{{ $textSource->changedProps('text-long-three-lines', 'text-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Text props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            label and diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:text</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text content, supplied
                            as a string or an array of lines. Empty content renders no label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-x
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">X coordinate of the
                            reference point.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-y
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Y coordinate of the
                            reference point; positive values point up.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Position relative to
                            the reference point: left, right, top, bottom, or center.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">offset
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Distance from the
                            reference point in the selected side direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:badge
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Displays the text in a
                            Flux badge. Width flags apply to this badge variant.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">badge-color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cyan</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the Flux
                            badge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:half</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Selects a 6rem text
                            area when long and half-long are false.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:half-long
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Selects an 18rem text
                            area when long is false.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:long</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Selects a 24rem text
                            area. Takes precedence over the other width flags. Without any width flag, the text area is
                            12rem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">align</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">center
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text alignment within
                            the selected width: left, center, or right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:justify
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enables justified text
                            alignment.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:max-lines
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Maximum number of
                            supplied text entries displayed; at least one. Wrapped lines are not counted separately.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order
                            override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Makes the label
                            bounding box available for DEV display.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Text Label preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- source-text-preview:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-primitives-text-preview"
                    :dev="true"
                    :coordinates="true"
                    min-height="41rem"
                    min-width="34rem"
                    horizontal-padding="4rem"
                >
                    {{-- half: 6rem text width. --}}
                    {{-- text-half:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.half"
                        :text="['Text label', 'Two lines of information']"
                        anchor-x="0rem"
                        anchor-y="34rem"
                        side="right"
                        align="left"
                        badge-color="red"
                        :half="true"
                        :dev="true"
                    />
                    {{-- text-half:end --}}
                    {{-- default: 12rem text width. --}}
                    {{-- text-default:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.default"
                        :text="['Text label', 'Two lines of information']"
                        anchor-x="0rem"
                        anchor-y="29rem"
                        side="right"
                        align="left"
                        badge-color="cyan"
                        :dev="true"
                    />
                    {{-- text-default:end --}}
                    {{-- default: 12rem text width. --}}
                    {{-- text-default-one-line:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.default"
                        :text="['Text label']"
                        anchor-x="0rem"
                        anchor-y="25rem"
                        side="right"
                        align="left"
                        badge-color="green"
                        :dev="true"
                    />
                    {{-- text-default-one-line:end --}}
                    {{-- default: 12rem text width. --}}
                    {{-- text-default-four-lines:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.default"
                        :text="['Text label', 'Second line', 'Third line', 'Fourth line']"
                        anchor-x="0rem"
                        anchor-y="20rem"
                        side="right"
                        align="left"
                        badge-color="fuchsia"
                        maxLines="4"
                        :dev="true"
                    />
                    {{-- text-default-four-lines:end --}}
                    {{-- half-long: 18rem text width. --}}
                    {{-- text-half-long:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.half-long"
                        :text="['Text label', 'Two lines of information']"
                        anchor-x="0rem"
                        anchor-y="14rem"
                        side="right"
                        align="left"
                        badge-color="emerald"
                        :half-long="true"
                        :dev="true"
                    />
                    {{-- text-half-long:end --}}
                    {{-- long: 24rem text width. --}}
                    {{-- text-long:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.long"
                        :text="['Text label', 'Two lines of information']"
                        anchor-x="0rem"
                        anchor-y="9rem"
                        side="right"
                        align="left"
                        badge-color="rose"
                        :long="true"
                        :dev="true"
                    />
                    {{-- text-long:end --}}
                    {{-- long: 24rem text width. --}}
                    {{-- text-long-three-lines:start --}}
                    <x-translation-workbench::ui.tw-graph.primitives.text
                        id="literature.primitives.text.long"
                        :text="['Text label', 'Three lines of information', 'Additional line']"
                        anchor-x="0rem"
                        anchor-y="3rem"
                        side="right"
                        align="left"
                        badge-color="sky"
                        :long="true"
                        :dev="true"
                    />
                    {{-- text-long-three-lines:end --}}
                </x-translation-workbench::ui.tw-graph>
                {{-- source-text-preview:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/primitives/primitives-text-label.blade.php
        </flux:field>
    </flux:callout>
</section>
