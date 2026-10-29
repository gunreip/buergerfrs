@php
    $blocker = $previewStatus['blocker'] ?? null;
    $alreadySet = (bool) ($previewStatus['already_set'] ?? false);
    $ready = !$alreadySet && !$blocker && ($previewStatus['ready'] ?? false);
    $heading = $alreadySet
        ? __('Assigned key already present')
        : ($ready
            ? __('Ready for source replacement')
            : __('Source replacement blocked'));
    $nextStep = match (true) {
        $alreadySet => __('No source replacement is needed for this occurrence.'),
        $ready => __('Choose a reason and prepare the replacement. The file is written only after confirmation.'),
        $blocker === 'Save and approve a revision in the source language before replacing the literal.' => __(
            'Save and approve the source-language text in Edit finding, then reopen this preview.',
        ),
        $blocker === 'Publish the approved source-language revision first. The file does not contain that value.' => __(
            'Publish the approved source-language revision, then reopen this preview.',
        ),
        default => __('Check the source-language file and the reported cause, then reopen this preview.'),
    };
@endphp

<flux:callout
    :color="$alreadySet ? 'sky' : ($ready ? 'green' : 'red')"
    :icon="$alreadySet ? 'info' : ($ready ? 'circle-check' : 'circle-alert')"
    :heading="$heading"
>
    <flux:callout.text>
        {{ $blocker ? __($blocker) : ($ready ? __('The exact source occurrence and the published, approved source-language value match this preview.') : __('The source replacement is not ready.')) }}
    </flux:callout.text>
    <div class="flex items-baseline gap-2">
        <flux:callout.heading class="shrink-0">{{ __('Next step') }}:</flux:callout.heading>
        <flux:callout.text class="min-w-0">{{ $nextStep }}</flux:callout.text>
    </div>
</flux:callout>
