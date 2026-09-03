{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/resume-a-einstein.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Resume A. Einstein')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Resume A. Einstein')"
            :description="__('Hand-authored tw-graph samples will be collected here.')"
        />

        @php
            $resumeGraphId = 'tw-graph-sample-resume-a-einstein';
            $resumeTopBottomGraphId = 'tw-graph-sample-resume-a-einstein-top-bottom';
            $resumeGraphDev = false;
            $resumeGraphCoordinates = false;
            $resumeEinstein = include base_path(
                'packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/resume-a-einstein-data.php',
            );
            $resumeImagePath = asset('vendor/translation-workbench/img/png') . '/';
            $resumeDateLabel = static fn(string $eventKey, string $align): array => [
                'text' => data_get($resumeEinstein, 'events.' . $eventKey . '.date'),
                'width' => 'half',
                'align' => $align,
                'color' => 'purple',
            ];
            $resumeTextLabel = static fn(string $eventKey, string $align): array => [
                'text' => data_get($resumeEinstein, 'events.' . $eventKey . '.text'),
                'width' => 'long',
                'align' => $align,
                'justify' => true,
            ];
            $resumeParts = [
                [
                    'type' => 'start',
                    'id' => 'resume.center.1.start',
                    'nodeLabelLeft' => $resumeDateLabel('1879', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1879', 'left'),
                    'startLabel' => [
                        'text' => data_get($resumeEinstein, 'events.1879.year'),
                        'width' => 'half',
                        'side' => 'bottom',
                    ],
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein-02.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.left.1.sideways',
                    'side' => 'left',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '7.25rem',
                    'nodeLabelLeft' => $resumeTextLabel('1896', 'left'),
                    'nodeLabelRight' => $resumeDateLabel('1896', 'left'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.right.1.sideways',
                    'side' => 'right',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeDateLabel('1900', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1900', 'right'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.left.2.sideways',
                    'side' => 'left',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeTextLabel('1902-1909', 'left'),
                    'nodeLabelRight' => $resumeDateLabel('1902-1909', 'left'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.right.2.sideways',
                    'side' => 'right',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'extension' => '4rem',
                    'nodeLabelLeft' => $resumeDateLabel('1905', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1905', 'right'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.left.3.sideways',
                    'side' => 'left',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeTextLabel('1909', 'left'),
                    'nodeLabelRight' => $resumeDateLabel('1909', 'left'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.right.3.sideways',
                    'side' => 'right',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeDateLabel('1914', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1914', 'right'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.left.4.sideways',
                    'side' => 'left',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeTextLabel('1915', 'left'),
                    'nodeLabelRight' => $resumeDateLabel('1915', 'left'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.right.4.sideways',
                    'side' => 'right',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeDateLabel('1919', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1919', 'right'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.left.5.sideways',
                    'side' => 'left',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeTextLabel('1921', 'left'),
                    'nodeLabelRight' => $resumeDateLabel('1921', 'left'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.right.5.sideways',
                    'side' => 'right',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeDateLabel('1933_emigration', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1933_emigration', 'right'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.left.6.sideways',
                    'side' => 'left',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '18.0rem',
                    'nodeLabelLeft' => $resumeTextLabel('1933_princeton', 'left'),
                    'nodeLabelRight' => $resumeDateLabel('1933_princeton', 'left'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'sideways',
                    'id' => 'resume.right.6.sideways',
                    'side' => 'right',
                    'arcRadius' => '3.5rem',
                    'bridgeLength' => '7.25rem',
                    'nodeLabelLeft' => $resumeDateLabel('1955', 'right'),
                    'nodeLabelRight' => $resumeTextLabel('1955', 'right'),
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
                [
                    'type' => 'end',
                    'id' => 'resume.center.1.end',
                    'length' => '3rem',
                    'devCounterEnd' => 'E',
                    'nodeImage' => [
                        'source' => $resumeImagePath . 'resume-a-einstein.png',
                        'size' => '3rem',
                        'alt' => 'Albert Einstein',
                    ],
                ],
            ];
            $resumeTopBottomParts = array_map(static function (array $part): array {
                $id = data_get($part, 'id');

                if (is_string($id) && str_starts_with($id, 'resume.')) {
                    $part['id'] = 'resume.top-bottom.' . substr($id, strlen('resume.'));
                }

                return $part;
            }, $resumeParts);
        @endphp

        <flux:callout
            class="mt-6"
            color="zinc"
            icon="file-text"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span class="inline-flex flex-wrap items-center gap-2">
                        <span>{{ __('Resume graph canvas') }}</span>
                        <flux:badge
                            size="sm"
                            color="zinc"
                        >
                            {{ $resumeGraphId }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('hand-authored') }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="sky"
                        >
                            {{ __('bottom to top') }}
                        </flux:badge>
                    </span>
                </span>
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Manual tw-graph canvas for the A. Einstein resume sample, rendered bottom to top.') }}
            </flux:callout.text>

            <flux:heading
                class="mt-6"
                size="xl"
            >
                {{ data_get($resumeEinstein, 'title') }}
            </flux:heading>
            <flux:heading size="lg">
                {{ data_get($resumeEinstein, 'subtitle') }}
            </flux:heading>
            <flux:text class="columns-3 hyphens-auto text-justify">
                {{ data_get($resumeEinstein, 'summary') }}
            </flux:text>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-12"
                    :graph-id="$resumeGraphId"
                    :dev="$resumeGraphDev"
                    :coordinates="$resumeGraphCoordinates"
                    color="sky"
                    line-length="4rem"
                    bridge-length="12rem"
                    stem-length="4rem"
                    slot-min-height="42rem"
                    horizontal-padding="16rem"
                >
                    <x-translation-workbench::ui.tw-graph.parts.chain
                        :parts="$resumeParts"
                        :anchor-start="['x' => '0rem', 'y' => '0rem']"
                        color="sky"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>
        </flux:callout>

        <flux:callout
            class="mt-6"
            color="zinc"
            icon="file-text"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span class="inline-flex flex-wrap items-center gap-2">
                        <span>{{ __('Resume graph canvas') }}</span>
                        <flux:badge
                            size="sm"
                            color="zinc"
                        >
                            {{ $resumeTopBottomGraphId }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ __('hand-authored') }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="sky"
                        >
                            {{ __('top to bottom') }}
                        </flux:badge>
                    </span>
                </span>
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Manual tw-graph canvas for the A. Einstein resume sample, rendered top to bottom.') }}
            </flux:callout.text>

            <flux:heading
                class="mt-6"
                size="xl"
            >
                {{ data_get($resumeEinstein, 'title') }}
            </flux:heading>
            <flux:heading size="lg">
                {{ data_get($resumeEinstein, 'subtitle') }}
            </flux:heading>
            <flux:text class="columns-3 hyphens-auto text-justify">
                {{ data_get($resumeEinstein, 'summary') }}
            </flux:text>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-12"
                    :graph-id="$resumeTopBottomGraphId"
                    :dev="$resumeGraphDev"
                    :coordinates="$resumeGraphCoordinates"
                    color="sky"
                    line-length="4rem"
                    bridge-length="12rem"
                    stem-length="4rem"
                    slot-min-height="42rem"
                    horizontal-padding="16rem"
                >
                    <x-translation-workbench::ui.tw-graph.parts.chain
                        :parts="$resumeTopBottomParts"
                        :anchor-start="['x' => '0rem', 'y' => '104rem']"
                        direction="top-bottom"
                        color="sky"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>
        </flux:callout>
    </flux:card>
</x-layouts::app>
