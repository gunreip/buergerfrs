@props(['issues' => []])

<flux:callout variant="warning" icon="exclamation-triangle" class="my-3" data-tw-graph-layout-issues="true" role="alert">
    <flux:callout.heading>{{ __('Graph layout could not be completed') }}</flux:callout.heading>
    <flux:callout.text>
        {{ __('The affected path was not rendered. Adjust the authored layout; lengths and anchors have not been changed automatically.') }}
        <ul class="mt-2 space-y-2">
            @foreach ($issues as $issue)
                <li>
                    <code>{{ $issue['component'] }}</code> — <code>{{ $issue['id'] }}</code><br>
                    <code>{{ $issue['property'] }}</code>:
                    {{ __('Calculated') }} <code>{{ $issue['actual'] === null ? __('unresolved') : $issue['actual'] . 'rem' }}</code>;
                    {{ __('Required') }} <code>{{ $issue['expected'] }}</code>.
                </li>
            @endforeach
        </ul>
    </flux:callout.text>
</flux:callout>
