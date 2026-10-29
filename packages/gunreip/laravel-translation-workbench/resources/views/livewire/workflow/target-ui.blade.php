<flux:callout
    id="workflow-target-ui"
    {{-- class="min-w-0" --}}
    color="cyan"
    icon="route"
    heading="{{ __('Target workflow UI — from literal to translated page') }}"
>

    {{-- <flux:callout.heading>
        {{ __('Target workflow UI — from literal to translated page') }}
    </flux:callout.heading> --}}
    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('This is the proposed user journey and a roadmap for the future UI, independent of the current screens, controls and commands. It shows the successful path plus known literals found again, moved, replaced or removed within the code. This roadmap can evolve as the workflow becomes clearer. Further alternatives and exceptional cases will be added later.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Read from top to bottom: the central boxes show the actions, the left labels describe the intended workspace, and the right labels show the result and where it is stored. Workspace names describe responsibilities, not existing menu items. The connected side path on the right expands step 2, scanning, and returns to the main axis below the summary labels. It describes the same scan, not an additional or parallel process.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Saving stores translations in the Workbench database. Publication writes the language files and then replaces the source literals with keys. The application displays the translations from those language files.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('On the first scan of a new literal, store its exact original source text as immutable raw_data together with its source location, and generate a SuggestedKey. Keep this original capture separate from editable translations and the confirmed translation key. Subsequent scans and editorial changes must not overwrite raw_data. The SuggestedKey is a proposal for review, not an approved key and not a source-code replacement.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Classify three independent properties: argument origin (direct string, variable or expression), text form (plain text, placeholders or plural forms), and a separately verified translation-key reference. These properties can coexist: a dynamic key can reference plural text with placeholders. Store the classification beside immutable raw_data, not instead of it.') }}
        </flux:callout.text>
        <flux:callout.text>
            {{ __('Planned UI decision: mark a value as DatabaseValue when it should be displayed unchanged from its database source. This completes its translation review without creating a translation, approval or language-file export. Database origin alone does not make this decision. Preserve the source evidence, decision scope, actor and time; unchanged rescans retain the decision. Changes to the source meaning or assignment require review, and the decision can be revised explicitly.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Placeholders such as :name belong to the translation text; their values are supplied when rendering, in PHP as well as Blade. Keep placeholder names when translating. Plural alternatives belong to one translation entry and may contain placeholders. Concatenated expressions should receive a review suggestion for a placeholder-based form, never a silent code rewrite.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('A static string may already be a translation key; a dot alone is not proof. Check its reference before treating it as a new literal. Variables may supply either text or keys. Inspect their origin and possible values without executing application code during scanning. Unresolved expressions remain findings for review, not invented translation values. Only resolved text literals continue into first capture and SuggestedKey creation. Published key usages follow their existing assignment; unresolved arguments or key references remain open for clarification.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Rescan cases: a literal is new, unchanged at its known position, or unchanged but moved within the code. Recognize it by source context and content, not by line number alone. Rediscovery retains the finding, key assignment, saved values and review status. Replacing the source text is a new capture plus reconciliation of the missing old occurrence; it does not automatically continue the old finding. Additional active occurrences follow the new-capture path. Ambiguous identity matches remain a separate case to add later.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Known and moved: the original occurrence is no longer at its previous position and context plus unchanged content identify exactly one successor. Both positions must be covered by the scan. Update the existing source location, retain the finding identity and editorial state, and record the old and new file/line with the scan ID and timestamp. Identical text alone does not prove a move; an additional active occurrence is a new capture, not an automatic relocation. There is no separate copy-detection case.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Literal replaced in source code: replacing XYZ with ABC means that XYZ is no longer found at that occurrence and ABC is captured as a new literal with its own immutable raw_data and SuggestedKey. Confirm removal only after a successful complete scan of the relevant scope. Retain the old finding and its history, and log removal and new capture separately. Do not automatically transfer its key, translations or approvals to the new finding.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Later wording changes belong in the Workbench translation editor. Editing a translation retains the assigned key and the original captured raw_data, records the editorial change, and follows review and publication. This is a later part of the Target Workflow UI, separate from discovering or replacing source literals.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Known and removed is a comparison with previously active occurrences after a successfully completed scan of their scope, not a case among newly found literals. An excluded file or failed scan is not evidence of deletion. Resolve moves and documented literal-to-key replacement during publication first; that publication retains the finding and its provenance. Only confirmed missing occurrences become inactive; keep their identity, translations and history, and log the last file/line, old and new state, scan ID and time once per state change.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('A removed occurrence leaves the active translation work list. If its key still has other active usages, those usages and their translations remain active. Without another usage, show it as unused and awaiting review; do not delete database records, keys or language-file entries automatically. The graph below follows one occurrence at a time; the scan result summarizes all occurrences.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Shared candidates: after capturing findings and reconciling source locations, compare the current literal content across the active database records, including findings outside the current scan scope. At least two active findings with identical literal content and different SuggestedKeys make the whole group sharedCandidate. Count each finding once, not once per scan. The marker is a review hint; it neither merges records nor changes their SuggestedKeys, translations or immutable raw_data.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Recalculate affected shared groups after additions and removals. Replacing XYZ with ABC affects the old and new text groups through removal and new capture, without rewriting the original raw_data. Remove sharedCandidate when fewer than two active members remain or the group no longer has different SuggestedKeys. Inactive findings do not count and lose the active marker; their history is retained. Record marker changes with the affected findings, group membership, old/new state and the triggering scan. Repeated unchanged checks do not create new change events.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Logs accompany the work. Each scan records its start, end and result, and links observations to that run. Finding creation, source relocation, confirmed removal and actual editorial changes belong in the history; unchanged rediscovery does not create a new content-change event. Scanning does not start editing or publication automatically.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Scan scope comes from translation-workbench configuration: paths, exclude_paths, file_patterns and ignored_filename_contains. Translation functions define the calls to inspect. Commands currently allow an explicit paths override; the roadmap requires making the effective scope and any override visible and recording them with the scan. Only successfully scanned scope can establish that an occurrence has disappeared.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Capture context alongside the immutable original: file and line, a bounded source excerpt, the translation call and its enclosing component, view or method where identifiable. This helps distinguish a button label from a heading, message or other usage. Store observed source context separately from later editorial explanations; do not guess the business meaning. Missing context remains open for clarification in the UI.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('The editorial entry starts with the scan result or the existing work list. A finding describes a source occurrence; a translation task describes the text to maintain for an assigned key and selected languages. Opening a finding does not create a duplicate task, change its status or publish it. Capture only observes and stores findings; it does not edit translations or publish them. Aligning the Artisan commands is a subsequent implementation task once this roadmap is sufficiently defined.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Additional active occurrence: capture each distinct source occurrence, even when the same literal already exists in the same file, on the same line or in another file. File, line and position identify the occurrence for later source replacement; they do not determine sharedCandidate membership. Do not infer whether text was copied. Rediscovery of the same occurrence is not an additional finding. Recalculate sharedCandidate using the existing rule: at least two active findings with identical text and different SuggestedKeys.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Comments are ignored when capturing active occurrences, including historical commented-out source copies. They need no separate workflow or status. If a previously active occurrence is now commented out, reconcile it as no longer active after a successful complete scan of its scope. Retain its captured raw_data and history; comments must not count towards sharedCandidate.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Rescanning a published translation call: use the recorded publication and key assignment to recognize the active source occurrence even though the literal has been replaced by a key. Retain the original raw_data, translations, approvals and history; update its observation and scan link. Do not create a new literal finding or SuggestedKey, and do not mark the published occurrence as removed. A key without a reliable occurrence assignment remains open for clarification; never invent a link or infer literal text from its spelling.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Before editing, inspect the source excerpt, usage and existing translations. Clarify meaning, placeholders and plural forms; store editorial explanations separately from captured source data, with author and timestamp when saved. Continue with a clear context in this initial path; unresolved cases remain open for later clarification. Reuse an existing translation task when its assignment is established. Identical text and sharedCandidate are review hints, not permission to merge tasks or keys automatically. Review any shared candidates before confirming the key assignment.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Key assignment is an explicit editorial decision. Accept or revise a SuggestedKey, or deliberately select a suitable existing key after comparing meaning, placeholders, plural forms and related usages. Identical wording or sharedCandidate alone does not justify sharing a key. Check the proposal against the configured key rules and existing assignments before saving. Conflicts remain open without overwriting existing values. This path continues with a valid confirmed assignment; conflict resolution and changes to already published keys will be detailed separately.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Saving the key assignment links the selected finding to the translation task and key in the Workbench database. Retain the original raw_data and existing translations. Record actual assignment changes with previous and new key, affected finding, author and time. Opening or resaving an unchanged assignment creates no duplicate task or change event. Source replacement and language-file export remain publication steps, not effects of confirming a key.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Translation entry starts by selecting the configured target languages and inspecting their existing values and status. Show the captured source and contextual explanation alongside the editable values. Distinguish an actual stored translation from a displayed fallback and from a missing value; a fallback is not a completed translation. Selecting languages does not overwrite or remove values in other languages.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Before saving, check required placeholders, plural syntax and locale-appropriate forms, and make missing or invalid inputs visible per language. Preserve input while the user corrects it. This validation path continues with valid values; deliberately unfinished input can instead be saved as a draft in the pause branch. Save the explicitly edited language values to the confirmed key, preserving other languages and immutable raw_data. Record actual value changes with language, old/new value, author and time. Saving is neither approval nor publication; changed values must be reviewed again and unchanged saves create no duplicate change events.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Review concerns explicitly selected saved keys and language values, together with their source context and related usages. Show the current saved text and relevant changes before confirmation. A displayed fallback, an unselected language or unsaved editor input is not implicitly approved. Check meaning, placeholders and plural forms; this path continues with a successful review, while corrections remain work for the editor.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Approval records the reviewed assignment and saved value state, selected languages, reviewer and time. It does not write language files or replace source literals. Before publication, verify that the selected data still matches the approved state; changed values or assignments require review again. Historical approval remains traceable but cannot approve later changes. Publication starts only through a separate explicit action with its own visible scope.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Publication begins with a preview of the approved keys, languages, target language files and exact source occurrences to change. Show the proposed differences and confirm this scope explicitly. Verify that approved data and source context still match the plan before writing; a stale plan requires a new preview and confirmation. Preserve entries outside the selected scope. A publication run links its language-file changes, source replacements and verification results under one run ID.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Write and verify the selected language entries before replacing source literals. Replace only the confirmed translation-call occurrences, preserving placeholders and arguments; matching text elsewhere is not permission for a global replacement. Already keyed usages require no literal replacement when only their translations change. Preserve raw_data and record the publication link so the next scan recognizes the same occurrence.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Verify the resulting language values and source references before marking the selected publication complete. A started run or a successful language-file write alone is not a completed publication. Record failures and any partial writes explicitly, stop dependent steps and retain the actual state for recovery; do not claim an automatic rollback. The language-write failure branch records incomplete publication before dependent source replacement. Source replacement has its own failure branch; detailed recovery execution will be specified separately. The UI exposes the result, affected files and history before the translated application is checked.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('SharedCandidate is an invitation to compare contexts, not a merge instruction. Compare the meaning, placeholders, plural forms and intended usage of the selected findings. If one shared key is appropriate, explicitly select the participating findings and propose that key for confirmation. Otherwise keep separate keys, even for identical wording. Preserve each finding and its raw_data and history; do not overwrite conflicting saved translations. Record the confirmed assignment and its reason. Existing published-key migrations remain a separate case.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Language activation is independent of translation completeness. An active language may have no own published value yet. Resolve each requested value through the configured language hierarchy: for example de-AT, then its configured German parent de or de-DE, then the source language en. This is a configured parent relationship, not an arbitrary search across sibling locales. Show the effective value and the language supplying it; fallback does not create or approve a translation for the requested locale.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Before publication replaces a source literal with a key, the literal remains visible from the source code. Once a key is used, its source-language value must be available as the final fallback even when target or sublanguage values are missing. Verify that source-language coverage before source replacement; do not require every target language to be translated or copy fallback values into missing translations. Language activation, value approval and publication are distinct decisions.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('If meaning or usage is unclear, explicitly defer editorial work with a question and the relevant source context. This is an editorial clarification state, not an inactive or deleted source occurrence. Keep raw_data, existing keys, translations and history; do not approve, publish or merge anything through deferral. Normal rescans still observe the active source occurrence and must not silently clear the clarification state.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Pending clarification remains accessible in the work list. Save the explanation and record author, time and actual state changes. Resume deliberately by reopening the same finding at context review, checking the current source and assignments again. Do not create a duplicate task or automatically restore approval. If the source has disappeared meanwhile, use the missing-occurrence review instead of continuing as if it were still active.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('If writing or verifying language files fails, stop source replacement for the selected publication run. Show which files and values were written, failed, skipped or remain uncertain. Missing translations outside the selected scope are not a publication failure when the configured fallback covers them. Keep the publication incomplete and retain its run ID, errors and actual file state. Do not mark it published or assume that database and filesystem changes were rolled back together.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('The outer recovery loop returns to approval readiness before a new publication preview. It represents deliberate resumption after inspection, not an automatic retry. Compare the actual files, approved values and current source against the failed plan. Correct the cause, prepare a fresh preview for the remaining or corrective work, and confirm it explicitly. Preserve the failed run and link the recovery run to it. Any changed translation or assignment returns to review first; already successful writes must remain visible rather than being silently repeated or forgotten.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Source replacement is verified per confirmed occurrence: the intended translation call uses the expected key, its arguments are preserved and the resulting file remains valid. Recheck source context before each write. Changed or ambiguous source is a conflict, not permission to replace matching text elsewhere. For already keyed usages, verify the expected reference without forcing a rewrite.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('If source writing or verification fails, stop further source changes and keep the run incomplete. Language files may already contain published values, and some source occurrences may already use keys while others still contain literals. Show these actual outcomes separately, including unknown outcomes, and preserve the publication link for every verified replacement so the next scan retains its finding identity. Do not undo successful replacements or delete language entries automatically. Recovery requires a fresh comparison and confirmed plan linked to the failed run.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Before saving edited translations, compare the current saved values and key assignment with the state loaded for editing. The comparison must guard the write itself so that a change after a preliminary check cannot be silently overwritten. Check the relevant edited values and assignment, not unrelated activity elsewhere. A conflict leaves the current database state untouched and preserves the unsaved input for comparison.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Resolve a concurrent edit deliberately: show the originally loaded values, current stored values and own proposed text. The user can adopt the current values, revise the proposed text or cancel; never reload over unsaved input or silently merge it. Re-enter language selection and editing with a fresh baseline, then validate and check again before saving. If the key assignment changed, clarify that assignment before saving against a different key. An aborted save is not a translation-change event; actual committed changes retain their normal history.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Pausing editorial work is explicit: save the selected inputs as an unpublished draft or deliberately leave without saving them. Do not silently discard unsaved input. A draft may contain unfinished text and unresolved validation hints; it is not an approved or published value and must not replace the effective published translation or fallback. Draft persistence must also detect concurrent edits before writing. Missing own translations in other active languages remain normal fallback cases, not a reason to block a draft.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Reopen the saved draft through the same translation task, with its language scope, input and validation hints. Compare it with the current key assignment and saved values; do not overwrite changes made since the draft was saved. Continue through input validation, conflict checking, saving and review. Keep raw_data and other language values unchanged, log actual draft changes with author and time, and do not create duplicate tasks or change events for an unchanged draft.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('If review finds corrections necessary, record the feedback against the reviewed key, languages and saved values, with reviewer and time. Do not approve the affected proposed values. Keep the current published translations and fallback unchanged, preserve the work and return to editing. After correction, validate, save and review the changed state again; feedback does not automatically rewrite text or restore an earlier approval. Unrelated language values are not rejected implicitly.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Revising a published translation reopens the existing key and selected languages, retaining the immutable raw_data and publication history. Compare the current saved and published values and inspect shared usages. Validate, save, review and approve the changed state again before publication. Until then, the current published values and fallback remain effective. With an unchanged key, publish the selected language values without replacing source literals again. A key change is a separate migration, not a text correction.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Key migration is an explicit plan for selected occurrences, not a new literal capture. Validate the target key, related usages and per-language values without overwriting conflicts. Publish and verify required target values before updating source references. Record each verified assignment change and preserve original raw_data and history. Remove an old export only when no remaining usage needs it; do not introduce a permanent compatibility alias automatically.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Shared assignments can be merged or split only after comparing meaning, placeholders, plural forms and affected usages. Explicitly select findings and resolve conflicting language values. Splitting changes only the selected occurrences; remaining usages retain their key. Neither identical wording nor sharedCandidate is an instruction to merge. Reassess approval for the changed scope and log old and new assignments while preserving each finding identity.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Archive is a deliberate lifecycle decision after reliable usage verification, not a consequence of an excluded file or incomplete scan. Remaining active usages prevent retirement. Preserve findings, immutable raw_data, translations and history. Removing unused language-file entries requires its own explicit preview and confirmation; archival is not physical deletion. A later reappearance requires review of the archived assignment and current state, not silent deletion or blind republication.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        color="cyan"
        icon="dot"
    >
        <flux:callout.text>
            {{ __('Maintenance proposals leave effective assignments and published values unchanged until an explicitly reviewed current plan is executed. Required language values precede source-reference changes; old export cleanup follows verified usage checks. Log verified outcomes separately from uncertain or failed steps. Partial execution stays incomplete, stops dependent writes and requires a fresh comparison and confirmed recovery plan; never assume a transaction spans database and files.') }}
        </flux:callout.text>
    </flux:callout>

    @php
        // Flux icon used for list items within textLabel lines.
        $workflowListIcon = 'dot';
    @endphp

    @include('translation-workbench::livewire.workflow.implementation-notes', ['implementationGraph' => 'target-ui'])

    <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
        <div class="mt-3 max-h-[96rem] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <x-translation-workbench::ui.tw-graph
                {{-- class="bg-zinc-800" --}}
                graph-id="translation-workflow-target-ui"
                :dev="true"
                :coordinates="true"
                :dev-counter-auto="true"
                min-width="48rem"
                min-height="24rem"
                horizontal-padding="4rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.flow-start
                    id="workflow.target-ui.start"
                    direction="top-bottom"
                    color="sky"
                    :start-label="[
                        'text' => [__('A new text needs a translation')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.author"
                    attach-to="workflow.target-ui.start.anchorNode-end"
                    direction="top-bottom"
                    color="sky"
                    before-length="3rem"
                    after-length="2rem"
                    :step-label="[
                        'text' => [__('1. Write translatable text in the code')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Developer · Code editor'),
                                ['icon' => $workflowListIcon, 'text' => __('Mark the literal for translation')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('SOURCE CODE'),
                                ['icon' => $workflowListIcon, 'text' => __('Literal: Save changes')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-scope"
                    attach-to="workflow.target-ui.author.anchorNode-end"
                    direction="top-bottom"
                    color="sky"
                    before-length="5rem"
                    after-length="4rem"
                    :step-label="[
                        'text' => [__('Read the configured scan scope')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('CONFIGURATION'),
                                ['icon' => $workflowListIcon, 'text' => __('translation-workbench.php')],
                                ['icon' => $workflowListIcon, 'text' => __('paths / exclude_paths')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('file_patterns / ignored_filename_contains'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('translation_functions')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('EFFECTIVE SCAN SCOPE'),
                                ['icon' => $workflowListIcon, 'text' => __('Show included files and exclusions')],
                                ['icon' => $workflowListIcon, 'text' => __('Make explicit path overrides visible')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record the effective scope in the scan log'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Outside this scope is not deleted')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan"
                    attach-to="workflow.target-ui.scan-scope.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="7rem"
                    after-length="8rem"
                    :step-label="['text' => [__('2. Start the scan')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Capture'),
                                ['icon' => $workflowListIcon, 'text' => __('Start scanning the configured scope')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Capture active source occurrences only'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Ignore commented-out code')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('SCAN RESULT'),
                                ['icon' => $workflowListIcon, 'text' => __('Literal and its source location')],
                                ['icon' => $workflowListIcon, 'text' => __('LOG: scan started · time and scope')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Follow the connected scan detail path'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Connected scan detail: preserve summary labels, then route right and return to the main axis. --}}
                @php
                    $workflowUiScanDetailOrigin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.scan.anchorNode-end',
                    );
                    // Authored clearance below the summary labels; both sideways paths share their bridge length.
                    $workflowUiScanDetailClearance = '8rem';
                    $workflowUiScanDetailBridgeLength = '42rem';
                    $workflowUiScanDetailEntry = [
                        'x' => $workflowUiScanDetailOrigin['x'],
                        'y' =>
                            'calc(' . $workflowUiScanDetailOrigin['y'] . ' + ' . $workflowUiScanDetailClearance . ')',
                    ];
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.scan-detail.entry"
                    :anchor-start="$workflowUiScanDetailEntry"
                    side="right"
                    direction="top-bottom"
                    :bridge-length="$workflowUiScanDetailBridgeLength"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.start"
                    attach-to="workflow.target-ui.scan-detail.entry.anchorNode-end"
                    direction="top-bottom"
                    before-length="3rem"
                    after-length="3rem"
                    color="lime"
                    :step-label="[
                        'text' => [__('Scan process · detail of step 2')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.scope"
                    attach-to="workflow.target-ui.scan-detail.start.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="3rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Resolve the effective scan scope')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('CONFIGURATION → FILE LIST'),
                                ['icon' => $workflowListIcon, 'text' => __('Apply paths, patterns and exclusions')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Ignore comments as active occurrences'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Record scope, scan ID and start time')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.read"
                    attach-to="workflow.target-ui.scan-detail.scope.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="4rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Read files and inspect translation calls')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('SOURCE → OBSERVATIONS'),
                                ['icon' => $workflowListIcon, 'text' => __('Inspect source without executing it')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Capture file, line, position and context'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Record unreadable or changed files')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.classify"
                    attach-to="workflow.target-ui.scan-detail.read.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="4rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Classify each observed occurrence')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('TEXT / KEY / EXPRESSION'),
                                ['icon' => $workflowListIcon, 'text' => __('Resolve literal text where possible')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Identify placeholders and plural forms'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep unresolved expressions for review'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.match"
                    attach-to="workflow.target-ui.scan-detail.classify.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="4rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Match observations to known findings')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('OBSERVATIONS → WORKBENCH DATABASE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Known: update observation; retain edits'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Uniquely moved: update source position'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('New or additional: raw_data + SuggestedKey'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Log creation or actual location changes'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.reconcile"
                    attach-to="workflow.target-ui.scan-detail.match.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Reconcile previously active occurrences')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('ONLY SUCCESSFULLY COMPLETED SCOPE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Account for moves and published keys first'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Confirmed missing: mark inactive')],
                                ['icon' => $workflowListIcon, 'text' => __('Retain original capture and history')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Incomplete scan: no removal inference'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.shared"
                    attach-to="workflow.target-ui.scan-detail.reconcile.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Recalculate affected shared candidates')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('ACTIVE FINDINGS ACROSS THE DATABASE'),
                                ['icon' => $workflowListIcon, 'text' => __('Same text + different SuggestedKeys')],
                                ['icon' => $workflowListIcon, 'text' => __('N ≥ 2: mark; otherwise remove marker')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Ignore inactive and commented occurrences'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Log actual marker changes')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-detail.finish"
                    attach-to="workflow.target-ui.scan-detail.shared.anchorNode-end"
                    direction="top-bottom"
                    color="lime"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Close the scan and provide its result')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'right' => [
                            'text' => [
                                __('SCAN LOG + RESULT'),
                                ['icon' => $workflowListIcon, 'text' => __('End time, counts and errors')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Status: complete, incomplete or failed'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Link results to findings for later UI review'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('No translation editing or publication'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.scan-detail.return"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.scan-detail.finish.anchorNode-end',
                    )"
                    side="left"
                    direction="top-bottom"
                    :bridge-length="$workflowUiScanDetailBridgeLength"
                    color="violet"
                />
                {{-- Visual continuity of the main axis while the connected side path explains the scan. --}}
                @php
                    $workflowUiScanDetailReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.scan-detail.return.anchorNode-end',
                    );
                    $workflowUiScanAxisLength =
                        'calc(' . $workflowUiScanDetailOrigin['y'] . ' - (' . $workflowUiScanDetailReturn['y'] . '))';
                    $__env
                        ->getConsumableComponentData('twGraphCalculatedLengths')
                        ?->record(
                            'workflow.target-ui.scan-detail.main-axis',
                            'segments.path',
                            'workflow.target-ui.scan-detail.main-axis',
                            'length',
                            ['start' => $workflowUiScanDetailOrigin, 'target' => $workflowUiScanDetailReturn],
                            'Vertical distance from the scan summary to the side-path return.',
                        );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path
                    id="workflow.target-ui.scan-detail.main-axis"
                    :anchor-start="$workflowUiScanDetailOrigin"
                    :anchor-end="$workflowUiScanDetailReturn"
                    :length="$workflowUiScanAxisLength"
                    direction="top-bottom"
                    color="lime"
                    :dashed="true"
                    :node-start="false"
                    :node-end="false"
                    :z-index="10"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.source-context"
                    attach-to="workflow.target-ui.scan-detail.return.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Capture the source context')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('SOURCE CONTEXT'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('File and line + bounded source excerpt'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Translation call and enclosing code')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Component / view / method when identifiable'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Usage: button, heading or message?')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('FINDING CONTEXT'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Store context beside immutable raw_data'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Link the observation to this scan')],
                                ['icon' => $workflowListIcon, 'text' => __('Business meaning is not inferred')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Clarify unknown purpose later in the UI'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.argument-origin"
                    attach-to="workflow.target-ui.source-context.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="5rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Classify the argument origin')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('ARGUMENT ORIGIN'),
                                ['icon' => $workflowListIcon, 'text' => __('Direct string in the call')],
                                ['icon' => $workflowListIcon, 'text' => __('PHP variable supplied to the call')],
                                ['icon' => $workflowListIcon, 'text' => __('Composed expression / concatenation')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('CAPTURE + CLASSIFICATION'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Preserve the original source expression'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Inspect origin and possible values')],
                                ['icon' => $workflowListIcon, 'text' => __('Unknown value: review needed')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.text-form"
                    attach-to="workflow.target-ui.argument-origin.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="['text' => [__('Describe the text form')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('TEXT FORM'),
                                ['icon' => $workflowListIcon, 'text' => __('Plain text')],
                                ['icon' => $workflowListIcon, 'text' => __('Placeholder text · :name / :count')],
                                ['icon' => $workflowListIcon, 'text' => __('Plural alternatives · trans_choice')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('EDITOR GUIDANCE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Placeholders can occur in plural text'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep placeholder names when translating'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Plural forms belong to one entry')],
                                ['icon' => $workflowListIcon, 'text' => __('Concatenation: suggest placeholders')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.key-reference"
                    attach-to="workflow.target-ui.text-form.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Check the translation-key reference')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('KEY REFERENCE'),
                                ['icon' => $workflowListIcon, 'text' => __('Is the argument an existing key?')],
                                ['icon' => $workflowListIcon, 'text' => __('Do not classify by dots alone')],
                                ['icon' => $workflowListIcon, 'text' => __('Variables can contain text or keys')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('WORKBENCH DATABASE'),
                                ['icon' => $workflowListIcon, 'text' => __('Origin + text form + key reference')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Store separately from original raw_data'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Log classification and its evidence')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Continue below with resolved literals'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Upper YES continues to key review; lower NO returns to the main axis without crossing it. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.input-kind"
                    attach-to="workflow.target-ui.key-reference.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="violet"
                    before-length="5rem"
                    after-length="2rem"
                    stem-length="4rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Key reference or unresolved argument?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · key reference or unresolved argument'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Check existing publication and assignment'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'sky',
                        'return' => false,
                        'returnOffset' => '24rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · resolved literal'),
                            ['icon' => $workflowListIcon, 'text' => __('Continue with literal recognition')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'violet',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.published-key"
                    attach-to="workflow.target-ui.input-kind.true.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="violet"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="8rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Published key and source occurrence reliably assigned?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · known published occurrence'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Retain raw_data, translations and history'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('Update last seen and scan observation')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep occurrence active; no removal')],
                            ['icon' => $workflowListIcon, 'text' => __('No new literal finding or SuggestedKey')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'green',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · assignment requires clarification'),
                            ['icon' => $workflowListIcon, 'text' => __('Record reference, location and evidence')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Retain existing data; do not guess a link'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('No automatic literal or SuggestedKey')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Make uncertainty visible for later review'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target-ui.key-observation-complete"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.published-key.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="3rem"
                    color="sky"
                    :end-label="[
                        'text' => [__('Include observation or clarification in scan result')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.input-kind.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.literal-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.literal-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.literal-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                    id="workflow.target-ui.recognize"
                    attach-to="workflow.target-ui.literal-return.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="violet"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="8rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Known and unchanged at the same position?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · same position'),
                            ['icon' => $workflowListIcon, 'text' => __('Reuse finding; update last seen')],
                            ['icon' => $workflowListIcon, 'text' => __('Link observation to this scan')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                    ]"
                    :elseifs="[
                        [
                            'key' => 'moved',
                            'conditionLabel' => [
                                'text' => [__('Known and uniquely identified as moved?')],
                                'width' => 'long',
                                'align' => 'center',
                            ],
                            'actionLabel' => [
                                'text' => [
                                    __('YES · moved in code'),
                                    [
                                        'icon' => $workflowListIcon,
                                        'text' => __('Update position; retain finding and edits'),
                                    ],
                                    [
                                        'icon' => $workflowListIcon,
                                        'text' => __('Log old → new position in this scan'),
                                    ],
                                ],
                                'width' => 'long',
                                'align' => 'center',
                                'color' => 'sky',
                            ],
                            'beforeLength' => '6rem',
                            'afterLength' => '3rem',
                        ],
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · new or additional active occurrence'),
                            ['icon' => $workflowListIcon, 'text' => __('Store original as immutable raw_data')],
                            ['icon' => $workflowListIcon, 'text' => __('Generate SuggestedKey')],
                            ['icon' => $workflowListIcon, 'text' => __('Create finding and source location')],
                            ['icon' => $workflowListIcon, 'text' => __('Log creation; link to this scan')],
                        ],
                        'maxLines' => 5,
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'violet',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.recognize.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.recognize-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.recognize-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.recognize-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.scan-result"
                    attach-to="workflow.target-ui.recognize-return.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Finish scan and show its result')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Scan result'),
                                ['icon' => $workflowListIcon, 'text' => __('New, unchanged and moved findings')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare previously active occurrences next'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Replaced literal: new capture + removal'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Open the work list when ready')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('SCAN LOG'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Start, end, scope and counts per case'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Observation links for this run')],
                                ['icon' => $workflowListIcon, 'text' => __('No duplicate translation tasks')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Additional occurrence → sharedCandidate check'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Missing occurrences are reconciled after the scan, not classified as newly observed literals. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.removed"
                    attach-to="workflow.target-ui.scan-result.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="violet"
                    before-length="4rem"
                    after-length="3rem"
                    stem-length="6rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Previously active occurrence confirmed removed?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · removed from code'),
                            ['icon' => $workflowListIcon, 'text' => __('Mark occurrence inactive; retain history')],
                            ['icon' => $workflowListIcon, 'text' => __('Log position and active → inactive')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '24rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · still present'),
                            ['icon' => $workflowListIcon, 'text' => __('Continue with the active finding')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'violet',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.remaining-usages"
                    attach-to="workflow.target-ui.removed.true.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="amber"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="6rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Does the translation key have other active usages?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · still in use elsewhere'),
                            ['icon' => $workflowListIcon, 'text' => __('Keep key and translations active')],
                            ['icon' => $workflowListIcon, 'text' => __('Only this occurrence is inactive')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · no remaining usage'),
                            ['icon' => $workflowListIcon, 'text' => __('Mark unused; await review')],
                            ['icon' => $workflowListIcon, 'text' => __('Retain key, values and language entries')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'amber',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.removal-result"
                    attach-to="workflow.target-ui.remaining-usages.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Show removal in the scan result and history')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Inactive occurrences'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Removed occurrence leaves active work list'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('HISTORY + SCAN RESULT'),
                                ['icon' => $workflowListIcon, 'text' => __('Last file/line · old and new state')],
                                ['icon' => $workflowListIcon, 'text' => __('Scan ID · timestamp · removal count')],
                                ['icon' => $workflowListIcon, 'text' => __('No repeated removal events')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.removal-shared"
                    attach-to="workflow.target-ui.removal-result.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="4rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Recalculate the affected shared group')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('WORKBENCH DATABASE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inactive finding: clear sharedCandidate'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Count remaining active findings once')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('SHARED GROUP + HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('N ≥ 2 and different SuggestedKeys: marked'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('N < 2: remove the remaining marker')],
                                ['icon' => $workflowListIcon, 'text' => __('Log membership and marker changes')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target-ui.removal-complete"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.removal-shared.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="3rem"
                    color="amber"
                    :end-label="[
                        'text' => [__('Retained for history and review')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.removed.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.active-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.active-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.active-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.shared"
                    attach-to="workflow.target-ui.active-return.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="6rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Same literal: N ≥ 2 active findings?'), __('With different SuggestedKeys?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · sharedCandidate'),
                            ['icon' => $workflowListIcon, 'text' => __('Mark all active group members')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep their individual SuggestedKeys')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · no shared candidate'),
                            ['icon' => $workflowListIcon, 'text' => __('Remove any existing group markers')],
                            ['icon' => $workflowListIcon, 'text' => __('Retain findings and translations')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'violet',
                    ]"
                />
                {{-- Keep the main workflow centered after the shared-candidate decision. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.shared.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.shared-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.shared-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.shared-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.shared-log"
                    attach-to="workflow.target-ui.shared-return.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Show shared candidates for review')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Findings'),
                                ['icon' => $workflowListIcon, 'text' => __('XYZ · SuggestedKey A')],
                                ['icon' => $workflowListIcon, 'text' => __('XYZ · SuggestedKey B')],
                                ['icon' => $workflowListIcon, 'text' => __('Both are marked sharedCandidate')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('HISTORY: marker changes only'),
                                ['icon' => $workflowListIcon, 'text' => __('Affected findings · group members')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Old/new marker · triggering scan/edit'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('A candidate is not an automatic merge'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.capture"
                    attach-to="workflow.target-ui.shared-log.anchorNode-end"
                    direction="top-bottom"
                    color="violet"
                    before-length="4rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('3. Open the captured findings')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Findings'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Open from scan result or existing work list'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inspect scan status, counts and errors'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Select a finding requiring attention')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('See the text together with its context'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('WORKBENCH DATABASE'),
                                ['icon' => $workflowListIcon, 'text' => __('Original raw_data remains unchanged')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('SuggestedKey available for new literals'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Untranslated remains open')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Existing editorial decisions are retained'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.editorial-context"
                    attach-to="workflow.target-ui.capture.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="10rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Understand the finding in its source context')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Context review'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inspect file, position and source excerpt'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('What does this text mean here?')],
                                ['icon' => $workflowListIcon, 'text' => __('Check placeholders and plural forms')],
                                ['icon' => $workflowListIcon, 'text' => __('Compare any existing translations')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('CONTEXT + EDITORIAL EXPLANATION'),
                                ['icon' => $workflowListIcon, 'text' => __('Captured raw_data remains immutable')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Save explanations separately from source'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('History: author, time and actual changes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Unclear purpose remains open for review'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Keep the ongoing path below the clarification branch to avoid crossed returns. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.context-clarification"
                    attach-to="workflow.target-ui.editorial-context.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="4rem"
                    after-length="3rem"
                    stem-length="6rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Does the context still need clarification?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · defer editorial work'),
                            ['icon' => $workflowListIcon, 'text' => __('Record the question and source context')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep the active finding and saved values')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('No approval or publication through deferral'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · context is understood'),
                            ['icon' => $workflowListIcon, 'text' => __('Continue to translation-task selection')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.clarification-resume"
                    attach-to="workflow.target-ui.context-clarification.true.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Later: clarify and reopen the same finding')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Pending clarification'),
                                ['icon' => $workflowListIcon, 'text' => __('Find deferred work in the work list')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Save the explanation separately from raw_data'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Recheck current source and assignment'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Resume deliberately at context review'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('EDITORIAL STATE + HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Deferral does not deactivate the finding'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Rescan does not clear clarification')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record question, explanation, author and time'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('No duplicate task or automatic approval'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Route below the summary labels, rise outside their left edge, then re-enter before context review. --}}
                @php
                    $clarificationLoopStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.clarification-resume.anchorNode-end',
                    );
                    $clarificationLoopClearance = '4rem';
                    $clarificationLoopBottom = [
                        'x' => $clarificationLoopStart['x'],
                        'y' => 'calc(' . $clarificationLoopStart['y'] . ' - ' . $clarificationLoopClearance . ')',
                    ];
                    $clarificationLoopTarget = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.capture.anchorNode-end',
                    );
                    $clarificationLoopTarget['y'] = 'calc(' . $clarificationLoopTarget['y'] . ' - 8rem)';
                    $clarificationLoopRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.clarification-loop.clearance',
                    'anchorStart' => $clarificationLoopStart,
                    'anchorEnd' => $clarificationLoopBottom,
                    'length' => $clarificationLoopClearance,
                    'direction' => 'top-bottom',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.clarification-loop.turn"
                    :anchor-start="$clarificationLoopBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="left"
                    bridge-length="26rem"
                    :arc-radius="$clarificationLoopRadius"
                    color="amber"
                    :node-end-dot="false"
                    :joint-arrow-end="true"
                />
                @php
                    $clarificationLoopRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.clarification-loop.turn.anchorNode-end',
                    );
                    $clarificationLoopRiseEnd = [
                        'x' => $clarificationLoopRiseStart['x'],
                        'y' => $clarificationLoopTarget['y'],
                    ];
                    $clarificationLoopRiseLength =
                        'calc(' . $clarificationLoopRiseEnd['y'] . ' - (' . $clarificationLoopRiseStart['y'] . '))';
                    $clarificationLoopBridgeLength =
                        'calc(' .
                        $clarificationLoopTarget['x'] .
                        ' - (' .
                        $clarificationLoopRiseEnd['x'] .
                        ') - 2 * ' .
                        $clarificationLoopRadius .
                        ')';
                    $clarificationLoopLengths = $__env->getConsumableComponentData('twGraphCalculatedLengths');
                    $clarificationLoopLengths?->record(
                        'workflow.target-ui.clarification-loop.rise',
                        'segments.path',
                        'workflow.target-ui.clarification-loop',
                        'length',
                        ['start' => $clarificationLoopRiseStart, 'target' => $clarificationLoopRiseEnd],
                        'Vertical distance to the context-review re-entry.',
                    );
                    $clarificationLoopLengths?->record(
                        'workflow.target-ui.clarification-loop.reentry.bridge',
                        'parts.sideways',
                        'workflow.target-ui.clarification-loop',
                        'bridgeLength',
                        [
                            'start' => $clarificationLoopRiseEnd,
                            'target' => $clarificationLoopTarget,
                            'arcRadius' => $clarificationLoopRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.clarification-loop.rise',
                    'anchorStart' => $clarificationLoopRiseStart,
                    'anchorEnd' => $clarificationLoopRiseEnd,
                    'length' => $clarificationLoopRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.clarification-loop.reentry"
                    :anchor-start="$clarificationLoopRiseEnd"
                    direction="bottom-top"
                    exit-direction="top-bottom"
                    side="right"
                    :bridge-length="$clarificationLoopBridgeLength"
                    :arc-radius="$clarificationLoopRadius"
                    :node-end-dot="true"
                    color="amber"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.context-clarification.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.context-ready-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.context-ready-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.context-ready-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.translation-task"
                    attach-to="workflow.target-ui.context-ready-return.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Select the translation task to work on')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Translation task'),
                                ['icon' => $workflowListIcon, 'text' => __('Finding = a source occurrence')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Task = key + text in selected languages'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Reuse an established task assignment')],
                                ['icon' => $workflowListIcon, 'text' => __('For a new task, confirm its key next')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('EXPLICIT EDITORIAL SCOPE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show related findings and saved values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('sharedCandidate is only a review hint'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('No automatic task or key merging')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Opening a task does not change its status'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.shared-choice"
                    attach-to="workflow.target-ui.translation-task.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="4rem"
                    after-length="3rem"
                    stem-length="8rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Shared candidates suitable for one common key?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · propose a shared key'),
                            ['icon' => $workflowListIcon, 'text' => __('Compare meaning, placeholders and usage')],
                            ['icon' => $workflowListIcon, 'text' => __('Select participating findings explicitly')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep findings and histories separate')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Resolve conflicting values before confirmation'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'cyan',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · keep a separate key'),
                            ['icon' => $workflowListIcon, 'text' => __('Different context or no suitable group')],
                            ['icon' => $workflowListIcon, 'text' => __('Identical wording does not force reuse')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Preserve existing translations and history'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('Continue to explicit key confirmation')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'violet',
                    ]"
                />
                {{-- Keep the main workflow centered after the shared-candidate decision. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.shared-choice.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.shared-choice-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.shared-choice-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.shared-choice-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.key"
                    attach-to="workflow.target-ui.shared-choice-return.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('4. Review and confirm the translation key')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Key assignment'),
                                ['icon' => $workflowListIcon, 'text' => __('Accept or edit the SuggestedKey')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Or deliberately select an existing key'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Compare meaning and related usages')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('PROPOSED ASSIGNMENT'),
                                ['icon' => $workflowListIcon, 'text' => __('Finding → actions.save_changes')],
                                ['icon' => $workflowListIcon, 'text' => __('SuggestedKey is not yet confirmation')],
                                ['icon' => $workflowListIcon, 'text' => __('Existing translations remain intact')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.key-check"
                    attach-to="workflow.target-ui.key.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="4rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Check the proposed key assignment')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Assignment check'),
                                ['icon' => $workflowListIcon, 'text' => __('Validate configured key rules')],
                                ['icon' => $workflowListIcon, 'text' => __('Check existing keys and assignments')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare placeholders and plural forms'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Existing key: confirm semantic suitability'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('VALIDATED PROPOSAL'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('sharedCandidate does not approve reuse'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Conflicts remain open for clarification'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Do not overwrite existing translations'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Continue here with a valid assignment'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.key-save"
                    attach-to="workflow.target-ui.key-check.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="4rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Confirm and save the key assignment')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Explicit confirmation'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Review finding, task and selected key'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Save the assignment deliberately')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Reuse the established translation task'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep source text and language files unchanged'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('WORKBENCH DATABASE + HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Finding → translation task → confirmed key'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Preserve original raw_data and translations'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Log previous/new key, author and time'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Unchanged save creates no duplicate event'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.target-languages"
                    attach-to="workflow.target-ui.key-save.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="10rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Select the target languages')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Language selection'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Active languages may lack own translations'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inspect saved values and status per language'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Keep source text and context visible')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('EDITORIAL LANGUAGE SCOPE'),
                                ['icon' => $workflowListIcon, 'text' => __('Stored value, fallback or missing?')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Displayed fallback is not a translation'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Other language values remain untouched'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Activation is independent of completeness'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.translate"
                    attach-to="workflow.target-ui.target-languages.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="4rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('5. Enter the translations')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Translation editor'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Edit the text for each selected language'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Later wording changes retain the key')],
                                ['icon' => $workflowListIcon, 'text' => __('Original raw_data stays immutable')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('EDITOR'),
                                ['icon' => $workflowListIcon, 'text' => __('For example: Änderungen speichern')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Keep the ongoing path below the draft branch to avoid crossed returns. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.pause-editing"
                    attach-to="workflow.target-ui.translate.anchorNode-end"
                    direction="top-bottom"
                    side="right"
                    color="cyan"
                    before-length="4rem"
                    after-length="3rem"
                    stem-length="6rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Pause editing and continue later?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · preserve work as an unpublished draft'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Save selected input explicitly; check conflicts'),
                            ],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Retain unfinished text and validation hints'),
                            ],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Keep published values and fallback unchanged'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · continue working on the translations'),
                            ['icon' => $workflowListIcon, 'text' => __('Proceed to input validation and saving')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.draft-resume"
                    attach-to="workflow.target-ui.pause-editing.true.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('Later: reopen the draft in the same task')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Saved drafts'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Find the draft through the existing task'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Restore saved input and selected languages'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare current key assignment and values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Resume at language selection and editing'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('UNPUBLISHED DRAFT + HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Draft is not an effective translation'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Do not overwrite concurrent changes')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record actual draft changes, author and time'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('No duplicate task or automatic publication'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Route below the summary labels, rise outside their right edge, then re-enter before language selection. --}}
                @php
                    $draftLoopStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.draft-resume.anchorNode-end',
                    );
                    $draftLoopClearance = '4rem';
                    $draftLoopBottom = [
                        'x' => $draftLoopStart['x'],
                        'y' => 'calc(' . $draftLoopStart['y'] . ' - ' . $draftLoopClearance . ')',
                    ];
                    $draftLoopTarget = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.key-save.anchorNode-end',
                    );
                    $draftLoopTarget['y'] = 'calc(' . $draftLoopTarget['y'] . ' - 8rem)';
                    $draftLoopRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.draft-loop.clearance',
                    'anchorStart' => $draftLoopStart,
                    'anchorEnd' => $draftLoopBottom,
                    'length' => $draftLoopClearance,
                    'direction' => 'top-bottom',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.draft-loop.turn"
                    :anchor-start="$draftLoopBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="right"
                    bridge-length="26rem"
                    :arc-radius="$draftLoopRadius"
                    color="amber"
                    :node-end-dot="false"
                    :joint-arrow-end="true"
                />
                @php
                    $draftLoopRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.draft-loop.turn.anchorNode-end',
                    );
                    $draftLoopRiseEnd = [
                        'x' => $draftLoopRiseStart['x'],
                        'y' => $draftLoopTarget['y'],
                    ];
                    $draftLoopRiseLength = 'calc(' . $draftLoopRiseEnd['y'] . ' - (' . $draftLoopRiseStart['y'] . '))';
                    $draftLoopBridgeLength =
                        'calc(' .
                        $draftLoopRiseEnd['x'] .
                        ' - (' .
                        $draftLoopTarget['x'] .
                        ') - 2 * ' .
                        $draftLoopRadius .
                        ')';
                    $draftLoopLengths = $__env->getConsumableComponentData('twGraphCalculatedLengths');
                    $draftLoopLengths?->record(
                        'workflow.target-ui.draft-loop.rise',
                        'segments.path',
                        'workflow.target-ui.draft-loop',
                        'length',
                        ['start' => $draftLoopRiseStart, 'target' => $draftLoopRiseEnd],
                        'Vertical distance to the language-selection re-entry.',
                    );
                    $draftLoopLengths?->record(
                        'workflow.target-ui.draft-loop.reentry.bridge',
                        'parts.sideways',
                        'workflow.target-ui.draft-loop',
                        'bridgeLength',
                        [
                            'start' => $draftLoopRiseEnd,
                            'target' => $draftLoopTarget,
                            'arcRadius' => $draftLoopRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.draft-loop.rise',
                    'anchorStart' => $draftLoopRiseStart,
                    'anchorEnd' => $draftLoopRiseEnd,
                    'length' => $draftLoopRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.draft-loop.reentry"
                    :anchor-start="$draftLoopRiseEnd"
                    direction="bottom-top"
                    exit-direction="top-bottom"
                    side="left"
                    :bridge-length="$draftLoopBridgeLength"
                    :arc-radius="$draftLoopRadius"
                    :node-end-dot="true"
                    color="amber"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.pause-editing.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiReturnStart['x'] .
                        ' - (' .
                        $workflowUiMainAxis['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.draft-continue.bridge',
                        'parts.sideways',
                        'workflow.target-ui.draft-continue',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.draft-continue"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="left"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.translation-check"
                    attach-to="workflow.target-ui.draft-continue.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Check the translation inputs')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Input validation'),
                                ['icon' => $workflowListIcon, 'text' => __('Verify required placeholders')],
                                ['icon' => $workflowListIcon, 'text' => __('Check plural syntax and locale forms')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show missing or invalid values per language'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Keep input available for correction')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('VALID INPUT FOR SAVING'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Continue here with valid language values'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Do not silently alter placeholders')],
                                ['icon' => $workflowListIcon, 'text' => __('Validation is not editorial approval')],
                                ['icon' => $workflowListIcon, 'text' => __('No source or language-file changes')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Keep the ongoing path below the conflict branch to avoid crossed returns. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.concurrent-edit"
                    attach-to="workflow.target-ui.translation-check.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="5rem"
                    after-length="3rem"
                    stem-length="5rem"
                    bridge-length="4rem"
                    label-gap="4.5rem"
                    :condition-label="[
                        'text' => [__('Has the edited data or key assignment changed meanwhile?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · stop this save and compare'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Preserve unsaved input and current stored values'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('No silent overwrite or automatic merge')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Resolve changed assignments before saving'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · editing baseline still matches'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Save only if the write-time check still matches'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.concurrent-edit-compare"
                    attach-to="workflow.target-ui.concurrent-edit.true.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Compare current values with the proposed edit')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Concurrent edit comparison'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show original, current and proposed values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Adopt current text, revise own input or cancel'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Changed key assignment needs clarification'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Resume editing with a refreshed baseline'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('PRESERVED INPUT + DATABASE STATE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Blocked save leaves stored translations intact'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Do not reload over unsaved input')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Only committed value changes enter edit history'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Validate and compare again before saving'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Route below the summary labels, rise outside their left edge, then re-enter before language selection. --}}
                @php
                    $concurrentEditLoopStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.concurrent-edit-compare.anchorNode-end',
                    );
                    $concurrentEditLoopClearance = '4rem';
                    $concurrentEditLoopBottom = [
                        'x' => $concurrentEditLoopStart['x'],
                        'y' => 'calc(' . $concurrentEditLoopStart['y'] . ' - ' . $concurrentEditLoopClearance . ')',
                    ];
                    $concurrentEditLoopTarget = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.key-save.anchorNode-end',
                    );
                    $concurrentEditLoopTarget['y'] = 'calc(' . $concurrentEditLoopTarget['y'] . ' - 8rem)';
                    $concurrentEditLoopRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.concurrent-edit-loop.clearance',
                    'anchorStart' => $concurrentEditLoopStart,
                    'anchorEnd' => $concurrentEditLoopBottom,
                    'length' => $concurrentEditLoopClearance,
                    'direction' => 'top-bottom',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.concurrent-edit-loop.turn"
                    :anchor-start="$concurrentEditLoopBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="left"
                    bridge-length="26rem"
                    :arc-radius="$concurrentEditLoopRadius"
                    color="amber"
                    :node-end-dot="true"
                    :joint-arrow-end="false"
                />
                @php
                    $concurrentEditLoopRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.concurrent-edit-loop.turn.anchorNode-end',
                    );
                    $concurrentEditLoopRiseEnd = [
                        'x' => $concurrentEditLoopRiseStart['x'],
                        'y' => $concurrentEditLoopTarget['y'],
                    ];
                    $concurrentEditLoopRiseLength =
                        'calc(' . $concurrentEditLoopRiseEnd['y'] . ' - (' . $concurrentEditLoopRiseStart['y'] . '))';
                    $concurrentEditLoopBridgeLength =
                        'calc(' .
                        $concurrentEditLoopTarget['x'] .
                        ' - (' .
                        $concurrentEditLoopRiseEnd['x'] .
                        ') - 2 * ' .
                        $concurrentEditLoopRadius .
                        ')';
                    $concurrentEditLoopLengths = $__env->getConsumableComponentData('twGraphCalculatedLengths');
                    $concurrentEditLoopLengths?->record(
                        'workflow.target-ui.concurrent-edit-loop.rise',
                        'segments.path',
                        'workflow.target-ui.concurrent-edit-loop',
                        'length',
                        ['start' => $concurrentEditLoopRiseStart, 'target' => $concurrentEditLoopRiseEnd],
                        'Vertical distance to the language-selection re-entry.',
                    );
                    $concurrentEditLoopLengths?->record(
                        'workflow.target-ui.concurrent-edit-loop.reentry.bridge',
                        'parts.sideways',
                        'workflow.target-ui.concurrent-edit-loop',
                        'bridgeLength',
                        [
                            'start' => $concurrentEditLoopRiseEnd,
                            'target' => $concurrentEditLoopTarget,
                            'arcRadius' => $concurrentEditLoopRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.concurrent-edit-loop.rise',
                    'anchorStart' => $concurrentEditLoopRiseStart,
                    'anchorEnd' => $concurrentEditLoopRiseEnd,
                    'length' => $concurrentEditLoopRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.concurrent-edit-loop.reentry"
                    :anchor-start="$concurrentEditLoopRiseEnd"
                    direction="bottom-top"
                    exit-direction="top-bottom"
                    side="right"
                    :bridge-length="$concurrentEditLoopBridgeLength"
                    :arc-radius="$concurrentEditLoopRadius"
                    :node-end-dot="true"
                    color="amber"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.concurrent-edit.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.concurrent-edit-ready.bridge',
                        'parts.sideways',
                        'workflow.target-ui.concurrent-edit-ready',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.concurrent-edit-ready"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="violet"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.save"
                    attach-to="workflow.target-ui.concurrent-edit-ready.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="['text' => [__('6. Save the translations')], 'width' => 'long', 'align' => 'center']"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Translation editor'),
                                ['icon' => $workflowListIcon, 'text' => __('Save the edited language values')],
                                ['icon' => $workflowListIcon, 'text' => __('Preserve unedited language values')],
                                ['icon' => $workflowListIcon, 'text' => __('Saving does not approve or publish')],
                                ['icon' => $workflowListIcon, 'text' => __('Changed values need review again')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('WORKBENCH DATABASE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Save language values for the confirmed key'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('HISTORY: language value · who and when'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Language, previous and new value')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Unchanged save: no duplicate change event'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.review-scope"
                    attach-to="workflow.target-ui.save.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Select the saved values for review')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Review selection'),
                                ['icon' => $workflowListIcon, 'text' => __('Select keys and languages explicitly')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inspect saved text and relevant changes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Check source context and related usages'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Review meaning, placeholders and plurals'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('REVIEW SCOPE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Only selected saved values are reviewed'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Unsaved input is not part of approval'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Fallback is not an approved translation'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Corrections return to editorial work')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.review-corrections"
                    attach-to="workflow.target-ui.review-scope.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="5rem"
                    after-length="3rem"
                    stem-length="4rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Does the review require corrections?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · return affected values for correction'),
                            ['icon' => $workflowListIcon, 'text' => __('Do not approve the proposed values')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Keep published values and fallback unchanged'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · reviewed values are suitable'),
                            ['icon' => $workflowListIcon, 'text' => __('Continue to explicit approval')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.review-feedback"
                    attach-to="workflow.target-ui.review-corrections.true.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Record review feedback and reopen editing')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Review feedback'),
                                ['icon' => $workflowListIcon, 'text' => __('Identify affected key and languages')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Explain meaning, placeholder or plural issues'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Keep input available for correction')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Validate, save and review the revised values'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('REVIEW HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Link feedback to the reviewed saved state'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record reviewer, time and required changes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('No automatic text rewrite or approval'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Other languages retain their own status'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Reuse the existing outer editing return; do not overlap a second full-height stem. --}}
                @php
                    $reviewFeedbackStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.review-feedback.anchorNode-end',
                    );
                    $reviewFeedbackClearance = '4rem';
                    $reviewFeedbackBottom = [
                        'x' => $reviewFeedbackStart['x'],
                        'y' => 'calc(' . $reviewFeedbackStart['y'] . ' - ' . $reviewFeedbackClearance . ')',
                    ];
                    $reviewFeedbackJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.concurrent-edit-loop.turn.anchorNode-end',
                    );
                    $reviewFeedbackBridge =
                        'calc(' .
                        $reviewFeedbackBottom['x'] .
                        ' - (' .
                        $reviewFeedbackJoin['x'] .
                        ') - 2 * ' .
                        $concurrentEditLoopRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.review-feedback-loop.turn.bridge',
                        'parts.sideways',
                        'workflow.target-ui.review-feedback-loop',
                        'bridgeLength',
                        [
                            'start' => $reviewFeedbackBottom,
                            'target' => $reviewFeedbackJoin,
                            'arcRadius' => $concurrentEditLoopRadius,
                        ],
                        'Horizontal clearance to the shared editing return minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.review-feedback-loop.clearance',
                    'anchorStart' => $reviewFeedbackStart,
                    'anchorEnd' => $reviewFeedbackBottom,
                    'length' => $reviewFeedbackClearance,
                    'direction' => 'top-bottom',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.review-feedback-loop.turn"
                    :anchor-start="$reviewFeedbackBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="left"
                    :bridge-length="$reviewFeedbackBridge"
                    :arc-radius="$concurrentEditLoopRadius"
                    :node-end-dot="true"
                    :joint-arrow-end="false"
                    color="amber"
                />
                @php
                    $reviewFeedbackRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.review-feedback-loop.turn.anchorNode-end',
                    );
                    $reviewFeedbackRiseLength =
                        'calc(' . $reviewFeedbackJoin['y'] . ' - (' . $reviewFeedbackRiseStart['y'] . '))';
                    $__env
                        ->getConsumableComponentData('twGraphCalculatedLengths')
                        ?->record(
                            'workflow.target-ui.review-feedback-loop.rise',
                            'segments.path',
                            'workflow.target-ui.review-feedback-loop',
                            'length',
                            ['start' => $reviewFeedbackRiseStart, 'target' => $reviewFeedbackJoin],
                            'Vertical distance to the shared editing return.',
                        );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.review-feedback-loop.rise',
                    'anchorStart' => $reviewFeedbackRiseStart,
                    'anchorEnd' => $reviewFeedbackJoin,
                    'length' => $reviewFeedbackRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'amber',
                    'nodeEnd' => false,
                ]" />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.review-corrections.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.review-ready-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.review-ready-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.review-ready-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="cyan"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.review"
                    attach-to="workflow.target-ui.review-ready-return.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('7. Review and release for publication')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Review'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Check the key and the language values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Explicitly approve the reviewed state'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Approval does not publish anything')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('WORKBENCH DATABASE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record approved keys, languages and values'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('HISTORY: review · who and when')],
                                ['icon' => $workflowListIcon, 'text' => __('Previous and new status')],
                                ['icon' => $workflowListIcon, 'text' => __('Approval belongs to this saved state')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.publication-readiness"
                    attach-to="workflow.target-ui.review.anchorNode-end"
                    direction="top-bottom"
                    color="cyan"
                    before-length="10rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Check approval before starting publication')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication preparation'),
                                ['icon' => $workflowListIcon, 'text' => __('Select approved keys and languages')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare current data with approved state'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Changed assignment or value: review again'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Start publication as a separate action'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('READY FOR PUBLICATION'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Continue here with unchanged approved data'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('No automatic export after approval')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Other languages are not implicitly included'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep previous approvals in the history'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.publication-preview"
                    attach-to="workflow.target-ui.publication-readiness.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Preview and confirm the publication changes')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication preview'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show keys, languages and target files'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show exact source occurrences and diffs'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Confirm the proposed changes explicitly'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Changed plan: preview and confirm again'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('CONFIRMED PUBLICATION SCOPE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Recheck approval and current source context'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Preserve entries outside this selection'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Create run ID; record actor, time and scope'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('No writes before explicit confirmation'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.publish"
                    attach-to="workflow.target-ui.publication-preview.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="5rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => [__('8. Publish the language files')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Read the released values from the database'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('LANGUAGE FILES'),
                                ['icon' => $workflowListIcon, 'text' => __('Write the values to lang/* files')],
                                ['icon' => $workflowListIcon, 'text' => __('PUBLICATION LOG: who and when')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keys, languages, files and write result'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.language-file-check"
                    attach-to="workflow.target-ui.publish.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="4rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Verify the written language entries')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication progress'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Read back the selected language values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Verify file validity and expected entries'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Continue only after successful verification'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Failure: stop dependent source replacement'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('LANGUAGE WRITE RESULT'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record files, keys and language outcomes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Language files written is not yet complete'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Keep evidence of any partial writes')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Verify source-language fallback before replacement'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.language-write-failure"
                    attach-to="workflow.target-ui.language-file-check.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="green"
                    before-length="5rem"
                    after-length="3rem"
                    stem-length="5rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Did language-file writing or verification fail?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · publication is incomplete'),
                            ['icon' => $workflowListIcon, 'text' => __('Stop dependent source replacement')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep errors and actual write outcomes')],
                            ['icon' => $workflowListIcon, 'text' => __('Do not assume an automatic rollback')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'red',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · selected language entries verified'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Continue with confirmed source occurrences'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'green',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.publication-recovery"
                    attach-to="workflow.target-ui.language-write-failure.true.anchorNode-end"
                    direction="top-bottom"
                    color="red"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Inspect the failed run before planning recovery')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication diagnostics'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show written, failed and pending files'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inspect actual state and correct the cause'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Prepare and confirm a fresh recovery preview'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Changed values require review again')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('RUN HISTORY + ACTUAL FILE STATE'),
                                ['icon' => $workflowListIcon, 'text' => __('Keep failed run ID and error details')],
                                ['icon' => $workflowListIcon, 'text' => __('Record uncertain outcomes explicitly')],
                                ['icon' => $workflowListIcon, 'text' => __('Link recovery to the original run')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Do not automatically repeat successful writes'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Route below the summary labels, rise outside their left edge, then re-enter before publication readiness. --}}
                @php
                    $publicationRecoveryLoopStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.publication-recovery.anchorNode-end',
                    );
                    $publicationRecoveryLoopClearance = '4rem';
                    $publicationRecoveryLoopBottom = [
                        'x' => $publicationRecoveryLoopStart['x'],
                        'y' =>
                            'calc(' .
                            $publicationRecoveryLoopStart['y'] .
                            ' - ' .
                            $publicationRecoveryLoopClearance .
                            ')',
                    ];
                    $publicationRecoveryLoopTarget = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.review.anchorNode-end',
                    );
                    $publicationRecoveryLoopTarget['y'] = 'calc(' . $publicationRecoveryLoopTarget['y'] . ' - 8rem)';
                    $publicationRecoveryLoopRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.publication-recovery-loop.clearance',
                    'anchorStart' => $publicationRecoveryLoopStart,
                    'anchorEnd' => $publicationRecoveryLoopBottom,
                    'length' => $publicationRecoveryLoopClearance,
                    'direction' => 'top-bottom',
                    'color' => 'red',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.publication-recovery-loop.turn"
                    :anchor-start="$publicationRecoveryLoopBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="left"
                    bridge-length="26rem"
                    :arc-radius="$publicationRecoveryLoopRadius"
                    color="red"
                    :node-end-dot="true"
                    :joint-arrow-end="false"
                />
                @php
                    $publicationRecoveryLoopRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.publication-recovery-loop.turn.anchorNode-end',
                    );
                    $publicationRecoveryLoopRiseEnd = [
                        'x' => $publicationRecoveryLoopRiseStart['x'],
                        'y' => $publicationRecoveryLoopTarget['y'],
                    ];
                    $publicationRecoveryLoopRiseLength =
                        'calc(' .
                        $publicationRecoveryLoopRiseEnd['y'] .
                        ' - (' .
                        $publicationRecoveryLoopRiseStart['y'] .
                        '))';
                    $publicationRecoveryLoopBridgeLength =
                        'calc(' .
                        $publicationRecoveryLoopTarget['x'] .
                        ' - (' .
                        $publicationRecoveryLoopRiseEnd['x'] .
                        ') - 2 * ' .
                        $publicationRecoveryLoopRadius .
                        ')';
                    $publicationRecoveryLoopLengths = $__env->getConsumableComponentData('twGraphCalculatedLengths');
                    $publicationRecoveryLoopLengths?->record(
                        'workflow.target-ui.publication-recovery-loop.rise',
                        'segments.path',
                        'workflow.target-ui.publication-recovery-loop',
                        'length',
                        ['start' => $publicationRecoveryLoopRiseStart, 'target' => $publicationRecoveryLoopRiseEnd],
                        'Vertical distance to the publication-readiness re-entry.',
                    );
                    $publicationRecoveryLoopLengths?->record(
                        'workflow.target-ui.publication-recovery-loop.reentry.bridge',
                        'parts.sideways',
                        'workflow.target-ui.publication-recovery-loop',
                        'bridgeLength',
                        [
                            'start' => $publicationRecoveryLoopRiseEnd,
                            'target' => $publicationRecoveryLoopTarget,
                            'arcRadius' => $publicationRecoveryLoopRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.publication-recovery-loop.rise',
                    'anchorStart' => $publicationRecoveryLoopRiseStart,
                    'anchorEnd' => $publicationRecoveryLoopRiseEnd,
                    'length' => $publicationRecoveryLoopRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'red',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.publication-recovery-loop.reentry"
                    :anchor-start="$publicationRecoveryLoopRiseEnd"
                    direction="bottom-top"
                    exit-direction="top-bottom"
                    side="right"
                    :bridge-length="$publicationRecoveryLoopBridgeLength"
                    :arc-radius="$publicationRecoveryLoopRadius"
                    :node-end-dot="true"
                    color="red"
                />

                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.language-write-failure.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.language-write-success.bridge',
                        'parts.sideways',
                        'workflow.target-ui.language-write-success',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.language-write-success"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="green"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.source"
                    attach-to="workflow.target-ui.language-write-success.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('9. Use the translation key in the code')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Replace the literal at its source location'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Only confirmed calls; preserve arguments'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Already keyed usage: no replacement needed'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Verify key, arguments and resulting file validity'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('SOURCE CODE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Translation call uses actions.save_changes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('PUBLICATION LOG: same publication run'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Changed source locations and keys')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.source-write-failure"
                    attach-to="workflow.target-ui.source.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="green"
                    before-length="5rem"
                    after-length="3rem"
                    stem-length="5rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Did source replacement or verification fail?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · publication is incomplete'),
                            ['icon' => $workflowListIcon, 'text' => __('Stop further source changes')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep per-occurrence replacement outcomes')],
                            ['icon' => $workflowListIcon, 'text' => __('Do not assume an automatic rollback')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'red',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · intended source references verified'),
                            ['icon' => $workflowListIcon, 'text' => __('Continue to the final publication result')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'green',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.source-recovery"
                    attach-to="workflow.target-ui.source-write-failure.true.anchorNode-end"
                    direction="top-bottom"
                    color="red"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Inspect partial source replacement before recovery')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication diagnostics'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show replaced, unchanged and failed occurrences'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare current source with the confirmed plan'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Prepare and confirm a fresh recovery preview'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Changed values require review again')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('SOURCE OUTCOMES + PUBLICATION HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Language files may already be written'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Verified replacements retain their finding link'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record errors and uncertain outcomes per occurrence'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('No automatic rollback of source or language files'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Join the existing outer recovery return at its marked node. --}}
                @php
                    $sourceRecoveryStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.source-recovery.anchorNode-end',
                    );
                    $sourceRecoveryClearance = '4rem';
                    $sourceRecoveryBottom = [
                        'x' => $sourceRecoveryStart['x'],
                        'y' => 'calc(' . $sourceRecoveryStart['y'] . ' - ' . $sourceRecoveryClearance . ')',
                    ];
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.source-recovery-loop.clearance',
                    'anchorStart' => $sourceRecoveryStart,
                    'anchorEnd' => $sourceRecoveryBottom,
                    'length' => $sourceRecoveryClearance,
                    'direction' => 'top-bottom',
                    'color' => 'red',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.source-recovery-loop.turn"
                    :anchor-start="$sourceRecoveryBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="left"
                    bridge-length="26rem"
                    :arc-radius="$publicationRecoveryLoopRadius"
                    color="red"
                    :node-end-dot="false"
                    :joint-arrow-end="true"
                />
                @php
                    $sourceRecoveryRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.source-recovery-loop.turn.anchorNode-end',
                    );
                    $sourceRecoveryJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.publication-recovery-loop.turn.anchorNode-end',
                    );
                    $sourceRecoveryRiseLength =
                        'calc(' . $sourceRecoveryJoin['y'] . ' - (' . $sourceRecoveryRiseStart['y'] . '))';
                    $__env
                        ->getConsumableComponentData('twGraphCalculatedLengths')
                        ?->record(
                            'workflow.target-ui.source-recovery-loop.rise',
                            'segments.path',
                            'workflow.target-ui.source-recovery-loop',
                            'length',
                            ['start' => $sourceRecoveryRiseStart, 'target' => $sourceRecoveryJoin],
                            'Vertical distance to the shared recovery return.',
                        );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.source-recovery-loop.rise',
                    'anchorStart' => $sourceRecoveryRiseStart,
                    'anchorEnd' => $sourceRecoveryJoin,
                    'length' => $sourceRecoveryRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'red',
                    'nodeEnd' => false,
                ]" />

                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.source-write-failure.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.source-write-success.bridge',
                        'parts.sideways',
                        'workflow.target-ui.source-write-success',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.source-write-success"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="green"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.publication-result"
                    attach-to="workflow.target-ui.source-write-success.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Verify and show the publication result')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Publication result'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Check source references and language values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show affected files and per-step outcomes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Mark complete only after verification'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Failures or partial writes stay visible'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('WORKBENCH DATABASE + PUBLICATION LOG'),
                                ['icon' => $workflowListIcon, 'text' => __('Link the published state to this run')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep original raw_data and source history'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Next scan recognizes the published usage'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('No automatic rollback is assumed')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.language-fallback"
                    attach-to="workflow.target-ui.publication-result.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Resolve the displayed language value')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('CONFIGURED FALLBACK CASCADE'),
                                ['icon' => $workflowListIcon, 'text' => __('Requested locale: de-AT')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Own value missing → German parent de / de-DE'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Parent value missing → source language en'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Use the first available published value'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('DISPLAYED VALUE AND ITS ORIGIN'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show which language supplies the value'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Fallback is not an own translation')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Before key publication: show source literal'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('After key publication: source-language fallback'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.display"
                    attach-to="workflow.target-ui.language-fallback.anchorNode-end"
                    direction="top-bottom"
                    color="green"
                    before-length="5rem"
                    after-length="1.6rem"
                    :step-label="[
                        'text' => [__('10. View the translated application')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Application · Translated page'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Open the page in the selected language'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('VISIBLE RESULT'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Laravel reads the key from the language files'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.published-edit"
                    attach-to="workflow.target-ui.display.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="4rem"
                    after-length="3rem"
                    stem-length="4rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Revise an already published translation?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · revise the existing translation'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Keep the confirmed key and original raw_data'),
                            ],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Keep published values and fallback unchanged'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · no text revision requested'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Check optional assignment and retirement changes'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'color' => 'cyan',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.published-edit-task"
                    attach-to="workflow.target-ui.published-edit.true.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Open the existing key for a new revision')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Published translation'),
                                ['icon' => $workflowListIcon, 'text' => __('Identify affected key and languages')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare current published and saved values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Inspect all usages when a key is shared'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Validate, save and review the revised values'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('TRANSLATION REVISION HISTORY'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep the previous publication traceable'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record author, time and actual value changes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Publish language values; source key stays unchanged'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Other languages retain their own status'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                {{-- Reuse the existing outer editing return; do not overlap a second full-height stem. --}}
                @php
                    $publishedEditStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.published-edit-task.anchorNode-end',
                    );
                    $publishedEditClearance = '4rem';
                    $publishedEditBottom = [
                        'x' => $publishedEditStart['x'],
                        'y' => 'calc(' . $publishedEditStart['y'] . ' - ' . $publishedEditClearance . ')',
                    ];
                    $publishedEditJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.review-feedback-loop.turn.anchorNode-end',
                    );
                    $publishedEditBridge =
                        'calc(' .
                        $publishedEditBottom['x'] .
                        ' - (' .
                        $publishedEditJoin['x'] .
                        ') - 2 * ' .
                        $concurrentEditLoopRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.published-edit-task-loop.turn.bridge',
                        'parts.sideways',
                        'workflow.target-ui.published-edit-task-loop',
                        'bridgeLength',
                        [
                            'start' => $publishedEditBottom,
                            'target' => $publishedEditJoin,
                            'arcRadius' => $concurrentEditLoopRadius,
                        ],
                        'Horizontal clearance to the shared editing return minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.published-edit-task-loop.clearance',
                    'anchorStart' => $publishedEditStart,
                    'anchorEnd' => $publishedEditBottom,
                    'length' => $publishedEditClearance,
                    'direction' => 'top-bottom',
                    'color' => 'amber',
                    'nodeEnd' => true,
                    'nodeEndDot' => false,
                    'jointArrowEnd' => true,
                ]" />
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.published-edit-task-loop.turn"
                    :anchor-start="$publishedEditBottom"
                    direction="top-bottom"
                    exit-direction="bottom-top"
                    side="left"
                    :bridge-length="$publishedEditBridge"
                    :arc-radius="$concurrentEditLoopRadius"
                    :node-end-dot="false"
                    :joint-arrow-end="true"
                    color="amber"
                />
                @php
                    $publishedEditRiseStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.published-edit-task-loop.turn.anchorNode-end',
                    );
                    $publishedEditRiseLength =
                        'calc(' . $publishedEditJoin['y'] . ' - (' . $publishedEditRiseStart['y'] . '))';
                    $__env
                        ->getConsumableComponentData('twGraphCalculatedLengths')
                        ?->record(
                            'workflow.target-ui.published-edit-task-loop.rise',
                            'segments.path',
                            'workflow.target-ui.published-edit-task-loop',
                            'length',
                            ['start' => $publishedEditRiseStart, 'target' => $publishedEditJoin],
                            'Vertical distance to the shared editing return.',
                        );
                @endphp
                <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                    'id' => 'workflow.target-ui.published-edit-task-loop.rise',
                    'anchorStart' => $publishedEditRiseStart,
                    'anchorEnd' => $publishedEditJoin,
                    'length' => $publishedEditRiseLength,
                    'direction' => 'bottom-top',
                    'color' => 'amber',
                    'nodeEnd' => false,
                ]" />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.published-edit.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.published-edit-complete-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.published-edit-complete-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.published-edit-complete-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="cyan"
                />
                {{-- Optional maintenance: prepare explicit changes before any effective assignment or file write. --}}
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.key-migration"
                    attach-to="workflow.target-ui.published-edit-complete-return.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="5rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Change a confirmed or published key?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · prepare a key migration'),
                            ['icon' => $workflowListIcon, 'text' => __('Select occurrences and old → new key')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Check syntax, usages and target conflicts'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('Keep current assignment until execution')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('No automatic overwrite or permanent alias'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                    ]"
                    :if-end="[
                        'text' => [__('NO · retain the current key')],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'cyan',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.key-migration.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.key-migration-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.key-migration-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.key-migration-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="cyan"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.shared-maintenance"
                    attach-to="workflow.target-ui.key-migration-return.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="4rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Change a shared assignment?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · prepare merge or split'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Merge: explicitly select compatible findings'),
                            ],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Split: select occurrences for a separate key'),
                            ],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Resolve language-value conflicts explicitly'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('Preserve every finding and its raw_data')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · retain existing assignments'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('sharedCandidate alone changes no assignment'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'cyan',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.shared-maintenance.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.shared-maintenance-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.shared-maintenance-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.shared-maintenance-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="cyan"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.archive-unused"
                    attach-to="workflow.target-ui.shared-maintenance-return.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="3rem"
                    after-length="3rem"
                    stem-length="4rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Archive an unused translation task?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · propose archive after usage verification'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Complete scan and known references required'),
                            ],
                            ['icon' => $workflowListIcon, 'text' => __('Remaining usages block retirement')],
                            ['icon' => $workflowListIcon, 'text' => __('Keep raw_data, values and history')],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('File cleanup is a separate confirmed change'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · keep the task and published values'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Missing or excluded scan scope proves nothing'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'cyan',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.archive-unused.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.archive-unused-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.archive-unused-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.archive-unused-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="cyan"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.maintenance-plan"
                    attach-to="workflow.target-ui.archive-unused-return.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Inspect the proposed maintenance plan')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('Workbench · Assignment and retirement plan'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show only explicitly selected changes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare current usages and saved values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show per-language conflicts and source references'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('No proposals: finish without writes')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('PREVIEW · DATABASE + SOURCE + LANGUAGE FILES'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Review merge, split, rekey and archive effects'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Include required source-language fallback'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep other usages and languages untouched'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Record reason, actor and selected scope'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                    id="workflow.target-ui.maintenance-approved"
                    attach-to="workflow.target-ui.maintenance-plan.anchorNode-end"
                    direction="top-bottom"
                    side="left"
                    color="cyan"
                    before-length="5rem"
                    after-length="3rem"
                    stem-length="5rem"
                    bridge-length="4rem"
                    :condition-label="[
                        'text' => [__('Non-empty plan checked and explicitly approved?')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :if-start="[
                        'text' => [
                            __('YES · execute the approved current plan'),
                            ['icon' => $workflowListIcon, 'text' => __('Recheck state before writing')],
                            ['icon' => $workflowListIcon, 'text' => __('Changed baseline requires a new review')],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'amber',
                        'return' => false,
                        'returnOffset' => '36rem',
                    ]"
                    :if-end="[
                        'text' => [
                            __('NO · finish without applying changes'),
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('Keep unresolved proposals for clarification'),
                            ],
                            [
                                'icon' => $workflowListIcon,
                                'text' => __('No approval: no assignment or file changes'),
                            ],
                        ],
                        'width' => 'long',
                        'align' => 'center',
                        'maxLines' => 5,
                        'color' => 'cyan',
                        'return' => false,
                        'returnOffset' => '0rem',
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.maintenance-apply"
                    attach-to="workflow.target-ui.maintenance-approved.true.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="3rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Apply and verify the confirmed maintenance changes')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('DEPENDENT STEPS · STOP ON FAILURE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Write and verify required target language values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Then update only selected source references'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Record verified assignment changes')],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Retire old exports only after usage verification'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('HISTORY · ACTUAL OUTCOMES'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Archive only when no active usage remains'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep identities, raw_data and previous values'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Log old/new links, actor, time and run ID'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Partial writes stay incomplete; no assumed rollback'),
                                ],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="workflow.target-ui.maintenance-outcome"
                    attach-to="workflow.target-ui.maintenance-apply.anchorNode-end"
                    direction="top-bottom"
                    color="amber"
                    before-length="5rem"
                    after-length="3.6rem"
                    :step-label="[
                        'text' => [__('Show the maintenance outcome and next action')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'left' => [
                            'text' => [
                                __('SUCCESS · VERIFIED STATE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Show affected tasks, keys, languages and files'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Keep archive separate from permanent deletion'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Next scan follows verified key replacements'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Allow later review of archived tasks')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                        'right' => [
                            'text' => [
                                __('FAILURE · PRESERVE THE ACTUAL STATE'),
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Stop dependent steps and show partial outcomes'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Compare current files, values and usages again'),
                                ],
                                [
                                    'icon' => $workflowListIcon,
                                    'text' => __('Prepare and explicitly confirm a fresh plan'),
                                ],
                                ['icon' => $workflowListIcon, 'text' => __('Do not blindly replay the failed run')],
                            ],
                            'width' => 'long',
                            'align' => 'left',
                            'maxLines' => 5,
                        ],
                    ]"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target-ui.maintenance-finished"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.maintenance-outcome.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="5rem"
                    color="amber"
                    :end-label="[
                        'text' => [__('Outcome and next action recorded')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                />
                {{-- Return to the original main axis using the actual decision output, not a fixed offset. --}}
                @php
                    $workflowUiReturnStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.maintenance-approved.false.anchorNode-end',
                    );
                    $workflowUiMainAxis = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.start.anchorNode-end',
                    );
                    $workflowUiReturnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
                        null,
                        'arc_radius',
                        '2.75rem',
                    );
                    $workflowUiReturnBridge =
                        'calc(' .
                        $workflowUiMainAxis['x'] .
                        ' - (' .
                        $workflowUiReturnStart['x'] .
                        ') - 2 * ' .
                        $workflowUiReturnRadius .
                        ')';
                    $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
                        'workflow.target-ui.maintenance-deferred-return.bridge',
                        'parts.sideways',
                        'workflow.target-ui.maintenance-deferred-return',
                        'bridgeLength',
                        [
                            'start' => $workflowUiReturnStart,
                            'target' => $workflowUiMainAxis,
                            'arcRadius' => $workflowUiReturnRadius,
                        ],
                        'Horizontal distance to the main axis minus both arc radii.',
                    );
                @endphp
                <x-translation-workbench::ui.tw-graph.parts.sideways
                    id="workflow.target-ui.maintenance-deferred-return"
                    :anchor-start="$workflowUiReturnStart"
                    direction="top-bottom"
                    side="right"
                    :arc-radius="$workflowUiReturnRadius"
                    :bridge-length="$workflowUiReturnBridge"
                    color="cyan"
                />
                <x-translation-workbench::ui.tw-graph.parts.end
                    id="workflow.target-ui.complete"
                    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                        'translation-workflow-target-ui',
                        'workflow.target-ui.maintenance-deferred-return.anchorNode-end',
                    )"
                    direction="top-bottom"
                    length="3rem"
                    color="green"
                    :end-label="[
                        'text' => [__('The translated text is visible')],
                        'width' => 'long',
                        'align' => 'center',
                    ]"
                />
            </x-translation-workbench::ui.tw-graph>
        </div>
    </x-translation-workbench::ui.tw-graph.preview-tools>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/target-ui.blade.php"
        :segments="3"
    />
</flux:callout>
