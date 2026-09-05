{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/04-release-and-end.blade.php --}}

<flux:callout
    color="amber"
    icon="merge"
>
    <flux:callout.heading>
        {{ __('5. Release merge and terminal scope') }}
    </flux:callout.heading>
    <flux:callout.text>
        @php
            $mergeLeftHardcopyPath = base_path(
                'packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/project-roadmap-03-merge-left-01.png',
            );
            $mergeLeftHardcopySrc = is_file($mergeLeftHardcopyPath)
                ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($mergeLeftHardcopyPath))
                : null;
        @endphp

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-6 space-y-3 hyphens-auto text-sm leading-6">
                    <p>
                        {{ __('The release train is modeled with a merge strang because several streams become one accepted release scope. This is intentionally different from a branch return: the visual message is consolidation, not just a side path reconnecting.') }}
                    </p>
                    <p>
                        {{ __('In this sample, merge-left represents a release bundle that flows into the roadmap at the release-candidate milestone. Its start label describes the bundle, while node labels explain the contributing streams and final acceptance.') }}
                    </p>
                    <p>
                        {{ __('Postponed mobile scope is closed separately with branch-end. The end cap makes the branch terminal while the centered end label explains why the side path does not merge into v1.0.') }}
                    </p>
                </div>

                <div class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100"><code
                        class="block min-w-[56rem]"
                    ><span class="block"><span class="text-zinc-400">&lt;</span><span
                                class="text-sky-300">x-translation-workbench::ui.tw-graph.strang.merge-left</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- used &#x40;props in this sample
                            --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >id</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"roadmap.left.1.release-train"</span></span><span
                                class="text-zinc-500"
                            >default: generated from graph id</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >attach-to</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"strang.trunk.node.5"</span></span><span
                                class="text-zinc-500"
                            >default: anchor-start</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >color</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"amber"</span></span><span class="text-zinc-500">default:
                                inherited graph color</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >bridge-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"14rem"</span></span><span class="text-zinc-500">default:
                                graph bridge-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >stem-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"4rem"</span></span><span class="text-zinc-500">default:
                                graph stem-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:start-label</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"['text' =&gt; [...], 'width' =&gt;
                                    'halfLong']"</span></span><span class="text-zinc-500">default: null</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:node-labels</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"[1 =&gt; ['right' =&gt; [...]], 5 =&gt; ['left' =&gt;
                                    [...]]]"</span></span><span class="text-zinc-500">default: []</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- potential &#x40;props for this tag
                            x-translation-workbench::ui.tw-graph.strang.merge-left --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:anchor-start="['x' =&gt; '0rem', 'y' =&gt; '0rem']"</span><span
                                class="text-zinc-500">default: 0/0 anchor</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:stem-continuation="[1 =&gt; ['4rem']]"</span><span class="text-zinc-500">default:
                                []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:extension-count="2"</span><span class="text-zinc-500">default: 0</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >extension-start-length="2.75rem"</span><span class="text-zinc-500">default:
                                arc-size</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >extension-stem-length="4rem"</span><span class="text-zinc-500">default:
                                stem-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >extension-bridge-length="14rem"</span><span class="text-zinc-500">default:
                                bridge-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >extension-arc-size="2.75rem"</span><span class="text-zinc-500">default:
                                arc-size</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:extension-node-labels="[...]"</span><span class="text-zinc-500">default: []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:counter-start="1"</span><span class="text-zinc-500">default: 1</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:dev-mode="$projectRoadmapDev"</span><span class="text-zinc-500">default: inherited
                                dev</span></span>
                        <span class="block"><span class="text-zinc-400">/&gt;</span></span></code></div>

                <div class="mt-4 grid gap-4 xl:grid-cols-2">
                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Merge start label') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('The start label identifies the incoming bundle. It belongs to the merge source, not to the trunk milestone where the merge attaches.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[30rem]"><span class="text-emerald-300">:start-label</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">'text' =&gt; ['Release train',
                                        'feature bundle A'],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'width' =&gt;
                                        'halfLong',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'align' =&gt;
                                        'center',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'color' =&gt;
                                        'amber',</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Merge node labels') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Node labels describe the incoming streams and the accepted result. For left merges, right-side labels usually sit inside the merge path and left-side labels can mark the final attach area.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[34rem]"><span class="text-emerald-300">:node-labels</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">1 =&gt; ['right' =&gt; ['text'
                                        =&gt; ['Design system', 'merged']]],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">2 =&gt; ['right' =&gt; ['text'
                                        =&gt; ['API platform', 'merged']]],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">5 =&gt; ['left' =&gt; ['text'
                                        =&gt; ['v1.0 scope', 'accepted']]],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Merge stem continuation') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Stem continuations can add vertical room before the merge bends toward the trunk. Use them when labels need a clearer rhythm than the default single stem.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[30rem]"><span
                                    class="text-amber-300">:stem-continuation</span><span
                                    class="text-zinc-400">=</span><span class="text-amber-100">"[</span>
                                <span class="block ps-4"><span class="text-amber-100">1 =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-amber-100">'4rem',</span></span>
                                <span class="block ps-8"><span class="text-amber-100">'right' =&gt; ['text' =&gt;
                                        ['additional source fact']],</span></span>
                                <span class="block ps-4"><span class="text-amber-100">],</span></span>
                                <span class="text-amber-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Merge extensions') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Merge extensions add further incoming records before the final merge path. They inherit merge defaults but can override extension stem, bridge, arc, and label values per extension.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[34rem]"><span
                                    class="text-amber-300">:extension-count</span><span
                                    class="text-zinc-400">=</span><span class="text-amber-100">"2"</span>
                                <span class="block"><span class="text-amber-300">:extension-node-labels</span><span
                                        class="text-zinc-400"
                                    >=</span><span class="text-amber-100">"[</span></span>
                                <span class="block ps-4"><span class="text-amber-100">1 =&gt; [1 =&gt; ['right' =&gt;
                                        ['text' =&gt; 'Origin A']]],</span></span>
                                <span class="block ps-4"><span class="text-amber-100">2 =&gt; [1 =&gt; ['right' =&gt;
                                        ['text' =&gt; 'Origin B']]],</span></span>
                                <span class="text-amber-100">]"</span></code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3 text-sm leading-6">
                @if ($mergeLeftHardcopySrc)
                    <figure class="space-y-2">
                        <img
                            class="w-full rounded-lg border-zinc-400 object-contain dark:border-zinc-800"
                            src="{{ $mergeLeftHardcopySrc }}"
                            alt="{{ __('Project roadmap merge-left hardcopy') }}"
                        >
                        <figcaption class="text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                            {{ __('The left release-train merge path with source labels and final accepted release scope.') }}
                        </figcaption>
                    </figure>
                @endif
            </div>
        </div>
    </flux:callout.text>
</flux:callout>
