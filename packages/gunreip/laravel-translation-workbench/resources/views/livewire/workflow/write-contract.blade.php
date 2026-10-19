<div
    class="space-y-4"
    id="workflow-write-contract"
>
    <flux:callout color="violet">
        <flux:callout.heading icon="document-text">{{ __('Shared write contract — implementation baseline') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('These are target rules for consolidation, not guarantees of the current implementation. The current entry points identify the code to migrate. Service names describe responsibilities; they do not claim that new PHP classes already exist. No writer is disabled and no translation data is changed by this page.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">{{ __('W03 progress — scalar and dynamic value editors extracted') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('The Workbench translation modal delegates source, target and selected sub-locale values to TranslationWorkbenchTranslationWriter. Dynamic multi-option saves and individual source corrections now use the same service with their existing event types and metadata. Each operation saves values and history in one transaction. Isolated tests cover blank values, selected variants/options, unchanged saves, service/editor equivalence and rollback when history fails.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('This is a first slice, not completion of W03: Shared authorization and stale-edit protection still need their own migration. Management and Sub-Languages remain unchanged until reconciliation and parity checks are complete. No new CLI editing command is registered.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">{{ __('W03/W04 progress — Bulk Equalize extracted') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchBulkKeyWriter now owns the shared selection rules, target-key validation and transactional key/review/history writes. The editor uses the same context for its preview; execution resolves that context again. Missing-key creation and relation reactivation remain available when joining an established shared-key workflow. TranslationWorkbenchKeyAttributes centralizes the existing key structure and classification rules used by Bulk Equalize and other review actions.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Tests cover normal selection, mixed literals, invalid targets, missing or inactive key links, changed selections, classification, retained translation values and rollback on history failure. This extraction does not publish files, migrate Legacy data or implement full concurrent-edit protection and operation replay deduplication.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">{{ __('W03/W04 progress — individual key reviews extracted') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchKeyReviewWriter now persists manual key changes, UI/dynamic classifications, candidate confirmations/rejections, dynamic single/multi decisions and suggested-key acceptance. Reviews, timeline events and the locale moves triggered by manual key changes remain in one transaction. The editor still resolves action eligibility, permissions and container-key validation.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Existing behavior is preserved: manual renaming moves language values; when a target locale value already exists it is retained and the old row becomes obsolete without deleting its text. Suggested-key acceptance records its existing key snapshot and does not perform the manual rename migration. These policies are documented current behavior, not completion of the proposed conflict-resolution contract.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">
            {{ __('W04/W07 progress — conflict and cleanup review writers extracted') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchConflictReviewWriter stores code-update decisions and notes with their history; identical decisions are a no-op. TranslationWorkbenchCleanupReviewWriter stores cleanup reviews and marks active language values obsolete only for the selected key. Status changes, inventory counts, reviews and timeline events share one transaction. Keep and needs-review do not reactivate obsolete values.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('The editor still refreshes the export preview report after cleanup; neither writer publishes source or language files. Isolated tests cover all decisions, unrelated values, unchanged files and rollback on history failure. Live-usage rechecks, shared authorization and approval invalidation remain planned work; this extraction does not complete the target cleanup contract.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">
            {{ __('W04/W07 progress — provenance and obsolete source reviews extracted') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchProvenanceReviewWriter records the four existing provenance decisions with report context and history. It does not restore literals or change keys and values. Reviews without linked key/finding records remain supported; repeated confirmations still create separate reviews, as before. TranslationWorkbenchCleanupReviewWriter now also owns the individual source-value obsolete action: only the selected source locale changes, its text remains stored and repeated confirmation is a no-op.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('The editor retains report resolution, report refresh and modal feedback. Tests first characterized the original actions and then verified the extracted writers, including missing records, unrelated locales, unchanged files and rollback on history failure. This preserves current behavior; shared authorization, stale-report protection and the planned publication/cleanup safeguards remain separate migration steps.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">{{ __('W03 progress — scalar-to-array key transform extracted') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchScalarTransformWriter owns target-path normalization, target validation, duplicate/conflict checks and the transactional move of keys and language values. Scalar-to-array means moving a scalar leaf to a nested translation-key path, not converting its text into an array value. Values in all locales are retained; obsolete source rows remain historical. Source and language files are not published by this action.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Workbench still resolves eligible scalar candidates and included selections. The writer rechecks target conflicts before writing. Tests cover excluded rows, duplicate and invalid targets, active and obsolete target conflicts, unlinked values, unchanged files and rollback when history fails. This does not provide full concurrent-edit protection.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="check-circle">{{ __('W04 progress — dynamic source links extracted') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchDynamicSourceLinkWriter confirms and unlinks the resolved runtime-source associations. Confirmation stores related option snapshots on the candidate rows; it does not modify translation values or original source values. Reconfirmation updates existing rows and records another review. Unlinking retains inactive rows, repeated unlinking is a no-op, and confirmation can reactivate those same rows.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Workbench still resolves eligible sources and refreshes the preview context before each action. The writer saves association changes, reviews and timeline events in one transaction. Tests cover multiple runtime sources, refreshed snapshots, unrelated associations, missing or obsolete context, reactivation and rollback. Shared authorization and concurrent-change protection remain open; no Legacy writer has been switched over by this extraction.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="amber">
        <flux:callout.heading icon="shield-check">{{ __('Cutover audit — route roles and remaining differences') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('The existing Admin or Super-Admin route requirement is now registered for Livewire follow-up requests as well. Tests cover middleware selection for Workbench, Management and Sub-Languages and rejection after role removal. This is route protection, not yet shared authorization inside the writers or a complete browser security test.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Management and Sub-Languages still write Legacy data. They differ from Workbench in whitespace handling, value statuses, unchanged-save behavior and history. Sub-Languages also owns explicit base-duplicate and override decisions. Resolve these contracts and data conflicts before switching both reads and writes together; the extracted writers alone do not complete that migration.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Next: define the shared action and authorization contract for UI and trusted CLI callers, then centralize editor validation and reconcile variant behavior. No new roles or automatic data winners are introduced by this audit.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('W03 progress — authorized scalar application boundary') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Workbench scalar saves now pass through TranslationWorkbenchScalarEditor. It checks the authenticated Admin or Super-Admin role, reloads the active key/finding assignment, validates string or null values and accepts only the configured source, main target and active target variants. TranslationWorkbenchEditorLocales supplies the same language selection to the UI and application boundary. Unauthorized locale requests reject the entire save instead of silently skipping that locale.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('The existing writer still persists values and history together. Tests cover guests, wrong and revoked roles, missing or unrelated assignments, inactive variants, malformed values and authorized saves. This slice does not implement revision checks, background/system actors or authorization for other writers. Direct use of the persistence writer is not a protected application entry point; Management and Sub-Languages remain on their existing paths.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('W03 progress — authorized dynamic option editing') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchDynamicEditor protects multi-option saves and individual source corrections. Scalar and dynamic editors share TranslationWorkbenchEditorAccess for current admin-role and active assignment checks. TranslationWorkbenchDynamicOptions supplies the same option rows to preview and saving, including stored values, active source options and confirmed runtime links.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Source and target locales, option identities and provenance are resolved server-side. Unknown fields or malformed values reject the whole save. Stored options remain editable when their source disappears; runtime-only options require a currently available source or confirmed link. Existing blank-value, no-op and history behavior remains covered by tests. Revision protection and the other review/publication actions still require their own migration.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('W03/W04 progress — authorized individual key reviews') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('TranslationWorkbenchKeyReviewEditor now protects manual key changes, suggested-key acceptance and UI/dynamic classification decisions. Current roles and active key/finding assignments are checked again at the application boundary. Manual targets are checked for syntax and container conflicts; suggested keys and review metadata are read from current records. Existing value-rekey and history policies are retained.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Classification attributes still come from trusted Livewire action code; they are not a public request payload contract. Detailed classification rules and revision checks are not fully centralized yet. Bulk operations, conflict/provenance reviews, cleanup and source-link actions need separate authorization boundaries because their record relationships differ.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('W03/W04 progress — authorized bulk and scalar transforms') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Bulk Equalize and Scalar to Array now require the current Admin or Super-Admin role in both the confirmation action and the write service. Bulk Equalize still supports creating missing keys and restoring assignments. Scalar candidate eligibility remains resolved by the Workbench; target and conflict checks remain in the writer. These actions do not publish source or language files.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">
            {{ __('W04 progress — authorized conflict, cleanup and provenance reviews') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Conflict decisions, cleanup reviews, individual source-value obsolete actions and provenance reviews now require the current Admin or Super-Admin role in both the Livewire action and the write service. Existing optional key/finding links, decision rules and history behavior remain unchanged. Report-context validation and revision checks are separate outstanding work; this role check does not publish source or language files.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('W04 progress — authorized dynamic source links') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Confirming and unlinking dynamic sources now require the current Admin or Super-Admin role in both the Livewire action and the write service. Source eligibility remains resolved by the Workbench. Option snapshots, reactivation, retained inactive links and history behavior remain unchanged; translations and original source values are not modified.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">
            {{ __('W01/W05/W06 progress — authorized UI refresh and pipeline entry points') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Current-tab refresh, all-tab refresh, code-update dry-run refresh and complete pipeline startup now check the current Admin or Super-Admin role before invoking commands or services. Shared-candidate refresh also updates database candidates. Tracked UI pipelines now reload the initiating user and check the current role before startup and before each command. Missing actors or invalid runs are rejected without standalone fallback. A running individual command is not interrupted; standalone CLI behavior remains unchanged.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('Pipeline request binding and single execution') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('UI runs store every command-specific execution option, including defaults. Background processing compares these options before startup and before each command. A pending run is claimed atomically; duplicate starts cannot change the active run. Older pending requests without a complete option snapshot must be restarted from the Workbench. A local process lock now distinguishes active from interrupted runs. An administrator can explicitly mark a verifiably interrupted run as failed; completed work is retained and no steps are retried. Unknown or remote process states cannot be corrected this way. Standalone CLI calls remain unchanged.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('Pipeline initiation and review authorship') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Foundation timeline events and shared command activity entries now include a scoped pipeline context: run ID, UI or CLI origin, and initiating user ID. Initiating a scan does not make its observations manual reviews. Existing authorship fields and review decisions remain unchanged. The context is restored on exit, including failures; standalone CLI pipelines have no initiating user.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="table-cells">{{ __('Repeatable Legacy / Foundation value comparison') }}
        </flux:callout.heading>
        <flux:callout.text>
            <code>php artisan translation-workbench:compare-values</code>
            {{ __('compares database values by key and normalized locale without changing them. Add --json for original values, IDs, statuses and base-duplicate flags. Text equality does not imply equal review status or export eligibility. Language files, fallback behavior and dynamic option values are outside this comparison.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        id="workflow-authorization-contract"
        color="amber"
    >
        <flux:callout.heading icon="shield-check">{{ __('Action and authorization contract — target behavior') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('This contract specifies the next implementation steps. Route role protection is already active; scalar and dynamic option authorization are implemented, while other action boundaries, revision checks and scoped system permissions remain open. Existing Admin or Super-Admin roles remain the basis; no new roles or command options are introduced here.') }}
        </flux:callout.text>
        <flux:table container:class="mt-3 max-h-[100vh] overflow-x-auto rounded-t-lg">
            <flux:table.columns class="bg-white dark:bg-zinc-800">
                <flux:table.column>{{ __('Caller') }}</flux:table.column>
                <flux:table.column>{{ __('Identity and permission') }}</flux:table.column>
                <flux:table.column>{{ __('Boundary') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Interactive user') }}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Use the authenticated user and check the current Admin or Super-Admin role before mutation.') }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('A user ID submitted by the browser is not proof of identity. Validate the selected records, locales and options separately.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('User-initiated background run') }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Persist the initiator server-side and recheck the current account and role before each writing step.') }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('A deleted user or revoked role blocks the step. A run ID is not permission and never triggers fallback to a system identity.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit system run') }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('A server-approved command or job receives named operations and a bounded scope. Console execution alone grants no rights.') }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Observation and import permissions cannot create human review approvals. Publication and cleanup require their own explicit permission and a valid plan.') }}
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>

    <flux:callout color="zinc">
        <flux:callout.heading icon="list-checks">{{ __('Shared action boundary — required checks') }}
        </flux:callout.heading>
        <flux:callout.text>
            <ol class="list-decimal space-y-2 pl-5">
                <li>{{ __('Resolve the trusted actor and authorize the requested operation. Editorial CLI actions require an explicit user association and the same role checks; no new editorial CLI command exists yet.') }}
                </li>
                <li>{{ __('Resolve scope: key/finding relationships, allowed locales and options, or the exact publication plan and files. Apply the same business validation for UI and CLI.') }}
                </li>
                <li>{{ __('Compare the expected revision and relevant file hashes with current state. Reject stale edits and changed plans before overwriting anything.') }}
                </li>
                <li>{{ __('Require explicit write intent for publication or removal. A scan may persist observations; an export preview does not publish files. Dry-run is scoped to the action, not automatically to every earlier pipeline step.') }}
                </li>
                <li>{{ __('Recheck current state during execution. Save database changes and history together; report partial file publication honestly. Repeating an operation ID cannot create a second decision or authorize changed input.') }}
                </li>
                <li>{{ __('Return a structured outcome to the caller. Toasts, modals and console output stay outside the writer. If saving succeeds but refreshing the report fails, report these as separate outcomes.') }}
                </li>
            </ol>
        </flux:callout.text>
        <flux:callout.text>
            {{ __('The server-side context carries actor, initiator/run where applicable, operation, bounded scope, expected revision, write intent and operation ID. System events must not invent a reviewed-by user. Every productive caller must pass through the shared application boundary before its protection can be considered complete.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="zinc">
        <flux:callout.heading icon="arrow-right">
            {{ __('First authorization slice — scalar value editing implemented') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Start with W03 scalar edits in Workbench: enforce the existing admin roles and validate locales and key/finding relationships at the application boundary. Test guests, wrong or revoked roles, valid administrators and invalid scope directly. Then migrate the remaining actions individually; Management, Sub-Languages and commands require their own verified cutover.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Cleanup decisions and physical removal remain distinct. Newly active usages or variant dependencies must block removal even after an earlier cleanup review. Current extracted writers are persistence services, not yet the shared authorization boundary.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout color="zinc">
        <flux:callout.heading icon="table-cells">{{ __('Ownership and permitted changes') }}</flux:callout.heading>
        <flux:callout.text class="mb-3">
            {{ __('Three current models must converge: Legacy translation_* tables, older package entries/values/occurrences, and Foundation. Foundation becomes authoritative only after reconciliation and verified cutover. Current entry points below are migration starting points, not an exhaustive list of every helper or external job.') }}
        </flux:callout.text>

        <flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
            <flux:table.columns class="bg-white dark:bg-zinc-800">
                <flux:table.column>{{ __('Operation / current entry points') }}</flux:table.column>
                <flux:table.column>{{ __('Target owner / authoritative state') }}</flux:table.column>
                <flux:table.column>{{ __('Permitted changes and boundaries') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row id="contract-w01">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W01 · {{ __('Capture observations') }}</flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">project:translations → audit-code /
                                sync-audits
                            </div>
                            <div class="wrap-break-word font-mono text-xs">translation:workbench → scan /
                                sync-foundation
                            </div>
                            <div class="wrap-break-word font-mono text-xs">discover-dynamic-options;
                                RuntimeDynamicTranslationCollector</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Capture service — consolidate the scanner and Foundation syncer') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Foundation findings, source files, occurrences and dynamic observations. Scan once; downstream analysis consumes the same result.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May update observed content, locations, fingerprints and observation status. Must not replace edited values, key assignments or approvals. Missing usages become scoped review candidates; they do not authorize deletion.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row id="contract-w02">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W02 · {{ __('Import and reconcile') }}</flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">translations:sync-audits / sync-dynamic-keys
                            </div>
                            <div class="wrap-break-word font-mono text-xs">translation-workbench:import-lang-values</div>
                            <div class="wrap-break-word font-mono text-xs">TranslationWorkbenchLangValueImporter</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Reconciliation service — evolve the Foundation importer') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Foundation import observations and conflicts, matched by translation key + locale, including language variants. Source paths and provenance remain available.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May seed previously absent values as imported and unreviewed. Equal values are a no-op; differences from edited or reviewed values become conflicts. A missing file or value never means unconditional deletion or loss of editorial review.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row id="contract-w03">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W03 · {{ __('Edit translations and key assignments') }}
                        </flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">
                                TranslationWorkbenchEntries::saveTranslationValue
                                / saveTranslationKeyModal</div>
                            <div class="wrap-break-word font-mono text-xs">TranslationList::saveTranslationEdit</div>
                            <div class="wrap-break-word font-mono text-xs">
                                TranslationSubLanguages::saveTranslationEntryEdit; bulk / variant actions</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Editorial write service — extract existing Workbench behavior') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Foundation keys, key/finding assignments and language values. All editor entry points delegate to this service after reconciliation.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May change only explicitly submitted fields after permission, locale, type and revision checks. Preserve untouched locales and variant/fallback semantics. Save is not publication or approval. Record the actor, origin and before/after state in the same DB transaction.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row id="contract-w04">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W04 · {{ __('Review and approve') }}</flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">TranslationWorkbenchEntries::setUiKeyReview
                                /
                                setDynamicKeyReview</div>
                            <div class="wrap-break-word font-mono text-xs">saveCodeUpdateConflictReview /
                                saveLangCleanupReview</div>
                            <div class="wrap-break-word font-mono text-xs">
                                TranslationUsageAudit::saveUsageAuditDecision;
                                Legacy reviewed fields</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Review service — consolidate decision storage and rules') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Foundation decisions linked to the exact key/value/finding revision. Publication approval additionally identifies a specific versioned change plan.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May approve, reject or request revision with actor and reason. Approval does not write source or language files. Changing the reviewed content invalidates the relevant approval; scans cannot grant or silently transfer it.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row id="contract-w05">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W05 · {{ __('Publish language files') }}</flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">TranslationWorkbenchPublicationRunner</div>
                            <div class="wrap-break-word font-mono text-xs">translation-workbench:export-lang-files
                            </div>
                            <div class="wrap-break-word font-mono text-xs">TranslationWorkbenchLangFileExporter</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Publication coordinator + language exporter — one approved plan') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Foundation supplies publishable values; lang/ remains the runtime output. The Legacy export command is comparison-only (--dry-run). Direct exporter writes are disabled; UI and CLI publication use the same coordinator.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May write only the reviewed diff with explicit write intent. Lock the scope, recheck input hashes, stage and validate files, then journal publication. Preserve unrelated keys/locales. Default preview does not publish; cleanup requires its own approved decision.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row id="contract-w06">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W06 · {{ __('Change source code') }}</flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">ProjectTranslations::applyLatestDiffPatch;
                                translations:rewrite-literals</div>
                            <div class="wrap-break-word font-mono text-xs">translations:usage-decisions:apply --write
                            </div>
                            <div class="wrap-break-word font-mono text-xs">translation-workbench:apply-code-updates /
                                apply-suspicious-key-restores</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Publication coordinator + source applier — same plan as language export') }}
                        </flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Approved Foundation key mappings, replacements and restores. Source files are output targets, not an alternative decision store.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May apply only approved replacements/restores against matching hashes and validated syntax. Required language values must be available before new keyed references are published. No opportunistic replanning during apply. Stop and journal failures for explicit recovery.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row id="contract-w07">
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">W07 · {{ __('Cleanup and deletion') }}</flux:heading>
                        <div class="mt-2 space-y-2">
                            <div class="wrap-break-word font-mono text-xs">TranslationLangBallast::setDecisionStatus;
                                translations:lang-ballast:apply</div>
                            <div class="wrap-break-word font-mono text-xs">CleanupStaleDynamicTranslationKeys;
                                Workbench
                                excluded-path cleanup</div>
                            <div class="wrap-break-word font-mono text-xs">Workbench obsolete reviews and exporter
                                pruning
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:heading size="sm">
                            {{ __('Cleanup policy + planner — execution through the publication coordinator') }}
                        </flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Foundation cleanup decisions and tombstones, with retained provenance and history. Physical deletion is distinct from obsolete observation status.') }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        <flux:text>
                            {{ __('May propose removal after a complete, valid scope check. Execution requires an approved deletion plan and rechecking live usages, references and variants. No removal from an incomplete scan or a missing import alone. Truncate/rebuild remains a separate maintenance operation.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>

    <flux:callout color="cyan">
        <flux:callout.heading icon="shield-check">{{ __('Rules shared by every entry point') }}
        </flux:callout.heading>
        <flux:callout.text>
            <ul class="list-disc space-y-2 pl-5">
                <li>{{ __('UI and CLI provide identity, permissions, scope, operation and expected revision. Shared services perform validation and writes; adapters must not bypass these rules through direct model updates.') }}
                </li>
                <li>{{ __('Dry-run changes neither business records nor source/language files. Reports and technical run logs are allowed. Capture may persist observations only in its explicit write mode. Edit, review and publish are separate operations.') }}
                </li>
                <li>{{ __('A successful business change and its history belong in one database transaction. File publication is not automatically atomic with the database: stage, validate, journal each write and retain recovery data. A partial or failed verification must not become success.') }}
                </li>
                <li>{{ __('Retries use the same operation identity; unchanged input must not create duplicate values, decisions or business-change events. Technical run logs may record repeated attempts.') }}
                </li>
                <li>{{ __('Statistics, Raw Data and TW-Graph read the authoritative state and history. They do not decide translations or publish changes; view preferences and caches are separate from business writes.') }}
                </li>
            </ul>
        </flux:callout.text>
    </flux:callout>

    <flux:callout color="amber">
        <flux:callout.heading icon="beaker">{{ __('Acceptance criteria — behavior tests still to implement') }}
        </flux:callout.heading>
        <flux:callout.text class="mb-3">
            {{ __('These scenarios are the acceptance contract for each migration step. They are not marked as passing merely because this documentation renders. Verify them against isolated fixtures, then test the UI and CLI adapters against the same service contract.') }}
        </flux:callout.text>
        <flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
            <flux:table.columns class="bg-white dark:bg-zinc-800">
                <flux:table.column>{{ __('Contract') }}</flux:table.column>
                <flux:table.column>{{ __('Required behavior test') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W01</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Re-scan edited and reviewed records, including a partial path scan. Values and decisions remain intact; records outside scope remain unchanged. Repeating an unchanged scan creates no duplicate findings or change events.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W02</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Import equal, missing and conflicting values for base and variant locales. Re-import after an edit preserves the edit and reports the discrepancy; missing-file handling preserves existing reviewed values.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W03</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Exercise single, bulk and variant edits through UI and CLI adapters. Require equivalent validation, values and history; reject stale edits without partial changes. Test empty string, missing value, scalar and structured values separately.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W04</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Approve a revision, then change its value, assignment or planned diff. The previous approval cannot authorize the new content. UI and CLI enforce the same reviewer permissions and transitions.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W05</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Compare preview with the applied diff. Reject changed files or stale approvals before publication. Test preservation of unrelated values, base/variant behavior and a mid-write failure; report actual partial state, never false success.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W06</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Reject changed source and invalid syntax without patching it. Apply an approved plan once; a repeated request does not duplicate replacements/history. Inject failure after language publication and verify honest recovery status.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="align-top"><code>W07</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">
                        {{ __('Mark a candidate obsolete, then add a live usage or variant dependency. Apply must refuse removal. Partial scans cannot authorize cleanup; previews delete nothing and approved cleanup preserves historical provenance.') }}
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout color="zinc">
        <flux:callout.heading icon="arrow-right">{{ __('First migration slice — W03 editorial writes') }}
        </flux:callout.heading>
        <flux:callout.text>
            <ol class="list-decimal space-y-2 pl-5">
                <li>{{ __('Refresh the read-only comparison of Legacy, older package values, Foundation and language files. Match key, locale, variant, value, status and provenance; resolve ambiguity explicitly. Do not select winners by row ID or latest timestamp alone.') }}
                </li>
                <li>{{ __('Characterize current Workbench edits, bulk actions, structured values and sub-language behavior in isolated tests. Extract their validated write behavior into one editorial service and route Workbench through it first.') }}
                </li>
                <li>{{ __('Reconcile the affected records before adapting Management and Sub-Languages. Switch both reads and writes together so saved values remain visible; retain the old path until functional parity and the acceptance criteria have been checked.') }}
                </li>
                <li>{{ __('Before cutover, guard or delegate every writer for the migrated scope, including imports, background commands and exporters. After cutover, stop independent Legacy writes for that scope. Preserve the former implementation in _old/ and its data/history for traceability; do not introduce permanent dual writes or reset the database.') }}
                </li>
            </ol>
        </flux:callout.text>
    </flux:callout>
</div>
