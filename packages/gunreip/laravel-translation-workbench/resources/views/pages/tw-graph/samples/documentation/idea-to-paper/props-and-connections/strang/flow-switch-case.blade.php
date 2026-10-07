<section
    class="min-w-0 space-y-4"
    id="reference-strang-flow-switch-case"
>
    <flux:heading size="lg">{{ __('strang.flow-switch-case — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-switch-case" />
    <flux:text>{{ __('Draws a SWITCH expression, ordered CASE routes and a shared output. Supports grouped entries, DEFAULT or a bypass, open CASE outputs for nested blocks, and explicit fall-through. It renders a diagram; it does not evaluate the supplied CASE text as executable code.') }}</flux:text>
    <flux:callout
        icon="information-circle"
        color="indigo"
    >
        <flux:callout.heading>{{ __('Reading this reference') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Blade attributes use kebab-case (case-expression); array keys use camelCase (stemLength). [] means one entry of a list, not an extra literal key. Defaults apply when omitted; partial arrays are not recursively merged with the top-level default array. Open an array section, then its nested child sections. Every field uses its full path; no path needs to be assembled from a shared label template.') }}</flux:callout.text>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="red"
        icon="book-open-check"
    >
        <flux:callout.heading>{{ __('Public component props') }}</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>id</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>{graphId}.switch</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Use a unique ID. It prefixes generated anchors and the root ID in DEV tooltips.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>attach-to</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Looks up an existing anchor in this canvas. If missing or unresolved, anchor-start is used. Render the target first.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array{x: string, y: string}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;,
                        &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Fallback position. Coordinates use the graph coordinate system; positive y points upwards.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>left | right</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal direction of CASE actions; annotation placement follows the route.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>direction</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bottom-top | top-bottom</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bottom-top</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Vertical progression through the CASE entries.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>case-expression</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | false</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[&#x27;text&#x27; =&gt; [&#x27;SWITCH
                        expression&#x27;], &#x27;width&#x27; =&gt; &#x27;halfLong&#x27;]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Expression step. A string supplies the caption; false omits its text. See case-expression.* below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>cases</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>list&lt;array&gt;</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[] — at least one required</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Ordered CASE definitions. See cases[] and cases[].entries[].') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>case-default</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | false</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[&#x27;text&#x27; =&gt; [&#x27;Default
                        action&#x27;], &#x27;width&#x27; =&gt; &#x27;halfLong&#x27;]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Final fallback action. Exactly false creates a continuous bypass without an action.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>10rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default CASE entry stem length, not the expression step stems.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>arc-radius</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2.75rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('CASE and fall-through arc radius; grouped fusion starts from half this radius and adapts to entry spacing.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Baseline bridge length. Action widths and grouping determine actual route spans. Plain rem values are normalized: minimum 0.25rem, values between 0.25 and 1.15 become 1.15rem; maximum is half the configured long label width (normally 12rem).') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas color → zinc</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default path and label color. Actions and grouped entries can override it.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>20</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base drawing layer; the fall-through joining arc sits one level lower.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>int</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('First DEV counter, assigned to the SWITCH expression. Set explicitly when composing with preceding components; subsequent SWITCH counters advance from this value.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="green"
        icon="brackets"
    >
        <flux:callout.heading>{{ __('Array props') }}</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('anchor-start — fallback coordinates') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Used when attach-to is empty or cannot resolve an anchor in this canvas.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start.x</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal coordinate in the graph.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start.y</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Vertical coordinate; positive y points upwards.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('case-expression.* — expression step') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('These fields belong directly in :case-expression. The expression is drawn through flow-step.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>SWITCH expression in the default
                                    array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Expression caption; use an explicit text key.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>width preset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>halfLong in default array; default
                                    when omitted from a custom array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('half / default / halfLong / long. Preset sizes come from canvas configuration.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.stemLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('First CASE entry only. First cases[].stemLength takes precedence. Does not set beforeLength or afterLength of the expression step; those remain 2rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Aligns text inside its label box.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Enables justified text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draws the text badge.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides expression badge color. color in this array is not forwarded as the step color.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas label_offset → 0.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text offset and automatic gap around expression content.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | right | top | bottom |
                                    center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text placement; does not mirror the SWITCH routes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by the text renderer. Prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by the text renderer. Prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-expression.half</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by the text renderer. Prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <x-translation-workbench::ui.tw-graph.code-box
                        class="mt-3"
                        max-height="24rem"
                    >:case-expression=&quot;[
                        &#x27;text&#x27; =&gt; [&#x27;SWITCH ($status)&#x27;],
                        &#x27;width&#x27; =&gt; &#x27;halfLong&#x27;,
                        &#x27;align&#x27; =&gt; &#x27;center&#x27;,
                        &#x27;stemLength&#x27; =&gt; &#x27;4rem&#x27;,
                        ]&quot;</x-translation-workbench::ui.tw-graph.code-box>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('cases[] — one route per CASE or CASE group') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Each route has one actionLabel. A group puts multiple entries before that shared action.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].key</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Nonempty and unique across routes. The key default is reserved. Also becomes part of generated IDs.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt; |
                                    label array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>CASE {key}</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Entry annotation for an ungrouped CASE. For groups use entries[].label instead.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].stemLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>First route:
                                    case-expression.stemLength → stem-length; others: stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Entry stem before the CASE or the first grouped entry. Set explicitly to reserve room for nested blocks or fall-through.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>list&lt;array&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Empty means an ordinary CASE. Two or more entries use fusion; one entry uses a direct connection.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].entryStemLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>rem length / resolvable expression</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>max(3rem, 2 × arc-radius)</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Uniform spacing after the first grouped entry. Must resolve to at least 3rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | action array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[&#x27;text&#x27; =&gt;
                                    [&#x27;Case action&#x27;]]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Action in the horizontal bridge. Must contain nonempty text; false or empty actions are rejected.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt; |
                                    label array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>BREAK; fall-through when
                                    fallThrough is set</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Output annotation. false hides text only; it does not change routing.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].fallThrough</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Routes to the next action without testing its CASE. The final CASE may enter DEFAULT or the bypass. Cannot combine with actionLabel.return=false.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>cases[].fallThroughJoinLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>arc-radius</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Join position inside the following bridge-in, measured from its start. Must fit that bridge. Increase the following stemLength / bridge lengths if there is insufficient space.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <x-translation-workbench::ui.tw-graph.code-box
                        class="mt-3"
                        max-height="24rem"
                    >:cases=&quot;[
                        [
                        &#x27;key&#x27; =&gt; &#x27;draft&#x27;,
                        &#x27;stemLength&#x27; =&gt; &#x27;4rem&#x27;,
                        &#x27;label&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;CASE draft&#x27;], &#x27;align&#x27;
                        =&gt; &#x27;left&#x27;],
                        &#x27;actionLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;Prepare editing&#x27;],
                        &#x27;color&#x27; =&gt; &#x27;amber&#x27;],
                        &#x27;exitLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;BREAK&#x27;], &#x27;align&#x27; =&gt;
                        &#x27;right&#x27;],
                        ],
                        ]&quot;</x-translation-workbench::ui.tw-graph.code-box>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('cases[].label — annotation options') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Fields of cases[].label. A string or list of strings supplies text directly; false hides the annotation without changing routing.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Generated caption when
                                                the entire label is omitted</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit arrays require nonempty text. Each list item or | separator creates a text line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default |
                                                halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Uses canvas width presets. Controls the box, not text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment within the label box.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justifies text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Action color →
                                                component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector color and badge fallback.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides only the badge color.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas
                                                connector-length → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Length of the connector from the anchor node.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas connector-gap →
                                                0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects long width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects halfLong width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects half width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].label.side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>placement controlled
                                                by SWITCH</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Opposite the action
                                                side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('The component supplies placement. This field does not independently mirror the route; use align for text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('cases[].entries[] — grouped inputs') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Entries belong to their enclosing CASE group, not to the outer cases list.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].key</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Unique and nonempty within this group.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                list&lt;string&gt; | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>CASE {entry key}</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node annotation; uses the annotation options below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Action color →
                                                component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Entry bridge/fusion color and annotation fallback. The common vertical entry stem keeps the component color.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].stemLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>unsupported</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>—</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Rejected. Use cases[].entryStemLength for uniform spacing.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <x-translation-workbench::ui.tw-graph.code-box
                                    class="mt-3"
                                    max-height="24rem"
                                >&#x27;key&#x27; =&gt; &#x27;editable&#x27;,
                                    &#x27;entryStemLength&#x27; =&gt; &#x27;5rem&#x27;,
                                    &#x27;entries&#x27; =&gt; [
                                    [&#x27;key&#x27; =&gt; &#x27;draft&#x27;, &#x27;label&#x27; =&gt; [&#x27;text&#x27;
                                    =&gt; [&#x27;CASE draft&#x27;]], &#x27;color&#x27; =&gt; &#x27;amber&#x27;],
                                    [&#x27;key&#x27; =&gt; &#x27;review&#x27;, &#x27;label&#x27; =&gt; [&#x27;text&#x27;
                                    =&gt; [&#x27;CASE review&#x27;]], &#x27;color&#x27; =&gt; &#x27;orange&#x27;],
                                    ],
                                    &#x27;actionLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;Prepare editing&#x27;],
                                    &#x27;return&#x27; =&gt; false],
                                    &#x27;exitLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;Nested
                                    SWITCH&#x27;]],</x-translation-workbench::ui.tw-graph.code-box>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>{{ __('cases[].entries[].label — annotation options') }}</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>{{ __('Fields of cases[].entries[].label. A string or list of strings supplies text directly; false hides the annotation without changing routing.') }}</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                            list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Generated
                                                            caption when the entire label is omitted</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit arrays require nonempty text. Each list item or | separator creates a text line.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half |
                                                            default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Uses canvas width presets. Controls the box, not text alignment.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left |
                                                            center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment within the label box.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justifies text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Entry
                                                            color → action color → component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector color and badge fallback.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Label
                                                            color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides only the badge color.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length
                                                            string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas
                                                            connector-length → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Length of the connector from the anchor node.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length
                                                            string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas
                                                            connector-gap → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Selects long width. Prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Selects halfLong width. Prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Selects half width. Prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].entries[].label.side</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>placement
                                                            controlled by SWITCH</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Opposite
                                                            the action side</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('The component supplies placement. This field does not independently mirror the route; use align for text alignment.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                </flux:accordion>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('cases[].actionLabel.* — action in a bridge') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Use these fields inside actionLabel. case-default accepts the visual action options directly at its root; return is only supported on CASE actions.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Case action in the
                                                default array</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Required when supplying a custom action array. A | in text separates lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>width preset</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>default</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('half / default / halfLong / long. Action widths determine the common output rail. Arbitrary CSS widths are not supported here.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Action route and badge fallback; not the whole SWITCH.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Action color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only override.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment, independent of route side.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justified text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum number of displayed text lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.bridgeOutLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Calculated route
                                                bridge length</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit outgoing bridge length. Closed routes compensate on bridge-in to retain the common output rail; fall-through keeps its own outgoing span.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.return</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('false leaves an open output for a nested component; explicitly return to .case.{key}.anchorNode-return afterwards.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.lineJumps</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>list&lt;array&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit crossings on bridge-out. Expand the lineJumps child section below. Does not automatically detect all crossings.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <x-translation-workbench::ui.tw-graph.code-box
                                    class="mt-3"
                                    max-height="24rem"
                                >&#x27;actionLabel&#x27; =&gt; [
                                    &#x27;text&#x27; =&gt; [&#x27;Prepare editing&#x27;],
                                    &#x27;width&#x27; =&gt; &#x27;default&#x27;,
                                    &#x27;color&#x27; =&gt; &#x27;amber&#x27;,
                                    &#x27;bridgeOutLength&#x27; =&gt; &#x27;2rem&#x27;,
                                    &#x27;return&#x27; =&gt; false,
                                    ],</x-translation-workbench::ui.tw-graph.code-box>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>{{ __('cases[].actionLabel.lineJumps[] — crossings on bridge-out') }}</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>{{ __('Each item declares one explicit crossing. Multiple items are allowed; invalid or overlapping jumps are reported by the DEV mismatch indicator.') }}</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.lineJumps[].over</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('ID of a perpendicular stem/bridge that actually intersects this bridge-out.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.lineJumps[].radius</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length
                                                            string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>0.5rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Must exceed half the line thickness and fit between endpoints and other jumps.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>cases[].actionLabel.lineJumps[].side</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>top |
                                                            bottom</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>top</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Side of the semicircle on a horizontal bridge.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                </flux:accordion>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('cases[].exitLabel — annotation options') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Fields of cases[].exitLabel. A string or list of strings supplies text directly; false hides the annotation without changing routing.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Generated caption when
                                                the entire label is omitted</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit arrays require nonempty text. Each list item or | separator creates a text line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default |
                                                halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Uses canvas width presets. Controls the box, not text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment within the label box.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justifies text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector color and badge fallback.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides only the badge color.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas
                                                connector-length → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Length of the connector from the anchor node.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas connector-gap →
                                                0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects long width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects halfLong width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects half width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>cases[].exitLabel.side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>placement controlled
                                                by SWITCH</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Action side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('The component supplies placement. This field does not independently mirror the route; use align for text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('case-default.* — fallback action or bypass') }}</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('This is the action array itself, not an actionLabel wrapper. Passing exactly false removes the DEFAULT action and its entry annotation; END SWITCH remains.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Default action in the default
                                    array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Required when supplying a custom action array. A | in text separates lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>width preset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>halfLong in default array; default
                                    when omitted from a custom array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('half / default / halfLong / long. Action widths determine the common output rail. Arbitrary CSS widths are not supported here.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Action route and badge fallback; not the whole SWITCH.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Action color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment, independent of route side.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justified text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum number of displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.bridgeOutLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Calculated route bridge length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit outgoing bridge length. Closed routes compensate on bridge-in to retain the common output rail; fall-through keeps its own outgoing span.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.lineJumps</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>list&lt;array&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit crossings on bridge-out. Expand the lineJumps child section below. Does not automatically detect all crossings.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.stemLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Entry stem before DEFAULT.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt; |
                                    array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>DEFAULT</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Entry annotation; expand case-default.label below.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt; |
                                    array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>END SWITCH</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Final output annotation; expand case-default.exitLabel below.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.return</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>unsupported</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>—</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('DEFAULT is the final route; this field does not open its output.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>case-default.fallThrough</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>unsupported</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>—</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('There is no subsequent CASE; this field does not configure fall-through.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <x-translation-workbench::ui.tw-graph.code-box
                        class="mt-3"
                        max-height="24rem"
                    >:case-default=&quot;[
                        &#x27;text&#x27; =&gt; [&#x27;Show status hint&#x27;],
                        &#x27;width&#x27; =&gt; &#x27;default&#x27;,
                        &#x27;color&#x27; =&gt; &#x27;zinc&#x27;,
                        &#x27;stemLength&#x27; =&gt; &#x27;4rem&#x27;,
                        &#x27;label&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;DEFAULT&#x27;], &#x27;align&#x27; =&gt;
                        &#x27;left&#x27;],
                        &#x27;exitLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;END SWITCH&#x27;], &#x27;align&#x27;
                        =&gt; &#x27;right&#x27;],
                        ]&quot;

                        {{-- Alternative: no matching CASE performs an action. --}}
                        :case-default=&quot;false&quot;</x-translation-workbench::ui.tw-graph.code-box>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('case-default.label — annotation options') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Fields of case-default.label. A string or list of strings supplies text directly; false hides the annotation without changing routing.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Generated caption when
                                                the entire label is omitted</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit arrays require nonempty text. Each list item or | separator creates a text line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default |
                                                halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Uses canvas width presets. Controls the box, not text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment within the label box.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justifies text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>DEFAULT action color →
                                                component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector color and badge fallback.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides only the badge color.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas
                                                connector-length → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Length of the connector from the anchor node.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas connector-gap →
                                                0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects long width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects halfLong width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects half width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.label.side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>placement controlled
                                                by SWITCH</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Opposite the action
                                                side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('The component supplies placement. This field does not independently mirror the route; use align for text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('case-default.exitLabel — annotation options') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Fields of case-default.exitLabel. A string or list of strings supplies text directly; false hides the annotation without changing routing.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string |
                                                list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Generated caption when
                                                the entire label is omitted</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit arrays require nonempty text. Each list item or | separator creates a text line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default |
                                                halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Uses canvas width presets. Controls the box, not text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment within the label box.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justifies text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector color and badge fallback.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides only the badge color.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Whether the text has a badge.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas
                                                connector-length → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Length of the connector from the anchor node.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas connector-gap →
                                                0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects long width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects halfLong width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Selects half width. Prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.exitLabel.side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>placement controlled
                                                by SWITCH</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Action side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('The component supplies placement. This field does not independently mirror the route; use align for text alignment.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>{{ __('case-default.lineJumps[] — crossings on bridge-out') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Each item declares one explicit crossing. Multiple items are allowed; invalid or overlapping jumps are reported by the DEV mismatch indicator.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.lineJumps[].over</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('ID of a perpendicular stem/bridge that actually intersects this bridge-out.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.lineJumps[].radius</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>0.5rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Must exceed half the line thickness and fit between endpoints and other jumps.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>case-default.lineJumps[].side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>top | bottom</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>top</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Side of the semicircle on a horizontal bridge.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="sky"
        icon="variable"
    >
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('Canvas settings provide line thickness, node size, text-width presets, connector defaults and path-tone rendering. color can be overridden at this component; DEV and coordinates belong to the canvas; side, direction, stem-length, arc-radius and bridge-length use the defaults listed above. pathTone is configured on the canvas, not in an action array.') }}</flux:text>
        <flux:text>{{ __('CASE action widths are normalized into a common route span. Grouped entries add fusion space. Fusion starts with half arc-radius; spacing can enlarge its radius and remove a short compensating stem. There is no collision avoidance for a nested block: reserve space explicitly with the following CASE stemLength and author the return connection. These calculations do not rewrite your source props.') }}</flux:text>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="amber"
        icon="cable"
    >
        <flux:callout.heading>{{ __('Connections') }}</flux:callout.heading>
        <flux:text>{{ __('Replace {id}, {key} and {entryKey} with your authored IDs. Anchors belong to the current canvas and become available after their component renders. Read coordinates through AnchorRegistry only after rendering the referenced component.') }}</flux:text>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Input</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Resolved attach-to or anchor-start.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Final output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connect the next independent step here, after DEFAULT or bypass.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.case.{key}.anchorNode-action</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Action entry</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('After CASE selection / fusion. With incoming fall-through, resolves to its join point on bridge-in.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.case.{key}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Action route output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connect a nested block here when actionLabel.return=false.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.case.{key}.anchorNode-return</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Following route on outer output rail</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Target for an explicit nested return. Exists for CASE routes, not the final DEFAULT.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.case.{key}.entries.{entryKey}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Grouped entry bridge end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Input handed to fusion; not the shared action output.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.case.{key}.fusion.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Shared fusion output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Available when the group contains at least two entries.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.case.default.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>DEFAULT route output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Only when DEFAULT is enabled. Prefer the stable public {id}.anchorNode-end for continuation.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.bypass.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Bypass output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Only when case-default=false. Continuation still uses {id}.anchorNode-end.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3"
            max-height="24rem"
        >{{-- Render outer first, with cases[].key=&quot;editable&quot; and actionLabel.return=false. --}}
            &lt;x-translation-workbench::ui.tw-graph.strang.flow-switch-case
            id=&quot;example.inner&quot;
            attach-to=&quot;example.outer.case.editable.anchorNode-end&quot;
            :cases=&quot;[
            [&#x27;key&#x27; =&gt; &#x27;text&#x27;, &#x27;actionLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;Open
            text editor&#x27;]]],
            ]&quot;
            /&gt;
            {{-- Author the return from example.inner.anchorNode-end
     to example.outer.case.editable.anchorNode-return using parts.
     Then attach the next step to example.outer.anchorNode-end. --}}</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="fuchsia"
        icon="infinity"
    >
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('Invalid side/direction, missing CASEs, duplicate or reserved route keys, duplicate group keys, empty action text, entries[].stemLength, entryStemLength below 3rem, and fallThrough combined with return=false raise errors. Fall-through also rejects insufficient routing space. Unknown nested array keys are not generally rejected: only the fields consumed by this component chain have an effect.') }}</flux:text>
    </flux:callout>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/strang/flow-switch-case.blade.php"
        segments="3"
    />
    <flux:callout color="green" icon="brackets" class="min-w-0 space-y-3">
        <flux:callout.heading>{{ __('Array props: explicit entry detours') }}</flux:callout.heading>
        <flux:text>{{ __('stemLength remains the complete vertical distance. The middle stem is stemLength − beforeLength − afterLength − 4 × arcRadius. Insufficient height, negative lengths, invalid sides/directions and unknown detour keys are rejected; the component does not enlarge or shorten the entry to make it fit. Omit entryDetour to retain the ordinary straight entry. The existing entry.anchorNode-end is preserved.') }}</flux:text>
        <flux:table>
            <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Default') }}</flux:table.column><flux:table.column>{{ __('Meaning') }}</flux:table.column></flux:table.columns>
            <flux:table.rows>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour<br>case-default.entryDetour</flux:table.cell>
                <flux:table.cell><code>null</code></flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Optional explicit entry detour for a single CASE or DEFAULT. Grouped entries reject this option. Uses paths.stem-detour; no automatic collision detection.') }}</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.side<br>case-default.entryDetour.side</flux:table.cell>
                <flux:table.cell><code>right</code></flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Physical side of the detour, independent of SWITCH side: left or right.') }}</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.bridgeLength<br>case-default.entryDetour.bridgeLength</flux:table.cell>
                <flux:table.cell><code>4rem</code></flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Horizontal bridge in both turns. Total horizontal offset is bridgeLength + 2 × arcRadius.') }}</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.arcRadius<br>case-default.entryDetour.arcRadius</flux:table.cell>
                <flux:table.cell><code>2rem</code></flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Positive radius for all four arcs.') }}</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.beforeLength<br>case-default.entryDetour.beforeLength</flux:table.cell>
                <flux:table.cell><code>0rem</code></flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Straight entry length before the outward turn.') }}</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.afterLength<br>case-default.entryDetour.afterLength</flux:table.cell>
                <flux:table.cell><code>2rem</code></flux:table.cell>
                <flux:table.cell class="whitespace-normal">{{ __('Straight entry length after the inward turn, ending at the original CASE anchor with its label.') }}</flux:table.cell>
            </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
</section>
