{{-- resources/views/components/admin/partials/user-list/⚡table.blade.php --}}

{{-- Table --}}
<flux:card class="mt-6">

    {{-- Card Header --}}
    <x-ui.headers.card
        :title="__('User List')"
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
                            :title="__('ID')"
                            :text="__('Unique identifier of the user, useful for tracking and reference.')"
                        >
                            {{ __('ID') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Name --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Name')"
                            :text="__('Full name of the user, useful for identification.')"
                        >
                            {{ __('Name') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column E-Mail --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('email')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('E-Mail')"
                            :text="__('Email address of the user, used for communication and login.')"
                        >
                            {{ __('E-Mail') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Roles --}}
                    <flux:table.column
                        sortable
                        wire:click="sortBy('roles.name')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('Roles')"
                            :text="__(
                                'Roles assigned to the user, determining their permissions and access levels.',
                            )"
                        >
                            {{ __('Roles') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actions --}}
                    <flux:table.column align="center">
                        <x-ui.tooltip.trigger
                            :title="__('Actions')"
                            :text="__('Available actions for the user, such as editing roles.')"
                        >
                            {{ __('Actions') }}
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
                                        :label="__('Without role')"
                                        :badge="$withoutRoleBadge"
                                    />
                                @endforelse
                            </flux:table.cell>

                            {{-- Column Actions --}}
                            <flux:table.cell class="w-48">
                                <flux:button.group class="justify-center">
                                    <x-ui.button.edit
                                        icon="pencil-square"
                                        label="{{ __('Edit Roles') }}"
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
                text="{{ __('Pagination') }}"
            />

            <div class="mt-4">
                <x-ui.table.pagination :paginator="$users" />
            </div>
        @endif

    </div>
</flux:card>
