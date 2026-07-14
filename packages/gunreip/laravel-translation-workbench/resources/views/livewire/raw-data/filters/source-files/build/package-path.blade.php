{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/source-files/build/package-path.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label @class([
        'text-emerald-600 dark:text-emerald-400' => $builtSourcePackagePath !== '' && $builtSourcePackagePathExists,
        'text-red-600 dark:text-red-400' => $builtSourcePackagePath !== '' && ! $builtSourcePackagePathExists,
    ])>
        {{ __('Filtered path/to/package result') }}
    </flux:label>

    <flux:textarea
        rows="auto"
        readonly
        copyable
        :invalid="$builtSourcePackagePath !== '' && ! $builtSourcePackagePathExists"
        description="{{ __('This is the path/to/package based on the selected package segments. You can copy it to your clipboard.') }}"
    >
        {{ $builtSourcePackagePath !== '' ? $builtSourcePackagePath : __('Select package segments') }}
    </flux:textarea>
</flux:field>
