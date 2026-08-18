{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-literal.blade.php --}}

{{-- Table Findings Cell Literal --}}
                <flux:table.cell>
                    <div class="max-w-md space-y-1 hyphens-auto text-wrap text-sm">
                        @if (!$isDynamicFinding && $translationWorkflowEdited)
                            <div class="space-y-2">
                                <div class="flex items-start gap-2">
                                    <x-ui.locale.flag
                                        :locale="$sourceMainLocale"
                                        class="mt-0.5 size-4"
                                    />

                                    <div class="min-w-0 pl-1">
                                        <x-translation-workbench::text.highlight
                                            :value="str($sourceTranslationValue)->limit(80)->toString()"
                                            :search="$findingSearch"
                                            :exact="$findingSearchExact"
                                            :case-sensitive="$findingSearchCaseSensitive"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-start gap-2">
                                    <x-ui.locale.flag
                                        :locale="$targetMainLocale"
                                        class="mt-0.5 size-4"
                                    />

                                    <div class="min-w-0 pl-1">
                                        <x-translation-workbench::text.highlight
                                            :value="str($targetTranslationValue)->limit(80)->toString()"
                                            :search="$findingSearch"
                                            :exact="$findingSearchExact"
                                            :case-sensitive="$findingSearchCaseSensitive"
                                        />
                                    </div>
                                </div>
                            </div>
                        @elseif (filled($literal))
                            <x-translation-workbench::text.highlight
                                :value="$literal"
                                :search="$findingSearch"
                                :exact="$findingSearchExact"
                                :case-sensitive="$findingSearchCaseSensitive"
                            />
                        @else
                            -
                        @endif

                        @if (!$translationWorkflowEdited && filled($literalTextSuggested) && $literalTextSuggested !== $literalText)
                            <div class="space-y-0.5 text-xs text-zinc-500">
                                <div class="text-[11px] font-semibold uppercase">
                                    {{ __('ui.suggest.suggested') }}
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
