@if (empty($summary['key_id']) && ($summary['candidates'] ?? 0) >= 2 && ($summary['linked'] ?? 0) > 0)
    @php
        $linkedKey =
            ($summary['linked_namespace'] ? $summary['linked_namespace'] . '::' : '') . $summary['linked_name'];
    @endphp
    @if ($asBadge)
        @php
            $message =
                $summary['key_count'] == 1
                    ? __(':count matching findings use key #:id · :key', [
                        'id' => $summary['single_key'],
                        'key' => $linkedKey,
                        'count' => $summary['linked'],
                    ])
                    : __(':count matching findings are linked across :keys translation keys.', [
                        'count' => $summary['linked'],
                        'keys' => $summary['key_count'],
                    ]);
        @endphp
        <flux:tooltip toggleable>
            <flux:badge
                as="button"
                size="sm"
                color="fuchsia"
            >
                {{ __('Shared candidate · :count linked', ['count' => $summary['linked']]) }}
            </flux:badge>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Linked findings')"
                :text="$message"
            />
        </flux:tooltip>
    @else
        <flux:callout
            class="my-3"
            color="fuchsia"
            icon="link"
            :heading="__('Linked findings')"
        >
            <x-translation-workbench::ui.common.details
                label-width="9rem"
                row-gap="0.125rem"
            >
                <x-translation-workbench::ui.common.details.item
                    class="tabular-nums"
                    :label="__('Linked findings')"
                    :item="$summary['linked']"
                />
                @if ($summary['key_count'] == 1)
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Key ID')"
                        :item="'#' . $summary['single_key']"
                    />
                    <x-translation-workbench::ui.common.details.item :label="__('Translation key')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$linkedKey"
                            :length="56"
                            :mono="true"
                            :heading="__('Translation key')"
                        />
                    </x-translation-workbench::ui.common.details.item>
                @else
                    <x-translation-workbench::ui.common.details.item
                        class="tabular-nums"
                        :label="__('Translation keys')"
                        :item="$summary['key_count']"
                    />
                @endif
            </x-translation-workbench::ui.common.details>
            <flux:callout.text>
                {{ __('For a separate translation, assign this finding its own key here. To reuse a translation, open a linked finding below, choose “View matching findings” and select finding #:id.', ['id' => $candidateFinding]) }}
            </flux:callout.text>
            <div class="mt-2 flex flex-wrap justify-end gap-2">
                @foreach ($summary['linked_targets'] ?? [] as $target)
                    <flux:button.group>
                        <flux:button
                            size="sm"
                            variant="filled"
                            color="fuchsia"
                            wire:click="open({{ $target['finding'] }})"
                        >
                            {{ __('Open finding #:id', ['id' => $target['finding']]) }}
                            @if ($summary['key_count'] > 1)
                                <span class="whitespace-normal break-all">{{ $target['name'] }}</span>
                            @endif
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="filled"
                            square
                            color="fuchsia"
                            wire:click="open({{ $target['finding'] }})"
                        >
                            <flux:icon.arrow-right size="5" />
                        </flux:button>
                    </flux:button.group>
                @endforeach
            </div>
        </flux:callout>
    @endif
@endif
