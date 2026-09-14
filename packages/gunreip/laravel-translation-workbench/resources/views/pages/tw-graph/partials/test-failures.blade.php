<div class="mt-3 min-w-0 space-y-3">
    <flux:heading size="sm">{{ __('Failure details') }}</flux:heading>
    @forelse ((array) data_get($check, 'parsed_output.failures', []) as $failure)
        <div class="space-y-2">
            <flux:text class="font-semibold break-all">{{ data_get($failure, 'test', 'Failed test') }}</flux:text>
            @if (data_get($failure, 'file'))
                <flux:text class="font-mono text-xs break-all">{{ data_get($failure, 'file') . (data_get($failure, 'line') ? ':' . data_get($failure, 'line') : '') }}</flux:text>
            @endif
            <pre class="max-h-96 max-w-3xl overflow-auto whitespace-pre-wrap break-all rounded-md bg-zinc-950 p-3 text-xs text-zinc-100">{{ data_get($failure, 'message', '') }}</pre>
        </div>
    @empty
        <pre class="max-h-96 max-w-3xl overflow-auto whitespace-pre-wrap break-all rounded-md bg-zinc-950 p-3 text-xs text-zinc-100">{{ data_get($check, 'output', __('No failure details available.')) }}</pre>
    @endforelse
</div>
