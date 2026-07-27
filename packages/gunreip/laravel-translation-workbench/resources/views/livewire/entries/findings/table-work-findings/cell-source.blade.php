{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-source.blade.php --}}

{{-- Table Findings Cell Source --}}
                <flux:table.cell>
                    <div class="flex max-w-md items-start gap-2">
                        {{-- Button Open Source in VS Code --}}
                        <flux:button
                            class="mt-0.5 h-5 w-5 shrink-0"
                            type="button"
                            size="xs"
                            variant="ghost"
                            icon="external-link"
                            icon:class="text-sky-500 dark:text-sky-400"
                            :href="$sourceEditorUrl"
                            :aria-label="__('Open source in VS Code')"
                        />

                        <div class="flex flex-wrap items-start gap-1.5">
                            <span
                                class="wrap-anywhere text-wrap font-mono text-xs text-zinc-700 dark:text-zinc-300"
                                title="{{ $finding->source_path }}"
                            >
                                <x-translation-workbench::text.highlight
                                    :value="$finding->source_path"
                                    :search="$findingSearch"
                                    :exact="$findingSearchExact"
                                    :case-sensitive="$findingSearchCaseSensitive"
                                />
                            </span>
                            {{-- Badge Code-Line-Number --}}
                            <flux:badge
                                class="shrink-0"
                                size="sm"
                                variant="subtle"
                            >
                                {{ __('Line') }} {{ $finding->source_line ?? '-' }}
                            </flux:badge>
                        </div>
                    </div>
                </flux:table.cell>
