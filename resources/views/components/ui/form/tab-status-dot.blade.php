{{-- resources/views/components/ui/form/tab-status-dot.blade.php --}}

{{--
    Keep this tooltip hover/focus based for now.

    Flux toggleable tooltips are fragile here because this component is rendered
    inside tab/section buttons. A click-toggle on the dot competes with the
    parent button click handling and can close immediately or fail to open.
--}}

<flux:tooltip
    position="bottom"
    align="center"
>
    <span
        aria-label="{{ __('ui.form.tab_status_dot.form_tab_status_status', ['status' => $statusLabel()]) }}"
        {{ $attributes->class([
            'inline-flex size-2.5 shrink-0 rounded-full ring-2',
            $toggleable ? 'cursor-pointer' : 'cursor-help',
            $statusClasses(),
        ]) }}
    ></span>

    <flux:tooltip.content class="w-80 max-w-[calc(100vw-2rem)] space-y-3 text-start">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 text-sm font-semibold text-white">
                    @if ($icon)
                        <x-ui.flux-icon
                            class="size-4 shrink-0 text-zinc-300"
                            :name="$icon"
                            stroke-width="1"
                        />
                    @endif

                    <span class="truncate">
                        {{ $label ?? __('ui.form.tab_status_dot.form_tab') }}
                    </span>
                </div>

                <div class="text-xs text-zinc-300">
                    {{ __('ui.form.tab_status_dot.current_status') }}
                    <span class="font-semibold text-white">
                        {{ $statusLabel() }}
                    </span>
                </div>
            </div>

            <span
                class="{{ $statusClasses() }} mt-1 inline-flex size-2.5 shrink-0 rounded-full ring-2"
                aria-hidden="true"
            ></span>
        </div>

        <div class="grid grid-cols-2 gap-2 rounded-md bg-white/5 p-2 text-xs text-zinc-200">
            <div>
                <div class="text-zinc-400">{{ __('ui.form.tab_status_dot.fields') }}</div>
                <div class="font-semibold text-white">{{ $filled() }} / {{ $total() }}</div>
            </div>

            <div>
                <div class="text-zinc-400">{{ __('ui.form.tab_status_dot.required') }}</div>
                <div class="font-semibold text-white">{{ $requiredFilled() }} / {{ $requiredTotal() }}</div>
            </div>
        </div>

        <div class="space-y-1.5">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-400">
                {{ __('ui.form.tab_status_dot.legend') }}
            </div>

            @foreach ($legendStatuses() as $legendStatus)
                <div @class([
                    'flex items-start gap-2 rounded-md px-2 py-1.5 text-xs',
                    'bg-white/10' => $legendStatus === $status(),
                ])>
                    <span
                        class="{{ $statusClasses($legendStatus) }} mt-1 inline-flex size-2 shrink-0 rounded-full ring-2"
                        aria-hidden="true"
                    ></span>

                    <div class="min-w-0">
                        <div @class([
                            'font-semibold text-white' => $legendStatus === $status(),
                            'font-medium text-zinc-200' => $legendStatus !== $status(),
                        ])>
                            {{ $statusLabel($legendStatus) }}
                        </div>

                        <div class="text-zinc-400">
                            {{ $statusDescription($legendStatus) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </flux:tooltip.content>
</flux:tooltip>
