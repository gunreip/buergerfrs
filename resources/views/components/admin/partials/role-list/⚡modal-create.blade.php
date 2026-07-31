{{-- resources/views/components/admin/partials/role-list/modal-create.blade.php --}}

{{-- Modal Create role --}}
<flux:modal
    class="bg-blue-950/40! md:w-[42rem]"
    wire:model.self="showCreateRoleModal"
>
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">
                {{-- i18n-native: __('admin.roles.modals.create.title') --}}
                {{ __('admin.roles.modals.create.title') }}
            </flux:heading>

            <flux:separator class="mb-6 mt-3" />

            <flux:text>
                {{-- i18n-native: __('admin.roles.modals.create.description') --}}
                {{ __('admin.roles.modals.create.description') }}
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                type="text"
                {{-- i18n-native: __('ui.labels.name') --}}
                label="{{ __('ui.labels.name') }}"
                wire:model.live="creatingRoleName"
                {{-- i18n-native: __('admin.roles.modals.create.name_placeholder') --}}
                placeholder="{{ __('admin.roles.modals.create.name_placeholder') }}"
            />

            <flux:input
                type="text"
                {{-- i18n-native: __('ui.labels.guard') --}}
                label="{{ __('ui.labels.guard') }}"
                wire:model.live="creatingGuardName"
                readonly
            />

            <flux:input
                type="text"
                {{-- i18n-native: __('ui.labels.category') --}}
                label="{{ __('ui.labels.category') }}"
                wire:model.live="creatingCategory"
            />

            <flux:input
                type="number"
                min="0"
                max="65535"
                {{-- i18n-native: __('ui.sort-order') --}}
                label="{{ __('ui.sort-order') }}"
                wire:model.live="creatingSortOrder"
            />

            <div class="col-span-2">
                <flux:textarea
                    {{-- i18n-native: __('ui.labels.description') --}}
                    label="{{ __('ui.labels.description') }}"
                    wire:model.live="creatingDescription"
                    rows="3"
                />
            </div>

            <div class="col-span-2">
                <flux:checkbox
                    {{-- i18n-native: __('ui.assignable-through-ui') --}}
                    label="{{ __('ui.assignable-through-ui') }}"
                    wire:model.live="creatingIsAssignable"
                />
            </div>
        </div>

        {{-- i18n-native: __('admin.roles.badge.title') --}}
        <flux:separator text="{{ __('admin.roles.badge.title') }}" />

        <div class="grid grid-cols-3 gap-4">
            <flux:select
                label="{{ __('admin.roles.badge.color') }}"
                wire:model.live="creatingBadgeColor"
            >
                @foreach ($roleBadgeColorOptions as $color => $label)
                    <flux:select.option value="{{ $color }}">
                        {{-- i18n-native: __('admin.roles.badge.color') --}}
                        label="{{ __('admin.roles.badge.color') }}"
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                {{-- i18n-native: __('admin.roles.badge.variant') --}}
                label="{{ __('admin.roles.badge.variant') }}"
                wire:model.live="creatingBadgeVariant"
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
                wire:model.live="creatingBadgeIcon"
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
                {{-- i18n-native: __('admin.roles.preview.new_role') --}}
                :label="$creatingRoleName !== '' ? $creatingRoleName : __('admin.roles.preview.new_role')"
                :badge="[
                    'color' => $creatingBadgeColor,
                    'variant' => $creatingBadgeVariant,
                    'icon' => $creatingBadgeIcon,
                ]"
            />
        </div>

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel wire:click="closeCreateRoleModal" />
            <x-ui.button.create
                wire:click="createRole"
                :disabled="trim($creatingRoleName) === ''"
            />
        </div>
    </div>
</flux:modal>
