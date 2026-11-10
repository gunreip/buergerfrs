@props(['content', 'maxHeight' => '24rem', 'copyContext' => null])

@php
    $copySuccessMessage = filled($copyContext)
        ? __(':context copied.', ['context' => $copyContext])
        : __('Copied.');
    $copyFailureMessage = filled($copyContext)
        ? __('Could not copy :context. Please select and copy the text manually.', ['context' => $copyContext])
        : __('Copy failed. Please select and copy the text manually.');
@endphp

<div
    {{ $attributes->class(['relative min-w-0 overflow-hidden rounded-lg border border-zinc-200 bg-zinc-50 dark:border-white/10 dark:bg-zinc-800']) }}
    x-data="{
        async copyContent() {
            try {
                await navigator.clipboard.writeText(this.$refs.content.textContent);
            } catch (error) {
                this.$flux.toast(@js($copyFailureMessage), { variant: 'danger', duration: 8000 });
                return;
            }
            this.$flux.toast(@js($copySuccessMessage), { variant: 'success', duration: 3000 });
        }
    }"
>
    <div class="inset-e-3 absolute top-3 z-10">
        <flux:button
            type="button"
            size="sm"
            variant="outline"
            icon="clipboard-document"
            :aria-label="__('Copy to clipboard')"
            :tooltip="__('Copy to clipboard')"
            x-on:click="copyContent()"
        />
    </div>
    <pre
        class="wrap-anywhere m-0 overflow-auto whitespace-pre-wrap py-3 pe-16 ps-3 font-mono text-xs text-zinc-800 dark:text-zinc-100"
        tabindex="0"
        style="max-height: {{ $maxHeight }}"
    ><code x-ref="content">{{ $content }}</code></pre>
</div>
