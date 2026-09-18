{{-- Shared UI only: every language example is an independently authored source file. --}}
@props(['sourceView', 'example' => 'switch'])
@php
    $sourceDirectory = dirname(\Illuminate\Support\Facades\View::getFinder()->find($sourceView)) . '/code-examples';
    $languages = ['php' => 'PHP', 'js' => 'JavaScript', 'c' => 'C', 'cpp' => 'C++', 'cs' => 'C#', 'java' => 'Java'];
@endphp
<section {{ $attributes->class('mt-5 min-w-0') }} x-data="{ exampleLanguage: 'php' }">
    <flux:heading size="sm">{{ __('Language examples') }}</flux:heading>
    {{ $slot }}
    <flux:tab.group class="mt-3 min-w-0 max-w-full">
        <flux:tabs x-model="exampleLanguage" scrollable scrollable:fade scrollable:scrollbar="hide">
            @foreach ($languages as $extension => $language)
                <flux:tab :name="$extension">{{ $language }}</flux:tab>
            @endforeach
        </flux:tabs>
        @foreach ($languages as $extension => $language)
            @php
                $sourceFile = $sourceDirectory . '/' . $example . '.' . $extension;
                $languageCode = file_get_contents($sourceFile);
                if ($languageCode === false) {
                    throw new \RuntimeException('Unable to read language example: ' . $sourceFile);
                }
            @endphp
            <flux:tab.panel :name="$extension" class="pt-3">
                <x-translation-workbench::ui.tw-graph.code-box max-height="28rem">{{ rtrim($languageCode) }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:tab.panel>
        @endforeach
    </flux:tab.group>
</section>
