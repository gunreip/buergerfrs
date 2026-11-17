<flux:input.group.prefix>
    <flux:tooltip>
        <flux:callout.text tabindex="0">{{ __('Per page') }}</flux:callout.text>
        <x-translation-workbench::ui.common.tooltip-content
            :heading="__('Per page')"
            :text="__('Choose 10, 25, 50 or 100 rows per page. The same filters apply to every page.')"
        />
    </flux:tooltip>
</flux:input.group.prefix>
