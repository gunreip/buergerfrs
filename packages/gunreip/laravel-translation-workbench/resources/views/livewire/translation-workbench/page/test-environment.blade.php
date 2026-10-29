<flux:error name="testcase" />

<x-ui.loading.lazy-indicator
    class="absolute inset-0 z-50"
    role="status"
    box-class="h-full w-full rounded-lg bg-white/75 dark:bg-zinc-900/75"
    target="toggleTestcase"
    :text="__('Switching workbench dataset…')"
/>
@if ($testToken !== '')
    <flux:callout
        class="space-y-4"
        color="amber"
        icon="beaker"
        heading="{{ __('UI test environment') }}"
        text="{{ __('Synthetic datasets: three variants per state, shared keys, multiple locales, archived and moved findings, drafts, approvals and publications. Source usage includes open, Translate and DatabaseValue decisions. File writes remain inside the test directory. Switching off discards this test run; switching on creates a fresh one.') }}"
    >
    </flux:callout>
@endif
