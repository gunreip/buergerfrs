{{-- resources/views/components/admin/partials/permission-list/⚡table-permissions.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Permission List') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                {{-- Number # --}}
                <flux:table.column align="center">
                    {{ __('#') }}
                </flux:table.column>

                {{-- Name --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('name')"
                >
                    {{ __('Name') }}
                </flux:table.column>

                {{-- Category --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('category')"
                >
                    {{ __('Category') }}
                </flux:table.column>

                {{-- Sort order --}}
                <flux:table.column
                    align="end"
                    sortable
                    wire:click="sortBy('sort_order')"
                >
                    {{ __('Sort') }}
                </flux:table.column>

                {{-- Guard --}}
                <flux:table.column
                    sortable
                    wire:click="sortBy('guard_name')"
                >
                    {{ __('Guard') }}
                </flux:table.column>

                {{-- Description --}}
                <flux:table.column>
                    {{ __('Description') }}
                </flux:table.column>

                {{-- Flags --}}
                <flux:table.column align="center">
                    {{ __('Flags') }}
                </flux:table.column>

                {{-- Roles --}}
                <flux:table.column
                    sortable
                    align="center"
                    wire:click="sortBy('roles_count')"
                >
                    {{ __('Roles') }}
                </flux:table.column>

                {{-- Assigned roles --}}
                <flux:table.column>
                    {{ __('Assigned roles') }}
                </flux:table.column>

                {{-- Actions --}}
                <flux:table.column align="center">
                    {{ __('Actions') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($permissions as $index => $permission)
                    <flux:table.row>
                        <flux:table.cell
                            class="w-32 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $index + 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-medium text-zinc-100">
                                {!! $highlightSearchMatch($permission->name, $search) !!}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            {!! $highlightSearchMatch(
                                $permission->category !== null && $permission->category !== '' ? Str::headline($permission->category) : __('Other'),
                                $search,
                            ) !!}
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
                                {!! $highlightSearchMatch($permission->description ?: __('No description available.'), $search) !!}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            @if ($permission->is_system)
                                <flux:badge
                                    color="purple"
                                    variant="subtle"
                                >
                                    {{ __('System') }}
                                </flux:badge>
                            @else
                                <flux:badge
                                    color="zinc"
                                    variant="subtle"
                                >
                                    {{ __('Custom') }}
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
                                        {{ __('Unassigned') }}
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
                                {{ __('No permissions registered yet.') }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</flux:card>
