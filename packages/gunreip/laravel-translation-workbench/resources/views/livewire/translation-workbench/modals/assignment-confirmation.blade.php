@php
    $currentAssignmentKey = $context['finding']['key_id'] ?? null;
    $hasCurrentAssignment = !empty($context['finding']['link_id']) && $currentAssignmentKey !== null;
    $confirmationAlreadyAssigned = $hasCurrentAssignment
        && ($assignmentSelection['key'] ?? null) !== null
        && (int) $currentAssignmentKey === (int) $assignmentSelection['key'];
@endphp
{{-- TODO: Modal width!!! --}}
<flux:modal
    class="w-full max-w-5xl"
    name="generation-assignment-confirmation"
    wire:model="confirmOpen"
>
    <x-translation-workbench::ui.common.modal-loading target="assign" />
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3 pe-10">
            <flux:heading class="flex items-center gap-2" size="xl">
                <flux:icon.key class="size-5 shrink-0" />
                {{ __('Confirm key assignment') }}
            </flux:heading>
            @if ($confirmationAlreadyAssigned)
                <flux:badge color="green" icon="check">
                    {{ __('Already assigned') }}
                </flux:badge>
            @elseif ($hasCurrentAssignment)
                <flux:badge color="amber" icon="arrow-path">
                    {{ __('Different key selected') }}
                </flux:badge>
            @endif
        </div>
        <flux:callout
            color="sky"
            icon="key"
            heading="{{ __('Finding to assign') }}"
        >
            <flux:callout.text>
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item
                        class="break-all"
                        :label="__('Finding ID')"
                        :item="'#' . $selected"
                    />
                </x-translation-workbench::ui.common.details>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="green"
            icon="link"
            heading="{{ __('Translation key assignment') }}"
        >
            <flux:callout.text>
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item :label="__('Translation key')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$assignmentSelection['name'] ?? ''"
                            :heading="__('Translation key')"
                            :mono="true"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item
                        :label="__('Key record ID')"
                        :item="($assignmentSelection['key'] ?? null) === null
                            ? __('Assigned when the new key record is created.')
                            : '#' . $assignmentSelection['key']"
                    />
                    @if ($confirmationAlreadyAssigned)
                        <x-translation-workbench::ui.common.details.item
                            :label="__('Current assignment')"
                            :item="__('Finding #:id already uses this translation key. No further confirmation is needed.', ['id' => $selected])"
                        />
                    @else
                        <x-translation-workbench::ui.common.details.item
                            :label="__('On confirmation')"
                            :item="($assignmentSelection['key'] ?? null) === null
                                ? __('Create this translation key and link finding #:id to it.', ['id' => $selected])
                                : __('Link finding #:id to this existing translation key.', ['id' => $selected])"
                        />
                    @endif
                </x-translation-workbench::ui.common.details>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="hyphens-auto text-justify"
            color="amber"
            icon="shield-alert"
            heading="{{ __('Information') }}"
            text="{{ __('Only this finding is assigned. Its source capture and other findings remain unchanged.') }}"
        >
        </flux:callout>

        <flux:error name="assignment" />

        <div class="flex justify-end">
            <flux:button.group>
                <flux:button
                    icon="handshake"
                    size="sm"
                    variant="primary"
                    color="green"
                    :disabled="$confirmationAlreadyAssigned"
                    wire:loading.attr="disabled"
                    wire:target="assign"
                    wire:click="assign"
                />
                <flux:button
                    variant="primary"
                    size="sm"
                    color="green"
                    :disabled="$confirmationAlreadyAssigned"
                    wire:loading.attr="disabled"
                    wire:target="assign"
                    wire:click="assign"
                >
                    {{ __('Confirm assignment') }}
                </flux:button>
            </flux:button.group>
        </div>
    </div>
</flux:modal>
