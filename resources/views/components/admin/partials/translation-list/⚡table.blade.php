{{-- resources/views/components/admin/partials/translation-list/⚡table.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Translation List')"
        :description="__('Review and manage translation keys, their values across languages, and associated metadata.')"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column align="center">
                        {{ __('Status') }}
                    </flux:table.column>

                    <flux:table.column>
                        {{ __('Key / Suggested Key') }}
                    </flux:table.column>

                    <flux:table.column>
                        {{ __('Native Text') }}
                    </flux:table.column>

                    <flux:table.column>
                        {{ __('Values') }}
                    </flux:table.column>

                    <flux:table.column>
                        {{ __('Usage') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($translationKeys as $translationKey)
                        <flux:table.row wire:key="translation-key-{{ $translationKey->id }}">
                            <flux:table.cell
                                class="align-top"
                                align="center"
                            >
                                <div class="space-y-2">
                                    <flux:badge
                                        size="sm"
                                        variant="outline"
                                    >
                                        {{ str($translationKey->status)->headline() }}
                                    </flux:badge>

                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $translationKey->classification }}
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="align-top">
                                <div class="space-y-1">
                                    @if ($translationKey->key)
                                        <div
                                            class="wrap-break-words text-wrap font-mono text-zinc-900 dark:text-zinc-100">
                                            {{ $translationKey->key }}
                                        </div>
                                    @elseif ($translationKey->suggested_key)
                                        <div
                                            class="wrap-break-words text-wrap font-mono text-amber-700 dark:text-amber-300">
                                            {{ $translationKey->suggested_key }}
                                        </div>

                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('suggested') }}
                                        </div>
                                    @else
                                        <div class="text-xs text-zinc-400">
                                            —
                                        </div>
                                    @endif

                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $translationKey->namespace ?? '—' }}
                                        @if ($translationKey->group)
                                            / {{ $translationKey->group }}
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="align-top">
                                <div class="max-w-md text-wrap text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $translationKey->native_text ?: '—' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="align-top">
                                <div class="space-y-2">
                                    @forelse ($translationKey->values as $value)
                                        <div class="rounded-lg border border-zinc-200 p-2 text-xs dark:border-zinc-700">
                                            <div class="mb-1 flex items-center justify-between gap-2">
                                                <span class="font-mono font-semibold">
                                                    {{ $value->locale }}
                                                </span>

                                                <flux:badge
                                                    size="sm"
                                                    variant="outline"
                                                >
                                                    {{ $value->status }}
                                                </flux:badge>
                                            </div>

                                            <div class="text-zinc-600 dark:text-zinc-300">
                                                {{ $value->value ?: '—' }}
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-zinc-400">
                                            —
                                        </span>
                                    @endforelse
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="align-top tabular-nums">
                                <div class="space-y-1 text-zinc-500 dark:text-zinc-400">
                                    <div>
                                        {{ $translationKey->usages_count }} {{ __('usage(s)') }}
                                    </div>

                                    @if ($translationKey->last_seen_at)
                                        <div>
                                            {{ __('Last seen') }}:
                                            {{ $translationKey->last_seen_at->format('Y-m-d H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No translation records found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($translationKeys->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('Pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$translationKeys" />
            </div>
        @endif

    </div>
</flux:card>
