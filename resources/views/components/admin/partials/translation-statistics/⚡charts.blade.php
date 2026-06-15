{{-- resources/views/components/admin/partials/translation-statistics/⚡charts.blade.php --}}

{{-- By Status + By Classification bar charts --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Distribution')"
        :description="__('Key breakdown by status and classification.')"
    />

    <div class="mt-4 grid gap-4 sm:grid-cols-2">

        {{-- By Status --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('By Status') }}
            </h4>

            <div class="space-y-1.5">
                @foreach ($keysByStatus->sortDesc() as $status => $count)
                    @php
                        $pct = $totalKeys > 0 ? round(($count / $totalKeys) * 100, 1) : 0;
                        $barColor = match ($status) {
                            'ok' => 'bg-green-500',
                            'missing' => 'bg-amber-500',
                            'obsolete' => 'bg-amber-400',
                            'dynamic' => 'bg-teal-500',
                            'native' => 'bg-yellow-500',
                            'invalid' => 'bg-red-500',
                            default => 'bg-zinc-400',
                        };
                    @endphp

                    <div class="flex items-center gap-3">
                        <span class="w-28 truncate text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ str($status)->headline() }}
                        </span>

                        <div class="h-2 min-w-0 flex-1 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                            <div
                                class="{{ $barColor }} h-full rounded-full"
                                style="width: {{ $pct }}%"
                            ></div>
                        </div>

                        <span class="w-20 text-right text-sm tabular-nums text-zinc-500 dark:text-zinc-400">
                            {{ number_format($count) }}
                            <span class="ml-1 text-xs opacity-60">{{ $pct }}%</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- By Classification --}}
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('By Classification') }}
            </h4>

            <div class="space-y-1.5">
                @foreach ($keysByClassification->sortDesc() as $classification => $count)
                    @php
                        $pct = $totalKeys > 0 ? round(($count / $totalKeys) * 100, 1) : 0;
                        $label = match ($classification) {
                            'backfill_by_translation' => __('admin.translation_list.meta.backfill'),
                            default => str($classification)->headline(),
                        };
                    @endphp

                    <div class="flex items-center gap-3">
                        <span class="w-28 truncate text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $label }}
                        </span>

                        <div class="h-2 min-w-0 flex-1 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                            <div
                                class="h-full rounded-full bg-sky-500"
                                style="width: {{ $pct }}%"
                            ></div>
                        </div>

                        <span class="w-20 text-right text-sm tabular-nums text-zinc-500 dark:text-zinc-400">
                            {{ number_format($count) }}
                            <span class="ml-1 text-xs opacity-60">{{ $pct }}%</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</flux:card>
