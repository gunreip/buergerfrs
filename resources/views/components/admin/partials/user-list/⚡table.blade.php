{{-- resources/views/components/admin/partials/user-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">

    {{-- Card Header --}}
    <x-ui.headers.card
        :title="__('admin.user_list.table.user_list')"
        :description="__('Overview of all users in the system, their email addresses and assigned roles.')"
    />

    <div class="mx-auto max-w-full">

        <div class="overflow-hidden rounded-t-lg">

            {{-- Table --}}
            <flux:table class="mb-6">

                {{-- Table Head --}}
                <flux:table.columns class="bg-zinc-800 text-zinc-400">

                    {{-- Column ID --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('id')"
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.user_list.table.id')"
                            :text="__('admin.user_list.table.unique_identifier_of_the_user_useful_for_tracking_and_reference')"
                        >
                            {{ __('admin.user_list.table.id') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Name --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.name')"
                            :text="__('admin.user_list.table.full_name_of_the_user_useful_for_identification')"
                        >
                            {{ __('ui.labels.name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column E-Mail --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('email')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.user_list.table.e_mail')"
                            :text="__('Email address of the user, used for communication and login.')"
                        >
                            {{ __('admin.user_list.table.e_mail') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Roles --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('roles.name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.roles')"
                            :text="__('admin.user_list.table.roles_assigned_to_the_user_determining_their_permissions_and_access_levels',
                            )"
                        >
                            {{ __('ui.labels.roles') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actions --}}
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('ui.labels.actions')"
                            :text="__('Available actions for the user, such as editing roles.')"
                        >
                            {{ __('ui.labels.actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                {{-- Table Body  --}}
                <flux:table.rows>
                    @foreach ($users as $user)
                        {{-- Table Row --}}
                        <flux:table.row wire:key="user-list-row-{{ $user->id }}">

                            {{-- Column ID --}}
                            <flux:table.cell
                                class="w-32 tabular-nums"
                                align="end"
                            >
                                {{ $user->id }}
                            </flux:table.cell>

                            {{-- Colunn Name --}}
                            <flux:table.cell class="w-lg">
                                <x-ui.text.highlight
                                    :value="$user->name"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            {{-- Column E-Mail --}}
                            <flux:table.cell>
                                <x-ui.text.highlight
                                    :value="$user->email"
                                    :search="$search"
                                />
                            </flux:table.cell>

                            {{-- Column Roles --}}
                            <flux:table.cell class="w-54">
                                @forelse ($user->roles as $role)
                                    <x-ui.role-badge
                                        :label="$role->name"
                                        :badge="$roleBadges[$role->name] ?? null"
                                    />
                                @empty
                                    <x-ui.role-badge
                                        :label="__('admin.user_list.filter.without_role')"
                                        :badge="$withoutRoleBadge"
                                    />
                                @endforelse
                            </flux:table.cell>

                            {{-- Column Actions --}}
                            <flux:table.cell class="w-48">
                                <flux:button.group class="justify-center">
                                    <x-ui.button.edit
                                        icon="pencil-square"
                                        label="{{ __('admin.user_list.modal.edit_roles') }}"
                                        size="sm"
                                        wire:click="openEditRolesModal({{ $user->id }})"
                                    />
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <flux:separator
                class="mt-4"
                text="{{ __('admin.client_list.table.pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$users" />
            </div>
        @endif

    </div>
</flux:card>
