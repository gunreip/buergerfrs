{{-- resources/views/components/admin/partials/user-list/modal.blade.php --}}

<flux:modal
    class="md:w-[32rem]"
    wire:model.self="showEditRolesModal"
>
    <div class="space-y-6">
        <div class="-mb-1 flex items-start justify-between gap-4">
            <flux:field>
                <x-ui.headers.card
                    :title="__('admin.user_list.modal.edit_roles')"
                    :description="__('admin.user_list.modal.change_assigned_roles_for_the_selected_user')"
                />
            </flux:field>

            {{-- <flux:heading size="xl">
                {{ __('admin.user_list.modal.edit_roles') }}
            </flux:heading> --}}
        </div>

        <flux:separator class="mb-6" />

        <div class="-mb-1 grid gap-4">

            <flux:text class="text-sm">
                {{ __('admin.user_list.modal.change_assigned_roles_for') }}:
                <p class="ml-6">
                    <span class="font-semibold text-zinc-100">
                        {{ $editingUserName }}
                    </span>
                    <span class="text-zinc-400">
                        ({{ $editingUserEmail }})
                    </span>.
                </p>
            </flux:text>

            <flux:text class="text-sm">
                {{ __('Current role') }}:
                <p class="ml-6">
                    <span class="font-semibold text-zinc-100">
                        {{ $editingCurrentRoleName !== '' ? $editingCurrentRoleName : __('admin.user_list.filter.without_role') }}
                    </span>
                </p>
            </flux:text>
        </div>

        <flux:separator />

        <div>
            <flux:fieldset>
                <flux:legend class="mb-2 text-base font-semibold uppercase tracking-wide text-zinc-500">
                    {{ __('ui.labels.roles') }}
                </flux:legend>

                <flux:radio.group wire:model.live="editingRoleName">
                    <div class="space-y-5">
                        @foreach ($roleGroups as $category => $groupedRoles)
                            <div>
                                <flux:text class="mb-2 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    {{ __(Str::headline($category)) }}
                                </flux:text>

                                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                                    @foreach ($groupedRoles as $role)
                                        <flux:radio
                                            value="{{ $role->name }}"
                                            label="{{ $role->name }}"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:radio.group>
            </flux:fieldset>
        </div>

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel wire:click="closeEditRolesModal" />
            <x-ui.button.save
                label="{{ __('admin.user_list.modal.change_role') }}"
                wire:click="saveEditRoles"
                :disabled="$editingRoleName === ''"
            />
        </div>
    </div>
</flux:modal>
