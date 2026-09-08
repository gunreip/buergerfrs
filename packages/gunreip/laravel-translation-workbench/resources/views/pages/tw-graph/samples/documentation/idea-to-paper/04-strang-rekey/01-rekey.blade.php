{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/04-strang-rekey/01-rekey.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-07-rekey';
    $renderMode = $renderMode ?? 'documentation';
    $rekeyProps = [
        [
            'name' => 'attach-to / :anchor-start',
            'default' => 'anchor-start at 0/0',
            'effect' => 'Places the rekey strand. Source strands end at attach-to; target strands start at attach-to.',
        ],
        [
            'name' => 'bridge-length',
            'default' => 'bridge-length',
            'effect' => 'Controls the horizontal distance between the outer path and the trunk reference.',
        ],
        [
            'name' => 'stem-length',
            'default' => 'stem-length',
            'effect' => 'Controls the visible vertical stem used for the rekey continuation.',
        ],
        [
            'name' => ':stem-continuation',
            'default' => '[]',
            'effect' => 'Adds rekey source/target continuation stems. Each entry may carry left/right labels.',
        ],
        [
            'name' => ':compressed-stem-parts',
            'default' => 'source only',
            'effect' => 'Marks omitted history inside a rekey-source path with the compressed stem convention.',
        ],
        [
            'name' => 'end-length / cap-length',
            'default' => 'target only',
            'effect' => 'Closes a rekey-target with an end segment and centered end label.',
        ],
        [
            'name' => ':start-label / :end-label',
            'default' => 'null',
            'effect' => 'Source uses start-label at the old key side; target uses end-label at the new continuing side.',
        ],
        [
            'name' => ':node-labels',
            'default' => '[]',
            'effect' => 'Labels concrete facts at source/target anchors, using named label arrays with text, width, align, color, and justify.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ rekeyVariant: 'default' }"
    >
        <flux:callout
            color="violet"
            icon="replace"
        >
            <flux:callout.heading>
                {{ __('7. Rekey source / target') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Rekey strands show that a record does not simply vanish: a source strand points from an older key into the current chain, while a target strand points from the current chain toward the key that continues elsewhere.') }}
            </flux:callout.text>

            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey._rekey-code-tabs')

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column class="w-40">{{ __('Prop') }}</flux:table.column>
                        <flux:table.column class="w-40">{{ __('Default') }}</flux:table.column>
                        <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($rekeyProps as $prop)
                            <flux:table.row>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['name'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['default'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell
                                    class="min-w-0 whitespace-normal break-words text-xs leading-5 text-zinc-600 dark:text-zinc-300"
                                >
                                    {{ $prop['effect'] }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>

        <flux:callout
            color="zinc"
            icon="square-dashed-text"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span>{{ __('Step 7 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="rekeyVariant === 'default'"
                    >
                        {{ __('default') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="violet"
                        x-show="rekeyVariant === 'source'"
                    >
                        {{ __('source') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="purple"
                        x-show="rekeyVariant === 'target'"
                    >
                        {{ __('target') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="rekeyVariant === 'compressed'"
                    >
                        {{ __('source gap') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey._rekey-preview-variants', [
                'dev' => $dev,
                'coordinates' => $coordinates,
                'graphId' => $graphId,
                'renderMode' => $renderMode,
            ])
        </flux:callout>
    </section>
@endif
