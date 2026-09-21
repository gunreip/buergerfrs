<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Attributes\Locked;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks;
use Livewire\Component;

class TwGraphDocumentation extends Component
{
    // Only navigation IDs are client state. View names remain in the authored Blade includes.
    private const TABS = [
        'main' => ['idea-to-paper-inventory', 'idea-to-paper-props-and-connections', 'idea-to-paper-canvas', 'idea-to-paper-primitives', 'idea-to-paper-segments', 'idea-to-paper-parts', 'idea-to-paper-paths', 'idea-to-paper-trunk', 'idea-to-paper-merge', 'idea-to-paper-branch', 'idea-to-paper-rekey', 'idea-to-paper-flow'],
        'reference_index' => ['reference-strang', 'reference-parts'],
        'reference_strang' => ['reference-flow-switch-case', 'reference-flow-start', 'reference-flow-step', 'reference-flow-if', 'reference-flow-if-else', 'reference-flow-if-elseif', 'reference-flow-if-elseif-multi', 'reference-flow-if-ternary', 'reference-trunk', 'reference-merge-left', 'reference-merge-right', 'reference-branch-left', 'reference-branch-right', 'reference-branch-end', 'reference-rekey-source-left', 'reference-rekey-source-right', 'reference-rekey-target-left', 'reference-rekey-target-right'],
        'reference_parts' => ['reference-parts-start', 'reference-parts-end', 'reference-parts-sideways', 'reference-parts-chain', 'reference-parts-fusion'],
        'results' => ['idea-to-paper-draft', 'idea-to-paper-result', 'idea-to-paper-flow-result'],
        'canvas_canvas_props' => ['canvas-props-line', 'canvas-props-stem-length', 'canvas-props-node-size', 'canvas-props-cap-length', 'canvas-props-min-width'],
        'canvas_index' => ['canvas-default', 'canvas-borders', 'canvas-default-trunk', 'canvas-coordinates', 'canvas-height', 'canvas-props'],
        'flow_flow_if' => ['flow-if-simple', 'flow-if-else', 'flow-if-elseif', 'flow-if-elseif-multi', 'flow-if-ternary', 'flow-if-nested-1', 'flow-if-nested-2', 'flow-if-nested-3', 'flow-if-nested-4', 'flow-if-nested-5', 'flow-if-nested-6', 'flow-if-nested-7', 'flow-if-nested-8', 'flow-if-nested-9'],
        'flow_index' => ['flow-start', 'flow-step', 'flow-branch-steps', 'flow-if', 'flow-switch-case', 'flow-while'],
        'flow_while' => ['flow-while-test'],
        'flow_switch_case' => ['flow-switch-case-default', 'flow-switch-case-grouped', 'flow-switch-case-grouped-3', 'flow-switch-case-grouped-multi', 'flow-switch-case-nested', 'flow-switch-case-without-default', 'flow-switch-case-fallthrough', 'flow-switch-case-nested-1', 'flow-switch-case-nested-2', 'flow-switch-case-two-nested-1', 'flow-switch-case-two-nested-2', 'flow-switch-case-two-nested-3', 'flow-switch-case-action-sequence'],
        'parts_index' => ['idea-to-paper-parts-start', 'idea-to-paper-parts-end', 'idea-to-paper-parts-sideways', 'idea-to-paper-parts-chain', 'idea-to-paper-parts-fusion'],
        'paths_index' => ['idea-to-paper-paths-trunk', 'idea-to-paper-paths-merge', 'idea-to-paper-paths-merge-extension', 'idea-to-paper-paths-branch', 'idea-to-paper-paths-branch-extension', 'idea-to-paper-paths-branch-return', 'idea-to-paper-paths-branch-return-extension', 'idea-to-paper-paths-branch-return-bridge', 'idea-to-paper-paths-stem-detour'],
        'primitives_index' => ['idea-to-paper-primitives-line', 'idea-to-paper-primitives-arc', 'idea-to-paper-primitives-line-jump', 'idea-to-paper-primitives-text-label', 'idea-to-paper-primitives-markers-connectors'],
        'primitives_primitives_markers_connectors' => ['primitives-markers-node', 'primitives-markers-joint-arrow', 'primitives-markers-connector'],
        'segments_index' => ['idea-to-paper-segments-path', 'idea-to-paper-segments-start-end', 'idea-to-paper-segments-arc', 'idea-to-paper-segments-labels', 'idea-to-paper-segments-step', 'idea-to-paper-segments-stem-compressed', 'idea-to-paper-segments-fusion'],
        'strang_branch_index' => ['branch-default', 'branch-offset', 'branch-step', 'branch-continuation', 'branch-return', 'branch-mismatch'],
        'strang_merge_index' => ['idea-to-paper-merge-base', 'idea-to-paper-merge-start', 'idea-to-paper-merge-mismatch', 'idea-to-paper-merge-extension', 'idea-to-paper-merge-aggregated'],
        'strang_rekey_index' => ['rekey-default', 'rekey-source', 'rekey-target', 'rekey-compressed'],
        'strang_trunk_index' => ['trunk-default', 'trunk-stem-count', 'trunk-stem-lengths', 'trunk-direction', 'trunk-start', 'trunk-start-shift', 'trunk-end'],
        'strang_trunk_trunk_end' => ['trunk-end-overview', 'trunk-end-default', 'trunk-end-long-end', 'trunk-end-wide-label', 'trunk-end-cap', 'trunk-end-color'],
        'strang_trunk_trunk_start' => ['trunk-start-overview', 'trunk-start-compare', 'trunk-start-default', 'trunk-start-long-start', 'trunk-start-wide-labels', 'trunk-start-spacing', 'trunk-start-colors'],
    ];

    #[Url(as: 'graph-tabs', history: true)]
    public array $tabs = [];

    public bool $dev = true;

    public bool $coordinates = false;

    public bool $inventoryArchive = false;

    public string $inventoryRoot = '';

    public int $inventoryPage = 1;

    public string $inventorySource = '';

    #[Locked]
    public ?string $referenceOrigin = null;

    #[Locked]
    public ?string $referenceOriginComponent = null;

    #[Locked]
    public array $referenceOriginTabs = [];

    public function openReference(string $component, ?string $example = null): void
    {
        $selection = DocumentationLinks::reference($component);
        if ($selection === null) {
            return;
        }
        $origin = $example !== null ? (DocumentationLinks::EXAMPLES[$example] ?? null) : null;
        if ($origin !== null && in_array($component, $origin['components'], true)) {
            $this->referenceOrigin = $example;
            $this->referenceOriginComponent = $component;
            $this->referenceOriginTabs = array_replace($this->tabs, $origin['tabs']);
        } else {
            $this->referenceOrigin = null;
            $this->referenceOriginComponent = null;
            $this->referenceOriginTabs = [];
        }
        $this->tabs = array_replace($this->tabs, $selection);
        $this->dispatch('tw-graph-documentation-navigate', target: 'tw-graph-documentation');
    }

    public function openExample(string $example): void
    {
        $destination = DocumentationLinks::EXAMPLES[$example] ?? null;
        if ($destination === null) {
            return;
        }
        $this->tabs = array_replace($this->tabs, $destination['tabs']);
        $this->dispatch('tw-graph-documentation-navigate', target: isset($destination['tabs']['results']) ? 'tw-graph-documentation-results' : 'tw-graph-documentation');
    }

    public function returnToExample(): void
    {
        if ($this->referenceOrigin === null || !isset(DocumentationLinks::EXAMPLES[$this->referenceOrigin])) {
            return;
        }
        $this->tabs = $this->referenceOriginTabs;
        $this->openExample($this->referenceOrigin);
    }

    public function updatedInventoryRoot(): void
    {
        $this->inventoryPage = 1;
    }

    public function updatedInventoryArchive(): void
    {
        $this->inventoryRoot = '';
        $this->inventoryPage = 1;
        $this->inventorySource = '';
    }

    public function render(): View
    {
        if (($this->tabs['flow_flow_if'] ?? null) === 'flow-if-true-false') {
            $this->tabs['flow_flow_if'] = 'flow-if-else';
        }
        if (in_array($this->tabs['flow_index'] ?? null, ['flow-if-true-false', 'flow-if-else'], true)) {
            $this->tabs['flow_index'] = 'flow-if';
            $this->tabs['flow_flow_if'] = 'flow-if-else';
        }

        $selection = [];
        foreach (self::TABS as $group => $options) {
            $value = $this->tabs[$group] ?? null;
            $selection[$group] = in_array($value, $options, true) ? $value : $options[0];
        }
        $this->tabs = $selection;

        return view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index', [
            'documentationTabs' => $selection,
        ]);
    }
}
