{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/build/file-path.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label @class([
        'text-emerald-600 dark:text-emerald-400' => $builtSourceFilePath !== '' && $builtSourceFilePathExists,
        'text-red-600 dark:text-red-400' => $builtSourceFilePath !== '' && ! $builtSourceFilePathExists,
    ])>
        {{ __('Filtered path/to/file result') }}
    </flux:label>

    <flux:textarea
        rows="auto"
        readonly
        copyable
        :invalid="$builtSourceFilePath !== '' && ! $builtSourceFilePathExists"
        description="{{ __('This is the path/to/file based on the selected source file segments. You can copy it to your clipboard.') }}"
    >
        {{ $builtSourceFilePath !== '' ? $builtSourceFilePath : __('Select source file segments') }}
    </flux:textarea>
</flux:field>
