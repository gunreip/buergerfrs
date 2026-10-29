<flux:callout
    color="sky"
    icon="beaker"
>
    <flux:callout.heading>
        {{ __('Recorded isolated workflow rehearsal') }}
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('This report describes a synthetic test case, independently of the selected key. It does not approve or publish production translations and does not verify the current source files.') }}
    </flux:callout.text>

    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading>
                <flux:callout.heading>
                    {{ __('Workflow verification details') }}
                </flux:callout.heading>
            </flux:accordion.heading>
            <flux:accordion.content>
                @if ($baselineRehearsal)
                    <div class="space-y-3">
                        <flux:callout.text>
                            {{ __('Synthetic target key') }}:
                            <code>{{ $baselineRehearsal['synthetic_target'] }}</code>
                        </flux:callout.text>
                        <flux:callout.text>
                            {{ __('Successful assertions') }}: {{ $baselineRehearsal['assertions'] }}
                        </flux:callout.text>
                        <flux:callout.text>
                            {{ __('Recorded at') }}:
                            {{ $baselineRehearsal['recorded_at'] ?? __('Not recorded') }}
                        </flux:callout.text>
                        @if (($baselineRehearsal['combined_preflight_verified'] ?? false) === true && isset($baselineRehearsal['combined_preflight_fingerprint']))
                            <flux:callout.text>
                                {{ __('Recorded joint preflight fingerprint') }}:
                                <code class="break-all">{{ $baselineRehearsal['combined_preflight_fingerprint'] }}</code>
                            </flux:callout.text>
                        @endif
                        @if (($baselinePreview['snapshot']['input_dump_sha256'] ?? null) !== $baselineRehearsal['input_dump_sha256'])
                            <flux:callout color="amber">
                                <flux:callout.text>
                                    {{ __('This report cannot be matched to the displayed baseline snapshot.') }}
                                </flux:callout.text>
                            </flux:callout>
                        @endif
                        <flux:table container:class="max-h-80 overflow-x-auto rounded rounded-t-lg">
                            <flux:table.columns class="bg-white dark:bg-zinc-800">
                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Step') }}
                                    </flux:callout.heading>
                                </flux:table.column>

                                <flux:table.column>
                                    <flux:callout.heading>
                                        {{ __('Recorded result') }}
                                    </flux:callout.heading>
                                </flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>

                                @foreach ([
        'versioned_schema_verified' => __('Install a separate versioned generation and detect changed constraints, indexes and triggers'),
        'selected_generation_transfer_verified' => __('Transfer selected evidence with new finding identities and without old reviews or history'),
        'complete_name_inventory_verified' => __('Preserve all evidenced key names and reject conflicts outside the selected transfer'),
        'selected_generation_application_verified' => __('Apply and replay the confirmed correction in the newly populated generation'),
        'selected_generation_publication_verified' => __('Edit, review, publish and read back a revision in the newly populated generation'),
        'fresh_generation_capture_verified' => __('Import the full verified static capture into a fresh generation; preserve pending and excluded evidence'),
        'fresh_generation_arguments_verified' => __('Preserve source roles and unresolved arguments without creating translation decisions'),
        'fresh_generation_published_values_verified' => __('Preserve published language values and verify exact readback and explicit fallback without creating drafts'),
        'explicit_database_binding_verified' => __('Verify the explicit database, schema and baseline; reject missing or shadowed tables'),
        'database_snapshot_comparison_verified' => __('Compare snapshot occurrences and published evidence with the isolated database'),
        'combined_preflight_verified' => __('Bind source and database checks to one proposal'),
        'confirmed_preflight_application_verified' => __('Recheck the confirmed proposal before writing and reject changed files'),
        'correction_applied' => __('Apply confirmed key correction'),
        'explicit_draft_import_verified' => __('Import exact locale value as draft'),
        'explicit_review_cycle_verified' => __('Approve and revoke the draft'),
        'imported_draft_file_export_verified' => __('Export to a temporary language file'),
        'publication_readback_verified' => __('Read back and verify the published value'),
        'source_change_preview_verified' => __('Preview source changes without writing source files'),
        'source_reconciliation_verified' => __('Reconcile rewritten findings and preserve their key links'),
        'position_movements_verified' => __('Reconcile position shifts of unchanged calls'),
    ] as $field => $label)
                                    @php($verified = $field === 'correction_applied' ? ($baselineRehearsal[$field] ?? null) === 'isolated_database_only' : ($baselineRehearsal[$field] ?? null) === true)

                                    <flux:table.row>
                                        <flux:table.cell>
                                            <flux:callout.text>
                                                {{ $label }}
                                            </flux:callout.text>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge :color="$verified ? 'green' : 'amber'">
                                                {{ $verified ? __('Verified in rehearsal') : __('Not verified in this report') }}
                                            </flux:badge>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach

                            </flux:table.rows>
                        </flux:table>
                        @include('translation-workbench::livewire.workflow.baseline-transfer-balance')
                        @include('translation-workbench::livewire.workflow.baseline-open-values')
                        <flux:text>
                            <flux:callout.text>
                                {{ __('Production write actions are not connected to this preview.') }}
                            </flux:callout.text>
                        </flux:text>
                    </div>
                @else
                    <flux:text>
                        <flux:callout.text>
                            {{ __('No valid isolated workflow report is available. Opening this section does not run any commands.') }}
                        </flux:callout.text>
                    </flux:text>
                @endif
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:callout>
