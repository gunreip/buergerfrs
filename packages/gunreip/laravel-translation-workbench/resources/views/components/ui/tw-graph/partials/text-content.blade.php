        @if ($badge)
            <flux:badge color="{{ $badgeColor }}">
                <span
                    @class([
                        'tw-graph-protocol-primitive-text-badge wrap-anywhere inline-flex flex-col gap-0.5 whitespace-normal leading-tight hyphens-auto',
                        'items-start text-left' => $resolvedAlign === 'left',
                        'items-end text-right' => $resolvedAlign === 'right',
                        'items-center text-center' => $resolvedAlign === 'center',
                        'text-justify' => (bool) $justify,
                        'w-96' => (bool) $long,
                        'w-72' => ! (bool) $long && (bool) $halfLong,
                        'w-24' => ! (bool) $long && ! (bool) $halfLong && (bool) $half,
                        'w-48' => ! (bool) $long && ! (bool) $halfLong && ! (bool) $half,
                    ])
                    style="text-align: {{ $resolvedTextAlign }};"
                >
                    @foreach ($lines as $line)
                        <span @class(['tw-graph-protocol-primitive-text-line wrap-anywhere text-wrap', 'block w-full' => (bool) $justify, 'text-xs' => !$loop->first])>
                            @if (is_array($line) && data_get($line, 'ordinal'))
                                <span>{{ data_get($line, 'ordinal.number') }}</span><sup class="text-[0.58em] leading-none">{{ data_get($line, 'ordinal.suffix') }}</sup>
                                <span>{{ data_get($line, 'text') }}</span>
                            @else
                                {{ $line }}
                            @endif
                        </span>
                    @endforeach
                </span>
            </flux:badge>
        @else
            <span class="font-mono text-[0.625rem] text-zinc-100">
                {{ $lines->join(' / ') }}
            </span>
        @endif
