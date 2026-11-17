@props(['path', 'line' => null, 'column' => null])

@php
    $sourcePath = str_replace('\\', '/', (string) $path);
    $root = app(\Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment::class)->root();
    $absolutePath = str_starts_with($sourcePath, '/') ? $sourcePath : $root . '/' . $sourcePath;
    $file = str_replace('%2F', '/', rawurlencode($absolutePath));
    $distro = rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro'));
    $position = max(1, (int) $line) . ':' . max(1, (int) $column);
@endphp

<flux:tooltip>
    <flux:button
        class="shrink-0"
        size="xs"
        variant="ghost"
        square
        icon="external-link"
        :href="'vscode://vscode-remote/wsl+'.$distro.$file.
        ':'.$position"
        :aria-label="__('Open source in VS Code at :position', ['position' => $position])"
    />
    <x-translation-workbench::ui.common.tooltip-content
        :heading="__('Open source in VS Code')"
        :text="$path . ':' . $position"
    />
</flux:tooltip>
