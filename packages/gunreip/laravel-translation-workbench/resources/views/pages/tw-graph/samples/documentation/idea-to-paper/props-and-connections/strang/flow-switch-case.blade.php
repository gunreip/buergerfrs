<section
    class="min-w-0 space-y-4"
    id="reference-strang-flow-switch-case"
>
    <flux:heading size="lg">strang.flow-switch-case — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-switch-case" />
    <flux:text>Draws a SWITCH expression, ordered CASE routes and a shared output. Supports grouped entries, DEFAULT or
        a bypass, open CASE outputs for nested blocks, and explicit fall-through. It renders a diagram; it does not
        evaluate the supplied CASE text as executable code.</flux:text>
    <flux:callout
        icon="information-circle"
        color="indigo"
    >
        <flux:callout.heading>Reading this reference</flux:callout.heading>
        <flux:callout.text>Blade attributes use kebab-case (case-expression); array keys use camelCase (stemLength). []
            means one entry of a list, not an extra literal key. Defaults apply when omitted; partial arrays are not
            recursively merged with the top-level default array. Open an array section, then its nested child sections.
            Every field uses its full path; no path needs to be assembled from a shared label template.
        </flux:callout.text>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="red"
        icon="book-open-check"
    >
        <flux:callout.heading>Public component props</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>Prop / path</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Default / fallback</flux:table.column>
                <flux:table.column>Meaning and limits</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">id</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{graphId}.switch</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Use a unique ID. It prefixes generated anchors
                        and the root ID in DEV tooltips.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">attach-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Looks up an existing anchor in this canvas. If
                        missing or unresolved, anchor-start is used. Render the target first.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">anchor-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array{x: string, y: string}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;,
                        &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Fallback position. Coordinates use the graph
                        coordinate system; positive y points upwards.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">left | right</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Horizontal direction of CASE actions;
                        annotation placement follows the route.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bottom-top | top-bottom</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Vertical progression through the CASE entries.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">case-expression</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[&#x27;text&#x27; =&gt; [&#x27;SWITCH
                        expression&#x27;], &#x27;width&#x27; =&gt; &#x27;halfLong&#x27;]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Expression step. A string supplies the caption;
                        false omits its text. See case-expression.* below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">cases</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">list&lt;array&gt;</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[] — at least one required</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Ordered CASE definitions. See cases[] and
                        cases[].entries[].</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">case-default</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[&#x27;text&#x27; =&gt; [&#x27;Default
                        action&#x27;], &#x27;width&#x27; =&gt; &#x27;halfLong&#x27;]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Final fallback action. Exactly false creates a
                        continuous bypass without an action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">10rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default CASE entry stem length, not the
                        expression step stems.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">2.75rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">CASE and fall-through arc radius; grouped
                        fusion starts from half this radius and adapts to entry spacing.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Baseline bridge length. Action widths and
                        grouping determine actual route spans. Plain rem values are normalized: minimum 0.25rem, values
                        between 0.25 and 1.15 become 1.15rem; maximum is half the configured long label width (normally
                        12rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">color name | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Canvas color → zinc</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default path and label color. Actions and
                        grouped entries can override it.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">dev-mode</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Canvas dev</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit DEV override; otherwise inherits the
                        canvas setting.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">z-index</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base drawing layer; the fall-through joining
                        arc sits one level lower.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="green"
        icon="brackets"
    >
        <flux:callout.heading>Array props</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>anchor-start — fallback coordinates</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Used when attach-to is empty or cannot resolve an anchor in this canvas.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">anchor-start.x</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">0rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Horizontal coordinate in the graph.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">anchor-start.y</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">0rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Vertical coordinate; positive y
                                    points upwards.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>case-expression.* — expression step</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>These fields belong directly in :case-expression. The expression is drawn through
                        flow-step.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.text
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">SWITCH expression in the default
                                    array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Expression caption; use an explicit
                                    text key.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.width
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">width preset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">halfLong in default array; default
                                    when omitted from a custom array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half / default / halfLong / long.
                                    Preset sizes come from canvas configuration.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.stemLength
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">stem-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">First CASE entry only. First
                                    cases[].stemLength takes precedence. Does not set beforeLength or afterLength of the
                                    expression step; those remain 2rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.align
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Aligns text inside its label box.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.justify
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Enables justified text.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.maxLines
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed lines;
                                    expression
                                    spacing counts at most three lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.badge
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draws the text badge.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.badgeColor
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Overrides expression badge color.
                                    color in this array is not forwarded as the step color.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.offset
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas label_offset → 0.75rem
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text offset and automatic gap
                                    around expression content.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.side
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom |
                                    center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text placement; does not mirror
                                    the SWITCH routes.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.long
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by the text
                                    renderer. Prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.halfLong
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by the text
                                    renderer. Prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-expression.half
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by the text
                                    renderer. Prefer width.</flux:table.cell>
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
                <flux:accordion.heading>cases[] — one route per CASE or CASE group</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Each route has one actionLabel. A group puts multiple entries before that shared action.
                    </flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].key</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Nonempty and unique across routes.
                                    The key default is reserved. Also becomes part of generated IDs.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].label</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt; |
                                    label array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">CASE {key}</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Entry annotation for an ungrouped
                                    CASE. For groups use entries[].label instead.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].stemLength
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">First route:
                                    case-expression.stemLength → stem-length; others: stem-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Entry stem before the CASE or the
                                    first grouped entry. Set explicitly to reserve room for nested blocks or
                                    fall-through.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].entries</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">list&lt;array&gt;
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Empty means an ordinary CASE. Two
                                    or more entries use fusion; one entry uses a direct connection.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].entryStemLength
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">rem length / resolvable expression
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">max(3rem, 2 × arc-radius)
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Uniform spacing after the first
                                    grouped entry. Must resolve to at least 3rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].actionLabel
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | action array
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">[&#x27;text&#x27; =&gt;
                                    [&#x27;Case action&#x27;]]</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Action in the horizontal bridge.
                                    Must contain nonempty text; false or empty actions are rejected.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].exitLabel
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt; |
                                    label array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">BREAK; fall-through when
                                    fallThrough is set</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Output annotation. false hides
                                    text only; it does not change routing.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].fallThrough
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Routes to the next action without
                                    testing its CASE. The final CASE may enter DEFAULT or the bypass. Cannot combine
                                    with actionLabel.return=false.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">cases[].fallThroughJoinLength
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">arc-radius</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Join position inside the following
                                    bridge-in, measured from its start. Must fit that bridge. Increase the following
                                    stemLength / bridge lengths if there is insufficient space.</flux:table.cell>
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
                            <flux:accordion.heading>cases[].label — annotation options</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields of cases[].label. A string or list of strings supplies text directly;
                                    false hides the annotation without changing routing.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.text
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string |
                                                list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Generated caption when
                                                the entire label is omitted</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit arrays
                                                require nonempty text. Each list item or | separator creates a text
                                                line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.width
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default |
                                                halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">halfLong
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Uses canvas width
                                                presets. Controls the box, not text alignment.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.align
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment within the
                                                label box.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.justify
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justifies text.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.maxLines
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed
                                                lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Action color →
                                                component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector color and
                                                badge fallback.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].label.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Label color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Overrides only the
                                                badge color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.badge
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Whether the text has a
                                                badge.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].label.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas
                                                connector-length → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of the
                                                connector from the anchor node.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].label.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas connector-gap →
                                                0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Gap between connector
                                                and text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.long
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects long width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.halfLong
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects halfLong
                                                width. Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.half
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects half width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].label.side
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">placement controlled
                                                by SWITCH</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Opposite the action
                                                side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">The component supplies
                                                placement. This field does not independently mirror the route; use align
                                                for text alignment.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>cases[].entries[] — grouped inputs</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Entries belong to their enclosing CASE group, not to the outer cases list.
                                </flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].entries[].key
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">required
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Unique and nonempty
                                                within this group.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].entries[].label</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string |
                                                list&lt;string&gt; | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">CASE {entry key}
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node annotation; uses
                                                the annotation options below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].entries[].color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Action color →
                                                component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Entry bridge/fusion
                                                color and annotation fallback. The common vertical entry stem keeps the
                                                component color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].entries[].stemLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">unsupported
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">—</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Rejected. Use
                                                cases[].entryStemLength for uniform spacing.</flux:table.cell>
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
                                        <flux:accordion.heading>cases[].entries[].label — annotation options
                                        </flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>Fields of cases[].entries[].label. A string or list of strings
                                                supplies text directly; false hides the annotation without changing
                                                routing.</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>Prop / path</flux:table.column>
                                                    <flux:table.column>Type</flux:table.column>
                                                    <flux:table.column>Default / fallback</flux:table.column>
                                                    <flux:table.column>Meaning and limits</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string |
                                                            list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Generated
                                                            caption when the entire label is omitted</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit
                                                            arrays require nonempty text. Each list item or | separator
                                                            creates a text line.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half |
                                                            default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">halfLong
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Uses
                                                            canvas width presets. Controls the box, not text alignment.
                                                        </flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left |
                                                            center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment
                                                            within the label box.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justifies
                                                            text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum
                                                            displayed lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Entry
                                                            color → action color → component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector
                                                            color and badge fallback.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Label
                                                            color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Overrides
                                                            only the badge color.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Whether
                                                            the text has a badge.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length
                                                            string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas
                                                            connector-length → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Length of
                                                            the connector from the anchor node.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length
                                                            string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas
                                                            connector-gap → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Gap
                                                            between connector and text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Selects
                                                            long width. Prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Selects
                                                            halfLong width. Prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Selects
                                                            half width. Prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].entries[].label.side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">placement
                                                            controlled by SWITCH</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Opposite
                                                            the action side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">The
                                                            component supplies placement. This field does not
                                                            independently mirror the route; use align for text
                                                            alignment.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                </flux:accordion>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>cases[].actionLabel.* — action in a bridge</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Use these fields inside actionLabel. case-default accepts the visual action
                                    options directly at its root; return is only supported on CASE actions.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string |
                                                list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Case action in the
                                                default array</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Required when
                                                supplying a custom action array. A | in text separates lines.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">width preset
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half / default /
                                                halfLong / long. Action widths determine the common output rail.
                                                Arbitrary CSS widths are not supported here.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Action route and badge
                                                fallback; not the whole SWITCH.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Action color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only override.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Whether the text has a
                                                badge.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Text alignment,
                                                independent of route side.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justified text.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum number of
                                                displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.bridgeOutLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Calculated route
                                                bridge length</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit outgoing
                                                bridge length. Closed routes compensate on bridge-in to retain the
                                                common output rail; fall-through keeps its own outgoing span.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.return</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false leaves an open
                                                output for a nested component; explicitly return to
                                                .case.{key}.anchorNode-return afterwards.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].actionLabel.lineJumps</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">list&lt;array&gt;
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit crossings on
                                                bridge-out. Expand the lineJumps child section below. Does not
                                                automatically detect all crossings.</flux:table.cell>
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
                                        <flux:accordion.heading>cases[].actionLabel.lineJumps[] — crossings on
                                            bridge-out</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>Each item declares one explicit crossing. Multiple items are
                                                allowed; invalid or overlapping jumps are reported by the DEV mismatch
                                                indicator.</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>Prop / path</flux:table.column>
                                                    <flux:table.column>Type</flux:table.column>
                                                    <flux:table.column>Default / fallback</flux:table.column>
                                                    <flux:table.column>Meaning and limits</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].actionLabel.lineJumps[].over</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">required
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">ID of a
                                                            perpendicular stem/bridge that actually intersects this
                                                            bridge-out.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].actionLabel.lineJumps[].radius</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length
                                                            string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">0.5rem
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Must
                                                            exceed half the line thickness and fit between endpoints and
                                                            other jumps.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">
                                                            cases[].actionLabel.lineJumps[].side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">top |
                                                            bottom</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">top
                                                        </flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Side of
                                                            the semicircle on a horizontal bridge.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                </flux:accordion>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>cases[].exitLabel — annotation options</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields of cases[].exitLabel. A string or list of strings supplies text
                                    directly; false hides the annotation without changing routing.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].exitLabel.text
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string |
                                                list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Generated caption when
                                                the entire label is omitted</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit arrays
                                                require nonempty text. Each list item or | separator creates a text
                                                line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default |
                                                halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Uses canvas width
                                                presets. Controls the box, not text alignment.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment within the
                                                label box.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justifies text.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed
                                                lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector color and
                                                badge fallback.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Label color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Overrides only the
                                                badge color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Whether the text has a
                                                badge.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas
                                                connector-length → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of the
                                                connector from the anchor node.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas connector-gap →
                                                0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Gap between connector
                                                and text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].exitLabel.long
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects long width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                cases[].exitLabel.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects halfLong
                                                width. Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].exitLabel.half
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects half width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">cases[].exitLabel.side
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">placement controlled
                                                by SWITCH</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Action side
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">The component supplies
                                                placement. This field does not independently mirror the route; use align
                                                for text alignment.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>case-default.* — fallback action or bypass</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>This is the action array itself, not an actionLabel wrapper. Passing exactly false
                        removes the DEFAULT action and its entry annotation; END SWITCH remains.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.text
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Default action in the default
                                    array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Required when supplying a custom
                                    action array. A | in text separates lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.width
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">width preset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">halfLong in default array; default
                                    when omitted from a custom array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half / default / halfLong / long.
                                    Action widths determine the common output rail. Arbitrary CSS widths are not
                                    supported here.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.color
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Action route and badge fallback;
                                    not the whole SWITCH.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.badgeColor
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Action color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only override.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.badge
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Whether the text has a badge.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.align
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text alignment, independent of
                                    route side.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.justify
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justified text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.maxLines
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum number of displayed text
                                    lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.bridgeOutLength
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Calculated route bridge length
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit outgoing bridge length.
                                    Closed routes compensate on bridge-in to retain the common output rail; fall-through
                                    keeps its own outgoing span.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.lineJumps
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">list&lt;array&gt;
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit crossings on bridge-out.
                                    Expand the lineJumps child section below. Does not automatically detect all
                                    crossings.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.stemLength
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">stem-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Entry stem before DEFAULT.
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.label
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt; |
                                    array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">DEFAULT</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Entry annotation; expand
                                    case-default.label below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.exitLabel
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt; |
                                    array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">END SWITCH</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Final output annotation; expand
                                    case-default.exitLabel below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.return
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">unsupported</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">—</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">DEFAULT is the final route; this
                                    field does not open its output.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">case-default.fallThrough
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">unsupported</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">—</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">There is no subsequent CASE; this
                                    field does not configure fall-through.</flux:table.cell>
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
                            <flux:accordion.heading>case-default.label — annotation options</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields of case-default.label. A string or list of strings supplies text
                                    directly; false hides the annotation without changing routing.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string |
                                                list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Generated caption when
                                                the entire label is omitted</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit arrays
                                                require nonempty text. Each list item or | separator creates a text
                                                line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default |
                                                halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">halfLong
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Uses canvas width
                                                presets. Controls the box, not text alignment.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment within the
                                                label box.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justifies text.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed
                                                lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">DEFAULT action color →
                                                component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector color and
                                                badge fallback.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Label color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Overrides only the
                                                badge color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Whether the text has a
                                                badge.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas
                                                connector-length → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of the
                                                connector from the anchor node.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas connector-gap →
                                                0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Gap between connector
                                                and text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects long width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects halfLong
                                                width. Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects half width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.label.side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">placement controlled
                                                by SWITCH</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Opposite the action
                                                side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">The component supplies
                                                placement. This field does not independently mirror the route; use align
                                                for text alignment.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>case-default.exitLabel — annotation options</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields of case-default.exitLabel. A string or list of strings supplies text
                                    directly; false hides the annotation without changing routing.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string |
                                                list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Generated caption when
                                                the entire label is omitted</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit arrays
                                                require nonempty text. Each list item or | separator creates a text
                                                line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default |
                                                halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Uses canvas width
                                                presets. Controls the box, not text alignment.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment within the
                                                label box.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justifies text.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed
                                                lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector color and
                                                badge fallback.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Label color
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Overrides only the
                                                badge color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Whether the text has a
                                                badge.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas
                                                connector-length → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of the
                                                connector from the anchor node.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas connector-gap →
                                                0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Gap between connector
                                                and text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects long width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects halfLong
                                                width. Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Selects half width.
                                                Prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.exitLabel.side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">placement controlled
                                                by SWITCH</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Action side
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">The component supplies
                                                placement. This field does not independently mirror the route; use align
                                                for text alignment.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>case-default.lineJumps[] — crossings on bridge-out
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Each item declares one explicit crossing. Multiple items are allowed; invalid
                                    or overlapping jumps are reported by the DEV mismatch indicator.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.lineJumps[].over</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">required
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">ID of a perpendicular
                                                stem/bridge that actually intersects this bridge-out.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.lineJumps[].radius</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">0.5rem
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Must exceed half the
                                                line thickness and fit between endpoints and other jumps.
                                            </flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">
                                                case-default.lineJumps[].side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">top | bottom
                                            </flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">top</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Side of the semicircle
                                                on a horizontal bridge.</flux:table.cell>
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
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Canvas settings provide line thickness, node size, text-width presets, connector defaults and
            path-tone rendering. color and dev-mode can be overridden at this component; side, direction, stem-length,
            arc-radius and bridge-length use the defaults listed above. pathTone is configured on the canvas, not in an
            action array.</flux:text>
        <flux:text>CASE action widths are normalized into a common route span. Grouped entries add fusion space. Fusion
            starts with half arc-radius; spacing can enlarge its radius and remove a short compensating stem. There is
            no collision avoidance for a nested block: reserve space explicitly with the following CASE stemLength and
            author the return connection. These calculations do not rewrite your source props.</flux:text>
    </flux:callout>
    <flux:callout
        class="min-w-0 space-y-3"
        color="amber"
        icon="cable"
    >
        <flux:callout.heading>Connections</flux:callout.heading>
        <flux:text>Replace {id}, {key} and {entryKey} with your authored IDs. Anchors belong to the current canvas and
            become available after their component renders. Read coordinates through AnchorRegistry only after rendering
            the referenced component.</flux:text>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>Prop / path</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Default / fallback</flux:table.column>
                <flux:table.column>Meaning and limits</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.anchorNode-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Input</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Resolved attach-to or anchor-start.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Final output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Connect the next independent step here, after
                        DEFAULT or bypass.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.case.{key}.anchorNode-action
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Action entry</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">After CASE selection / fusion. With incoming
                        fall-through, resolves to its join point on bridge-in.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.case.{key}.anchorNode-end
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Action route output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Connect a nested block here when
                        actionLabel.return=false.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.case.{key}.anchorNode-return
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Following route on outer output rail
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Target for an explicit nested return. Exists
                        for CASE routes, not the final DEFAULT.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">
                        {id}.case.{key}.entries.{entryKey}.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Grouped entry bridge end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Input handed to fusion; not the shared action
                        output.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.case.{key}.fusion.anchorNode-end
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Shared fusion output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Available when the group contains at least two
                        entries.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.case.default.anchorNode-end
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">DEFAULT route output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Only when DEFAULT is enabled. Prefer the
                        stable public {id}.anchorNode-end for continuation.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.bypass.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Bypass output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Only when case-default=false. Continuation
                        still uses {id}.anchorNode-end.</flux:table.cell>
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
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>Invalid side/direction, missing CASEs, duplicate or reserved route keys, duplicate group keys, empty
            action text, entries[].stemLength, entryStemLength below 3rem, and fallThrough combined with return=false
            raise errors. Fall-through also rejects insufficient routing space. Unknown nested array keys are not
            generally rejected: only the fields consumed by this component chain have an effect.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
        .../props-and-connections/strang/flow-switch-case.blade.php</flux:field>
    <flux:callout color="green" icon="brackets" class="min-w-0 space-y-3">
        <flux:callout.heading>Array props: explicit entry detours</flux:callout.heading>
        <flux:text>stemLength remains the complete vertical distance. The middle stem is stemLength − beforeLength − afterLength − 4 × arcRadius. Insufficient height, negative lengths, invalid sides/directions and unknown detour keys are rejected; the component does not enlarge or shorten the entry to make it fit. Omit entryDetour to retain the ordinary straight entry. The existing entry.anchorNode-end is preserved.</flux:text>
        <flux:table>
            <flux:table.columns><flux:table.column>Prop / path</flux:table.column><flux:table.column>Default</flux:table.column><flux:table.column>Meaning</flux:table.column></flux:table.columns>
            <flux:table.rows>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour<br>case-default.entryDetour</flux:table.cell>
                <flux:table.cell>null</flux:table.cell>
                <flux:table.cell class="whitespace-normal">Optional explicit entry detour for a single CASE or DEFAULT. Grouped entries reject this option. Uses paths.stem-detour; no automatic collision detection.</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.side<br>case-default.entryDetour.side</flux:table.cell>
                <flux:table.cell>right</flux:table.cell>
                <flux:table.cell class="whitespace-normal">Physical side of the detour, independent of SWITCH side: left or right.</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.bridgeLength<br>case-default.entryDetour.bridgeLength</flux:table.cell>
                <flux:table.cell>4rem</flux:table.cell>
                <flux:table.cell class="whitespace-normal">Horizontal bridge in both turns. Total horizontal offset is bridgeLength + 2 × arcRadius.</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.arcRadius<br>case-default.entryDetour.arcRadius</flux:table.cell>
                <flux:table.cell>2rem</flux:table.cell>
                <flux:table.cell class="whitespace-normal">Positive radius for all four arcs.</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.beforeLength<br>case-default.entryDetour.beforeLength</flux:table.cell>
                <flux:table.cell>0rem</flux:table.cell>
                <flux:table.cell class="whitespace-normal">Straight entry length before the outward turn.</flux:table.cell>
            </flux:table.row>
            <flux:table.row>
                <flux:table.cell class="whitespace-normal">cases[].entryDetour.afterLength<br>case-default.entryDetour.afterLength</flux:table.cell>
                <flux:table.cell>2rem</flux:table.cell>
                <flux:table.cell class="whitespace-normal">Straight entry length after the inward turn, ending at the original CASE anchor with its label.</flux:table.cell>
            </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
</section>
