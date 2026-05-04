{{-- resources/views/components/admin/partials/role-list/table.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Role List') }}
    </flux:heading>

    <div class="mx-auto max-w-full">
        <div class="overflow-hidden rounded-t-lg">
            <flux:table>
                <flux:table.columns class="bg-zinc-800 text-zinc-400">
                    <flux:table.column align="center">
                        {{ __('Badge') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('name')"
                    >
                        {{ __('Name') }}
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        wire:click="sortBy('category')"
                    >
                        {{ __('Category') }}
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('sort_order')"
                    >
                        {{ __('Sort') }}
                    </flux:table.column>

                    <flux:table.column>
                        {{ __('Description') }}
                    </flux:table.column>

                    <flux:table.column align="center">
                        {{ __('Flags') }}
                    </flux:table.column>

                    <flux:table.column
                        align="center"
                        sortable
                        wire:click="sortBy('users_count')"
                    >
                        {{ __('Users') }}
                    </flux:table.column>

                    <flux:table.column align="center">
                        {{ __('Actions') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($roles as $role)
                        <flux:table.row>
                            <flux:table.cell>
                                <x-ui.role-badge
                                    :label="$role->name"
                                    :badge="$roleBadges[$role->name] ?? null"
                                />
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="font-medium text-zinc-100">
                                    {!! $highlightSearchMatch($role->name, $search) !!}
                                </span>

                                <div class="text-xs text-zinc-500">
                                    {{ $role->guard_name }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                {!! $highlightSearchMatch(
                                    $role->category !== null && $role->category !== '' ? Str::headline($role->category) : __('Other'),
                                    $search,
                                ) !!}
                            </flux:table.cell>

                            <flux:table.cell
                                class="tabular-nums"
                                align="end"
                            >
                                {{ $role->sort_order }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="text-sm text-zinc-300">
                                    {!! $highlightSearchMatch($role->description ?: __('No description available.'), $search) !!}
                                </span>
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
                                    <flux:button
                                        type="button"
                                        variant="primary"
                                        color="sky"
                                        size="sm"
                                        icon="pencil-square"
                                        wire:click="openEditRoleModal({{ $role->id }})"
                                    >
                                        {{ __('Edit Role') }}
                                    </flux:button>
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</flux:card>
