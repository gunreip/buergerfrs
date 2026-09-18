# TW-Graph prop contracts

`PropContractTest.php` checks observable geometry against public prop values and defaults. `Tests\Support\PropContract` collects all mismatches within a dataset before failing it. Messages identify the component, prop, expected value and rendered value.

Current coverage includes `parts.start`, `parts.sideways` and `strang.flow-switch-case`: independent bridge lengths, arc radius, stem length, default outgoing bridge length and fall-through on/off. `SwitchCaseViewTest.php` additionally covers the join offset and endpoint markers. This is explicit contract coverage, not a universal detector for arbitrary source-code overrides.

When adding a component prop or conditional rendering mode:

- Test an omitted prop and at least two distinct explicit values.
- Exercise directions and conditional modes that could overwrite it.
- Measure rendered geometry or inspect the relevant rendered element, rather than recomputing the expected result through the same production helper.
- Document intentional clamping, inheritance and alignment compensation; test that contract separately. Do not bless a sample-specific override as the expected result.
- Keep sample-specific dimensions in the handmade example and explain them in its prop table.

`php artisan clear:project --tw-graph-tests` runs the Props contracts group and all other TW-Graph test files. Unassigned `*Test.php` files are discovered automatically. No stop-on-failure flag is used. Process timeouts/errors are reported while subsequent checks continue. Confirmed mismatches remain failures and result in a nonzero final exit code; they are not downgraded to warnings or reported as a successful run.

Console, JSON/HTML report and the diagnostics view retain individual failures, including Pest's `error_details` output. The watcher continues after a failed run.

Focused run (isolated test database):

```sh
DB_CONNECTION=sqlite DB_DATABASE=:memory: DB_URL= php vendor/bin/pest tests/Unit/TwGraph/PropContractTest.php tests/Unit/TwGraph/SwitchCaseViewTest.php tests/Unit/TwGraph/ClearProjectTwGraphReportTest.php
```
