{{-- resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php --}}

{{-- Modal Edit role --}}
<flux:modal
    class="md:w-[42rem]"
    wire:model.self="showEditRoleModal"
>
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">
                {{ __('Edit Role') }}
            </flux:heading>

            <flux:separator class="mb-6 mt-3" />

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Name') }}
                    </flux:text>
                    <div class="mt-1 font-semibold text-zinc-100">
                        {{ $editingRoleName }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Guard') }}
                    </flux:text>
                    <div class="mt-1 font-semibold text-zinc-100">
                        {{ $editingGuardName }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('System role') }}
                    </flux:text>
                    <div class="mt-1 font-semibold text-zinc-100">
                        {{ $editingIsSystem ? __('Yes') : __('No') }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{ __('Assigned users') }}
                    </flux:text>
                    <div class="mt-1 font-semibold tabular-nums text-zinc-100">
                        {{ $editingUsersCount }}
                    </div>
                </div>
            </div>
        </div>

        <flux:separator />

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                type="text"
                label="{{ __('Category') }}"
                wire:model.live="editingCategory"
            />

            <flux:input
                type="number"
                min="0"
                max="65535"
                label="{{ __('Sort order') }}"
                wire:model.live="editingSortOrder"
            />

            <div class="col-span-2">
                <flux:textarea
                    label="{{ __('Description') }}"
                    wire:model.live="editingDescription"
                    rows="3"
                />
            </div>

            <div class="col-span-2">
                <flux:checkbox
                    label="{{ __('Assignable through UI') }}"
                    wire:model.live="editingIsAssignable"
                />
            </div>
        </div>

        <flux:separator text="{{ __('Badge') }}" />

        <div class="grid grid-cols-3 gap-4">
            <flux:select
                label="{{ __('Color') }}"
                wire:model.live="editingBadgeColor"
            >
                @foreach ($roleBadgeColorOptions as $color => $label)
                    <flux:select.option value="{{ $color }}">
                        {{ __($label) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                label="{{ __('Variant') }}"
                wire:model.live="editingBadgeVariant"
            >
                @foreach ($roleBadgeVariantOptions as $variant => $label)
                    <flux:select.option value="{{ $variant }}">
                        {{ __($label) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                label="{{ __('Icon') }}"
                wire:model.live="editingBadgeIcon"
            >
                @foreach ($roleBadgeIconOptions as $icon => $option)
                    <flux:select.option value="{{ $icon }}">
                        {{ $option['label'] ?? Str::headline($icon) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div>
            <flux:text class="mb-2 text-sm text-zinc-400">
                {{ __('Preview') }}
            </flux:text>

            <x-ui.role-badge
                :label="$editingRoleName"
                :badge="[
                    'color' => $editingBadgeColor,
                    'variant' => $editingBadgeVariant,
                    'icon' => $editingBadgeIcon,
                ]"
            />
        </div>

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel wire:click="closeEditRoleModal" />

            <x-ui.button.save
                :label="__('Save Changes')"
                wire:click="saveRole"
            />
        </div>
    </div>
</flux:modal>
