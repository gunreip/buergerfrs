@props(['path', 'line', 'column', 'showPosition' => false])

<div class="flex min-w-0 items-center gap-1">
    <div class="min-w-0">
        <x-translation-workbench::ui.common.path-preview :path="$path" :vsc="false" :length="80" side="center"
            :line="$showPosition ? $line : null" :column="$showPosition ? $column : null" />
    </div>
    <x-translation-workbench::ui.common.vsc-link :path="$path" :line="$line" :column="$column" />
</div>
