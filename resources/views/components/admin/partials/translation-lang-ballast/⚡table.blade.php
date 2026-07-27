{{-- resources/views/components/admin/partials/translation-lang-ballast/⚡table.blade.php --}}

{{-- PHP Values --}}
@php
    $activeActionFilter = $activeActionFilter ?? 'action_files';

    $isActionFilesTab = $activeActionFilter === 'action_files';

    $tableRows = $isActionFilesTab ? $actionFileRows : $actionRows;

    $tableTitle = match ($activeActionFilter) {
        'remove' => __('Lang cleanup candidates'),
        'add' => __('Missing in lang'),
        'base_duplicates' => __('Sub-language duplicates'),
        'review' => __('Needs review'),
        default => __('Affected lang files'),
    };

    $tableDescription = match ($activeActionFilter) {
        'remove' => __(
            'Entries that exist in lang/* but are no longer exportable from the current database state. These are lang file cleanup candidates, not database delete candidates.',
        ),
        'add' => __('Exportable database entries that are currently missing from lang/* files.'),
        'base_duplicates' => __(
            'Sub-language values that are identical to their main language value. They are intentionally not exported, but remain visible for later database cleanup.',
        ),
        'review' => __(
            'Entries where the audit cannot safely derive an automatic lang file cleanup or add recommendation.',
        ),
        default => __('Lang files grouped by generated audit candidates from the latest lang ballast audit.'),
    };

    $decisionStatusMeta = [
        'open' => [
            'label' => __('ui.open'),
            'color' => 'amber',
        ],
        'reviewed' => [
            'label' => __('admin.translation_list.modal_edit.reviewed'),
            'color' => 'sky',
        ],
        'approved' => [
            'label' => __('ui.approved'),
            'color' => 'emerald',
        ],
        'ignored' => [
            'label' => __('Ignored'),
            'color' => 'zinc',
        ],
    ];
@endphp

{{-- Card --}}
<flux:card class="mt-6">
    {{-- Card Header --}}
    <x-ui.headers.card
        :title="$tableTitle"
        :description="$tableDescription"
    />

    @if ($tableRows->hasPages())
        {{-- Pagination Top --}}
        <div
            class="my-2"
            id="translation-lang-ballast-pagination-top"
        >
            <x-ui.table.pagination
                class="m-0! p-0!"
                id="translation-lang-ballast-pagination-top"
                :paginator="$tableRows"
                scroll-to="#translation-lang-ballast-pagination-top"
            />
        </div>
    @endif

    {{-- Table-Part --}}
    <div
        class="mx-auto max-w-full scroll-mt-6"
        id="translation-lang-ballast-table"
    >
        <div class="overflow-hidden rounded-t-lg">

            @if ($isActionFilesTab)
                @include('components.admin.partials.translation-lang-ballast.table.⚡table-action-files')
            @else
                @include('components.admin.partials.translation-lang-ballast.table.⚡table-lang-cleanup')
            @endif
        </div>
    </div>

    @if ($tableRows->hasPages())
        {{-- Pagination Bottom --}}
        <div class="mt-2">
            <x-ui.table.pagination
                :paginator="$tableRows"
                scroll-to="#translation-lang-ballast-pagination-top"
            />
        </div>
    @endif

</flux:card>
