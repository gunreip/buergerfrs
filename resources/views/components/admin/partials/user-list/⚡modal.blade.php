{{-- resources/views/components/admin/partials/user-list/modal.blade.php --}}

<flux:modal
    class="md:w-[32rem]"
    wire:model.self="showEditRolesModal"
>
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">
                {{ __('Edit Roles') }}
            </flux:heading>

            <flux:separator class="mb-6 mt-3" />

            <flux:text class="mt-2">
                {{ __('Change assigned roles for') }}:
                <p class="my-2 ml-6">
                    <span class="font-semibold text-zinc-100">
                        {{ $editingUserName }}
                    </span>
                    <span class="text-zinc-400">
                        ({{ $editingUserEmail }})
                    </span>.
                </p>
            </flux:text>

            <flux:text class="mt-1 text-sm">
                {{ __('Current role') }}:
                <p class="my-2 ml-6">
                    <span class="font-semibold text-zinc-100">
                        {{ $editingCurrentRoleName !== '' ? $editingCurrentRoleName : __('Without role') }}
                    </span>
                </p>
            </flux:text>
        </div>

        <flux:separator />

        <div>
            <flux:fieldset>
                <flux:legend class="mb-2 text-base font-semibold uppercase tracking-wide text-zinc-500">
                    {{ __('Roles') }}
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
            <flux:button
                type="button"
                variant="primary"
                color="red"
                wire:click="closeEditRolesModal"
            >
                {{ __('Cancel') }}
            </flux:button>

            <flux:button
                type="button"
                variant="primary"
                color="green"
                wire:click="saveEditRoles"
                :disabled="$editingRoleName === ''"
            >
                {{ __('Change Role') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
