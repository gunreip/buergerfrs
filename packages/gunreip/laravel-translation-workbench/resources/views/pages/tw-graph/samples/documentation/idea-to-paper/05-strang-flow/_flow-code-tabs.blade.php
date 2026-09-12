{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/05-strang-flow/_flow-code-tabs.blade.php --}}

<flux:tab.group class="mt-4 min-w-0 max-w-full">
    <flux:tabs
        scrollable
        scrollable:fade
        scrollable:scrollbar="hide"
    >
        <flux:tab
            name="flow-start"
            x-on:click="flowVariant = 'start'"
        >
            {{ __('Flow start') }}
        </flux:tab>
        <flux:tab
            name="flow-step"
            x-on:click="flowVariant = 'step'"
        >
            {{ __('Flow step') }}
        </flux:tab>
        <flux:tab
            name="flow-decision"
            x-on:click="flowVariant = 'decision'"
        >
            {{ __('Flow decision') }}
        </flux:tab>
        <flux:tab
            name="flow-branch-steps"
            x-on:click="flowVariant = 'branchSteps'"
        >
            {{ __('Flow branch steps') }}
        </flux:tab>
        <flux:tab
            name="flow-if"
            x-on:click="flowVariant = 'if'; flowIfVariant = 'if'"
        >
            {{ __('Flow IF') }}
        </flux:tab>
    </flux:tabs>

    <flux:tab.panel name="flow-start">
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-start', ['sectionContent' => 'code'])
    </flux:tab.panel>

    <flux:tab.panel name="flow-step">
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-step', ['sectionContent' => 'code'])
    </flux:tab.panel>

    <flux:tab.panel name="flow-decision">
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-decision', ['sectionContent' => 'code'])
    </flux:tab.panel>

    <flux:tab.panel name="flow-branch-steps">
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-branch-steps', ['sectionContent' => 'code'])
    </flux:tab.panel>

    <flux:tab.panel name="flow-if">
        <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('IF starts from the already branched flow. The condition is not the first split itself; it is a control marker that can be placed on any existing branch, so ELSE, DEFAULT, and ENDIF can be added later without changing the basic branch-step structure.') }}
        </p>

        <flux:tab.group class="mt-4 min-w-0 max-w-full">
            <flux:tabs
                scrollable
                scrollable:fade
                scrollable:scrollbar="hide"
            >
                <flux:tab
                    name="flow-if-if"
                    x-on:click="flowIfVariant = 'if'"
                >
                    {{ __('IF') }}
                </flux:tab>
                <flux:tab
                    name="flow-if-endif"
                    x-on:click="flowIfVariant = 'ifEndif'"
                >
                    {{ __('IF ENDIF') }}
                </flux:tab>
                <flux:tab
                    name="flow-if-else-endif"
                    x-on:click="flowIfVariant = 'ifElseEndif'"
                >
                    {{ __('IF ELSE ENDIF') }}
                </flux:tab>
                <flux:tab
                    name="flow-if-elseif-endif"
                    x-on:click="flowIfVariant = 'ifElseifEndif'"
                >
                    {{ __('IF ELSEIF ENDIF') }}
                </flux:tab>
                <flux:tab
                    name="flow-if-test"
                    x-on:click="flowIfVariant = 'test'"
                >
                    {{ __('Flow IF Test') }}
                </flux:tab>
            </flux:tabs>

            <flux:tab.panel name="flow-if-if">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-if', ['sectionContent' => 'code'])
            </flux:tab.panel>

            <flux:tab.panel name="flow-if-endif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-endif', ['sectionContent' => 'code'])
            </flux:tab.panel>

            <flux:tab.panel name="flow-if-else-endif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-else-endif', ['sectionContent' => 'code'])
            </flux:tab.panel>

            <flux:tab.panel name="flow-if-elseif-endif">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-elseif-endif', ['sectionContent' => 'code'])
            </flux:tab.panel>

            <flux:tab.panel name="flow-if-test">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-test', ['sectionContent' => 'code'])
            </flux:tab.panel>
        </flux:tab.group>
    </flux:tab.panel>
</flux:tab.group>

@include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow._flow-props')
