{{-- resources/views/components/admin/partials/app-settings/⚡table-icon-registry.blade.php --}}

{{-- Table for icon registry entries with availability status to identify any issues with registered icons that may affect their use in role badges or other UI elements. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('admin.app_settings.table_icon_registry.icon_registry') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column align="center">
                    {{ __('ui.labels.number_short') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('admin.roles.badge.icon') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('admin.app_settings.table_icon_registry.label') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('admin.app_settings.table_icon_registry.view') }}
                </flux:table.column>

                <flux:table.column
                    align="center"
                    sortable
                >
                    {{ __('admin.app_settings.table_icon_registry.status') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($iconRegistryRows as $index => $icon)
                    <flux:table.row>
                        <flux:table.cell
                            class="w-16 tabular-nums text-zinc-400"
                            align="end"
                        >
                            {{ $index + 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <x-ui.flux-icon
                                    :name="$icon['name']"
                                    variant="micro"
                                    stroke-width="1"
                                />

                                <span class="font-medium text-zinc-100">
                                    {{ $icon['name'] }}
                                </span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $icon['label'] }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-mono text-xs text-zinc-400">
                                {{ $icon['view'] }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            @if ($icon['available'])
                                <flux:badge
                                    color="green"
                                    variant="subtle"
                                >
                                    {{ __('admin.app_settings.table_icon_registry.available') }}
                                </flux:badge>
                            @else
                                <flux:badge
                                    color="red"
                                    variant="subtle"
                                >
                                    {{ __('admin.app_settings.table_icon_registry.missing') }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</flux:card>
