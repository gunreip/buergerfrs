{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/02-trunk.blade.php --}}

<flux:callout
    color="emerald"
    icon="git-commit-horizontal"
>
    <flux:callout.heading>
        {{ __('3. Trunk as milestone timeline') }}
    </flux:callout.heading>
    <flux:callout.text>
        @php
            $trunkHardcopyPath = base_path(
                'packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/project-roadmap-01-trunk.png',
            );
            $trunkHardcopySrc = is_file($trunkHardcopyPath)
                ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($trunkHardcopyPath))
                : null;
        @endphp

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-6 space-y-3 hyphens-auto text-sm leading-6">
                    <p>
                        {{ __('The trunk is deliberately explicit: the number of stems, the stem lengths, start and end labels, and each milestone label are authored directly at the component call. That keeps this sample useful as a real authoring reference instead of hiding the layout behind helper variables.') }}
                    </p>
                    <p>
                        {{ __('The important pattern is that the trunk owns the timeline rhythm. Branches and merges attach to registered trunk nodes, but they do not redefine what the central project timeline means.') }}
                    </p>
                </div>

                <div class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100"><code
                        class="block min-w-[56rem]"
                    ><span class="block"><span class="text-zinc-400">&lt;</span><span
                                class="text-sky-300">x-translation-workbench::ui.tw-graph.strang.trunk</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- used &#x40;props in this sample
                            --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >id</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"roadmap.center.1.timeline"</span></span><span
                                class="text-zinc-500"
                            >default: generated from graph id</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:stem-count</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"8"</span></span><span class="text-zinc-500">default:
                                10</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >start-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"4rem"</span></span><span class="text-zinc-500">default:
                                stem-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:stem-lengths</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"[1 =&gt; '2rem', 2 =&gt; '8rem', ...]"</span></span><span
                                class="text-zinc-500"
                            >default: []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >end-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"4rem"</span></span><span class="text-zinc-500">default:
                                stem-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:start-label</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"['text' =&gt; [...], 'width' =&gt;
                                    'halfLong']"</span></span><span class="text-zinc-500">default: null</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:end-label</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"['text' =&gt; [...], 'width' =&gt;
                                    'halfLong']"</span></span><span class="text-zinc-500">default: null</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:start-node-labels</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100"
                                >"['left' =&gt; [...], 'right' =&gt; [...]]"</span></span><span
                                class="text-zinc-500">default: []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:node-labels</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"[2 =&gt; ['left' =&gt; [...], 'right' =&gt;
                                    [...]]]"</span></span><span class="text-zinc-500">default: []</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- potential &#x40;props for this tag
                            x-translation-workbench::ui.tw-graph.strang.trunk --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >direction="bottom-top"</span><span class="text-zinc-500">default: bottom-top</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >color="emerald"</span><span class="text-zinc-500">default: inherited graph
                                color</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >stem-length="5rem"</span><span class="text-zinc-500">default: graph
                                stem-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >end-cap-length="1.75rem"</span><span class="text-zinc-500">default:
                                cap-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >start-label-space="3rem"</span><span class="text-zinc-500">default: 3rem</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:start-shift-enabled="true"</span><span class="text-zinc-500">default: config</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >start-shift-length="10rem"</span><span class="text-zinc-500">default: config</span></span>
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
                            {{ __('Stem length overrides') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Use stem-lengths when the trunk rhythm needs local spacing. Missing entries keep the inherited stem-length default.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[28rem]"><span class="text-emerald-300">:stem-lengths</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">1 =&gt; '2rem',</span> <span
                                        class="text-zinc-500"
                                    >// shorter opening rhythm</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">2 =&gt; '8rem',</span> <span
                                        class="text-zinc-500"
                                    >// room for attached branches</span></span>
                                <span class="block ps-4"><span class="text-zinc-500">// 3 missing: uses stem-length
                                        default</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Start label') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('The start label describes the whole trunk. It is centered at the trunk start cap and uses the normal text-label options.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[28rem]"><span class="text-emerald-300">:start-label</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">'text' =&gt; ['Project
                                        roadmap', '2026 initiative'],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'width' =&gt;
                                        'halfLong',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'align' =&gt;
                                        'center',</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('End label') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('The end label closes the trunk with a summary. It should not repeat every node detail; it states the terminal roadmap state.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[28rem]"><span class="text-emerald-300">:end-label</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">'text' =&gt; ['Roadmap
                                        closed', 'v1.0 shipped and monitored'],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'width' =&gt;
                                        'halfLong',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'align' =&gt;
                                        'center',</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Start node labels') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Start-node labels attach to the first trunk anchor. Use left and right explicitly so width, alignment, and color stay local to each label.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[32rem]"><span
                                    class="text-emerald-300">:start-node-labels</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">'left' =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'text' =&gt; ['Discovery',
                                        'problem framing'],</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'align' =&gt;
                                        'right',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'right' =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'text' =&gt; 'Stakeholders
                                        agree on goals.',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'width' =&gt;
                                        'long',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'justify' =&gt;
                                        true,</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2 xl:col-span-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Node labels') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Node labels are keyed by trunk anchor number. Each anchor may carry a left label, a right label, or both. The label configuration remains explicit at the place where the label is authored.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[40rem]"><span class="text-emerald-300">:node-labels</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">2 =&gt; [</span> <span
                                        class="text-zinc-500"
                                    >// trunk.center.1.anchorNode-2</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'left' =&gt; [</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'text' =&gt; ['M1',
                                        'Architecture baseline'],</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'width' =&gt;
                                        'default',</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'align' =&gt;
                                        'right',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">],</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'right' =&gt; [</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'text' =&gt; 'Delivery scope
                                        is stable enough for parallel work.',</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'width' =&gt;
                                        'long',</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'align' =&gt;
                                        'left',</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'justify' =&gt;
                                        true,</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>
                </div>

            </div>
            <div class="space-y-3 text-sm leading-6">
                @if ($trunkHardcopySrc)
                    <figure class="space-y-2">
                        <img
                            class="w-full rounded-lg border-zinc-400 object-contain dark:border-zinc-800"
                            src="{{ $trunkHardcopySrc }}"
                            alt="{{ __('Project roadmap trunk hardcopy') }}"
                        >
                        <figcaption class="text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                            {{ __('Trunk rhythm, milestone labels, and the central canvas defaults used by the roadmap graph.') }}
                        </figcaption>
                    </figure>
                @endif
            </div>
        </div>
    </flux:callout.text>
</flux:callout>
