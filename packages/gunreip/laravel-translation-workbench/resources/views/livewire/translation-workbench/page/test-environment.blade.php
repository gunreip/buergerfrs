<flux:error name="testcase" />
@if ($testToken !== '')
    <flux:callout
        data-workbench-test-environment
        class="space-y-4"
        color="amber"
        icon="beaker"
        heading="{{ __('UI test environment') }}"
        text="{{ __('Synthetic datasets: three variants per state, shared keys, multiple locales, archived and moved findings, drafts, approvals and publications. Source usage includes open, Translate and DatabaseValue decisions. File writes remain inside the test directory. Switching off discards this test run; switching on creates a fresh one.') }}"
    >
    </flux:callout>
@endif
