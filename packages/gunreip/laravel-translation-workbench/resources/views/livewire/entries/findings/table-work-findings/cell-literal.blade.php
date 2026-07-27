{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-literal.blade.php --}}

{{-- Table Findings Cell Literal --}}
                <flux:table.cell>
                    <div class="max-w-md space-y-1 hyphens-auto text-wrap text-sm">
                        @if (filled($literal))
                            <x-translation-workbench::text.highlight
                                :value="$literal"
                                :search="$findingSearch"
                                :exact="$findingSearchExact"
                                :case-sensitive="$findingSearchCaseSensitive"
                            />
                        @else
                            -
                        @endif

                        @if (filled($literalTextSuggested) && $literalTextSuggested !== $literalText)
                            <div class="space-y-0.5 text-xs text-zinc-500">
                                <div class="text-[11px] font-semibold uppercase">
                                    {{ __('Suggested') }}
                                </div>
                                <div>
                                    <x-translation-workbench::text.highlight
                                        :value="$literalTextSuggested"
                                        :search="$findingSearch"
                                        :exact="$findingSearchExact"
                                        :case-sensitive="$findingSearchCaseSensitive"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                </flux:table.cell>
