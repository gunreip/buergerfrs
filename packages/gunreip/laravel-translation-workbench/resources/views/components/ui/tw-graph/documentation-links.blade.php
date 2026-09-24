{{-- Navigation UI only; destinations are defined in DocumentationLinks. --}}
@props(['example' => null, 'reference' => null])
@php
    $catalog = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks::class;
    $interactive = isset($this) && $this instanceof \Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
    $exampleDefinition = $example !== null ? $catalog::EXAMPLES[$example] ?? null : null;
    $destinations = $reference !== null ? $catalog::examples($reference) : [];
    $origin = $interactive && $this->referenceOriginComponent === $reference ? $this->referenceOrigin : null;
@endphp
<nav
    class="mb-3 flex min-w-0 flex-wrap items-center gap-2"
    data-tw-graph-documentation-links
    aria-label="{{ __('Related documentation') }}"
>
    @if ($exampleDefinition)
        {{-- <flux:text size="sm">Deep Reference:</flux:text> --}}
        @foreach ($exampleDefinition['components'] as $linkedComponent)
            @php($referenceAction = $interactive ? "openReference('$linkedComponent', '$example')" : null)
            <flux:button
                size="sm"
                variant="ghost"
                icon="link"
                :href="$catalog::url($catalog::reference($linkedComponent))"
                :wire:click.prevent="$referenceAction"
            >{{ $linkedComponent }}</flux:button>
        @endforeach
    @elseif ($reference)
        @if ($origin && isset($catalog::EXAMPLES[$origin]))
            <flux:button
                size="sm"
                icon="arrow-left"
                :href="$catalog::url($this->referenceOriginTabs, isset($catalog::EXAMPLES[$origin]['tabs']['results']) ? 'tw-graph-documentation-results' : 'tw-graph-documentation')"
                wire:click.prevent="returnToExample"
            >{{ __('Back to example') }}: {{ $catalog::EXAMPLES[$origin]['label'] }}</flux:button>
        @endif
        @if (count($destinations) === 1)
            @foreach ($destinations as $key => $destination)
                @php($exampleAction = $interactive ? "openExample('$key')" : null)
                <flux:button
                    size="sm"
                    icon="eye"
                    :href="$catalog::url($destination['tabs'], isset($destination['tabs']['results']) ?
                        'tw-graph-documentation-results' : 'tw-graph-documentation')"
                    :wire:click.prevent="$exampleAction"
                >{{ __('Preview') }}: {{ $destination['label'] }}</flux:button>
            @endforeach
        @elseif ($destinations)
            <flux:dropdown>
                <flux:button
                    size="sm"
                    icon="eye"
                    icon:trailing="chevron-down"
                >{{ __('Previews') }} ({{ count($destinations) }})</flux:button>
                <flux:menu>
                    @foreach ($destinations as $key => $destination)
                        @php($exampleAction = $interactive ? "openExample('$key')" : null)
                        <flux:menu.item
                            :href="$catalog::url($destination['tabs'], isset($destination['tabs']['results']) ?
                                'tw-graph-documentation-results' : 'tw-graph-documentation')"
                            :wire:click.prevent="$exampleAction"
                        >{{ $destination['label'] }}</flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @else
            <flux:text size="sm">{{ __('No preview for this component in these documentation tabs yet.') }}
            </flux:text>
        @endif
    @endif
</nav>
