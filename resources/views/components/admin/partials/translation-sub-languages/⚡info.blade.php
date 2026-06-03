{{-- resources/views/components/admin/partials/translation-sub-languages/⚡info.blade.php --}}

<flux:callout
    class="mt-6"
    color="sky"
    icon="information-circle"
>
    <flux:callout.heading>
        {{ __('Sub-Language Behavior') }}
    </flux:callout.heading>

    <flux:callout.text class="font-extralight">
        {{ __('Behavior: Main language values are the baseline; Sub-language values override matching keys only when they exist.') }}
    </flux:callout.text>
</flux:callout>
