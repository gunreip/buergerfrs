{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/02-graph-wrapper.blade.php --}}

<flux:callout
    color="cyan"
    icon="square-code"
>
    <flux:callout.heading>
        {{ __('2. Graph wrapper and canvas defaults') }}
    </flux:callout.heading>
    <flux:callout.text>
        <div class="space-y-3 text-sm leading-6">
            <p>
                {{ __('The outer tw-graph component creates the graph canvas and defines common defaults. Child strangs inherit these values unless they override a prop locally.') }}
            </p>

            <div class="overflow-x-auto rounded-md bg-zinc-950 p-4 font-mono text-xs leading-5 text-zinc-100"><code class="block min-w-[52rem]"><span class="block"><span class="text-zinc-400">&lt;</span><span class="text-sky-300">x-translation-workbench::ui.tw-graph</span></span>
<span class="block ps-4 text-zinc-500">&#123;&#123;-- used &#x40;props in this sample --&#125;&#125;</span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">class</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"px-28 py-14"</span></span><span class="text-zinc-500">layout utility</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">:graph-id</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"$projectRoadmapGraphId"</span></span><span class="text-zinc-500">required per graph</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">:dev</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"$projectRoadmapDev"</span></span><span class="text-zinc-500">default: false</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">:coordinates</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"$projectRoadmapCoordinates"</span></span><span class="text-zinc-500">default: false</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">color</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"emerald"</span></span><span class="text-zinc-500">fallback: zinc</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">line-length</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"4rem"</span></span><span class="text-zinc-500">default: 4rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">bridge-length</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"16rem"</span></span><span class="text-zinc-500">default: 20rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">stem-length</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"5rem"</span></span><span class="text-zinc-500">default: 4rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">slot-min-height</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"78rem"</span></span><span class="text-zinc-500">default: 52rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span><span class="text-emerald-300">horizontal-padding</span><span class="text-zinc-400">=</span><span class="text-emerald-100">"36rem"</span></span><span class="text-zinc-500">default: 12rem</span></span>
<span class="block ps-4 text-zinc-500">&#123;&#123;-- potential &#x40;props for this tag x-translation-workbench::ui.tw-graph --&#125;&#125;</span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">node-size="0.95rem"</span><span class="text-zinc-500">default: 0.95rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">line-width="0.25rem"</span><span class="text-zinc-500">default: 0.25rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">arc-size="2.75rem"</span><span class="text-zinc-500">default: 2.75rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">cap-length="1.75rem"</span><span class="text-zinc-500">default: 1.75rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">connector-length="2rem"</span><span class="text-zinc-500">default: 2rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">connector-gap="0.25rem"</span><span class="text-zinc-500">default: 0.25rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">label-offset="0.75rem"</span><span class="text-zinc-500">default: 0.75rem</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">min-width="..."</span><span class="text-zinc-500">default: calculated bounds</span></span>
<span class="grid grid-cols-[minmax(24rem,1fr)_minmax(12rem,18rem)] gap-8 ps-4"><span class="text-amber-300">min-height="..."</span><span class="text-zinc-500">default: calculated bounds</span></span>
<span class="block"><span class="text-zinc-400">&gt;</span></span>
<span class="block ps-4 text-zinc-500">&#123;&#123;-- central milestone timeline --&#125;&#125;</span>
<span class="block ps-4 text-zinc-500">&#123;&#123;-- feature branches, release merge, and branch-end live here --&#125;&#125;</span>
<span class="block ps-4 text-zinc-500">...</span>
<span class="block"><span class="text-zinc-400">&lt;/</span><span class="text-sky-300">x-translation-workbench::ui.tw-graph</span><span class="text-zinc-400">&gt;</span></span></code></div>
        </div>
    </flux:callout.text>
</flux:callout>
