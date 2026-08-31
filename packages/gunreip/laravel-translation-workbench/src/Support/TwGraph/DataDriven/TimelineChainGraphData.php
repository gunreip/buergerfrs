<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven;

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\FindingInspector;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\GraphFacts;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\MergeOutcomes;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RenderPreviewBuilder;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RekeyPreviewBuilder;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\ValueNormalizer;
use Illuminate\Support\Collection;

/**
 * Public facade for building the data-driven timeline-chain graph payload.
 *
 * This class keeps the package-facing API stable and coordinates the specialized
 * builders below. It does not render graph components itself; component geometry
 * and visual output stay in the tw-graph Blade/CSS component chain.
 *
 * Related classes:
 * - GraphFacts: static trunk/merge/branch/rekey facts and component intent.
 * - MergeOutcomes: origin finding status grouping used by merge/branch previews.
 * - RenderPreviewBuilder: complete preview payload consumed by the Blade view.
 * - MergePreviewBuilder: data-driven merge and merge-extension strang props.
 * - BranchPreviewBuilder: data-driven branch and branch-extension strang props.
 * - BranchLabelCollisionResolver: branch stem-label overlap detection and bridge spacing.
 * - LayoutCorrectionConfig: graph-family correction deltas applied after calculated layout facts.
 * - RekeyPreviewBuilder: rekey-source/rekey-target facts and preview props.
 * - LangValueLabels: active source/target lang value node labels.
 * - LabelFormatter: shared graph label/timestamp/key formatting.
 * - LocaleResolver: active target locale lookup for graph-relevant labels.
 * - FindingInspector: focused debug/inspection payload for one finding.
 * - ValueNormalizer: small shared normalization helpers.
 */
final class TimelineChainGraphData
{
    /**
     * Build the first data-driven graph intent from an aggregated timeline-chain row.
     *
     * This is deliberately not a renderer. The result describes which graph strangs
     * should exist and which source rows motivated them; coordinates stay in the
     * component/rendering layer.
     *
     * @param  array<string, mixed>|null  $mainRow
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $rootRows
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function fromTimelineChain(
        ?array $mainRow,
        Collection|array $rootRows,
        Collection|array $originRows,
    ): array {
        if ($mainRow === null) {
            return [
                'state' => 'empty',
                'meta' => [
                    'reason' => 'No timeline-chain main row is available.',
                ],
                'strangs' => [],
            ];
        }

        $roots = collect($rootRows)->values();
        $origins = collect($originRows)->values();
        $translationKey = (string) ($mainRow['translation_key'] ?? '');
        $graphId = 'timeline-chain-' . (int) ($mainRow['id'] ?? 0);
        $mergeOutcomes = MergeOutcomes::from($mainRow, $origins);

        return [
            'state' => 'ready',
            'meta' => [
                'graph_id' => $graphId,
                'source' => 'translation_workbench_timeline_chains',
                'chain_id' => (int) ($mainRow['id'] ?? 0),
                'chain_type' => (string) ($mainRow['chain_type'] ?? 'single'),
                'chain_status' => (string) ($mainRow['chain_status'] ?? 'inactive'),
                'translation_key' => $translationKey,
            ],
            'facts' => [
                'key_ids' => ValueNormalizer::integerList($mainRow['key_ids'] ?? []),
                'finding_ids' => ValueNormalizer::integerList($mainRow['finding_ids'] ?? []),
                'review_ids' => ValueNormalizer::integerList($mainRow['review_ids'] ?? []),
                'timeline_event_ids' => ValueNormalizer::integerList($mainRow['timeline_event_ids'] ?? []),
                'lang_value_ids' => ValueNormalizer::integerList($mainRow['lang_value_ids'] ?? []),
                'related_translation_keys' => collect($mainRow['related_translation_keys'] ?? [])
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->filter()
                    ->values()
                    ->all(),
            ],
            'strangs' => [
                'trunk' => GraphFacts::trunk($mainRow, $roots),
                'merge' => GraphFacts::merge($origins),
                'branch' => GraphFacts::branch($roots),
                'rekey' => self::rekey($mainRow),
            ],
            'component_intent' => GraphFacts::componentIntent($mainRow, $roots, $origins),
            'render_preview' => RenderPreviewBuilder::build($mainRow, $roots, $origins, $mergeOutcomes),
            'merge_outcomes' => $mergeOutcomes,
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<string, mixed>
     */
    private static function rekey(array $mainRow): array
    {
        return RekeyPreviewBuilder::facts($mainRow);
    }

    /**
     * Build a focused inspection payload for deciding which finding details belong
     * directly in the graph and which should move into tooltips later.
     *
     * @param  array<string, mixed>|null  $mainRow
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function inspectFinding(int $findingId, ?array $mainRow, Collection|array $originRows): array
    {
        return FindingInspector::inspect($findingId, $mainRow, $originRows);
    }

}
