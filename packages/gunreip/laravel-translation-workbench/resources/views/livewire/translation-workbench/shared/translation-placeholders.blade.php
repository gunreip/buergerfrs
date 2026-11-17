@php
    $states = $field === 'sourceLanguageValue'
        ? ['source' => ['amber', 'exclamation-circle', __('Source placeholder')]]
        : [
            'matching' => ['green', 'check', __('Matching placeholder')],
            'missing' => ['red', 'exclamation-circle', __('Missing source placeholder')],
            'unexpected' => ['red', 'exclamation-circle', __('Unexpected target placeholder')],
        ];
@endphp
<div
    x-data="{
        names(text) {
            return [...new Set((text ?? '').match(/(?<![:\p{L}\p{N}_]):[\p{L}_][\p{L}\p{N}_]*/gu) ?? [])];
        },
        get groups() {
            const source = this.names(this.$wire.sourceLanguageValue);
            @if ($field === 'sourceLanguageValue')
                return { source };
            @else
                const target = this.names(this.$wire.$get(@js($field)));
                return {
                    matching: source.filter(name => target.includes(name)),
                    missing: source.filter(name => !target.includes(name)),
                    unexpected: target.filter(name => !source.includes(name)),
                };
            @endif
        }
    }"
    x-show="Object.values(groups).some(names => names.length)"
    x-cloak
    class="flex flex-wrap items-center gap-1"
>
    @foreach ($states as $state => [$color, $icon, $description])
        <template x-for="name in groups['{{ $state }}']" :key="name">
            <flux:badge :color="$color" :icon="$icon" size="sm" :title="$description">
                <span class="sr-only">{{ $description }}</span>
                <span x-text="name"></span>
            </flux:badge>
        </template>
    @endforeach
</div>
