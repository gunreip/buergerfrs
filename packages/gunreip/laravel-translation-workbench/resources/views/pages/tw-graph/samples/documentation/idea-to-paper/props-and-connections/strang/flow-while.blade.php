<section class="mt-4 min-w-0 space-y-4">
    <flux:heading size="lg">{{ __('strang.flow-while — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-while" />
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('Reading this reference') }}</flux:callout.heading>
        <flux:callout.text>{{ __('A pre-test loop: TRUE executes the body and retests the condition; FALSE leaves the loop. The built-in action may close the body directly or expose it for additional independently authored components. flow-step owns the condition geometry; paths.loop composes the closed route from the shared segments and LabelBridge geometry. It runs bottom-to-top; side mirrors the body and return horizontally.') }}</flux:callout.text>
    </flux:callout>
    <flux:callout class="min-w-0" color="red" icon="book-open-check">
        <flux:callout.heading>{{ __('Public component props') }}</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default') }}</flux:table.column><flux:table.column>{{ __('Purpose') }}</flux:table.column></flux:table.columns>
            <flux:table.rows>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string enum</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>left</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Physical side of the body and return: left or right. The FALSE exit stays on the main axis.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>attach-to</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string | null</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>null</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Connect the loop after initialization. The return rejoins here without repeating initialization.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>condition-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>WHILE pending items?</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Condition text, width, align and badge color.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>action-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>Process next item</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Action inside the horizontal body bridge. This example explicitly removes an item to make progress.') }}</flux:table.cell></flux:table.row>



                <flux:table.row><flux:table.cell class="whitespace-normal"><code>arc-radius</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length string | null</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>inherited / 2.75rem</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Common radius of the four loop corners. Inherits canvas arc-radius unless explicitly set.') }}</flux:table.cell></flux:table.row>


                <flux:table.row><flux:table.cell class="whitespace-normal"><code>stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length string</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>4rem</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('FALSE stem to the public exit. The return stem follows the condition height automatically.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>true-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>TRUE</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Information at the body entry; text, width, align, side and connector settings.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>false-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>FALSE</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Information at the loop exit; text, width, align, side and connector settings.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell class="whitespace-normal"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>color name | null</code></flux:table.cell><flux:table.cell class="whitespace-normal"><code>inherited / zinc</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Loop paths and labels; explicit label colors remain configurable.') }}</flux:table.cell></flux:table.row>


                <flux:table.row><flux:table.cell><code>id</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string</code></flux:table.cell><flux:table.cell><code>required</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Public authoring root ID; retained in DEV tooltips.') }}</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell><code>anchor-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell><flux:table.cell><code>x=0rem, y=0rem</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Used when attach-to is absent.') }}</flux:table.cell></flux:table.row>

                <flux:table.row><flux:table.cell><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>integer</code></flux:table.cell><flux:table.cell><code>20</code></flux:table.cell><flux:table.cell class="whitespace-normal">{{ __('Layer for the loop paths and labels.') }}</flux:table.cell></flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>true-bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>2rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Separate bridge after the TRUE arc. Its end owns the TRUE label; the return bridge grows by the same length.') }}</flux:table.cell>
                </flux:table.row>
                            <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>1 (flow-while)</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('First DEV counter passed into the condition and loop route. The number of subsequent markers depends on the open or closed body and its labels.') }}</flux:table.cell>
                </flux:table.row>
</flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout color="green" icon="brackets">
        <flux:callout.heading>{{ __('Array props') }}</flux:callout.heading>
        <flux:text class="mt-3">{{ __('anchor-start.x and anchor-start.y are rem coordinates. condition-label owns beforeLength (2rem), labelGap (4rem) and afterLength (2rem). action-label owns beforeLength (4rem) and afterLength (4rem). Both accept text (string or array; the step condition supports up to five authored lines, while action captions default to maxLines=3), width (half, default, halfLong, long), align (left, center, right), color, badgeColor, badge and maxLines. action-label.return defaults to true. Set it to false to omit the automatic return and attach further components to body.anchorNode-end; finish with paths.loop-return targeting anchorNode-return. action-label.color colors the entire TRUE route from true.arc-in through body.arc-out; the condition and return keep the loop color. false-label.color colors the FALSE stem, its end Dot and DEV counter; without this key they inherit the loop color. The action width also determines the horizontal return span.') }}</flux:text>
        <flux:text class="mt-3">{{ __('true-label and false-label accept text, width, align, color, badgeColor, badge, maxLines, side (left, right, top, bottom), connectorLength and connectorGap. true-label.anchor selects bridge (default) or condition. The latter reuses the condition end Dot; the unlabelled entry bridge end then becomes a joint-arrow. side is relative to this anchor. TRUE defaults to top; FALSE defaults to the side opposite the body. These labels explain the route and do not introduce actions.') }}</flux:text>

        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>anchor-start</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default / fallback') }}</flux:table.column><flux:table.column>{{ __('Meaning and limits') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor-start.x</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal coordinate when attach-to is absent.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor-start.y</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Vertical coordinate when attach-to is absent.') }}</flux:table.cell>
                </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>condition-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default / fallback') }}</flux:table.column><flux:table.column>{{ __('Meaning and limits') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.text</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[WHILE pending items?]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Condition content. Up to five authored lines; additional lines produce a mismatch diagnostic.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.width</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>default</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width preset: half, default, halfLong or long.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.align</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.badgeColor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>loop color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Condition badge color.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.badge</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render a badge around the text.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Condition caption placement.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.offset</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>canvas label offset / 0.75rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Distance from the step axis.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.justify</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Justify caption text.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.beforeLength</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Stem before the condition.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.labelGap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit space for condition content. WHILE supplies this value instead of the automatic step gap.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label.afterLength</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Stem after the condition.') }}</flux:table.cell>
                </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>action-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default / fallback') }}</flux:table.column><flux:table.column>{{ __('Meaning and limits') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.text</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[Process next item]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Body action caption.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.width</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>default</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width preset; contributes to the body and return span.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.align</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>loop color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Entire TRUE route color, from entry arc to body exit arc.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.badgeColor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>action color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption badge color.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.badge</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render a badge around the text.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.justify</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Justify caption text.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.maxLines</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Displayed caption line limit.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.beforeLength</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Bridge before the action.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.afterLength</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Bridge after the action.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.return</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Close the loop automatically. False exposes body.anchorNode-end for independently authored actions and a return path.') }}</flux:table.cell>
                </flux:table.row>
                        <flux:table.row>
                                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.lineJumps[].over</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">{{ __('ID of an existing perpendicular stem/bridge intersecting this line.') }}</flux:table.cell>
                                </flux:table.row>
<flux:table.row>
                                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.lineJumps[].radius</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top"><code>0.5rem</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Must fit between endpoints and exceed half the line thickness.') }}</flux:table.cell>
                                </flux:table.row>
<flux:table.row>
                                    <flux:table.cell class="whitespace-normal align-top"><code>action-label.lineJumps[].side</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top"><code>top | bottom</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top"><code>top</code></flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Side of the semicircle relative to its owning line.') }}</flux:table.cell>
                                </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>true-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default / fallback') }}</flux:table.column><flux:table.column>{{ __('Meaning and limits') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.text</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[TRUE]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Informational caption; it does not introduce an action.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.width</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>half</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption width preset.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.align</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>loop color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption color; false-label.color also colors the exit stem and marker.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.badgeColor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>label color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit badge color.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.badge</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render a badge around the caption.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.maxLines</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Displayed caption line limit.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>top</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Placement: left, right, top or bottom.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.connectorLength</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>canvas connector length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connector length from the anchor.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.connectorGap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>canvas connector gap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and caption.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-label.anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bridge</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('bridge attaches at the TRUE bridge end; condition attaches at the condition end.') }}</flux:table.cell>
                </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>false-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default / fallback') }}</flux:table.column><flux:table.column>{{ __('Meaning and limits') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.text</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[FALSE]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Informational caption; it does not introduce an action.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.width</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>half</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption width preset.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.align</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>loop color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption color; false-label.color also colors the exit stem and marker.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.badgeColor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>label color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit badge color.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.badge</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render a badge around the caption.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.maxLines</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Displayed caption line limit.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>opposite body side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Placement: left, right, top or bottom.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.connectorLength</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>canvas connector length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connector length from the anchor.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-label.connectorGap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>canvas connector gap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Gap between connector and caption.') }}</flux:table.cell>
                </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
        <pre class="overflow-x-auto"><code>:action-label="['text' =&gt; ['Process item'], 'beforeLength' =&gt; '4rem', 'afterLength' =&gt; '4rem', 'return' =&gt; false]"
:true-label="['text' =&gt; ['TRUE'], 'anchor' =&gt; 'condition', 'side' =&gt; 'right']"</code></pre>
    </flux:callout>
    <flux:callout color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Return stem length (closed basic loop) = condition-label.beforeLength + condition-label.labelGap + condition-label.afterLength. Body action span = action-label.beforeLength + action-label width + action-label.afterLength. The lower return bridge adds true-bridge-length, matching the separate TRUE entry bridge. Its end Dot owns the TRUE label; the preceding arc ends in a joint-arrow. Total horizontal displacement adds two arc radii. The four corners share arc-radius; the two reverse turns lead back to the exact start anchor. stem-length controls only the independent FALSE exit.') }}</flux:callout.text>
    </flux:callout>
    <flux:callout color="amber" icon="cable">
        <flux:callout.heading>{{ __('Connections') }}</flux:callout.heading>
        <flux:callout.text>{{ __('anchorNode-start and return.anchorNode-end coincide before the condition. condition.anchorNode-end is the TRUE/FALSE split. true.bridge.anchorNode-end connects the TRUE entry bridge to the action bridge and owns the TRUE information label. body.anchorNode-end is the downward continuation after the built-in action. anchorNode-return is the re-entry target before the condition. return.anchorNode-end exists only when the automatic return is rendered. anchorNode-end is exclusively the FALSE exit. Initialization attaches before the start; the next independent action attaches to the exit. The body may execute zero times.') }}</flux:callout.text>
    </flux:callout>
    <flux:callout color="fuchsia" icon="infinity">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Side must be left or right. Length props must resolve to positive rem values (including resolvable calc expressions). An unresolved attach-to is an error. Dimensions are not silently reduced to fit the example. Removed global before-length, after-length, label-gap, bridge-length and bridge-out-length are rejected instead of being accepted as unused attributes. The author must leave enough label space; this component does not automatically avoid collisions or prove termination.') }}</flux:callout.text>
    </flux:callout>
</section>
