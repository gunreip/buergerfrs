{{-- Fixed desktop positions: Finding ID | Key ID | Search | Context | Toggles | Per page.
     Empty slots reserve their column only when the complete row fits the container. --}}
<div {{ $attributes->class('@container my-4') }}>
    <div
        class="@min-[40rem]:grid-cols-2 @min-[76rem]:grid-cols-[16rem_16rem_minmax(0,1fr)_18rem_6rem_14rem] grid grid-cols-1 items-end gap-3">
        @foreach (['findingId', 'keyId', 'search', 'context', 'toggles', 'perPage'] as $position)
            @php($content = ${$position} ?? null)
            <div
                @class(['min-w-0', 'hidden @min-[76rem]:block' => $content === null])
                @if ($content === null) aria-hidden="true" @endif
            >
                {{ $content }}
            </div>
        @endforeach
    </div>
</div>
