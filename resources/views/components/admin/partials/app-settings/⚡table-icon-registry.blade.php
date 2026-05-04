{{-- resources/views/components/admin/partials/app-settings/⚡table-icon-registry.blade.php --}}

{{-- Table for icon registry entries with availability status to identify any issues with registered icons that may affect their use in role badges or other UI elements. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Icon Registry') }}
    </flux:heading>

    <div class="overflow-hidden rounded-t-lg">
        <flux:table>
            <flux:table.columns class="bg-zinc-800 text-zinc-400">
                <flux:table.column align="center">
                    {{ __('#') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('Icon') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('Label') }}
                </flux:table.column>

                <flux:table.column sortable>
                    {{ __('View') }}
                </flux:table.column>

                <flux:table.column
                    align="center"
                    sortable
                >
                    {{ __('Status') }}
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
                                <x-ui.safe-flux-icon
                                    :name="$icon['name']"
                                    category="role_user_management"
                                    variant="micro"
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
                                    {{ __('Available') }}
                                </flux:badge>
                            @else
                                <flux:badge
                                    color="red"
                                    variant="subtle"
                                >
                                    {{ __('Missing') }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</flux:card>
