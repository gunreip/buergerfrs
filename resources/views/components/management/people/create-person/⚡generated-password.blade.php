{{-- resources/views/components/management/people/create-person/⚡generated-password.blade.php --}}

@if ($createdPersonNumber !== '' || $createdPersonId !== null || $createdUserId !== null)
    <flux:callout
        class="mt-6"
        color="green"
        icon="check-circle"
    >
        <flux:callout.heading>
            {{ __('Person created') }}
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('Person number: :personNumber | Created person ID: :personId | Created user ID: :userId', [
                'personNumber' => $createdPersonNumber !== '' ? $createdPersonNumber : '—',
                'personId' => $createdPersonId ?? '—',
                'userId' => $createdUserId ?? '—',
            ]) }}
        </flux:callout.text>

        @if ($createdPersonId !== null && $createdDocumentId !== null)
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <flux:button
                    :href="route('management.people.documents.inline', ['person' => $createdPersonId, 'document' =>
                        $createdDocumentId
                    ])"
                    icon="eye"
                    size="sm"
                    target="_blank"
                    variant="ghost"
                >
                    {{ __('Open document') }}
                </flux:button>

                <flux:button
                    :href="route('management.people.documents.download', ['person' => $createdPersonId, 'document' =>
                        $createdDocumentId
                    ])"
                    icon="arrow-down-tray"
                    size="sm"
                    target="_blank"
                    variant="ghost"
                >
                    {{ __('Download document') }}
                </flux:button>
            </div>
        @endif
    </flux:callout>
@endif

@if ($generatedPassword !== '')
    <flux:callout
        class="mt-4"
        color="orange"
        icon="key-round"
    >
        <flux:callout.heading>
            {{ __('Generated temporary password') }}
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('This password is shown once here and is also written to the local development JSONL password log.') }}
        </flux:callout.text>

        <div class="mt-3 flex items-center gap-3">
            <code class="rounded-lg bg-zinc-950 px-3 py-2 font-mono text-sm text-zinc-100">
                {{ $generatedPassword }}
            </code>

            <x-ui.button.confirm
                :label="__('Clear password')"
                icon="eye-off"
                wire:click="clearGeneratedPassword"
            />
        </div>
    </flux:callout>
@endif
