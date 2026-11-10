<flux:callout
    class="mb-4 scroll-mt-6"
    id="runtime-option-observation"
    color="sky"
    icon="square-code"
    heading="{{ __('Observed database option values') }}"
    text="{{ __('Read-only value preview. Use Review source usage above to save a Translate or DatabaseValue decision for an individual source occurrence.') }}"
>
    @if ($runtimeOptions === null)
        <flux:callout.text>
            {{ __('No verified observation is available, or its source definitions changed.') }}
        </flux:callout.text>
    @else
        <x-translation-workbench::ui.common.details>
            <x-translation-workbench::ui.common.details.item :label="__('Observed at')">
                <x-ui.date-time.date-time :value="$runtimeOptions['observed_at']" />
            </x-translation-workbench::ui.common.details.item>
        </x-translation-workbench::ui.common.details>
        <flux:callout.text>
            {{ __('Read-only snapshot of application tables, separate from the captured findings. These are observed values, not translations or DatabaseValue decisions. Code defaults and unsaved UI additions are not included. Empty results do not retire findings.') }}
        </flux:callout.text>

        <flux:accordion class="mt-3">
            @foreach ($runtimeOptions['groups'] as $groupId => $group)
                @php
                    $sectionHeading = match ($groupId) {
                        'document_types' => __('Document types'),
                        'document_categories' => __('Document categories'),
                        'role_categories' => __('Role categories'),
                    };
                @endphp
                <flux:accordion.item>
                    <flux:accordion.heading>
                        <flux:callout.heading>
                            {{ $sectionHeading }}
                            ({{ $group['count'] }})
                        </flux:callout.heading>
                    </flux:accordion.heading>

                    <flux:accordion.content>
                        <x-translation-workbench::ui.common.details>
                            <x-translation-workbench::ui.common.details.item :label="__('Table / columns')">
                                <flux:callout.text>
                                    <code>{{ $group['table'] }} · {{ $group['columns'] }}</code>
                                </flux:callout.text>
                            </x-translation-workbench::ui.common.details.item>
                            <x-translation-workbench::ui.common.details.item :label="__('Selection')">
                                <flux:callout.text>
                                    <code>{{ $group['selection'] }}</code>
                                </flux:callout.text>
                            </x-translation-workbench::ui.common.details.item>
                        </x-translation-workbench::ui.common.details>

                        <flux:callout
                            class="mt-5"
                            color="sky"
                            icon="list-check"
                            :heading="$sectionHeading"
                        >
                            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.observed-values-table')
                        </flux:callout>
                    </flux:accordion.content>
                </flux:accordion.item>
            @endforeach
        </flux:accordion>
    @endif
</flux:callout>
