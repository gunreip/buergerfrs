<x-translation-workbench::ui.common.heading-counter-group group="primitives-text-label">
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
            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            {{ __('default · 12rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->example('text-single-default') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:callout.heading
                class="mt-4"
                size="sm"
                icon="asterisk"
            >
                {{ __('Changed props for the other examples') }}
            </flux:callout.heading>
            <flux:callout.text class="mt-2">
                {{ __('The numbered code examples refer to the individual previews below the comparison canvas. Only text props that differ from the two-line default label are listed, including content, color, width flags and maxLines. All individual previews use the same anchor position.') }}
            </flux:callout.text>
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="half"
                            variant="accordion"
                        >
                            {{ __('half · 6rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->changedProps('text-single-half', 'text-single-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default-one-line"
                            variant="accordion"
                        >
                            {{ __('default · 12rem · one line') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->changedProps('text-single-default-one-line', 'text-single-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default-four-lines"
                            variant="accordion"
                        >
                            {{ __('default · 12rem · four lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->changedProps('text-single-default-four-lines', 'text-single-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="half-long"
                            variant="accordion"
                        >
                            {{ __('half-long · 18rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->changedProps('text-single-half-long', 'text-single-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="long"
                            variant="accordion"
                        >
                            {{ __('long · 24rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->changedProps('text-single-long', 'text-single-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="long-three-lines"
                            variant="accordion"
                        >
                            {{ __('long · 24rem · three lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->changedProps('text-single-long-three-lines', 'text-single-default', 'x-translation-workbench::ui.tw-graph.primitives.text') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:separator
                class="mt-4"
                :text="__('Comparison preview source')"
            />
            <flux:accordion transition>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <flux:accordion.heading>{{ __('Complete comparison: seven text labels') }}
                        </flux:accordion.heading>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $textSource->example('source-text-preview') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Text props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>text</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the label and diagnostics.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:text</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Text content, supplied as a string or an array of lines. Empty content renders no label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-x</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('X coordinate of the reference point.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-y</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Y coordinate of the reference point; positive values point up.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Position relative to the reference point: left, right, top, bottom, or center.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>offset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Distance from the reference point in the selected side direction.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:badge</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Displays the text in a Flux badge. Width flags apply to this badge variant.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>badge-color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cyan</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Color of the Flux badge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Selects a 6rem text area when long and half-long are false.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:half-long</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Selects an 18rem text area when long is false.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:long</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Selects a 24rem text area. Takes precedence over the other width flags. Without any width flag, the text area is 12rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>align</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>center</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Text alignment within the selected width: left, center, or right.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:justify</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enables justified text alignment.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:max-lines</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>3</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Maximum number of supplied text entries displayed; at least one. Wrapped lines are not counted separately.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}
                                </flux:table.cell>
                            </flux:table.row>

                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Text Label preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <flux:callout.heading
                    class="mt-4"
                    size="sm"
                    icon="scale"
                >
                    {{ __('Comparison: all labels') }}
                </flux:callout.heading>
                <flux:callout.text class="mt-2">
                    {{ __('The shared canvas keeps all seven labels aligned for direct comparison. The numbered previews below show each example separately.') }}
                </flux:callout.text>
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
                        />
                        {{-- text-long-three-lines:end --}}
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-text-preview:end --}}
                </div>
                <flux:callout.heading
                    class="mt-6"
                    size="sm"
                    icon="asterisk"
                >
                    {{ __('Individual examples') }}
                </flux:callout.heading>
                <flux:callout.text class="mt-2">
                    {{ __('The numbers match the code headings. Content, colors and text widths match the comparison above; the separate canvases place every label at the same height.') }}
                </flux:callout.text>
                <div
                    class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2"
                    data-text-examples
                >
                    <div
                        class="min-w-0"
                        data-text-example="default"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default"
                            size="sm"
                        >
                            {{ __('default · 12rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-default:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-default"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.default"
                                    :text="['Text label', 'Two lines of information']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-default:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-text-example="half"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="half"
                            size="sm"
                        >
                            {{ __('half · 6rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-half:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-half"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.half"
                                    :text="['Text label', 'Two lines of information']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="red"
                                    :half="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-half:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-text-example="default-one-line"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default-one-line"
                            size="sm"
                        >
                            {{ __('default · 12rem · one line') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-default-one-line:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-default-one-line"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.default-one-line"
                                    :text="['Text label']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="green"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-default-one-line:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-text-example="default-four-lines"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default-four-lines"
                            size="sm"
                        >
                            {{ __('default · 12rem · four lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-default-four-lines:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-default-four-lines"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.default-four-lines"
                                    :text="['Text label', 'Second line', 'Third line', 'Fourth line']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="fuchsia"
                                    maxLines="4"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-default-four-lines:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-text-example="half-long"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="half-long"
                            size="sm"
                        >
                            {{ __('half-long · 18rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-half-long:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-half-long"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.half-long"
                                    :text="['Text label', 'Two lines of information']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="emerald"
                                    :half-long="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-half-long:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-text-example="long"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="long"
                            size="sm"
                        >
                            {{ __('long · 24rem · two lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-long:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-long"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.long"
                                    :text="['Text label', 'Two lines of information']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="rose"
                                    :long="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-long:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-text-example="long-three-lines"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="long-three-lines"
                            size="sm"
                        >
                            {{ __('long · 24rem · three lines') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- text-single-long-three-lines:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-text-single-long-three-lines"
                                :dev="true"
                                :coordinates="true"
                                min-height="14rem"
                                min-width="34rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.text
                                    id="literature.primitives.text.single.long-three-lines"
                                    :text="['Text label', 'Three lines of information', 'Additional line']"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    side="right"
                                    align="left"
                                    badge-color="sky"
                                    :long="true"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- text-single-long-three-lines:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-text-label.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
