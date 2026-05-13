{{-- resources/views/components/admin/partials/app-settings/⚡table-role-badge.blade.php --}}

{{-- Table role badge settings and icon registry entries for detailed diagnostics. This allows for a granular review of each role badge configuration, including visual previews, to identify specific misconfigurations such as incorrect color/variant/icon settings or missing icons that could affect the display of role badges in the application UI. The icon registry table similarly surfaces issues with registered icons that may impact their availability for use in badges or other UI elements. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Role Badge Settings') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column align="center">
                    {{ __('#') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('Role') }}
                </flux:table.column>

                <flux:table.column>
                    {{ __('Preview') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('Color') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('Variant') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('Icon') }}
                </flux:table.column>

                <flux:table.column align="center">
                    {{ __('Status') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($roleBadgeRows as $index => $row)
                    <flux:table.row>
                        <flux:table.cell
                            class="w-32 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $index + 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-medium text-zinc-100">
                                {{ $row['displayLabel'] }}
                            </span>

                            @if ($row['isPseudoRoleBadgeKey'])
                                <div class="font-mono text-xs text-zinc-400">
                                    {{ $row['roleName'] }}
                                </div>

                                <div class="text-xs text-sky-300">
                                    {{ __('UI state, not a database role') }}
                                </div>
                            @elseif (!$row['roleExists'])
                                <div class="text-xs text-orange-300">
                                    {{ __('Role does not exist') }}
                                </div>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <x-ui.role-badge
                                :label="$row['displayLabel']"
                                :badge="[
                                    'color' => $row['color'],
                                    'variant' => $row['variant'],
                                    'icon' => $row['icon'],
                                ]"
                            />
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $row['color'] }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $row['variant'] }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <x-ui.safe-flux-icon
                                    :name="$row['icon']"
                                    category="role_user_management"
                                    variant="micro"
                                />

                                <span>
                                    {{ $row['icon'] }}
                                </span>
                            </div>

                            <div class="text-xs text-zinc-500">
                                {{ $row['iconView'] }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <div class="flex justify-center gap-1">
                                @if ($row['isPseudoRoleBadgeKey'])
                                    <flux:badge
                                        color="sky"
                                        variant="subtle"
                                    >
                                        {{ __('Pseudo state') }}
                                    </flux:badge>
                                @elseif ($row['roleExists'])
                                    <flux:badge
                                        color="green"
                                        variant="subtle"
                                    >
                                        {{ __('Role OK') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="orange"
                                        variant="subtle"
                                    >
                                        {{ __('Missing role') }}
                                    </flux:badge>
                                @endif

                                @if (!$row['usesFallbackIcon'])
                                    <flux:badge
                                        color="green"
                                        variant="subtle"
                                    >
                                        {{ __('Icon OK') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="red"
                                        variant="subtle"
                                    >
                                        {{ __('Icon fallback') }}
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</flux:card>
