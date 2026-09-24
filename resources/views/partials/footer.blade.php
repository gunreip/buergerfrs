<footer class="w-full py-2 text-center text-xs text-zinc-500 dark:text-zinc-400">
    @php($versionInfo = app(\App\Support\AppVersion::class)->details())
    <div>
        buergerfrs {{ $versionInfo['versions']['buergerfrs'] }} ·
        Translation Workbench {{ $versionInfo['versions']['translation-workbench'] }} ·
        TW-Graph {{ $versionInfo['versions']['tw-graph'] }}
    </div>
    <div>
        Git: {{ $versionInfo['git'] }}
        @if ($versionInfo['watch'])
            · Watch {{ $versionInfo['watch']['count'] }}
        @endif
    </div>
</footer>
