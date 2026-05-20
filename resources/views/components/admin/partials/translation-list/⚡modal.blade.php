{{-- resources/views/components/admin/partials/translation-list/⚡modal.blade.php --}}

<flux:modal
    class="w-full max-w-7xl"
    wire:model="translationKeyModalOpen"
>
    @if ($selectedTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header with ID badge --}}
                <x-ui.headers.card
                    :title="__('Translation key review')"
                    :description="__('Read-only review of the selected translation key, its values and usage metadata.')"
                />

                {{-- Badge with translation key ID for reference in discussions or issue tracking, useful for quickly identifying the specific translation key being reviewed, especially when communicating about it in a team setting or across different tools. --}}
                <flux:badge
                    class="mr-8 mt-2"
                    variant="subtle"
                    color="zinc"
                >
                    #{{ $selectedTranslationKey->id }}
                </flux:badge>
            </div>

            <div class="grid gap-3 md:grid-cols-4">

                {{-- Callout components for key metadata such as status, group/namespace, source, and usage count. These provide a quick overview of important attributes of the translation key at a glance, with visual emphasis and contextual information to aid in understanding the key's state and relevance without needing to delve into the details immediately. The status callout can surface information about whether the key is active, missing translations, or has other noteworthy conditions that may require attention. The group/namespace callout helps identify the organizational context of the key within the translation system, while the source callout can indicate where the key is defined or used in the codebase. The usage callout provides insight into how widely used the key is across the application, which can be helpful for prioritizing review or identifying potential impact of changes to the key. --}}
                <flux:callout
                    color="sky"
                    icon="tag"
                >
                    {{-- Status --}}
                    <flux:callout.heading>
                        {{ __('Status') }}
                    </flux:callout.heading>

                    <flux:callout.text class="space-y-2">

                        {{-- Badge with the translation key status, using contextual colors and labels to indicate the current state of the translation key, such as whether it is active, missing translations, or has other relevant conditions. This provides an immediate visual cue about the key's status, which can help prioritize review or identify potential issues that may need attention. The additional text below the badge can provide further classification or context about the key's status, such as whether it is considered critical, deprecated, or has other noteworthy attributes that may be relevant during the review process. --}}
                        <x-ui.badge.context
                            context="translation.key.status"
                            :value="$selectedTranslationKey->status"
                            :label="str($selectedTranslationKey->status)->headline()"
                        />

                        {{-- Classification --}}
                        <x-ui.badge.context
                            context="translation.key.classification"
                            :value="$selectedTranslationKey->classification"
                            :label="str($selectedTranslationKey->classification)->headline()"
                        />
                    </flux:callout.text>
                </flux:callout>

                {{-- Group Callout with namespace and group information --}}
                <flux:callout
                    color="violet"
                    icon="folder"
                >
                    {{-- Group --}}
                    <flux:callout.heading>
                        {{ __('Group') }}
                    </flux:callout.heading>

                    <flux:callout.text class="text-sm">
                        <div>
                            <span class="font-semibold">{{ __('Namespace') }}:</span>
                            {{ $selectedTranslationKey->namespace ?? '—' }}
                        </div>

                        <div>
                            <span class="font-semibold">{{ __('Group') }}:</span>
                            {{ $selectedTranslationKey->group ?? '—' }}
                        </div>
                    </flux:callout.text>
                </flux:callout>

                {{-- Source and usage callouts in the modal provide important contextual information about the translation key. The source callout indicates where the translation key is defined or used in the codebase, which can help reviewers understand its origin and relevance. The usage callout provides insight into how widely used the key is across the application, which can be helpful for prioritizing review or identifying potential impact of changes to the key. Both callouts use visual emphasis and contextual information to aid in understanding the key's state and relevance without needing to delve into the details immediately, allowing for a more efficient review process. --}}
                <flux:callout
                    color="amber"
                    icon="scan-search"
                >
                    {{-- Source --}}
                    <flux:callout.heading>
                        {{ __('Source') }}
                    </flux:callout.heading>

                    {{-- Source-Path information about where the translation key is defined or used in the codebase, which can help reviewers understand its origin and relevance. This can include file paths, line numbers, or other contextual information that indicates where the key is located within the application's code. Providing this information in a callout allows for quick reference during the review process, enabling reviewers to easily locate the key in the codebase if needed for further investigation or context. If source information is not available, a placeholder (e.g., '—') can be displayed to indicate that this information is not currently accessible. --}}
                    <flux:callout.text class="text-sm">
                        {{ $selectedTranslationKey->source ?? '—' }}
                    </flux:callout.text>
                </flux:callout>

                {{-- Usage callout with count of how many times the translation key is used across the application, providing insight into its relevance and potential impact. This information can help reviewers prioritize their attention, as keys that are used more frequently may require more careful consideration during review or changes. The usage count can be displayed prominently within the callout, using visual emphasis to draw attention to this important piece of metadata about the translation key. If usage information is not available, a placeholder (e.g., '—') can be displayed to indicate that this information is not currently accessible. --}}
                <flux:callout
                    color="green"
                    icon="route"
                >
                    {{-- Usage --}}
                    <flux:callout.heading>
                        {{ __('Usage') }}
                    </flux:callout.heading>

                    {{-- Counter --}}
                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ $selectedTranslationKey->usages->count() }}
                    </flux:callout.text>
                </flux:callout>
            </div>

            @php
                $selectedKey = trim((string) ($selectedTranslationKey->key ?? ''));
                $selectedSuggestedKey = trim((string) ($selectedTranslationKey->suggested_key ?? ''));

                if ($selectedKey === '' && $selectedSuggestedKey !== '') {
                    $keySuggestionState = 'missing_key';
                    $keySuggestionLabel = __('Missing key');
                    $keySuggestionText = __(
                        'No translation key is set. The suggested key can be used as a starting point.',
                    );
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey === $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'matches_suggested_key';
                    $keySuggestionLabel = __('Matches suggested key');
                    $keySuggestionText = __('The current key matches the generated suggestion.');
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey !== $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'differs_from_suggested_key';
                    $keySuggestionLabel = __('Differs from suggested key');
                    $keySuggestionText = __(
                        'The current key differs from the generated suggestion. This can be intentional, but should be reviewed.',
                    );
                } else {
                    $keySuggestionState = 'no_suggestion';
                    $keySuggestionLabel = __('No suggestion');
                    $keySuggestionText = __('No suggested key is available for this entry.');
                }
            @endphp

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Key suggestion check') }}
                        </div>

                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $keySuggestionText }}
                        </div>
                    </div>

                    <x-ui.badge.context
                        context="translation.key.suggestion"
                        :value="$keySuggestionState"
                        :label="$keySuggestionLabel"
                    />
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-2">

                {{-- Translation Key and Native Text --}}
                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Key --}}
                    <x-ui.text.copyable-field
                        :title="__('Key')"
                        :value="$selectedTranslationKey->key"
                        :mono="true"
                    />
                </div>

                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Suggested key --}}
                    <x-ui.text.copyable-field
                        :title="__('Suggested key')"
                        :value="$selectedTranslationKey->suggested_key"
                        :mono="true"
                    />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                {{-- Native text --}}
                <div class="dark:border-zinc-700">

                    {{-- Native Text --}}
                    <x-ui.text.copyable-field
                        :title="__('Native text')"
                        :value="$selectedTranslationKey->native_text"
                    />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                {{-- Values and Usages --}}
                <div class="mb-3 flex items-center justify-between gap-3">

                    {{-- Values --}}
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Values') }}
                    </div>

                    {{-- Counter --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $selectedTranslationKey->values->count() }}
                    </flux:badge>
                </div>

                {{-- Values List with locale flags, translation values, and status badges. This section provides a comprehensive overview of all the translations associated with the key, allowing reviewers to quickly assess the completeness and quality of the translations for that key. Each value is displayed with its corresponding locale flag for easy identification, along with the actual translation value and a status badge that indicates the current state of that translation (e.g., active, missing, needs review). This information is crucial for understanding the overall health of the translations for that key and for identifying any potential issues or discrepancies that may require attention during the review process. If no translation values are available, a placeholder message can be displayed to indicate that this information is not currently accessible. --}}
                <div class="grid gap-3 md:grid-cols-2">

                    @forelse ($selectedTranslationKey->values as $value)
                        <div class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">

                            {{-- Value locale --}}
                            <x-ui.text.copyable-field
                                :value="$value->value"
                                :badge="$value->status"
                                badge-context="translation.value.status"
                            >
                                <x-slot:label>
                                    <span class="inline-flex items-center gap-2">

                                        {{-- Locale flag --}}
                                        <x-ui.locale.flag
                                            :locale="$value->locale"
                                            size="sm"
                                        />

                                        {{-- Locale code --}}
                                        <code>{{ $value->locale }}</code>
                                    </span>
                                </x-slot:label>
                            </x-ui.text.copyable-field>
                        </div>

                        {{-- No translation values available. --}}
                    @empty
                        <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No translation values available.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex min-h-0 flex-1 flex-col rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                {{-- Usages and detailed information about where the translation key is used across the application, including file paths, line numbers, and raw usage snippets. This section provides crucial context for reviewers to understand how the translation key is utilized within the codebase, which can help identify any potential issues or discrepancies during the review process. Each usage entry includes information about the file path where the key is used, along with an optional line number for more precise localization. Additionally, if available, a raw usage snippet can be displayed to provide further context about how the key is used in that specific instance. By presenting this information in an organized manner within the modal, reviewers can efficiently navigate through the usage details and gain a comprehensive understanding of the translation key's role within the application. If no usage records are available, a placeholder message can be displayed to indicate that this information is not currently accessible. --}}
                <div class="mb-3 flex shrink-0 items-center justify-between gap-3">

                    {{-- Usages --}}
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('Usages') }}
                    </div>

                    {{-- Counter --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $selectedTranslationKey->usages->count() }}
                    </flux:badge>
                </div>

                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto pr-2">
                    @forelse ($selectedTranslationKey->usages as $usage)
                        <div class="text-sm dark:border-zinc-700">
                            <div
                                class="flex flex-wrap items-center justify-between gap-2 border-t pt-1 dark:border-zinc-700">
                                <div class="">
                                    <span class="font-semibold">{{ __('Path') }}:</span>
                                    <code class="wrap-anywhere whitespace-normal px-3 text-xs">
                                        {{ $usage->file ?? '—' }}
                                    </code>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if (!empty($usage->line))
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ __('Line') }} {{ $usage->line }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>

                            @if (!empty($usage->raw))
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('Key')"
                                    :value="$usage->raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif
                        </div>
                    @empty
                        <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No usage records available.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex shrink-0 justify-end">
                <x-ui.button.cancel
                    label="{{ __('Close') }}"
                    icon="circle-x"
                    wire:click="closeTranslationKey"
                />
            </div>
        </div>
    @else
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('No translation key selected.') }}
        </div>
    @endif
</flux:modal>
