{{-- resources/views/components/admin/partials/translation-list/modal-history/timeline/⚡moved.blade.php --}}

@props(['historyEvent', 'historyContext' => [], 'historyLocale' => null])

@if ($historyEvent->old_file || $historyEvent->new_file || $historyEvent->old_line || $historyEvent->new_line)
    @if ($historyEvent->old_file === $historyEvent->new_file)
        <div class="mt-3 grid gap-3 md:grid-cols-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                <flux:icon.file-input class="inset mr-1 inline h-4 w-4 min-w-0" />
                {{ __('File location') }}
            </div>

            <code class="wrap-anywhere col-span-3 text-xs">
                {{ $historyEvent->old_file ?: $historyEvent->new_file ?: '—' }}
            </code>
        </div>

        <div class="mt-2 grid gap-3 md:grid-cols-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                <flux:icon.arrow-left class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
                {{ __('Code line before') }}
            </div>

            <code class="wrap-anywhere text-xs">
                {{ $historyEvent->old_line ?: '—' }}
            </code>

            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                <flux:icon.arrow-right
                    class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-300 dark:text-orange-600"
                />
                {{ __('after') }}
            </div>

            <code class="wrap-anywhere text-xs">
                {{ $historyEvent->new_line ?: '—' }}
            </code>
        </div>
    @else
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <flux:icon.map-pin class="inset mr-1 inline h-4 w-4 min-w-0 text-blue-300 dark:text-blue-600" />
                    {{ __('Location before') }}
                </div>

                <code class="wrap-anywhere mt-1 block text-xs">
                    {{ $historyEvent->old_file ?: '—' }}:{{ $historyEvent->old_line ?: '—' }}
                </code>
            </div>

            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <flux:icon.map-pin
                        class="inset mr-1 inline h-4 w-4 min-w-0 text-orange-300 dark:text-orange-600"
                    />
                    {{ __('Location after') }}
                </div>

                <code class="wrap-anywhere mt-1 block text-xs">
                    {{ $historyEvent->new_file ?: '—' }}:{{ $historyEvent->new_line ?: '—' }}
                </code>
            </div>
        </div>
    @endif
@endif
