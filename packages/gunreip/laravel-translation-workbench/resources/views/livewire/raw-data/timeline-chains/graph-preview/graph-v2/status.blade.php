{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/status.blade.php --}}

<div class="flex flex-wrap items-center gap-2">
    <flux:badge
        size="sm"
        color="rose"
    >
        {{ __('Active context') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="cyan"
    >
        {{ __('geometry-cache dev-preview') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="zinc"
    >
        {{ __('anchor-points frozen') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="amber"
    >
        {{ __('merge cached reference') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="{{ data_get($graphV2, 'trunkColor', 'green') }}"
    >
        {{ __('trunk') }}: {{ data_get($graphV2, 'trunkStartLength', '0') }} + {{ count(data_get($graphV2, 'trunkPathSegments', [])) }} + {{ data_get($graphV2, 'trunkEndLength', '0') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="{{ data_get($graphV2, 'readSource') === 'cache' ? 'green' : (data_get($graphV2, 'readSource') === 'seed' ? 'amber' : 'red') }}"
        title="{{ data_get($graphV2, 'readSource') === 'cache' ? data_get($graphV2, 'cache')->path() : (data_get($graphV2, 'cache')->seedPath() ?: '') }}"
    >
        {{ __('Read') }}: {{ data_get($graphV2, 'readSource') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="{{ data_get($graphV2, 'writeOk') ? 'sky' : 'red' }}"
        title="{{ data_get($graphV2, 'cache')->path() }}"
    >
        {{ __('Write') }}: {{ data_get($graphV2, 'writeOk') ? __('storage') : __('blocked') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="{{ data_get($graphV2, 'cacheFileWritable') ? 'green' : 'red' }}"
        title="{{ data_get($graphV2, 'cachePath') }}"
    >
        {{ __('File') }}: {{ data_get($graphV2, 'cacheFileWritable') ? __('writable') : __('blocked') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="{{ data_get($graphV2, 'cacheDirectoryWritable') ? 'green' : 'red' }}"
        title="{{ data_get($graphV2, 'cacheDirectory') }}"
    >
        {{ __('Dir') }}: {{ data_get($graphV2, 'cacheDirectoryWritable') ? __('writable') : __('blocked') }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="zinc"
        title="{{ __('get_current_user') }}: {{ data_get($graphV2, 'processUser') }}"
    >
        {{ __('PHP') }}: {{ data_get($graphV2, 'processEffectiveUser') }}:{{ data_get($graphV2, 'processEffectiveGroup') }}
    </flux:badge>
</div>
