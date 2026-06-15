{{-- resources/views/components/admin/partials/permission-list/⚡table-permissions.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.table.title')"
        :description="__('admin.permissions.table.description')"
    />

    <div class="mx-auto max-w-full">

        <div class="overflow-hidden rounded-t-lg">
            <flux:table class="mb-6">
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    {{-- Number # --}}
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.number')"
                            :text="__('admin.permission_list.table_permissions.sequential_number_of_the_permission_in_the_current_list_useful_for_reference',
                            )"
                        >
                            {{ __('ui.labels.number_short') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Name --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.name')"
                            :text="__('admin.permission_list.table_permissions.name_of_the_permission_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.labels.name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Category --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('category')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.category')"
                            :text="__('admin.permission_list.table_permissions.category_of_the_permission_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.labels.category') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Sort order --}}
                    <flux:table.column
                        align="end"
                        sortable
                        wire:click="sortBy('sort_order')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.permissions.table.columns.sort')"
                            :text="__('admin.permission_list.table_permissions.sort_order_of_the_permission_useful_for_identification_and_reference')"
                        >
                            {{ __('admin.permissions.table.columns.sort') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Guard --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('guard_name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.guard')"
                            :text="__('admin.permission_list.table_permissions.guard_of_the_permission_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.labels.guard') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Description --}}
                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.description')"
                            :text="__('admin.permission_list.table_permissions.description_of_the_permission_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.labels.description') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Flags --}}
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('admin.permissions.table.columns.flags')"
                            :text="__('admin.permission_list.table_permissions.flags_indicating_special_attributes_of_the_permission_such_as_system_or_custom_u',
                            )"
                        >
                            {{ __('admin.permissions.table.columns.flags') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Roles --}}
                    <flux:table.column
                        sortable
                        align="center"
                        wire:click="sortBy('roles_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.roles')"
                            :text="__('admin.permission_list.table_permissions.number_of_roles_associated_with_the_permission_useful_for_identification_and_ref',
                            )"
                        >
                            {{ __('ui.labels.roles') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Assigned roles --}}
                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('admin.permissions.table.columns.assigned_roles')"
                            :text="__('admin.permission_list.table_permissions.roles_assigned_to_the_permission_useful_for_identification_and_reference')"
                        >
                            {{ __('ui.assigned_roles') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Actions --}}
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.actions')"
                            :text="__('admin.permission_list.table_permissions.actions_available_for_the_permission_useful_for_identification_and_reference',
                            )"
                        >
                            {{ __('ui.labels.actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($permissions as $index => $permission)
                        <flux:table.row wire:key="permission-list-row-{{ $permission->id }}">
                            <flux:table.cell
                                class="w-32 tabular-nums text-zinc-400"
                                align="end"
                            >
                                {{ $permissions->firstItem() + $index }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$permission->name"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$permission->category !== null && $permission->category !== '' ? Str::headline($permission->category) : __('ui.states.other')"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            <flux:table.cell
                                class="tabular-nums text-zinc-300"
                                align="end"
                            >
                                {{ $permission->sort_order }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge
                                    color="zinc"
                                    variant="subtle"
                                >
                                    {{ $permission->guard_name }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="text-sm text-zinc-300">
                                    <x-ui.text.highlight
                                        :value="$permission->description ?:
                                            __('ui.messages.no_description_available')"
                                        :search="$search"
                                    />
                                </span>
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @if ($permission->is_system)
                                    <flux:badge
                                        color="purple"
                                        variant="subtle"
                                    >
                                        {{ __('ui.states.system') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="zinc"
                                        variant="subtle"
                                    >
                                        {{ __('ui.states.custom') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <span class="tabular-nums">
                                    {{ $permission->roles_count }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($permission->roles as $role)
                                        <flux:badge
                                            color="sky"
                                            variant="subtle"
                                        >
                                            {{ $role->name }}
                                        </flux:badge>
                                    @empty
                                        <flux:badge
                                            color="orange"
                                            variant="subtle"
                                        >
                                            {{ __('ui.states.unassigned') }}
                                        </flux:badge>
                                    @endforelse
                                </div>
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <x-ui.button.edit
                                    size="sm"
                                    wire:click="openEditPermissionModal({{ $permission->id }})"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="10">
                                <flux:text>
                                    {{ __('admin.permissions.table.empty') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($permissions->hasPages())
            <flux:separator text="{{ __('admin.client_list.table.pagination') }}" />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$permissions" />
            </div>
        @endif

    </div>
</flux:card>
