<flux:tooltip>
    <flux:badge size="sm" color="amber" tabindex="0">{{ __('Duplicate finding') }}</flux:badge>
    <x-translation-workbench::ui.common.tooltip-content
        :heading="__('Duplicate finding')"
        :text="__('Finding #:active already records the exact call. You can keep it and archive #:old as a duplicate after comparing the source.', ['active' => $retained, 'old' => $findingId])"
    />
</flux:tooltip>
