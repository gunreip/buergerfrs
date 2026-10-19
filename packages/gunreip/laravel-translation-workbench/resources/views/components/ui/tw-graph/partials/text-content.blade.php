        @if ($badge)
            <flux:badge
                color="{{ $badgeColor }}"
                class="tw-graph-protocol-text-surface"
                style="--tw-graph-text-surface-light: {{ \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb($badgeColor, \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::surfaceRgb('zinc')) }}; --tw-graph-text-surface-dark: {{ \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb($badgeColor, \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::darkSurfaceRgb('zinc')) }};"
            >
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
                            @if (is_array($line) && filled($line['icon'] ?? null))
                                <span class="inline-flex items-start gap-1">
                                    <flux:icon :name="$line['icon']" class="inline-block size-4 shrink-0" />
                                    <span>{{ $line['text'] }}</span>
                                </span>
                            @elseif (is_array($line) && data_get($line, 'ordinal'))
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
                {{ $lines->map(static fn ($line) => is_array($line) ? ($line['text'] ?? '') : $line)->join(' / ') }}
            </span>
        @endif
