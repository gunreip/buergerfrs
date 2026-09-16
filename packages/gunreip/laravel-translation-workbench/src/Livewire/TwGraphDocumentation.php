<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class TwGraphDocumentation extends Component
{
    // Only navigation IDs are client state. View names remain in the authored Blade includes.
    private const TABS = [
        'main' => ['idea-to-paper-canvas', 'idea-to-paper-primitives', 'idea-to-paper-segments', 'idea-to-paper-parts', 'idea-to-paper-paths', 'idea-to-paper-trunk', 'idea-to-paper-merge', 'idea-to-paper-branch', 'idea-to-paper-rekey', 'idea-to-paper-flow'],
        'results' => ['idea-to-paper-draft', 'idea-to-paper-result', 'idea-to-paper-flow-result'],
        'canvas_canvas_props' => ['canvas-props-line', 'canvas-props-stem-length', 'canvas-props-node-size', 'canvas-props-cap-length', 'canvas-props-min-width'],
        'canvas_index' => ['canvas-default', 'canvas-borders', 'canvas-default-trunk', 'canvas-coordinates', 'canvas-height', 'canvas-props'],
        'flow_flow_if' => ['flow-if-simple', 'flow-if-else', 'flow-if-elseif', 'flow-if-elseif-multi', 'flow-if-ternary', 'flow-if-nested-1', 'flow-if-nested-2', 'flow-if-nested-test'],
        'flow_index' => ['flow-start', 'flow-step', 'flow-branch-steps', 'flow-if'],
        'parts_index' => ['idea-to-paper-parts-start', 'idea-to-paper-parts-end', 'idea-to-paper-parts-sideways', 'idea-to-paper-parts-chain'],
        'paths_index' => ['idea-to-paper-paths-trunk', 'idea-to-paper-paths-merge', 'idea-to-paper-paths-merge-extension', 'idea-to-paper-paths-branch', 'idea-to-paper-paths-branch-extension', 'idea-to-paper-paths-branch-return', 'idea-to-paper-paths-branch-return-extension', 'idea-to-paper-paths-branch-return-bridge'],
        'primitives_index' => ['idea-to-paper-primitives-line', 'idea-to-paper-primitives-arc', 'idea-to-paper-primitives-text-label', 'idea-to-paper-primitives-markers-connectors'],
        'primitives_primitives_markers_connectors' => ['primitives-markers-node', 'primitives-markers-joint-arrow', 'primitives-markers-connector'],
        'segments_index' => ['idea-to-paper-segments-path', 'idea-to-paper-segments-start-end', 'idea-to-paper-segments-arc', 'idea-to-paper-segments-labels', 'idea-to-paper-segments-step', 'idea-to-paper-segments-stem-compressed'],
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
