{{-- resources/views/components/admin/partials/role-list/modal-create.blade.php --}}

{{-- Modal Create role --}}
<flux:modal
    class="bg-blue-950/40! md:w-[42rem]"
    wire:model.self="showCreateRoleModal"
>
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">
                {{ __('Create Role') }}
            </flux:heading>

            <flux:separator class="mb-6 mt-3" />

            <flux:text>
                {{ __('Create a new assignable role for user and role management.') }}
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                type="text"
                label="{{ __('Name') }}"
                wire:model.live="creatingRoleName"
                placeholder="{{ __('Support Manager') }}"
            />

            <flux:input
                type="text"
                label="{{ __('Guard') }}"
                wire:model.live="creatingGuardName"
                readonly
            />

            <flux:input
                type="text"
                label="{{ __('Category') }}"
                wire:model.live="creatingCategory"
            />

            <flux:input
                type="number"
                min="0"
                max="65535"
                label="{{ __('Sort order') }}"
                wire:model.live="creatingSortOrder"
            />

            <div class="col-span-2">
                <flux:textarea
                    label="{{ __('Description') }}"
                    wire:model.live="creatingDescription"
                    rows="3"
                />
            </div>

            <div class="col-span-2">
                <flux:checkbox
                    label="{{ __('Assignable through UI') }}"
                    wire:model.live="creatingIsAssignable"
                />
            </div>
        </div>

        <flux:separator text="{{ __('Badge') }}" />

        <div class="grid grid-cols-3 gap-4">
            <flux:select
                label="{{ __('Color') }}"
                wire:model.live="creatingBadgeColor"
            >
                @foreach ($roleBadgeColorOptions as $color => $label)
                    <flux:select.option value="{{ $color }}">
                        {{ __($label) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                label="{{ __('Variant') }}"
                wire:model.live="creatingBadgeVariant"
            >
                @foreach ($roleBadgeVariantOptions as $variant => $label)
                    <flux:select.option value="{{ $variant }}">
                        {{ __($label) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                label="{{ __('Icon') }}"
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
                {{ __('Preview') }}
            </flux:text>

            <x-ui.role-badge
                :label="$creatingRoleName !== '' ? $creatingRoleName : __('New role')"
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
