{{-- resources/views/components/admin/partials/html-view-audit/⚡modal.blade.php --}}

<flux:modal
    class="md:w-[56rem]"
    wire:model.self="showFindingDetailsModal"
>
    <div class="max-h-[82vh] space-y-6 overflow-y-auto pr-2">
        <div class="flex items-start justify-between gap-4">
            <flux:field>
                <x-ui.headers.card
                    :title="__('Finding details')"
                    :description="__('Detailed information about the selected HTML/Blade structure audit finding.')"
                />
            </flux:field>
        </div>

        @if ($selectedFinding)
            @php
                $statusMeta = $tableLegend['status'][$selectedFinding->status] ?? [];
                $sectionMeta = $tableLegend['section'][$selectedFinding->section] ?? [];
                $typeMeta = $tableLegend['type'][$selectedFinding->type] ?? [];
            @endphp

            <flux:separator text="{{ __('admin.permissions.overview.title') }}" />

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('admin.user_list.table.id') }}
                    </flux:text>

                    <flux:heading size="md">
                        #{{ $selectedFinding->id }}
                    </flux:heading>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('admin.app_settings.table_icon_registry.status') }}
                    </flux:text>

                    <div class="flex items-center gap-2">
                        <flux:badge
                            :color="$statusMeta['color'] ?? 'zinc'"
                            variant="subtle"
                        >
                            <x-ui.flux-icon
                                class="size-4"
                                :name="$statusMeta['icon'] ?? 'bug'"
                            />
                        </flux:badge>

                        <flux:heading size="md">
                            {{ $statusMeta['label'] ?? $selectedFinding->status }}
                        </flux:heading>
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Section') }}
                    </flux:text>

                    <div class="flex items-center gap-2">
                        <flux:badge
                            :color="$sectionMeta['color'] ?? 'zinc'"
                            variant="subtle"
                        >
                            <x-ui.flux-icon
                                class="size-4"
                                :name="$sectionMeta['icon'] ?? 'code-xml'"
                            />
                        </flux:badge>

                        <flux:heading size="md">
                            {{ $sectionMeta['label'] ?? $selectedFinding->section }}
                        </flux:heading>
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('admin.client_list.table.type') }}
                    </flux:text>

                    <div class="flex items-center gap-2">
                        <flux:badge
                            :color="$typeMeta['color'] ?? 'zinc'"
                            variant="subtle"
                        >
                            <x-ui.flux-icon
                                class="size-4"
                                :name="$typeMeta['icon'] ?? 'tag'"
                            />
                        </flux:badge>

                        <flux:heading size="md">
                            {{ $typeMeta['label'] ?? $selectedFinding->type }}
                        </flux:heading>
                    </div>
                </div>
            </div>

            <flux:separator text="{{ __('Location') }}" />

            <div class="space-y-4">
                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('File') }}
                    </flux:text>

                    <div class="break-all font-mono text-sm">
                        {{ $selectedFinding->file }}
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('Tag') }}
                        </flux:text>

                        <div class="break-all font-mono text-sm">
                            {{ $selectedFinding->tag ?? 'n. a.' }}
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('Closing tag') }}
                        </flux:text>

                        <div class="break-all font-mono text-sm">
                            {{ $selectedFinding->closing_tag ?? 'n. a.' }}
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('Opening line') }}
                        </flux:text>

                        <div class="font-mono text-sm tabular-nums">
                            {{ $selectedFinding->opened_line ?? 'n. a.' }}
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('Closing line') }}
                        </flux:text>

                        <div class="font-mono text-sm tabular-nums">
                            {{ $selectedFinding->closing_line ?? 'n. a.' }}
                        </div>
                    </div>
                </div>
            </div>

            <flux:separator text="{{ __('Expected / Actual') }}" />

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Expected closing') }}
                    </flux:text>

                    <div class="break-all font-mono text-sm">
                        {{ $selectedFinding->expected_closing ?? 'n. a.' }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Actual closing') }}
                    </flux:text>

                    <div class="break-all font-mono text-sm">
                        {{ $selectedFinding->actual_closing ?? 'n. a.' }}
                    </div>
                </div>
            </div>

            <flux:separator text="{{ __('admin.translation_list.table.history') }}" />

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('First seen') }}
                    </flux:text>

                    <div class="font-mono text-sm">
                        {{ $selectedFinding->first_seen_at?->toDateTimeString() ?? 'n. a.' }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('admin.translation_list.table.last_seen') }}
                    </flux:text>

                    <div class="font-mono text-sm">
                        {{ $selectedFinding->last_seen_at?->toDateTimeString() ?? 'n. a.' }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Resolved at') }}
                    </flux:text>

                    <div class="font-mono text-sm">
                        {{ $selectedFinding->resolved_at?->toDateTimeString() ?? 'n. a.' }}
                    </div>
                </div>
            </div>

            @if ($selectedFinding->previousFinding)
                <flux:separator text="{{ __('Previous finding') }}" />

                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('admin.user_list.table.id') }}
                        </flux:text>

                        <div class="font-mono text-sm">
                            #{{ $selectedFinding->previousFinding->id }}
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('admin.app_settings.table_icon_registry.status') }}
                        </flux:text>

                        <div class="font-mono text-sm">
                            {{ $selectedFinding->previousFinding->status }}
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('Opening line') }}
                        </flux:text>

                        <div class="font-mono text-sm tabular-nums">
                            {{ $selectedFinding->previousFinding->opened_line ?? 'n. a.' }}
                        </div>
                    </div>

                    <div>
                        <flux:text class="text-zinc-400">
                            {{ __('Closing line') }}
                        </flux:text>

                        <div class="font-mono text-sm tabular-nums">
                            {{ $selectedFinding->previousFinding->closing_line ?? 'n. a.' }}
                        </div>
                    </div>
                </div>
            @endif

            <flux:separator text="{{ __('Technical details') }}" />

            <div class="space-y-4">
                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Fingerprint') }}
                    </flux:text>

                    <div class="break-all font-mono text-xs">
                        {{ $selectedFinding->fingerprint }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Source fingerprint') }}
                    </flux:text>

                    <div class="break-all font-mono text-xs">
                        {{ $selectedFinding->source_fingerprint }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Resolved source') }}
                    </flux:text>

                    <div class="font-mono text-sm">
                        {{ $selectedFinding->resolved_source ?? 'n. a.' }}
                    </div>
                </div>

                @if (!empty($selectedFinding->snapshot_payload))
                    <details class="group rounded-lg border border-zinc-700 bg-zinc-950/30">
                        <summary
                            class="flex cursor-pointer select-none items-center justify-between gap-3 px-3 py-2 text-sm text-zinc-300 hover:text-zinc-100"
                        >
                            <span>
                                {{ __('Snapshot payload') }}
                            </span>

                            <span class="text-xs text-zinc-500 group-open:hidden">
                                {{ __('show') }}
                            </span>

                            <span class="hidden text-xs text-zinc-500 group-open:inline">
                                {{ __('hide') }}
                            </span>
                        </summary>

                        <pre class="max-h-64 overflow-auto border-t border-zinc-700 bg-zinc-950/60 p-3 text-xs text-zinc-200">{{ json_encode($selectedFinding->snapshot_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
                @endif
            </div>
        @else
            <div class="rounded-lg border border-zinc-700 p-4 text-sm text-zinc-400">
                {{ __('No finding selected.') }}
            </div>
        @endif

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel
                :label="__('ui.actions.close')"
                wire:click="closeFindingDetailsModal"
            />
        </div>
    </div>
</flux:modal>
