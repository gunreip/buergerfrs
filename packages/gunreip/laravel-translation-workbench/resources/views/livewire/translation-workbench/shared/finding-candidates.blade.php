@php($summary = $sharedSummaries[$candidateFinding] ?? [])
@if (($summary['candidates'] ?? 0) >= 2 || ($summary['shared'] ?? 0) >= 2)
    <div class="flex flex-col items-start gap-2">
        @if (($summary['candidates'] ?? 0) >= 2 && ($summary['shared'] ?? 0) < 2)
            <flux:tooltip toggleable>
                <flux:badge
                    as="button"
                    color="sky"
                    size="sm"
                    label="{{ __('Shared candidate') }}{{ ($showCandidateCount ?? true) ? ' · '.($candidateDisplayCount ?? $summary['candidates']) : '' }}"
                />
                <flux:tooltip.content class="max-w-[24rem] space-y-2 hyphens-auto text-justify">
                    <p>
                        {{ __('Shared candidate') }} · {{ $summary['candidates'] }}
                    </p>
                    <p class="dark:text-zinc-300">
                        {{ __('Because the literal is identical, this entry is a candidate for multiple entries requiring the use of the same translation key.') }}
                    </p>
                </flux:tooltip.content>
            </flux:tooltip>
        @endif
        @if (($summary['shared'] ?? 0) >= 2)
            <flux:tooltip toggleable>
                <flux:badge
                    as="button"
                    color="green"
                    size="sm"
                    label="{{ __('Shared key') }} · {{ $summary['shared'] }}"
                />
                <flux:tooltip.content class="max-w-[24rem] space-y-2 hyphens-auto text-justify">
                    <p>
                        {{ __('Shared key') }} · {{ $summary['shared'] }}
                    </p>
                    <p class="dark:text-zinc-300">
                        {{ __('Because the translation key is used in multiple entries, this entry shares the same key.') }}
                    </p>
                </flux:tooltip.content>
            </flux:tooltip>
        @endif
    </div>
@endif
