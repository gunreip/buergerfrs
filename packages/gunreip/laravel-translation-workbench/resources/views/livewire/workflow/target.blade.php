<flux:callout id="workflow-target" color="violet" class="min-w-0">
    <flux:callout.heading icon="route">{{ __('Target workflow — shared processing and publication') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">
        {{ __('Proposal, not implemented behavior. One connected graph shows responsibilities, stored results and publication gates. Foundation is the authoritative translation store; UI and CLI call the same services. Reading top to bottom describes the lifecycle, which can span several runs and human review sessions.') }}
    </flux:callout.text>
    <div class="mb-3 flex flex-wrap gap-2">
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Commands and responsibilities') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p>{{ __('translation:workbench is the proposed shared entry point. Existing scan/sync, import, discovery, plan and apply commands must delegate to the shared services after consolidation. These are proposed responsibilities, not new commands already available. Legacy writers are retired or delegated only after parity has been verified.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Preview and writes') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p>{{ __('A capture run may persist observations; an explicit dry-run must not change business data or source/language files, but may write reports and logs. Editorial writes require an explicit edit operation. Publication requires an approved plan, explicit write intent and a fresh preflight; scan never implies publication.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('One authoritative store') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p>{{ __('Findings describe observations; keys, values and reviews describe editorial decisions. A later scan or file import must not silently replace reviewed values. UI and CLI use the same conflict, locale, variant and permission rules.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Publication and recovery') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p>{{ __('Database and filesystem changes are not automatically one atomic transaction. Stage and validate first, publish required language values before new source references, and apply cleanup last. Stop on failure, record partial progress and use the run journal for recovery. Never mark a partial or unverified run successful.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
        <flux:tooltip toggleable>
            <flux:button size="sm" variant="outline" icon="information-circle">{{ __('Transition from the current state') }}</flux:button>
            <flux:tooltip.content class="max-w-xl whitespace-normal">
                <p>{{ __('Migration is a separate prerequisite: reconcile Legacy, older package and Foundation data by key/locale, value, status and provenance; resolve conflicts and retain history. No database reset and no reverse replacement of translation keys with literals is part of this graph.') }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </div>
    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[48rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph
                graph-id="translation-workflow-target"
                :dev="true"
                :coordinates="true"
                :dev-counter-auto="true"
                min-width="48rem"
                min-height="24rem"
                horizontal-padding="4rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.flow-start
                    id="workflow.target.start"
                    direction="top-bottom"
                    color="violet"
                    :start-label="['text' => [__('Shared translation workflow'), __('Workbench UI / Artisan CLI')], 'width' => 'long', 'align' => 'center']"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.scope"
                    attach-to="workflow.target.start.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('1. Select scope and operation')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: UI / CLI → one orchestrator'), __('Proposed entry: translation:workbench'), __('Paths, locales, operation and run ID')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('One run context and permission check'), __('Explicit preview / write intent'), __('Same validation for UI and CLI')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.capture"
                    attach-to="workflow.target.scope.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('2. Capture source observations once')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: shared capture service'), __('IN: literals, keyed calls and usages'), __('Dynamic providers / runtime observations')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('OUT: Foundation findings + occurrences'), __('Provenance and source hashes'), __('No overwrite of editorial decisions')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.reconcile"
                    attach-to="workflow.target.capture.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('3. Reconcile existing language files')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: shared reconciliation service'), __('IN: files ↔ Foundation key + locale'), __('Include sub-language variants')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('OUT: missing / equal / conflicting values'), __('Existing edits and reviews are preserved'), __('Changed files become review candidates')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.analyse"
                    attach-to="workflow.target.reconcile.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('4. Derive candidates and classifications')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: shared analysis services'), __('IN: observations + current decisions'), __('Duplicates, shared keys, dynamic sources')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('OUT: reviewable candidates + conflicts'), __('Obsolete means review, not deletion'), __('No automatic key rewrite or cleanup')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.review"
                    attach-to="workflow.target.analyse.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('5. Edit and review in Workbench')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: editor via shared write services'), __('Assign keys; translate; resolve conflicts'), __('Bulk actions and variants use same rules')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('OUT: authoritative Foundation decisions'), __('History: actor, origin, before / after'), __('CLI edits require the same validations')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.plan"
                    attach-to="workflow.target.review.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('6. Build one versioned change plan')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: shared planner'), __('IN: reviewed values + mappings'), __('Approved cleanup / restore decisions')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('OUT: language + source + cleanup diff'), __('Expected hashes and affected records'), __('Reports only; source/lang unchanged')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                {{-- Both outputs remain separate: a stopped run must not reach publication/success. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target.approval"
                    attach-to="workflow.target.plan.anchorNode-end"
                    direction="top-bottom"
                    side="right"
                    color="sky"
                    bridge-length="3rem"
                    stem-length="5rem"
                    before-length="3rem"
                    after-length="3rem"
                    :condition-label="['text' => [__('Preview, conflicts, or no approval?')], 'width' => 'long', 'align' => 'center']"
                    :if-start="[
                        'text' => [__('YES: stop without publication'), __('Review / correct, then create a new plan')],
                        'width' => 'long',
                        'align' => 'center',
                        'return' => false,
                        'returnOffset' => '12rem',
                        'color' => 'amber',
                    ]"
                    :if-end="[
                        'text' => [__('NO: approved plan + explicit write'), __('Preflight against current hashes')],
                        'width' => 'long',
                        'align' => 'center',
                        'return' => false,
                        'returnOffset' => '0rem',
                        'color' => 'green',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target.approval-stop"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target',
                        'workflow.target.approval.true.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="3rem"
                    color="amber"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.preflight"
                    attach-to="workflow.target.approval.false.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('7. Validate and stage the approved plan')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: one apply coordinator'), __('Lock scope; recheck hashes and policies'), __('Validate syntax; prepare recovery data')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('Stale / invalid plan → STOP, no writes'), __('Stage source + language changes'), __('Journal ties all changes to the plan')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.publish"
                    attach-to="workflow.target.preflight.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('8. Publish the approved changes')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: the same apply coordinator'), __('Publish required language values first'), __('Then source changes; cleanup last')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('Only planned, validated changes'), __('Failure → stop and report partial state'), __('Recover using staged data and journal')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.verify"
                    attach-to="workflow.target.publish.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('9. Verify the published result')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: shared verification service'), __('Re-read affected files / usages'), __('Check syntax, keys and expected values')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('Refresh affected observations once'), __('Record results under the same run ID'), __('No repeated full write pipeline')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                {{-- Both outputs remain separate: a stopped run must not reach publication/success. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target.verification"
                    attach-to="workflow.target.verify.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="sky"
                    bridge-length="3rem"
                    stem-length="5rem"
                    before-length="3rem"
                    after-length="3rem"
                    :condition-label="['text' => [__('Verification failed?')], 'width' => 'long', 'align' => 'center']"
                    :if-start="[
                        'text' => [__('YES: run remains failed'), __('Investigate / recover; no success status')],
                        'width' => 'long',
                        'align' => 'center',
                        'return' => false,
                        'returnOffset' => '12rem',
                        'color' => 'amber',
                    ]"
                    :if-end="[
                        'text' => [__('NO: publication verified'), __('Finalize run and reports')],
                        'width' => 'long',
                        'align' => 'center',
                        'return' => false,
                        'returnOffset' => '0rem',
                        'color' => 'green',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target.verification-stop"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target',
                        'workflow.target.verification.true.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="3rem"
                    color="amber"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target.history"
                    attach-to="workflow.target.verification.false.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="3rem"
                    after-length="6rem"
                    :step-label="['text' => [__('10. Expose one consistent result')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => ['text' => [__('OWNER: Workbench read models'), __('Statistics, reports and dataset details'), __('Unified history with preserved origins')], 'width' => 'long', 'align' => 'left'],
                        'right' => ['text' => [__('TW-Graph renders the supplied history'), __('No translation decisions in TW-Graph'), __('Next change starts a new scoped run')], 'width' => 'long', 'align' => 'left'],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target.complete"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target',
                        'workflow.target.history.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="3rem"
                    color="green"
                />
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/target.blade.php"
        :segments="3"
    />
</flux:callout>
