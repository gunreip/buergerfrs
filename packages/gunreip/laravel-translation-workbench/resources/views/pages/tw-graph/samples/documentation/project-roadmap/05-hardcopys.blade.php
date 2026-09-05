{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/05-hardcopys.blade.php --}}

<flux:callout
    color="zinc"
    icon="image"
>
    <flux:callout.heading>
        {{ __('6. Hardcopy notes') }}
    </flux:callout.heading>
    <flux:callout.text>
        <div class="space-y-3 text-sm leading-6">
            <p>
                {{ __('Hardcopys can be added here as focused visual annotations once a specific graph state should be documented. Keep each image tied to one authoring question, for example trunk rhythm, feature branch spacing, release merge, or deferred scope.') }}
            </p>
            <p>
                {{ __('Suggested local convention: store project-roadmap hardcopys below public/hardcopys/tw-graph/project-roadmap and reference them from this partial only after the image file exists.') }}
            </p>
            <pre class="overflow-x-auto rounded-md bg-zinc-950 p-3 text-xs text-zinc-100"><code>&lt;img
    src="{{ asset('hardcopys/tw-graph/project-roadmap/01-trunk-rhythm.png') }}"
    alt="{{ __('Project roadmap trunk rhythm') }}"
&gt;</code></pre>
        </div>
    </flux:callout.text>
</flux:callout>
