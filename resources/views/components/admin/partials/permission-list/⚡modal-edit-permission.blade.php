{{-- resources/views/components/admin/partials/permission-list/⚡modal-edit-permission.blade.php --}}

<flux:modal
    class="md:w-[48rem]"
    wire:model.self="showEditPermissionModal"
>
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <flux:field>
                <x-ui.headers.card
                    :title="__('ui.title.filter')"
                    :description="__('admin.permissions.modals.edit.description')"
                />

            </flux:field>
        </div>

        <flux:separator text="{{ __('admin.permissions.modals.edit.metadata_section') }}" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <flux:text class="text-zinc-400">
                    {{ __('ui.labels.name') }}
                </flux:text>

                <flux:heading size="md">
                    {{ $editingPermissionName }}
                </flux:heading>
            </div>

            <div>
                <flux:text class="text-zinc-400">
                    {{ __('ui.labels.guard') }}
                </flux:text>

                <flux:heading size="md">
                    {{ $editingPermissionGuard }}
                </flux:heading>
            </div>

            <div>
                <flux:text class="text-zinc-400">
                    {{ __('ui.assign.assigned.assigned-roles') }}
                </flux:text>

                <flux:heading size="md">
                    {{ $editingRolesCount }}
                </flux:heading>
            </div>

            <div>
                <flux:text class="text-zinc-400">
                    {{ __('admin.permissions.modals.edit.editable_scope') }}

                </flux:text>

                <flux:heading size="md">
                    {{ __('admin.permissions.modals.edit.metadata_only') }}
                </flux:heading>
            </div>
        </div>

        <flux:separator
            class="mt-4"
            text="{{ __('admin.permissions.modals.edit.details_section') }}"
        />

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                type="text"
                label="{{ __('ui.labels.category') }}"
                wire:model.live="editingCategory"
                placeholder="{{ __('admin.permissions.modals.edit.category_placeholder') }}"
            />

            <flux:input
                type="number"
                label="{{ __('ui.sort.sort.sort-order') }}"
                wire:model.live="editingSortOrder"
                min="0"
                step="1"
            />
        </div>

        <flux:textarea
            label="{{ __('ui.labels.description') }}"
            wire:model.live="editingDescription"
            rows="4"
            placeholder="{{ __('admin.permissions.modals.edit.description_placeholder') }}"
        />

        <flux:checkbox
            wire:model.live="editingIsSystem"
            label="{{ __('admin.permissions.labels.system_permission') }}"
        />

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel wire:click="closeEditPermissionModal" />

            <x-ui.button.save
                icon="check"
                label="{{ __('ui.button.save.save') }}"
                wire:click="savePermissionMetadata"
                wire:loading.attr="disabled"
                :disabled="$editingPermissionId === null || !$this->hasPermissionMetadataChanges()"
            />
        </div>
    </div>
</flux:modal>
