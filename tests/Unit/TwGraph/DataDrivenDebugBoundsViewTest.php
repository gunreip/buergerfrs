<?php

use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the data driven debug bounds table columns and controls visible in the source', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain("{{ __('Debug bounds') }}")
        ->toContain('twGraphDebugBoundsCollisionOnly')
        ->toContain("{{ __('Collisions only') }}")
        ->toContain("{{ __('#') }}")
        ->toContain("{{ __('Scope') }}")
        ->toContain("{{ __('Side') }}")
        ->toContain("{{ __('Type') }}")
        ->toContain("{{ __('Element ID') }}")
        ->toContain("{{ __('Coordinates') }}")
        ->toContain("{{ __('Dimension') }}")
        ->toContain("{{ __('Collision Type') }}")
        ->toContain("{{ __('Collision') }}")
        ->toContain("{{ __('Collision Delta') }}")
        ->toContain("{{ __('Applied Compensation') }}")
        ->toContain("{{ __('Applied Correction') }}")
        ->toContain("{{ filled(data_get(\$debugBoundRow, 'side')) ? data_get(\$debugBoundRow, 'side') : 'center' }}");
});

it('keeps debug bounds rows wired to normalized display ids and copyable raw ids', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain("'debug_no' => \$twGraphDebugBoundNumberById[\$debugBoundId] ?? ''")
        ->toContain("'display_id' => \\Gunreip\\TranslationWorkbench\\Support\\TwGraph\\DevIdentifier::label")
        ->toContain("title=\"{{ data_get(\$debugBoundRow, 'id') }}\"")
        ->toContain("navigator.clipboard?.writeText(@js(data_get(\$debugBoundRow, 'id')))")
        ->toContain("{{ data_get(\$debugBoundRow, 'display_id') }}")
        ->toContain("{{ data_get(\$debugBoundRow, 'debug_no') }}");
});

it('keeps collision diagnostics split between calculated and applied values', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain("'collision_type' => \$twGraphDebugCollisionTypeById[\$debugBoundId] ?? 'none'")
        ->toContain("'collision_delta' =>")
        ->toContain("'applied_compensation' =>")
        ->toContain("'applied_correction' =>")
        ->toContain("(array) (\$twGraphDebugCollisionDeltaById[\$debugBoundId] ?? [])")
        ->toContain("(array) (\$twGraphDebugAppliedCompensationById[\$debugBoundId] ?? [])")
        ->toContain("(array) (\$twGraphDebugAppliedCorrectionById[\$debugBoundId] ?? [])")
        ->toContain(")->join(' | ')")
        ->toContain("{{ __('Collision Delta') }}")
        ->toContain("{{ __('Applied Compensation') }}")
        ->toContain("{{ __('Applied Correction') }}");
});

it('keeps debug bounds table rows filterable by collision yes without dropping non collision data', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain('$twGraphDebugCollisionBoundRows = $twGraphDebugBoundRows->where(\'collision\', true)->values();')
        ->toContain("x-show=\"!twGraphDebugBoundsCollisionOnly || @js((bool) data_get(\$debugBoundRow, 'collision'))\"")
        ->toContain("data_get(\$debugBoundRow, 'collision') ? 'red' : 'zinc'")
        ->toContain("x={{ data_get(\$debugBoundRow, 'x') }}")
        ->toContain("w={{ data_get(\$debugBoundRow, 'width') }}");
});

it('keeps collision details in the debug bounds table instead of graph badges', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain("{{ __('Debug bounds') }}")
        ->toContain("{{ __('Collision Delta') }}")
        ->not->toContain('branch collision L:')
        ->not->toContain('branch collision R:')
        ->not->toContain('Branch end segment overlaps branch bridge');
});

it('keeps debug bounds table badges readable for scope side and collision severity', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain("data_get(\$debugBoundRow, 'scope') === 'trunk' ? 'green'")
        ->toContain("data_get(\$debugBoundRow, 'scope') === 'merge' ? 'amber' : 'sky'")
        ->toContain("filled(data_get(\$debugBoundRow, 'side')) ? data_get(\$debugBoundRow, 'side') : 'center'")
        ->toContain("data_get(\$debugBoundRow, 'collision_type') === 'sub' ? 'red'")
        ->toContain("data_get(\$debugBoundRow, 'collision_type') === 'main-sub' ? 'amber'")
        ->toContain("data_get(\$debugBoundRow, 'collision_type') === 'main' ? 'zinc'");
});

it('keeps data driven dev switches and debug sections gated by the dev prop contract', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven',
    ));

    expect($source)
        ->toContain('$twGraphDataDrivenDevEnabled = filter_var($dev ?? true')
        ->toContain('$twGraphDataDrivenCoordinatesEnabled = filter_var($coordinates ?? true')
        ->toContain('@if ($twGraphDataDrivenDevEnabled)')
        ->toContain('x-on:click.stop="twGraphDataDrivenDev = !twGraphDataDrivenDev"')
        ->toContain('@if ($twGraphDataDrivenCoordinatesEnabled)')
        ->toContain('x-on:click.stop="twGraphDataDrivenCoordinates = !twGraphDataDrivenCoordinates"')
        ->toContain("'tw-graph-protocol-dev-disabled': !twGraphDataDrivenDev")
        ->toContain("'tw-graph-protocol-coordinates-disabled': !twGraphDataDrivenCoordinates")
        ->toContain(':dev="$twGraphDataDrivenDevEnabled"')
        ->toContain(':coordinates="$twGraphDataDrivenCoordinatesEnabled"')
        ->toContain('x-show="twGraphDataDrivenDev"');
});
