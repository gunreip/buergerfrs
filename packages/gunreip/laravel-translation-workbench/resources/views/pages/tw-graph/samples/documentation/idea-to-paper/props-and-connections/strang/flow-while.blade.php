<section class="mt-4 min-w-0 space-y-4">
    <flux:heading size="lg">strang.flow-while</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-while" />
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>Reading this reference</flux:callout.heading>
        <flux:callout.text>A pre-test loop: TRUE executes the body and retests the condition; FALSE leaves the loop. The built-in action may close the body directly or expose it for additional independently authored components. flow-step owns the condition geometry; paths.loop composes the closed route from the shared segments and LabelBridge geometry. It runs bottom-to-top; side mirrors the body and return horizontally.</flux:callout.text>
    </flux:callout>
    <flux:callout class="min-w-0" color="red" icon="book-open-check">
        <flux:callout.heading>Public component props</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns><flux:table.column>Prop / anchor</flux:table.column><flux:table.column>Default</flux:table.column><flux:table.column>Purpose</flux:table.column></flux:table.columns>
            <flux:table.rows>
                <flux:table.row><flux:table.cell class="whitespace-normal">side</flux:table.cell><flux:table.cell class="whitespace-normal">left</flux:table.cell><flux:table.cell class="whitespace-normal">Physical side of the body and return: left or right. The FALSE exit stays on the main axis.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">attach-to</flux:table.cell><flux:table.cell class="whitespace-normal">null</flux:table.cell><flux:table.cell class="whitespace-normal">Connect the loop after initialization. The return rejoins here without repeating initialization.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">condition-label</flux:table.cell><flux:table.cell class="whitespace-normal">WHILE pending items?</flux:table.cell><flux:table.cell class="whitespace-normal">Condition text, width, align and badge color.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">action-label</flux:table.cell><flux:table.cell class="whitespace-normal">Process next item</flux:table.cell><flux:table.cell class="whitespace-normal">Action inside the horizontal body bridge. This example explicitly removes an item to make progress.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">condition-label.beforeLength</flux:table.cell><flux:table.cell class="whitespace-normal">2rem</flux:table.cell><flux:table.cell class="whitespace-normal">Stem before the condition.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">condition-label.labelGap</flux:table.cell><flux:table.cell class="whitespace-normal">4rem</flux:table.cell><flux:table.cell class="whitespace-normal">Space reserved for the condition text.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">condition-label.afterLength</flux:table.cell><flux:table.cell class="whitespace-normal">2rem</flux:table.cell><flux:table.cell class="whitespace-normal">Stem between the condition and the TRUE/FALSE split.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">arc-radius</flux:table.cell><flux:table.cell class="whitespace-normal">inherited / 2.75rem</flux:table.cell><flux:table.cell class="whitespace-normal">Common radius of the four loop corners. Inherits canvas arc-radius unless explicitly set.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">action-label.beforeLength</flux:table.cell><flux:table.cell class="whitespace-normal">4rem</flux:table.cell><flux:table.cell class="whitespace-normal">Bridge before the body action.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">action-label.afterLength</flux:table.cell><flux:table.cell class="whitespace-normal">4rem</flux:table.cell><flux:table.cell class="whitespace-normal">Bridge after the body action.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">stem-length</flux:table.cell><flux:table.cell class="whitespace-normal">4rem</flux:table.cell><flux:table.cell class="whitespace-normal">FALSE stem to the public exit. The return stem follows the condition height automatically.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">true-label</flux:table.cell><flux:table.cell class="whitespace-normal">TRUE</flux:table.cell><flux:table.cell class="whitespace-normal">Information at the body entry; text, width, align, side and connector settings.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">false-label</flux:table.cell><flux:table.cell class="whitespace-normal">FALSE</flux:table.cell><flux:table.cell class="whitespace-normal">Information at the loop exit; text, width, align, side and connector settings.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">color</flux:table.cell><flux:table.cell class="whitespace-normal">inherited / zinc</flux:table.cell><flux:table.cell class="whitespace-normal">Loop paths and labels; explicit label colors remain configurable.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">anchorNode-start</flux:table.cell><flux:table.cell class="whitespace-normal">connection</flux:table.cell><flux:table.cell class="whitespace-normal">Initialization and return meet before the condition.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal">anchorNode-end</flux:table.cell><flux:table.cell class="whitespace-normal">connection</flux:table.cell><flux:table.cell class="whitespace-normal">FALSE exit. Attach the next independent action here.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>id</flux:table.cell><flux:table.cell>required</flux:table.cell><flux:table.cell class="whitespace-normal">Public authoring root ID; retained in DEV tooltips.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>anchor-start</flux:table.cell><flux:table.cell>x=0rem, y=0rem</flux:table.cell><flux:table.cell class="whitespace-normal">Used when attach-to is absent.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>dev-mode</flux:table.cell><flux:table.cell>inherited</flux:table.cell><flux:table.cell class="whitespace-normal">Show DEV geometry and node counters.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>z-index</flux:table.cell><flux:table.cell>20</flux:table.cell><flux:table.cell class="whitespace-normal">Layer for the loop paths and labels.</flux:table.cell></flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Separate bridge after the TRUE arc. Its end owns the TRUE label; the return bridge grows by the same length.</flux:table.cell>
                </flux:table.row>
                            <flux:table.row>
                    <flux:table.cell class="whitespace-normal">counter-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">1 (flow-while)</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">First DEV counter of this WHILE. The open loop uses six
                        numbers, a closed loop ten. Steps, manual arcs and independent returns specify their following
                        counter values explicitly in this example.</flux:table.cell>
                </flux:table.row>
</flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout color="green" icon="brackets">
        <flux:callout.heading>Array props</flux:callout.heading>
        <flux:text class="mt-3">anchor-start.x and anchor-start.y are rem coordinates. condition-label owns beforeLength (2rem), labelGap (4rem) and afterLength (2rem). action-label owns beforeLength (4rem) and afterLength (4rem). Both accept text (string or up to three lines), width (half, default, halfLong, long), align (left, center, right), color, badgeColor, badge and maxLines. action-label.return defaults to true. Set it to false to omit the automatic return and attach further components to body.anchorNode-end; finish with paths.loop-return targeting anchorNode-return. action-label.color colors the entire TRUE route from true.arc-in through body.arc-out; the condition and return keep the loop color. false-label.color colors the FALSE stem, its end Dot and DEV counter; without this key they inherit the loop color. The action width also determines the horizontal return span.</flux:text>
        <flux:text class="mt-3">true-label and false-label accept text, width, align, color, badgeColor, badge, maxLines, side (left, right, top, bottom), connectorLength and connectorGap. true-label.anchor selects bridge (default) or condition. The latter reuses the condition end Dot; the unlabelled entry bridge end then becomes a joint-arrow. side is relative to this anchor. TRUE defaults to top; FALSE defaults to the side opposite the body. These labels explain the route and do not introduce actions.</flux:text>
    </flux:callout>
    <flux:callout color="sky" icon="variable">
        <flux:callout.heading>Geometry</flux:callout.heading>
        <flux:callout.text>Return stem length (closed basic loop) = condition-label.beforeLength + condition-label.labelGap + condition-label.afterLength. Body action span = action-label.beforeLength + action-label width + action-label.afterLength. The lower return bridge adds true-bridge-length, matching the separate TRUE entry bridge. Its end Dot owns the TRUE label; the preceding arc ends in a joint-arrow. Total horizontal displacement adds two arc radii. The four corners share arc-radius; the two reverse turns lead back to the exact start anchor. stem-length controls only the independent FALSE exit.</flux:callout.text>
    </flux:callout>
    <flux:callout color="amber" icon="cable">
        <flux:callout.heading>Connections</flux:callout.heading>
        <flux:callout.text>anchorNode-start and return.anchorNode-end coincide before the condition. condition.anchorNode-end is the TRUE/FALSE split. true.bridge.anchorNode-end connects the TRUE entry bridge to the action bridge and owns the TRUE information label. body.anchorNode-end is the downward continuation after the built-in action. anchorNode-return is the re-entry target before the condition. return.anchorNode-end exists only when the automatic return is rendered. anchorNode-end is exclusively the FALSE exit. Initialization attaches before the start; the next independent action attaches to the exit. The body may execute zero times.</flux:callout.text>
    </flux:callout>
    <flux:callout color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation</flux:callout.heading>
        <flux:callout.text>Side must be left or right. Length props must resolve to positive rem values (including resolvable calc expressions). An unresolved attach-to is an error. Dimensions are not silently reduced to fit the example. Removed global before-length, after-length, label-gap, bridge-length and bridge-out-length are rejected instead of being accepted as unused attributes. The author must leave enough label space; this component does not automatically avoid collisions or prove termination.</flux:callout.text>
    </flux:callout>
</section>
