{{-- Dated annotations to the original plan, not a replacement graph or live readiness check. --}}
@php
    $workflowImplementationNotes = [
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.reconcile',
            'target-ui' => 'workflow.target-ui.capture',
            'plan' => __('Make the scan result and remaining work accessible before editing translations.'),
            'actual' => __('The recorded transfer balance separates source capture, key assignment, pending roles, published values and language-file scope. Each table has its own unit; overlapping subsets are not added together. Missing or inconsistent reports do not produce totals. Preserved evidence is not marked as a completed translation.'),
            'status' => __('✓ Read-only transfer balance available; complete historical accounting and production transition pending'),
            'evidence' => 'workbench-implementation-status-2026-10-20.md',
        ],
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.publish',
            'target-ui' => 'workflow.target-ui.publish',
            'plan' => __('Preserve published translations and use an explicit locale fallback when an exact translation is missing.'),
            'actual' => __('The full fresh generation preserves 2,016 published values from 54 files and nine locales. Exact readback includes file provenance; the explicit de-at → de → en chain is checked without inventing missing locale values. Blocked and unassigned values remain evidence. No drafts, approvals or history are created.'),
            'status' => __('✓ Published baseline and explicit fallback verified in isolation'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.reconcile',
            'target-ui' => 'workflow.target-ui.capture',
            'plan' => __('Distinguish translation sources, option sources and value usages before editing translations.'),
            'actual' => __('The fresh generation preserves 31 pending translation sources, 28 option sources and 13 value usages. It retains 59 argument records and six producer descriptions as undecided evidence. Interrupted imports roll back; repeated imports do not duplicate records. Findings, key links, revisions and history remain unchanged.'),
            'status' => __('✓ Source roles and unresolved arguments verified in isolation'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.reconcile',
            'target-ui' => 'workflow.target-ui.capture',
            'plan' => __('Capture source occurrences and distinguish literals, existing keys and cases requiring review.'),
            'actual' => __('The full verified static capture is imported into a separate fresh versioned schema. All raw fields and positions are checked. Only evidenced valid source keys receive links; blocked key structures, pending cases and exclusions remain explicit. No old reviews, revisions or history are activated. This uses the saved source snapshot, not a new production scan.'),
            'status' => __('✓ Full static capture and existing-key links verified in isolation'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.publish',
            'target-ui' => 'workflow.target-ui.publish',
            'plan' => __('Save, review, publish and verify translations as separate steps.'),
            'actual' => __('The newly populated generation now exercises evidence import, an edited revision, renewed approval, temporary publication and readback. Editing invalidates the previous approval. Repeated requests retain their results; stale edits and changed export files are rejected. Findings and import evidence remain unchanged.'),
            'status' => __('✓ Fresh generation editing and publication verified in isolation; UI integration pending'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Deviation'),
            'color' => 'amber',
            'target' => 'workflow.target.reconcile',
            'target-ui' => 'workflow.target-ui.recognize',
            'plan' => __('Moved occurrence: update its position and retain the finding.'),
            'actual' => __('The isolated implementation archives the old position and creates a successor finding. Immutable capture data stays unchanged; the key link is carried forward. An unchanged rescan retains the current finding.'),
            'status' => __('Verified in isolation; UI integration pending'),
            'evidence' => 'copied-rescan-2026-10-19.md; key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Deviation'),
            'color' => 'amber',
            'target' => 'workflow.target.history',
            'target-ui' => 'workflow.target-ui.capture',
            'plan' => __('Retain history when migrating to the new workflow.'),
            'actual' => __('Work history starts with the captured findings. Earlier history remains in the backup/archive, not in the active timeline. Existing source keys and published translations remain evidence for the capture.'),
            'status' => __('Decision recorded; production transition pending'),
            'evidence' => 'generation-mapping-2026-10-19.md; evidence-staging-result-2026-10-19.md',
        ],
        [
            'type' => __('Added implementation task'),
            'color' => 'sky',
            'target' => 'workflow.target.preflight',
            'target-ui' => 'workflow.target-ui.publication-preview',
            'plan' => __('Check the current state before applying a confirmed change.'),
            'actual' => __('For key corrections, bind the source-file and database evidence to one proposal fingerprint. Recheck before writing and reject changed files before commit. This makes the existing preflight requirement concrete.'),
            'status' => __('✓ Package service extracted and verified in isolation'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.publish',
            'target-ui' => 'workflow.target-ui.publish',
            'plan' => __('Save, review, publish and verify translations as separate steps.'),
            'actual' => __('The synthetic correction case exercises draft import, approval and revocation, temporary language-file export and readback. This does not yet provide the complete user-facing publication workflow.'),
            'status' => __('✓ Isolated scenario verified; application integration pending'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Added implementation task'),
            'color' => 'sky',
            'target' => 'workflow.target.preflight',
            'target-ui' => 'workflow.target-ui.publication-preview',
            'plan' => __('Use one explicitly selected authoritative working generation.'),
            'actual' => __('Key-correction services now check the supplied database, schema, baseline and required tables inside each transaction. Missing columns and tables hidden by another search path are rejected. Versioned schema setup and user authorization remain separate tasks.'),
            'status' => __('Package guard implemented; application setup pending'),
            'evidence' => 'workbench-implementation-status-2026-10-20.md; key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Added implementation task'),
            'color' => 'sky',
            'target' => 'workflow.target.capture',
            'target-ui' => 'workflow.target-ui.capture',
            'plan' => __('Prepare reliable storage for findings, keys and translations.'),
            'actual' => __('Schema version 1 is installed only into a new, explicitly named schema. A generation ID, SQL checksums and structural definitions identify it. The isolated tests detect changed constraints, indexes, column types, functions and disabled triggers. Data transfer and UI activation are still pending.'),
            'status' => __('✓ Empty generation installation verified in isolation'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Implementation evidence'),
            'color' => 'sky',
            'target' => 'workflow.target.reconcile',
            'target-ui' => 'workflow.target-ui.capture',
            'plan' => __('Capture findings, keys and translations from verified evidence.'),
            'actual' => __('The isolated rehearsal transfers the separate findings and published evidence of one selected blocked key into the new schema. Target identities retain explicit origin references; old reviews and history are not imported. Package services then confirm and apply a synthetic correction in that generation. This is not a full data migration.'),
            'status' => __('Selected transfer and correction: see recorded rehearsal result'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Added implementation task'),
            'color' => 'sky',
            'target' => 'workflow.target.preflight',
            'target-ui' => 'workflow.target-ui.key-save',
            'plan' => __('Check the proposed translation key against existing names and array paths.'),
            'actual' => __('A partial data transfer still carries the full evidenced name inventory. Preview and database checks reject exact, parent and child path conflicts outside the selected findings. The inventory does not activate those other keys or translations. Schema version 2 adds this separate evidence.'),
            'status' => __('✓ Complete evidenced name inventory verified in isolation'),
            'evidence' => 'key-correction-application-2026-10-19.json',
        ],
        [
            'type' => __('Open implementation task'),
            'color' => 'zinc',
            'target' => 'workflow.target.review',
            'target-ui' => 'workflow.target-ui.review',
            'plan' => __('The UI supports the intended work, review and confirmation steps.'),
            'actual' => __('Adapt the Workbench screens and dialogs to the workflow. Existing controls are not workflow constraints. Connect an explicit new-generation database target, permissions, confirmation state and understandable conflict handling before enabling writes.'),
            'status' => __('Open'),
            'evidence' => 'workbench-implementation-status-2026-10-20.md',
        ],
    ];
@endphp

<flux:callout color="sky" class="my-3">
    <flux:callout.heading icon="clipboard-document-check">
        {{ __('Plan and implementation') }} · 2026-10-20
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('The original graph is preserved as the plan. These dated notes record implementation evidence, deviations and added tasks. A check mark applies only to the stated scope, not to production readiness. Unlisted graph steps have not been assessed here.') }}
    </flux:callout.text>
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading>{{ __('Compare the plan with the implementation') }}</flux:accordion.heading>
            <flux:accordion.content>
                <flux:table container:class="overflow-x-auto">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Graph section / original plan') }}</flux:table.column>
                        <flux:table.column>{{ __('Implementation / difference') }}</flux:table.column>
                        <flux:table.column>{{ __('Status / evidence') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($workflowImplementationNotes as $note)
                            <flux:table.row>
                                <flux:table.cell class="align-top whitespace-normal">
                                    <flux:badge :color="$note['color']">{{ $note['type'] }}</flux:badge>
                                    <code class="my-2 block break-all text-xs">{{ $note[$implementationGraph] }}</code>
                                    <flux:text>{{ $note['plan'] }}</flux:text>
                                </flux:table.cell>
                                <flux:table.cell class="max-w-xl align-top whitespace-normal">
                                    <flux:text>{{ $note['actual'] }}</flux:text>
                                </flux:table.cell>
                                <flux:table.cell class="max-w-sm align-top whitespace-normal">
                                    <flux:text>{{ $note['status'] }}</flux:text>
                                    <flux:tooltip toggleable>
                                        <flux:button size="xs" variant="ghost" icon="document-text">{{ __('Evidence') }}</flux:button>
                                        <flux:tooltip.content class="max-w-sm whitespace-normal break-words">
                                            <code>docs/audits/translation-workbench-2026-10-15/{{ $note['evidence'] }}</code>
                                        </flux:tooltip.content>
                                    </flux:tooltip>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:callout>
