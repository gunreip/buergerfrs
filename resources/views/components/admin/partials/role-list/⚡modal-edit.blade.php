{{-- resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php --}}

{{-- Modal Edit role --}}
<flux:modal
    class="md:w-[42rem]"
    wire:model.self="showEditRoleModal"
>
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">
                {{-- i18n-native: __('admin.roles.modals.edit.title') --}}
                {{ __('admin.roles.modals.edit.title') }}
            </flux:heading>

            <flux:separator class="mb-6 mt-3" />

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <flux:text class="text-zinc-400">
                        {{-- i18n-native: __('ui.labels.name') --}}
                        {{ __('ui.labels.name') }}
                    </flux:text>
                    <div class="mt-1 font-semibold text-zinc-100">
                        {{ $editingRoleName }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{-- i18n-native: __('ui.labels.guard') --}}
                        {{ __('ui.labels.guard') }}
                    </flux:text>
                    <div class="mt-1 font-semibold text-zinc-100">
                        {{ $editingGuardName }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{-- i18n-native: __('admin.roles.labels.system_role') --}}
                        {{ __('admin.roles.labels.system_role') }}
                    </flux:text>
                    <div class="mt-1 font-semibold text-zinc-100">
                        {{-- i18n-native: __('ui.filters.yes') --}}
                        {{-- i18n-native: __('ui.states.no') --}}
                        {{ $editingIsSystem ? __('ui.filters.yes') : __('ui.states.no') }}
                    </div>
                </div>

                <div>
                    <flux:text class="text-zinc-400">
                        {{-- i18n-native: __('ui.assigned-users') --}}
                        {{ __('ui.assigned-users') }}
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
                {{-- i18n-native: __('ui.labels.category') --}}
                label="{{ __('ui.labels.category') }}"
                wire:model.live="editingCategory"
            />

            <flux:input
                type="number"
                min="0"
                max="65535"
                {{-- i18n-native: __('ui.sort-order') --}}
                label="{{ __('ui.sort-order') }}"
                wire:model.live="editingSortOrder"
            />

            <div class="col-span-2">
                <flux:textarea
                    {{-- i18n-native: __('ui.labels.description') --}}
                    label="{{ __('ui.labels.description') }}"
                    wire:model.live="editingDescription"
                    rows="3"
                />
            </div>

            <div class="col-span-2">
                <flux:checkbox
                    {{-- i18n-native: __('ui.assignable-through-ui') --}}
                    label="{{ __('ui.assignable-through-ui') }}"
                    wire:model.live="editingIsAssignable"
                />
            </div>
        </div>

        {{-- i18n-native: __('admin.roles.badge.title') --}}
        <flux:separator text="{{ __('admin.roles.badge.title') }}" />

        <div class="grid grid-cols-3 gap-4">
            <flux:select
                {{-- i18n-native: __('admin.roles.badge.color') --}}
                label="{{ __('admin.roles.badge.color') }}"
                wire:model.live="editingBadgeColor"
            >
                @foreach ($roleBadgeColorOptions as $color => $label)
                    <flux:select.option value="{{ $color }}">
                        {{ __($label) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                {{-- i18n-native: __('admin.roles.badge.variant') --}}
                label="{{ __('admin.roles.badge.variant') }}"
                wire:model.live="editingBadgeVariant"
            >
                @foreach ($roleBadgeVariantOptions as $variant => $label)
                    <flux:select.option value="{{ $variant }}">
                        {{ __($label) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                {{-- i18n-native: __('admin.roles.badge.icon') --}}
                label="{{ __('admin.roles.badge.icon') }}"
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
                {{-- i18n-native: __('ui.labels.preview') --}}
                {{ __('ui.labels.preview') }}
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
                {{-- i18n-native: __('ui.actions.save_changes') --}}
                :label="__('ui.actions.save_changes')"
                wire:click="saveRole"
            />
        </div>
    </div>
</flux:modal>
