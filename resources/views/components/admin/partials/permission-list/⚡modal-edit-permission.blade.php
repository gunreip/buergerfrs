{{-- resources/views/components/admin/partials/permission-list/⚡modal-edit-permission.blade.php --}}

<flux:modal
    class="md:w-[48rem]"
    wire:model.self="showEditPermissionModal"
>
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <flux:field>
                <x-ui.headers.card
                    :title="__('Edit Permission')"
                    :description="__('Edit metadata for this permission. Name and guard are not changed here.')"
                />

            </flux:field>
        </div>

        <flux:separator text="{{ __('Permission Metadata') }}" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <flux:text class="text-zinc-400">
                    {{ __('Name') }}
                </flux:text>

                <flux:heading size="md">
                    {{ $editingPermissionName }}
                </flux:heading>
            </div>

            <div>
                <flux:text class="text-zinc-400">
                    {{ __('Guard') }}
                </flux:text>

                <flux:heading size="md">
                    {{ $editingPermissionGuard }}
                </flux:heading>
            </div>

            <div>
                <flux:text class="text-zinc-400">
                    {{ __('Assigned roles') }}
                </flux:text>

                <flux:heading size="md">
                    {{ $editingRolesCount }}
                </flux:heading>
            </div>

            <div>
                <flux:text class="text-zinc-400">
                    {{ __('Editable scope') }}
                </flux:text>

                <flux:heading size="md">
                    {{ __('Metadata only') }}
                </flux:heading>
            </div>
        </div>

        <flux:separator
            class="mt-4"
            text="{{ __('Edit Permission Details') }}"
        />

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                type="text"
                label="{{ __('Category') }}"
                wire:model.live="editingCategory"
                placeholder="{{ __('e.g. users, settings, system') }}"
            />

            <flux:input
                type="number"
                label="{{ __('Sort order') }}"
                wire:model.live="editingSortOrder"
                min="0"
                step="1"
            />
        </div>

        <flux:textarea
            label="{{ __('Description') }}"
            wire:model.live="editingDescription"
            rows="4"
            placeholder="{{ __('Describe what this permission allows.') }}"
        />

        <flux:checkbox
            wire:model.live="editingIsSystem"
            label="{{ __('System permission') }}"
        />

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel wire:click="closeEditPermissionModal" />

            <x-ui.button.save
                icon="check"
                label="{{ __('Save') }}"
                wire:click="savePermissionMetadata"
                wire:loading.attr="disabled"
                :disabled="$editingPermissionId === null || !$this->hasPermissionMetadataChanges()"
            />
        </div>
    </div>
</flux:modal>
