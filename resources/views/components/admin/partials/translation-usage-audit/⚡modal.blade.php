{{-- resources/views/components/admin/partials/translation-usage-audit/⚡modal.blade.php --}}

<flux:modal
    class="scrollbar-gutter-stable w-full max-w-7xl"
    wire:model="usageAuditModalOpen"
>
    @if ($selectedItem)
        <div
            class="flex max-h-[calc(100vh-8rem)] flex-col gap-6"
            x-data="{
                showSourceValue: false,
                showUiKeyInformation: false,
            }"
        >
            <div class="flex shrink-0 items-start justify-between gap-4">
                <x-ui.headers.card
                    :title="__('Usage audit review')"
                    :description="__('Detailed review of the selected source-language literal.')"
                />

                <flux:badge
                    class="mr-8"
                    variant="subtle"
                    color="{{ $selectedAuditType === 'duplicate' ? 'amber' : 'sky' }}"
                >
                    {{ $selectedAuditType }}
                </flux:badge>
            </div>

            <div class="scrollbar-gutter-auto -mr-4 flex flex-col gap-6 overflow-y-auto pr-4">
                <div class="flex-1 space-y-4">
                    {{-- Source information --}}
                    @include('components.admin.partials.translation-usage-audit.modal.⚡source')

                    {{-- KPIS-Infomations --}}
                    @include('components.admin.partials.translation-usage-audit.modal.⚡kpis')
                </div>

                {{-- UI key information --}}
                @include('components.admin.partials.translation-usage-audit.modal.⚡ui-key-information')

                {{-- Translation keys --}}
                @include('components.admin.partials.translation-usage-audit.modal.⚡translation-keys')

                {{-- Translation values --}}
                @include('components.admin.partials.translation-usage-audit.modal.⚡translation-values')

                {{-- Usage Locations --}}
                @include('components.admin.partials.translation-usage-audit.modal.⚡usage-locations')
            </div>

            <div class="shrink-0 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <div class="flex justify-end">
                    <x-ui.button.close wire:click="closeUsageAuditModal" />
                </div>
            </div>
        </div>
    @endif
</flux:modal>
