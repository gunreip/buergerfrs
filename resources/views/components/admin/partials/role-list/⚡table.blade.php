{{-- resources/views/components/admin/partials/role-list/⚡table.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('Role List')"
        :description="__('Review and manage user roles, their permissions, and assigned users.')"
    />

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column
                        sortable
                        wire:click="sortBy('id')"
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ID')"
                            :text="__('Unique identifier of the role, useful for tracking and reference.')"
                        >
                            {{ __('ID') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('Badge')"
                            :text="__('Visual representation of the role, useful for quick identification.')"
                        >
                            {{ __('Badge') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Name')"
                            :text="__('Name of the role, useful for identification and reference.')"
                        >
                            {{ __('Name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('category')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Category')"
                            :text="__('Category of the role, useful for identification and reference.')"
                        >
                            {{ __('Category') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('sort_order')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Sort')"
                            :text="__('Sort order of the role, useful for identification and reference.')"
                        >
                            {{ __('Sort') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column>
                        <x-ui.tooltip.trigger
                            :title="__('Description')"
                            :text="__('Description of the role, useful for identification and reference.')"
                        >
                            {{ __('Description') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('Flags')"
                            :text="__('Flags associated with the role, useful for identification and reference.')"
                        >
                            {{ __('Flags') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('users_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Users')"
                            :text="__(
                                'Number of users associated with the role, useful for identification and reference.',
                            )"
                        >
                            {{ __('Users') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('Actions')"
                            :text="__(
                                'Actions that can be performed on the role, useful for management and administration.',
                            )"
                        >
                            {{ __('Actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($roles as $role)
                        <flux:table.row wire:key="role-list-row-{{ $role->id }}">
                            <flux:table.cell
                                class="w-32 tabular-nums"
                                align="end"
                            >
                                {{ $role->id }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-ui.role-badge
                                    :label="$role->name"
                                    :badge="$roleBadges[$role->name] ?? null"
                                />
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$role->name"
                                    :search="$search"
                                />

                                <div class="text-xs text-zinc-500">
                                    {{ $role->guard_name }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$role->category !== null && $role->category !== '' ? Str::headline($role->category) : __('Other')"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            <flux:table.cell
                                class="tabular-nums"
                                align="end"
                            >
                                {{ $role->sort_order }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$role->description ?: '—'"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <div class="flex justify-center gap-1">
                                    @if ($role->is_system)
                                        <flux:badge
                                            color="purple"
                                            variant="subtle"
                                        >
                                            {{ __('System') }}
                                        </flux:badge>
                                    @endif

                                    @if ($role->is_assignable)
                                        <flux:badge
                                            color="green"
                                            variant="subtle"
                                        >
                                            {{ __('Assignable') }}
                                        </flux:badge>
                                    @else
                                        <flux:badge
                                            color="red"
                                            variant="subtle"
                                        >
                                            {{ __('Not assignable') }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell
                                class="tabular-nums"
                                align="end"
                            >
                                {{ $role->users_count }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <flux:button.group class="justify-center">
                                    <x-ui.button.edit
                                        icon="pencil-square"
                                        label="{{ __('Edit Role') }}"
                                        size="sm"
                                        wire:click="openEditRoleModal({{ $role->id }})"
                                    />
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($roles->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('Pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$roles" />
            </div>
        @endif

    </div>
</flux:card>
