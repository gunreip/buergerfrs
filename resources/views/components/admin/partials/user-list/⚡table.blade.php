{{-- resources/views/components/admin/partials/user-list/table.blade.php --}}

<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('User List') }}
    </flux:heading>

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table class="mb-6">
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column
                        sortable
                        wire:click="sortBy('id')"
                        align="center"
                    >
                        {{ __('ID') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        {{ __('Name') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('email')"
                    >
                        {{ __('E-Mail') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('roles.name')"
                    >
                        {{ __('Roles') }}
                    </flux:table.column>

                    <flux:table.column align="center">
                        {{ __('Actions') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($users as $user)
                        <flux:table.row>
                            <flux:table.cell
                                class="w-32 tabular-nums"
                                align="end"
                            >
                                {{ $user->id }}
                            </flux:table.cell>

                            <flux:table.cell class="w-lg">
                                {!! $highlightSearchMatch($user->name, $search) !!}
                            </flux:table.cell>

                            <flux:table.cell>
                                {!! $highlightSearchMatch($user->email, $search) !!}
                            </flux:table.cell>

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

        <flux:separator text="{{ __('Pagination') }}" />

        <x-ui.table.pagination :paginator="$users" />
    </div>
</flux:card>
