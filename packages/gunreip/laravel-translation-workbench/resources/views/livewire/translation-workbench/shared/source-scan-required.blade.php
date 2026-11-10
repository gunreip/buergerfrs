<flux:callout color="amber" icon="arrow-path" :heading="__('New scan required')">
    <flux:callout.text>{{ __('A stored occurrence in this file no longer matches the current source. The regular scanner must determine whether it was moved, changed or removed. Source replacement remains blocked until the scan results resolve this discrepancy.') }}</flux:callout.text>
    <x-translation-workbench::ui.common.details class="mt-3">
        <x-translation-workbench::ui.common.details.item :label="__('Finding to check')" :item="'#'.$scanRequired['finding']" />
        <x-translation-workbench::ui.common.details.item :label="__('Source file')">
            <x-translation-workbench::ui.common.path-preview :path="$scanRequired['path']" :length="80" side="center" />
        </x-translation-workbench::ui.common.details.item>
        <x-translation-workbench::ui.common.details.item :label="__('Stored line / column')" :item="$scanRequired['line'].':'.$scanRequired['column']" class="tabular-nums" />
    </x-translation-workbench::ui.common.details>
    <flux:callout.text class="mt-3">{{ __('Use the regular scan commands from Scan results → Run another scan in the project terminal. The configured source paths determine the scope. Preview first, then scan and save the results. No source files or translations are published by these commands.') }}</flux:callout.text>
    <div class="mt-3 space-y-3">
        <flux:field>
            <flux:label>{{ __('Preview only') }}</flux:label>
            <flux:input value="php artisan translation-workbench:generation-scan" readonly copyable />
        </flux:field>
        <flux:field>
            <flux:label>{{ __('Scan and save results') }}</flux:label>
            <flux:input :value="'php artisan translation-workbench:generation-scan --apply --actor='.auth()->id()" readonly copyable />
        </flux:field>
    </div>
    <flux:callout.text class="mt-3">{{ __('After the scan, refresh the source replacement list and select the key again. If the scanner reports an unresolved case, review that result before continuing. Do not archive the finding merely to bypass this blocker.') }}</flux:callout.text>
</flux:callout>
