<flux:modal
    class="w-[calc(100vw-4rem)] max-w-[82rem]"
    wire:model.self="showRolePermissionsModal"
>
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">
                {{ __('Manage Role Permissions') }}
            </flux:heading>

            <flux:text class="mt-2">
                {{ __('Assign permissions to a selected role.') }}
            </flux:text>
        </div>

        <flux:separator />

        <div class="flex items-end justify-between gap-4">
            <div class="w-full max-w-md">
                <flux:label for="role-permissions-role">
                    {{ __('Role') }}
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.shield-check />
                    </flux:input.group.prefix>

                    <flux:select
                        id="role-permissions-role"
                        wire:model.live="selectedRoleName"
                    >
                        <flux:select.option value="">
                            {{ __('Select role') }}
                        </flux:select.option>

                        @foreach ($roles as $role)
                            <flux:select.option value="{{ $role->name }}">
                                {{ $role->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>
            </div>

            <div class="flex shrink-0 justify-end gap-3">
                <x-ui.button.cancel wire:click="closeRolePermissionsModal" />

                <x-ui.button.save
                    wire:click="saveRolePermissions"
                    :disabled="$selectedRoleName === '' || ! $this->hasRolePermissionChanges()"
                />
            </div>
        </div>

        @if ($selectedRoleName !== '')
            <div class="grid grid-cols-2 gap-3 xl:grid-cols-10">
                <flux:callout
                    class="col-span-2"
                    color="sky"
                    icon="shield-check"
                >
                    <flux:callout.heading>
                        {{ __('Role') }}
                    </flux:callout.heading>

                    <flux:callout.text class="font-semibold">
                        {{ $selectedRoleName }}
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    class="col-span-2"
                    color="yellow"
                    icon="tag"
                >
                    <flux:callout.heading>
                        {{ __('Category') }}
                    </flux:callout.heading>

                    <flux:callout.text class="font-semibold">
                        {{ $selectedRoleCategory !== '' ? Str::headline($selectedRoleCategory) : __('Other') }}
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    class="col-span-2"
                    color="purple"
                    icon="badge-check"
                >
                    <flux:callout.heading>
                        {{ __('Current permissions') }}
                    </flux:callout.heading>

                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ $selectedRoleCurrentPermissionCount }}
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    class="col-span-2"
                    color="green"
                    icon="list-checks"
                >
                    <flux:callout.heading>
                        {{ __('Selected permissions') }}
                    </flux:callout.heading>

                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ count($selectedPermissionNames) }}
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    class="col-span-2"
                    :color="$this->hasRolePermissionChanges() ? 'orange' : 'zinc'"
                    :icon="$this->hasRolePermissionChanges() ? 'triangle-alert' : 'check'"
                >
                    <flux:callout.heading>
                        {{ __('Changes') }}
                    </flux:callout.heading>

                    <flux:callout.text class="font-semibold">
                        {{ $this->hasRolePermissionChanges() ? __('Unsaved changes') : __('No changes') }}
                    </flux:callout.text>
                </flux:callout>
            </div>
        @endif

        @if ($selectedRoleName !== '')
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                @foreach ($permissionsByCategory as $category => $categoryPermissions)
                    <flux:card class="space-y-2 place-self-stretch">
                        <flux:heading
                            class="mb-3"
                            size="md"
                        >
                            {{ Str::headline($category) }}
                        </flux:heading>

                        <div class="space-y-1">
                            @foreach ($categoryPermissions as $permission)
                                <label
                                    class="flex min-h-28 items-start gap-3 rounded-lg border border-zinc-700/40 bg-zinc-800/20 px-4 py-3 transition hover:cursor-pointer hover:bg-zinc-800/40"
                                >
                                    <flux:checkbox
                                        class="mt-1 shrink-0"
                                        value="{{ $permission->name }}"
                                        wire:model.live="selectedPermissionNames"
                                    />

                                    <span class="grid min-w-0 content-start gap-1">
                                        <span class="block truncate font-medium text-zinc-100">
                                            {{ $permission->name }}
                                        </span>

                                        @if ($permission->description)
                                            <span class="line-clamp-2 block text-sm leading-5 text-zinc-400">
                                                {{ $permission->description }}
                                            </span>
                                        @else
                                            <span class="block text-sm leading-5 text-zinc-500">
                                                {{ __('No description available.') }}
                                            </span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif

    </div>
</flux:modal>
