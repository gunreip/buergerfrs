{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/documentation.blade.php --}}

<x-layouts::app :title="__('TW-Graph Documentation')">
    @php
        $twGraphDocsPath = base_path('packages/gunreip/laravel-translation-workbench/docs/tw-graph');
        $twGraphDocs = [
            'README.md' => 'Overview',
            'concept.md' => 'Concept',
            'development-rules.md' => 'Development Rules',
            'authoring.md' => 'Authoring',
            'parts-authoring.md' => 'Parts Authoring',
            'data-driven.md' => 'Data Driven',
            'data-driven-cases.md' => 'Data Driven Cases',
            'layout-and-collisions.md' => 'Layout & Collisions',
            'debugging.md' => 'Debugging',
            'configuration.md' => 'Configuration',
            'element-references.md' => 'Element References',
            'components.md' => 'Components',
            'examples.md' => 'Examples',
            'glossary.md' => 'Glossary',
            'roadmap.md' => 'Roadmap',
        ];

        $requestedDoc = (string) request()->query('doc', 'README.md');
        $currentDoc = array_key_exists($requestedDoc, $twGraphDocs)
            ? $requestedDoc
            : 'README.md';
        $currentDocPath = $twGraphDocsPath . DIRECTORY_SEPARATOR . $currentDoc;
        $currentMarkdown = is_file($currentDocPath)
            ? (string) file_get_contents($currentDocPath)
            : '# Missing Documentation';
        $currentMarkdown = preg_replace_callback(
            '/\]\(([^)]+\.md)\)/',
            static function (array $match) use ($twGraphDocs): string {
                $docFile = basename($match[1]);

                if (! array_key_exists($docFile, $twGraphDocs)) {
                    return '](' . $match[1] . ')';
                }

                return '](' . route('admin.tw-graph.documentation', ['doc' => $docFile]) . ')';
            },
            $currentMarkdown,
        );
        $currentHtml = new \Illuminate\Support\HtmlString(\Illuminate\Support\Str::markdown($currentMarkdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    @endphp

    @once
        <style>
            .tw-graph-documentation-prose h1 {
                margin-bottom: 1rem;
                font-size: 1.5rem;
                font-weight: 700;
                line-height: 2rem;
            }

            .tw-graph-documentation-prose h2 {
                margin-top: 2rem;
                margin-bottom: 0.75rem;
                border-top: 1px solid color-mix(in oklab, currentColor 16%, transparent);
                padding-top: 1.25rem;
                font-size: 1rem;
                font-weight: 700;
            }

            .tw-graph-documentation-prose p,
            .tw-graph-documentation-prose ul,
            .tw-graph-documentation-prose ol,
            .tw-graph-documentation-prose pre {
                margin-top: 0.75rem;
            }

            .tw-graph-documentation-prose ul,
            .tw-graph-documentation-prose ol {
                padding-left: 1.25rem;
            }

            .tw-graph-documentation-prose ul {
                list-style: disc;
            }

            .tw-graph-documentation-prose ol {
                list-style: decimal;
            }

            .tw-graph-documentation-prose a {
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            .tw-graph-documentation-prose code {
                border-radius: 0.25rem;
                background: color-mix(in oklab, currentColor 8%, transparent);
                padding: 0.125rem 0.25rem;
                font-size: 0.8125rem;
            }

            .tw-graph-documentation-prose pre {
                overflow-x: auto;
                border-radius: 0.5rem;
                background: rgb(24 24 27);
                padding: 1rem;
                color: rgb(244 244 245);
            }

            .tw-graph-documentation-prose pre code {
                background: transparent;
                padding: 0;
                color: inherit;
            }

            .tw-graph-documentation-prose dt {
                margin-top: 1rem;
                font-weight: 700;
            }

            .tw-graph-documentation-prose dd {
                margin-top: 0.25rem;
                margin-left: 1rem;
            }
        </style>
    @endonce

    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('TW-Graph Documentation')"
            :description="__('Package documentation for TW Graph concepts, authoring, debugging, configuration, and data-driven rendering.')"
        />

        <div class="mt-6 grid gap-6 xl:grid-cols-[18rem_minmax(0,1fr)]">
            <flux:callout
                color="zinc"
                icon="book-open"
            >
                <flux:callout.heading>
                    {{ __('Documents') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <nav class="mt-3 grid gap-1">
                        @foreach ($twGraphDocs as $docFile => $docTitle)
                            <a
                                @class([
                                    'rounded-md px-3 py-2 text-sm transition',
                                    'bg-zinc-900 text-white shadow-sm dark:bg-white dark:text-zinc-950' => $currentDoc === $docFile,
                                    'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' => $currentDoc !== $docFile,
                                ])
                                href="{{ route('admin.tw-graph.documentation', ['doc' => $docFile]) }}"
                                wire:navigate
                            >
                                {{ $docTitle }}
                            </a>
                        @endforeach
                    </nav>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="zinc"
                icon="file-text"
            >
                <flux:callout.heading>
                    <span class="flex flex-wrap items-center justify-between gap-3">
                        <span>{{ $twGraphDocs[$currentDoc] }}</span>
                        <flux:badge
                            size="sm"
                            color="zinc"
                        >
                            {{ $currentDoc }}
                        </flux:badge>
                    </span>
                </flux:callout.heading>
                <flux:callout.text>
                    <article class="tw-graph-documentation-prose mt-4 max-w-none text-sm leading-6 text-zinc-800 dark:text-zinc-200">
                        {!! $currentHtml !!}
                    </article>
                </flux:callout.text>
            </flux:callout>
        </div>
    </flux:card>
</x-layouts::app>
