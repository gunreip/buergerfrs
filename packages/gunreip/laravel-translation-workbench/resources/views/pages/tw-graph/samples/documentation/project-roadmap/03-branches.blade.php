{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/03-branches.blade.php --}}

<flux:callout
    color="sky"
    icon="git-branch"
>
    <flux:callout.heading>
        {{ __('4. Feature branches') }}
    </flux:callout.heading>
    <flux:callout.text>
        @php
            $branchLeftHardcopyPath = base_path(
                'packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/project-roadmap-02-branch-left-01.png',
            );
            $branchLeftHardcopySrc = is_file($branchLeftHardcopyPath)
                ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($branchLeftHardcopyPath))
                : null;
        @endphp

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-6 space-y-3 hyphens-auto text-sm leading-6">
                    <p>
                        {{ __('Feature tracks are rendered as side branches. Each branch keeps its own visual decisions local: entry stem, bridge length, step label, continuation stems, labels, and optional return path.') }}
                    </p>
                    <ul class="list-disc space-y-1 pl-5">
                        <li><code>roadmap.left.1.design-system</code>
                            {{ __('branches left from the architecture baseline and returns after component work.') }}
                        </li>
                        <li><code>roadmap.right.1.api-platform</code>
                            {{ __('branches right from the same milestone and stays readable by using its own bridge length.') }}
                        </li>
                        <li><code>roadmap.right.2.mobile-companion</code>
                            {{ __('branches later and ends as postponed scope.') }}</li>
                    </ul>
                    <p>
                        {{ __('The step inside a branch is used for shared branch-level context, such as a decision or status change. Node labels remain reserved for concrete branch facts.') }}
                    </p>
                </div>

                <div class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100"><code
                        class="block min-w-[56rem]"
                    ><span class="block"><span class="text-zinc-400">&lt;</span><span
                                class="text-sky-300">x-translation-workbench::ui.tw-graph.strang.branch-left</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- used &#x40;props in this sample
                            --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >id</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"roadmap.left.1.design-system"</span></span><span
                                class="text-zinc-500"
                            >default: generated from graph id</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >attach-to</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"strang.trunk.node.2"</span></span><span
                                class="text-zinc-500"
                            >default: anchor-start</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >color</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"sky"</span></span><span class="text-zinc-500">default:
                                inherited graph color</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >entry-stem-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100"
                                >"0.5rem"</span></span><span class="text-zinc-500">default: 0rem</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >bridge-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"28rem"</span></span><span class="text-zinc-500">default:
                                graph bridge-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >stem-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"4rem"</span></span><span class="text-zinc-500">default:
                                graph stem-length</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:node-labels</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"[3 =&gt; ['left' =&gt; [...]]]"</span></span><span
                                class="text-zinc-500"
                            >default: []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:step</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"['stepLabel' =&gt; [...]]"</span></span><span
                                class="text-zinc-500"
                            >default: null</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:stem-continuation</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100"
                                >"[1 =&gt; [...], 2 =&gt; [...]]"</span></span><span class="text-zinc-500">default:
                                []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300"
                                >:branch-return</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"[1 =&gt; ['attachTo' =&gt; 'stem.2']]"</span></span><span
                                class="text-zinc-500"
                            >default: []</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- potential &#x40;props for this tag
                            x-translation-workbench::ui.tw-graph.strang.branch-left --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:anchor-start="['x' =&gt; '0rem', 'y' =&gt; '0rem']"</span><span
                                class="text-zinc-500">default: 0/0 anchor</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:bridge-continuation="[1 =&gt; ['12rem']]"</span><span class="text-zinc-500">default: one
                                bridge</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >:branch-extension="[...]"</span><span class="text-zinc-500">default: []</span></span>
                        <span class="grid grid-cols-[minmax(28rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span
                                class="text-amber-300"
                            >arc-size="2.75rem"</span><span class="text-zinc-500">default: graph arc-size</span></span>
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
                            {{ __('Branch node labels') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Branch labels use the same explicit node-number and side structure as trunk labels. In a left branch, left labels usually face outward.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[32rem]"><span class="text-emerald-300">:node-labels</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">3 =&gt; [</span> <span
                                        class="text-zinc-500"
                                    >// branch arc-out anchor</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'left' =&gt; [</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'text' =&gt; ['Feature branch',
                                        'design system'],</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'width' =&gt;
                                        'default',</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'align' =&gt;
                                        'right',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Branch step') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('The step belongs to the branch as a whole. Its label explains a decision or state, while beforeLength and afterLength tune the surrounding stem parts.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[32rem]"><span class="text-emerald-300">:step</span><span
                                    class="text-zinc-400"
                                >=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">'beforeLength' =&gt;
                                        '14.1rem',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'afterLength' =&gt;
                                        '4.1rem',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'stepLabel' =&gt;
                                        [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'text' =&gt; ['Decision',
                                        'tokens before components'],</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'width' =&gt;
                                        'halfLong',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Stem continuation') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Continuation stems extend the branch after the arc/bridge/step structure. Each stem can carry its own label configuration.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[32rem]"><span
                                    class="text-emerald-300">:stem-continuation</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">1 =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'4rem',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'left' =&gt; ['text' =&gt;
                                        ['Foundations', 'colors and spacing']],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">2 =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'5rem',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'left' =&gt; ['text' =&gt;
                                        ['Components', 'forms and navigation']],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Branch return') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('A branch return starts from a branch-local anchor, such as stem.2, and draws the return path back toward the trunk-side direction.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[32rem]"><span
                                    class="text-emerald-300">:branch-return</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">1 =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'attachTo' =&gt;
                                        'stem.2',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'bridgeLength' =&gt;
                                        '28rem',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'color' =&gt;
                                        'sky',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3 text-sm leading-6">
                @if ($branchLeftHardcopySrc)
                    <figure class="space-y-2">
                        <img
                            class="w-full rounded-lg border-zinc-400 object-contain dark:border-zinc-800"
                            src="{{ $branchLeftHardcopySrc }}"
                            alt="{{ __('Project roadmap branch-left hardcopy') }}"
                        >
                        <figcaption class="text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                            {{ __('The left design-system branch with branch-local labels, step decision, continuation stems, and return path.') }}
                        </figcaption>
                    </figure>
                @endif
            </div>
        </div>
    </flux:callout.text>
</flux:callout>

<flux:callout
    class="mt-4"
    color="violet"
    icon="git-branch"
>
    <flux:callout.heading>
        {{ __('4.1 Right-side feature branches') }}
    </flux:callout.heading>
    <flux:callout.text>
        @php
            $branchRightHardcopyPath = base_path(
                'packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/project-roadmap-05-branch-right-01-02.png',
            );
            $branchRightHardcopySrc = is_file($branchRightHardcopyPath)
                ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($branchRightHardcopyPath))
                : null;
        @endphp

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="mb-6 space-y-3 hyphens-auto text-sm leading-6">
                    <p>
                        {{ __('The two right-side branches use the same component chain, but different prop values. This makes the contrast useful as documentation: the API platform branch is a longer active feature track, while the mobile companion branch is shorter and ends as postponed scope.') }}
                    </p>
                    <p>
                        {{ __('Green values below are shared by both branch-right calls. Violet marks values used by the first right branch, and rose marks values used by the second right branch.') }}
                    </p>
                </div>

                <div class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100"><code
                        class="block min-w-[64rem]"
                    ><span class="block"><span class="text-zinc-400">&lt;</span><span
                                class="text-sky-300">x-translation-workbench::ui.tw-graph.strang.branch-right</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- shared component, different values per branch --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">id</span><span class="text-zinc-400">=</span><span
                                    class="text-violet-100">"roadmap.right.1.api-platform"</span><span
                                    class="text-zinc-500"> / </span><span class="text-rose-100">"roadmap.right.2.mobile-companion"</span></span><span
                                class="text-zinc-500">default: generated from graph id</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">attach-to</span><span class="text-zinc-400">=</span><span
                                    class="text-violet-100">"strang.trunk.node.2"</span><span
                                    class="text-zinc-500"> / </span><span class="text-rose-100">"strang.trunk.node.4"</span></span><span
                                class="text-zinc-500">default: anchor-start</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">color</span><span class="text-zinc-400">=</span><span
                                    class="text-violet-100">"violet"</span><span class="text-zinc-500"> / </span><span
                                    class="text-rose-100">"rose"</span></span><span class="text-zinc-500">default:
                                inherited graph color</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">entry-stem-length</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"0.5rem"</span></span><span
                                class="text-zinc-500">default: 0rem</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">bridge-length</span><span class="text-zinc-400">=</span><span
                                    class="text-violet-100">"35rem"</span><span class="text-zinc-500"> / </span><span
                                    class="text-rose-100">"15rem"</span></span><span class="text-zinc-500">default:
                                graph bridge-length</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">stem-length</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"4rem"</span></span><span class="text-zinc-500">default:
                                graph stem-length</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">:node-labels</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"[3 =&gt; ['right' =&gt; [...]]]"</span></span><span
                                class="text-zinc-500">default: []</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">:step</span><span class="text-zinc-400">=</span><span
                                    class="text-emerald-100">"['beforeLength' =&gt; '3rem', 'afterLength' =&gt; '3rem']"</span></span><span
                                class="text-zinc-500">default: null</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span><span
                                    class="text-emerald-300">:stem-continuation</span><span
                                    class="text-zinc-400">=</span><span class="text-violet-100">"two stems"</span><span
                                    class="text-zinc-500"> / </span><span class="text-rose-100">"one stem"</span></span><span
                                class="text-zinc-500">default: []</span></span>
                        <span class="block ps-4 text-zinc-500">&#123;&#123;-- potential &#x40;props for this tag
                            x-translation-workbench::ui.tw-graph.strang.branch-right --&#125;&#125;</span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span
                                class="text-amber-300">:anchor-start="['x' =&gt; '0rem', 'y' =&gt; '0rem']"</span><span
                                class="text-zinc-500">default: 0/0 anchor</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span
                                class="text-amber-300">:bridge-continuation="[1 =&gt; ['12rem']]"</span><span
                                class="text-zinc-500">default: one bridge</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span
                                class="text-amber-300">:branch-extension="[...]"</span><span
                                class="text-zinc-500">default: []</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span
                                class="text-amber-300">:branch-return="[...]"</span><span
                                class="text-zinc-500">default: []</span></span>
                        <span class="grid grid-cols-[minmax(30rem,1fr)_minmax(16rem,20rem)] gap-8 ps-4"><span
                                class="text-amber-300">arc-size="2.75rem"</span><span class="text-zinc-500">default:
                                graph arc-size</span></span>
                        <span class="block"><span class="text-zinc-400">/&gt;</span></span></code></div>

                <div class="mt-4 grid gap-4 xl:grid-cols-2">
                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Right branch labels') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Right-side branches usually place their labels on the right. The text alignment is left so the label reads away from the trunk.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[36rem]"><span class="text-emerald-300">:node-labels</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">3 =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'right' =&gt; [</span></span>
                                <span class="block ps-12"><span class="text-violet-100">'text' =&gt; ['Feature branch', 'API platform'],</span></span>
                                <span class="block ps-12"><span class="text-rose-100">'text' =&gt; ['Feature branch', 'mobile companion'],</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'width' =&gt; 'default',</span></span>
                                <span class="block ps-12"><span class="text-emerald-100">'align' =&gt; 'left',</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">],</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Right branch steps') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('Both examples use the same step geometry, but the label meaning differs: one branch freezes a contract, the other defers scope.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[34rem]"><span class="text-emerald-300">:step</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-emerald-100">'beforeLength' =&gt; '3rem',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'afterLength' =&gt; '3rem',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">'stepLabel' =&gt; [</span></span>
                                <span class="block ps-8"><span class="text-violet-100">'text' =&gt; ['Status change', 'contract freeze'],</span></span>
                                <span class="block ps-8"><span class="text-rose-100">'text' =&gt; ['Decision', 'defer after beta'],</span></span>
                                <span class="block ps-8"><span class="text-emerald-100">'width' =&gt; 'halfLong',</span></span>
                                <span class="block ps-4"><span class="text-emerald-100">],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Different continuation depth') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('The first right branch has two continuation stems, the second only one. That difference is local to the branch and does not require a different component.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[34rem]"><span class="text-emerald-300">:stem-continuation</span><span
                                    class="text-zinc-400">=</span><span class="text-emerald-100">"[</span>
                                <span class="block ps-4"><span class="text-violet-100">1 =&gt; ['4rem', 'right' =&gt; ['text' =&gt; ['Endpoints', 'bulk import ready']]],</span></span>
                                <span class="block ps-4"><span class="text-violet-100">2 =&gt; ['5rem', 'right' =&gt; ['text' =&gt; ['Telemetry', 'request traces linked']]],</span></span>
                                <span class="block ps-4"><span class="text-rose-100">1 =&gt; ['5rem', 'right' =&gt; ['text' =&gt; ['Postponed', 'moves to v1.1 discovery']]],</span></span>
                                <span class="text-emerald-100">]"</span></code>
                        </div>
                    </div>

                    <div class="grid h-full grid-rows-[auto_minmax(4.5rem,auto)_1fr] gap-2">
                        <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-50">
                            {{ __('Terminal right branch') }}
                        </h3>
                        <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ __('The mobile companion branch demonstrates that a branch can end instead of returning or merging. The branch-end component attaches to the branch-local end anchor.') }}
                        </p>
                        <div
                            class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100">
                            <code class="block min-w-[34rem]"><span class="block"><span class="text-zinc-400">&lt;</span><span
                                        class="text-sky-300">x-translation-workbench::ui.tw-graph.strang.branch-end</span></span>
                                <span class="block ps-4"><span class="text-emerald-300">side</span><span
                                        class="text-zinc-400">=</span><span class="text-emerald-100">"right"</span></span>
                                <span class="block ps-4"><span class="text-emerald-300">attach-to</span><span
                                        class="text-zinc-400">=</span><span
                                        class="text-emerald-100">"strang.branch-right.end"</span></span>
                                <span class="block ps-4"><span class="text-rose-300">color</span><span
                                        class="text-zinc-400">=</span><span class="text-rose-100">"rose"</span></span>
                                <span class="block ps-4"><span class="text-emerald-300">:end-label</span><span
                                        class="text-zinc-400">=</span><span
                                        class="text-emerald-100">"['text' =&gt; ['Deferred scope', 'tracked outside v1.0']]"</span></span>
                                <span class="block"><span class="text-zinc-400">/&gt;</span></span></code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3 text-sm leading-6">
                @if ($branchRightHardcopySrc)
                    <figure class="space-y-2">
                        <img
                            class="w-full rounded-lg border-zinc-400 object-contain dark:border-zinc-800"
                            src="{{ $branchRightHardcopySrc }}"
                            alt="{{ __('Project roadmap branch-right hardcopy') }}"
                        >
                        <figcaption class="text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                            {{ __('The two right-side branches: API platform as active branch, mobile companion as terminal postponed branch.') }}
                        </figcaption>
                    </figure>
                @endif
            </div>
        </div>
    </flux:callout.text>
</flux:callout>
