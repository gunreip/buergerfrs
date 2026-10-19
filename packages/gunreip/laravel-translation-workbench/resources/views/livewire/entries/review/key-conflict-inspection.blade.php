@if ($keyConflictInspection)
    @if ($pendingSeparation = $keyConflictInspection['pending_separation'] ?? null)
        <flux:callout
            class="mb-3"
            color="amber"
        >
            <flux:callout.heading>{{ __('Task separated — source update pending') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Key #:key · Review #:review', ['key' => $pendingSeparation['key_id'], 'review' => $pendingSeparation['review_id']]) }}
            </flux:callout.text>
            <flux:callout.text class="break-all font-mono">{{ $pendingSeparation['source_key'] ?? __('Literal') }} →
                {{ $pendingSeparation['target_key'] }}</flux:callout.text>
            <flux:callout.text>
                {{ __('The scanner retains this assignment while the old expression remains in the source. Enter and review the new translations before publication and the targeted code update.') }}
            </flux:callout.text>
        </flux:callout>
    @endif
    @if ($separation = $keyConflictInspection['separation'] ?? null)
        @include('translation-workbench::livewire.entries.review.key-separation-preview', [
            'separation' => $separation,
        ])
    @endif
    @foreach ($keyConflictInspection['retirements'] ?? [] as $retirement)
        <flux:callout
            class="mb-3"
            color="amber"
        >
            <flux:callout.heading>
                {{ __('Key #:id has no remaining active assignments', ['id' => $retirement['key_id']]) }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('All previous findings were explicitly assigned to key #:id. Archiving this unused key record retains its history and the shared translation values.', ['id' => $retirement['target_id']]) }}
            </flux:callout.text>
            <flux:callout.text>{{ __('Findings') }}:
                {{ implode(', ', array_map(fn($id) => '#' . $id, $retirement['finding_ids'])) }}</flux:callout.text>
            <flux:button
                class="mt-3"
                variant="outline"
                wire:click="retireUnassignedKey({{ $retirement['key_id'] }})"
                wire:loading.attr="disabled"
                wire:target="retireUnassignedKey"
                wire:confirm="{{ __('Archive key #:old and retain key #:target for its explicitly reassigned findings?', ['old' => $retirement['key_id'], 'target' => $retirement['target_id']]) }}"
            >
                {{ __('Archive key #:id', ['id' => $retirement['key_id']]) }}
            </flux:button>
            <flux:error name="translationKeyValue" />
        </flux:callout>
    @endforeach
    @if ($savedAssignment = $keyConflictInspection['assignment_decision'] ?? null)
        <flux:callout
            class="mb-3"
            :color="$savedAssignment['active'] ? 'green' : 'amber'"
        >
            <flux:callout.heading>{{ __('Saved finding assignment') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Finding #:finding → Key #:key', ['finding' => $savedAssignment['finding_id'], 'key' => $savedAssignment['key_id']]) }}
            </flux:callout.text>
            <flux:callout.text>
                {{ __('Review #:review', ['review' => $savedAssignment['review_id']]) }} ·
                {{ $savedAssignment['reviewed_at'] ?? '—' }}
            </flux:callout.text>
            <flux:callout.text>
                {{ $savedAssignment['active']
                    ? __('This selection is active. Other key records and their translation values remain independent.')
                    : __(
                        'The stored selection no longer matches the active assignment. Review the current links before scanning again.',
                    ) }}
            </flux:callout.text>
        </flux:callout>
    @endif
    <flux:callout.text>
        {{ __('Read-only snapshot. Close and reopen this section to refresh after changing the entered translation key. No assignments or values are changed.') }}
    </flux:callout.text>
    <flux:callout.text>
        {{ __('Values belong to the key text and are shared by its active records. Splitting assignments must not silently move shared values; merging must explicitly resolve occupied target values.') }}
    </flux:callout.text>
    <flux:callout.text>
        {{ __('Identical literal text can belong to different translation tasks. Different suggested keys or translation keys do not by themselves require merging; compare the source context and review decisions first.') }}
    </flux:callout.text>
    <dl class="mt-2 flex flex-col gap-2 text-sm">
        <div class="flex flex-col gap-1 sm:flex-row sm:gap-3">
            <dt class="min-w-48 font-medium sm:w-48">
                <flux:callout.heading>
                    {{ __('Saved translation key') }}:
                </flux:callout.heading>
            </dt>
            <dd class="min-w-0 break-all font-mono">{{ $keyConflictInspection['source'] }}</dd>
        </div>
        <div class="flex flex-col gap-1 sm:flex-row sm:gap-3">
            <dt class="min-w-48 font-medium sm:w-48">
                <flux:callout.heading>
                    {{ __('Entered translation key') }}:
                </flux:callout.heading>
            </dt>
            <dd @class([
                'min-w-0 break-all font-mono',
                'text-amber-600 dark:text-amber-400' =>
                    $keyConflictInspection['source'] !== $keyConflictInspection['target'],
            ])>{{ $keyConflictInspection['target'] }}</dd>
        </div>
    </dl>
    <div @class([
        'mt-3 grid grid-cols-1 gap-4',
        'xl:grid-cols-2' => count($keyConflictInspection['groups']) > 1,
    ])>
        @foreach ($keyConflictInspection['groups'] as $group)
            <flux:callout
                color="amber"
                x-data="{ showInactiveLinks: true }"
            >
                <flux:callout.heading class="break-all">
                    <flux:icon.key />{{ $group['key'] }}
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('Active key records') }}: {{ count($group['owners']) }}
                    · {{ __('Actively linked findings') }}: {{ collect($group['findings'])->where('link_status', 'active')->where('status', 'active')->pluck('id')->unique()->count() }}
                </flux:callout.text>
                @if ($group['ownership_conflict'] ?? count($group['owners']) > 1)
                    <flux:callout
                        class="mt-3"
                        color="red"
                        icon="megaphone"
                    >
                        <flux:callout.heading>
                            {{ __('Multiple active key records for the same translation key') }}
                        </flux:callout.heading>
                        <flux:callout.text>
                            {{ __('Several findings may use one key record. The conflict is between multiple active key records with the same translation key, not between their linked findings. Choosing a key record for one finding does not resolve these duplicate key records.') }}
                        </flux:callout.text>
                        <flux:button class="mt-3" type="button" variant="outline" icon="adjustments-horizontal"
                            wire:click="openKeyOwnershipConflict({{ $group['owners'][0]['id'] }})"
                            wire:loading.attr="disabled" wire:target="openKeyOwnershipConflict">
                            {{ __('Resolve duplicate key records') }}
                        </flux:button>
                        <flux:error name="translationKeyValue" />
                        <div class="mt-3 space-y-2">
                            @foreach ($group['owners'] as $relatedOwner)
                                @php
                                    $relatedFindings = collect($group['findings'])->where('key_id', $relatedOwner['id'])
                                        ->where('link_status', 'active')->where('status', 'active')->unique('id');
                                @endphp
                                <div class="flex flex-wrap items-center gap-2">
                                    <flux:badge size="sm">{{ __('Key') }} #{{ $relatedOwner['id'] }}</flux:badge>
                                    @forelse ($relatedFindings as $relatedFinding)
                                        @if ((int) $relatedFinding['id'] === (int) ($translationKeyFindingId ?? 0))
                                            <flux:badge size="sm" color="sky">Finding #{{ $relatedFinding['id'] }} · {{ __('Opened finding') }}</flux:badge>
                                        @else
                                            <flux:button type="button" size="xs" variant="outline"
                                                wire:click="openRelatedKeyFinding({{ $relatedFinding['id'] }})"
                                                wire:loading.attr="disabled" wire:target="openRelatedKeyFinding"
                                                :tooltip="__('Open finding #:id in the key editor', ['id' => $relatedFinding['id']])">
                                                Finding #{{ $relatedFinding['id'] }}
                                            </flux:button>
                                        @endif
                                    @empty
                                        <flux:text>{{ __('No active links.') }}</flux:text>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                        @if (isset($group['ambiguous_finding_ids']))
                            <flux:callout.text>
                                {{ __('Active findings with multiple assignments') }}:
                                {{ $group['ambiguous_finding_ids'] ? implode(', ', array_map(fn($id) => '#' . $id, $group['ambiguous_finding_ids'])) : __('None') }}
                            </flux:callout.text>
                        @endif
                    </flux:callout>
                @endif
                <flux:table container:class="mt-2 max-h-64 overflow-auto rounded rounded-t-lg">
                    <flux:table.columns
                        class="rounded rounded-t-lg bg-white dark:bg-zinc-800"
                        stick
                    >
                        {{-- Sequence Number --}}
                        <flux:table.column class="w-8">
                            <flux:callout.heading>
                                <flux:icon.tally-5 class="ml-2" />
                            </flux:callout.heading>
                        </flux:table.column>
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Key ID') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Suggested key') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Status') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Review status') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Reviewed at') }}
                            </flux:callout.heading>
                        </flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($group['owners'] as $owner)
                            <flux:table.row>
                                <flux:table.cell class="text-right tabular-nums">
                                    <flux:callout.text class="text-right tabular-nums">
                                        {{ $loop->index + 1 }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:callout.text>
                                        #{{ $owner['id'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal break-all font-mono">
                                    <flux:callout.text>
                                        {{ $owner['suggested_key'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:callout.text>
                                        {{ __($owner['status']) }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge
                                        :color="$owner['review_status'] === 'reviewed' ? 'green' : 'amber'"
                                        size="sm"
                                    >
                                        {{ __($owner['review_status']) }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:callout.text>
                                        {{ $owner['reviewed_at'] ?? '—' }}
                                    </flux:callout.text>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5">{{ __('No active key records.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
                <flux:callout.heading class="mt-3">
                    {{ __('Linked findings (including inactive links)') }}
                    <span class="font-normal">
                        {{ __('(:active active / :inactive inactive findings)', [
                            'active' => collect($group['findings'])->where('link_status', 'active')->count(),
                            'inactive' => collect($group['findings'])->where('link_status', '!=', 'active')->count(),
                        ]) }}
                    </span>
                </flux:callout.heading>
                <div class="max-h-64 overflow-auto rounded-lg">
                    <flux:table container:class="max-h-64 rounded rounded-t-lg">
                        <flux:table.columns
                            class="rounded rounded-t-lg bg-white dark:bg-zinc-800"
                            sticky
                        >
                            {{-- Sequential Number --}}
                            <flux:table.column class="w-8">
                                <flux:callout.heading>
                                    <flux:icon.tally-5 class="ml-2" />
                                </flux:callout.heading>
                            </flux:table.column>
                            {{-- Key / Finding --}}
                            <flux:table.column>
                                <flux:callout.heading>
                                    {{ __('Key / Finding') }}
                                </flux:callout.heading>
                            </flux:table.column>
                            {{-- Source --}}
                            <flux:table.column>
                                <flux:callout.heading>
                                    {{ __('Source') }}
                                </flux:callout.heading>
                            </flux:table.column>
                            {{-- Status / Link --}}
                            <flux:table.column>
                                <div class="flex w-full items-center justify-between gap-3 pr-3">
                                    <flux:callout.heading>
                                        {{ __('Status / Link') }}
                                    </flux:callout.heading>
                                    <flux:toggle
                                        class="ml-auto shrink-0 cursor-pointer"
                                        size="sm"
                                        inset
                                        variant="ghost"
                                        x-model="showInactiveLinks"
                                        :aria-label="__('Include inactive links')"
                                        :tooltip="__('Include inactive links')"
                                    >
                                        <x-slot:icon>
                                            <flux:icon.shield-plus
                                                class="group-data-checked:block hidden size-5 text-green-400"
                                                variant="outline"
                                            />
                                            <flux:icon.shield-minus
                                                class="group-data-checked:hidden size-5 text-red-400"
                                                variant="outline"
                                            />
                                        </x-slot:icon>
                                    </flux:toggle>
                                </div>
                            </flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($group['findings'] as $finding)
                                {{-- Sequential Number --}}
                                <flux:table.row
                                    x-show="showInactiveLinks || {{ $finding['link_status'] === 'active' ? 'true' : 'false' }}"
                                >
                                    {{-- Sequence Number --}}
                                    <flux:table.cell>
                                        <flux:callout.text class="text-right tabular-nums">
                                            {{ $loop->index + 1 }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                    {{-- Key / Finding --}}
                                    <flux:table.cell>
                                        <flux:callout.text>
                                            #{{ $finding['key_id'] }} / #{{ $finding['id'] }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                    {{-- Source --}}
                                    <flux:table.cell class="whitespace-normal break-all">
                                        <flux:callout.text>
                                            {{ $finding['path'] }}:{{ $finding['source_line'] }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                    {{-- Status / Link --}}
                                    <flux:table.cell>
                                        <flux:callout.text>
                                            {{ $finding['status'] }} / {{ $finding['link_status'] }} ·
                                            {{ $finding['relation_type'] }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                {{-- Empty --}}
                                <flux:table.row>
                                    <flux:table.cell colspan="3">
                                        <flux:callout.text>
                                            {{ __('No linked findings.') }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                            @if (count($group['findings']) > 0 && !collect($group['findings'])->contains('link_status', 'active'))
                                {{-- No Active Links --}}
                                <flux:table.row
                                    x-show="!showInactiveLinks"
                                    x-cloak
                                >
                                    <flux:table.cell colspan="3">
                                        <flux:callout.text>{{ __('No active links.') }}</flux:callout.text>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endif
                        </flux:table.rows>
                    </flux:table>
                </div>

                {{-- Stored Scalar Values --}}
                <flux:heading class="mt-3">
                    <flux:callout.heading>
                        {{ __('Stored scalar values (including obsolete values)') }}
                        <span class="font-normal">
                            {{ __('(:active active / :inactive inactive values)', [
                                'active' => collect($group['values'])->where('status', 'active')->count(),
                                'inactive' => collect($group['values'])->where('status', '!=', 'active')->count(),
                            ]) }}
                        </span>
                    </flux:callout.heading>
                </flux:heading>
                <div class="max-h-64 overflow-auto rounded-lg">
                    <flux:table container:class="max-h-64 rounded rounded-t-lg">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-800"
                            sticky
                        >
                            {{-- Sequential Number --}}
                            <flux:table.column class="w-8">
                                <flux:callout.heading>
                                    <flux:icon.tally-5 class="ml-2" />
                                </flux:callout.heading>
                            </flux:table.column>
                            {{-- Locale Status ID --}}
                            <flux:table.column>
                                <flux:callout.heading>
                                    {{ __('Locale / Status') }}
                                </flux:callout.heading>
                            </flux:table.column>
                            {{-- Value --}}
                            <flux:table.column>
                                <flux:callout.heading>
                                    {{ __('Value') }}
                                </flux:callout.heading>
                            </flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($group['values'] as $value)
                                <flux:table.row>
                                    {{-- Sequential Number --}}
                                    <flux:table.cell class="w-12">
                                        <flux:callout.text class="text-right tabular-nums">
                                            {{ $loop->index + 1 }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                    {{-- Locale Status ID --}}
                                    <flux:table.cell>
                                        <flux:callout.text class="flex items-center gap-2">
                                            <x-ui.locale.flag :locale="$value['locale']" />
                                            <span>{{ $value['locale'] }} · {{ $value['status'] }}
                                                (#{{ $value['id'] }})
                                            </span>
                                        </flux:callout.text>
                                    </flux:table.cell>
                                    {{-- Value --}}
                                    <flux:table.cell class="whitespace-normal break-all">
                                        <flux:callout.text>
                                            <span
                                                class="whitespace-pre-wrap">{{ $value['value'] ?? __('No value') }}</span>
                                        </flux:callout.text>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="2">
                                        <flux:callout.text>
                                            {{ __('No stored scalar values.') }}
                                        </flux:callout.text>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
                <flux:callout.text class="mt-2">
                    {{ __('Dynamic option values and published files are outside this scalar assignment snapshot.') }}
                </flux:callout.text>
            </flux:callout>
        @endforeach
    </div>
@endif
