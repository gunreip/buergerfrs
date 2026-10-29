# Translation Workbench UI test mode

The **Testcase** toggle is in the Workbench page header. It is available in local/testing environments to authorized Workbench editors.

- Every activation creates a new PostgreSQL database named `tw_ui_test_<random token>` and synthetic project files under `storage/app/private/workbench-ui-tests/<token>`.
- Normal Workbench components, assignment/draft/review services, publication and source-replacement code operate against that isolated dataset.
- Switching off removes the identified test database and test files. Switching on again seeds the original cases. Open dialogs, selections, unsaved fields and pagination are reset.
- The mode is bound to the authenticated browser session. A page refresh keeps the current test run. Old tabs or dialogs whose run no longer matches the session fail with HTTP 409 and must be reloaded; they never fall back to working data.
- The default Laravel database, authentication, application source and language paths are not changed. Test publications and source replacements receive explicit sandbox paths. VS Code links in the Findings table target fixture files in test mode.
- No working findings or translations are copied. Initial fixture history is produced by the normal services, with explicit `UI fixture` reasons.

## Initial cases

24 findings, with three variants for each scenario:

- Unassigned and assigned without a draft.
- Shared keys, each linked to three independent findings.
- Archived and moved findings with successors.

All initial literals are English (`en`). No translation revisions, reviews, publication runs or exported language values are prepared, including for `en`, `de` and `de-AT`. Users create these states through the normal workflow and can then test subsequent changes. Variants include short and long labels/keys, placeholders and multiline strings. Existing assignments, shared keys and archived/moved source relationships remain available as structural examples. Source usage provides nine cases: three open, three Translate and three DatabaseValue decisions. Observed option values are synthetic as well.

The fixture catalog is in `cases.json` inside the run directory; source filenames identify the scenario. These are extensible examples, not a claim that every possible application state is covered.

## Isolation checks

`php tests/Integration/TranslationWorkbench/ui-test-environment.php` uses its own temporary PostgreSQL cluster. It tests assignments, source decisions, language publication, source replacement, reset, stale-token rejection and unchanged working-data/file sentinels. It does not connect to the working database.

UI unit tests additionally cover field reset and repeated activation. Normal source-replacement integration tests protect the shared writer's relative-path contract.

Creating a test run requires PostgreSQL CREATE DATABASE permission. A failure is reported in the UI and application log; the application does not substitute the working database.

Test runs are removed when switching off or starting a replacement run in the same session. Abandoned/expired sessions can leave isolated databases/files behind; there is currently no automatic expiry cleanup. Their names and database comments identify them as UI test data.

## Implementation files

Under `packages/gunreip/laravel-translation-workbench/`:

- `src/Support/WorkbenchTestEnvironment.php` — session identity, database/file isolation and reset.
- `src/Support/WorkbenchTestFixtures.php` — synthetic scenarios.
- `src/Support/GenerationCaseDecisions.php` — injectable fixture snapshot, database and source root.
- `src/Generation/GenerationEditor.php` and `ScanCaptureDecisions.php` — explicit source/language roots.
- `src/Generation/SourceReplacementWriter.php` — source-root propagation and relative scanner path.
- `src/TranslationWorkbenchServiceProvider.php` — request-scoped environment service.
- `src/Livewire/TranslationWorkbench.php` — toggle, context reset, test environment selection.
- `src/Livewire/SourceCaseReview.php` — locked test-run identity in the child component.
- `resources/views/livewire/translation-workbench.blade.php` — page orchestrator.
- `resources/views/livewire/translation-workbench/tabs/source-usage/index.blade.php` — child component identity.
- `resources/views/livewire/translation-workbench/tabs/findings/table.blade.php` — fixture-aware VS Code path.

Tests:

- `tests/Unit/TranslationWorkbench/TranslationWorkbenchTest.php`
- `tests/Integration/TranslationWorkbench/ui-test-environment.php`


## View structure

The page entry is `resources/views/livewire/translation-workbench.blade.php`.
It includes these sections in order:

- `page/header.blade.php`: heading and testcase toggle.
- `page/test-environment.blade.php`: testcase errors, switch overlay and environment notice.
- `overview/index.blade.php`: working data, scan results, publication runs and source runs.
- `tabs/index.blade.php`: tab navigation, matching panels and shared loading overlay.
- `modals/index.blade.php`: editor and confirmation dialogs.

Each tab has its own `tabs/<tab>/index.blade.php`. The five table tabs
(`findings`, `translations`, `review`, `publication`, `published`) include
`filters.blade.php` and `table.blade.php` from their own folder.
The table partial includes its pagination; the tab entry owns its callout and errors.
`source-usage/index.blade.php` mounts the existing Livewire child and includes
`source-usage/observed-values.blade.php`. The child class `SourceCaseReview` renders
`source-usage/content.blade.php`, which includes `header.blade.php`,
`filters.blade.php`, `table.blade.php` (with pagination) and
`decision-modal.blade.php` from the same folder. The database-value preview includes
`observed-values-table.blade.php` there. All view files for this tab live together;
the separate Livewire scope preserves its actions, filters and modal state.

`shared/finding-candidates.blade.php` is shared by the Findings table and finding editor.
`scan/` holds scan-dialog content. These are not workbench tab entry points.

Tab order is Findings → Translations → Review → Publication → Published → Source usage.
Rendering conditions remain unchanged: Findings stays mounted; other tab content renders
when selected. Moving these views does not alter query, editing or publication behavior.
