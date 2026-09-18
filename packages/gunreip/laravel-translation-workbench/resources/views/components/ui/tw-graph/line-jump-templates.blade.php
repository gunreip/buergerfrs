{{-- Reuse the drawing primitive for crossings positioned after browser layout. --}}
@foreach (['top', 'bottom', 'left', 'right'] as $jumpSide)
    <template data-tw-graph-line-jump-template="{{ $jumpSide }}">
        <x-translation-workbench::ui.tw-graph.primitives.line-jump :side="$jumpSide" :template="true" />
    </template>
@endforeach
