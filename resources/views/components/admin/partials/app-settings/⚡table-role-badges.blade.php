{{-- resources/views/components/admin/partials/app-settings/⚡table-role-badge.blade.php --}}

{{-- Table role badge settings and icon registry entries for detailed diagnostics. This allows for a granular review of each role badge configuration, including visual previews, to identify specific misconfigurations such as incorrect color/variant/icon settings or missing icons that could affect the display of role badges in the application UI. The icon registry table similarly surfaces issues with registered icons that may impact their availability for use in badges or other UI elements. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('admin.app_settings.table_role_badges.role_badge_settings') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column align="center">
                    {{ __('ui.labels.number_short') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('ui.labels.role') }}
                </flux:table.column>

                <flux:table.column>
                    {{ __('ui.labels.preview') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('admin.roles.badge.color') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('admin.roles.badge.variant') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('admin.roles.badge.icon') }}
                </flux:table.column>

                <flux:table.column align="center">
                    {{ __('ui.state.status') }}
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
                                    {{ __('admin.app_settings.table_role_badges.ui_state_not_a_database_role') }}
                                </div>
                            @elseif (!$row['roleExists'])
                                <div class="text-xs text-orange-300">
                                    {{ __('admin.app_settings.table_role_badges.role_does_not_exist') }}
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
                                <x-ui.flux-icon
                                    :name="$row['icon']"
                                    variant="micro"
                                    stroke-width="1"
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
                                        {{ __('admin.app_settings.table_role_badges.pseudo_state') }}
                                    </flux:badge>
                                @elseif ($row['roleExists'])
                                    <flux:badge
                                        color="green"
                                        variant="subtle"
                                    >
                                        {{ __('admin.app_settings.table_role_badges.role_ok') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="orange"
                                        variant="subtle"
                                    >
                                        {{ __('admin.app_settings.table_role_badges.missing_role') }}
                                    </flux:badge>
                                @endif

                                @if (!$row['usesFallbackIcon'])
                                    <flux:badge
                                        color="green"
                                        variant="subtle"
                                    >
                                        {{ __('admin.app_settings.table_role_badges.icon_ok') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        color="red"
                                        variant="subtle"
                                    >
                                        {{ __('admin.app_settings.table_role_badges.icon_fallback') }}
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
