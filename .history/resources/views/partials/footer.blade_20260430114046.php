<footer class="w-full py-2 text-center text-xs text-zinc-500 dark:text-zinc-400">
    @php
        $version = null;
        if (file_exists(public_path('version.txt'))) {
            $version = trim(file_get_contents(public_path('version.txt')));
        }
    @endphp
    @if ($version)
        <span>Version: {{ $version }}</span>
    @else
        <span>Version: n/a</span>
    @endif
</footer>
