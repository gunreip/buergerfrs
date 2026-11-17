@switch($state)
    @case('unchanged')
        {{ __('Unchanged') }}
        @break
    @case('reformatted')
        {{ __('Call reformatted at the same position') }}
        @break
    @case('unique_move')
        {{ __('Unique move') }} · {{ $destination }}
        @if (($reason ?? null) === 'occupied_destination')
            · {{ __('Destination occupied by an unresolved finding') }}
        @endif
        @break
    @case('review')
        {{ __('Decision required') }} · {{ match ($reason) {
            'ambiguous' => __('Several findings or positions match this expression.'),
            'no_match' => __('No remaining exact match in this file.'),
            default => __('The scan flagged this occurrence for review.'),
        } }}
        @break
    @default
        {{ __('No position preview recorded') }}
@endswitch
