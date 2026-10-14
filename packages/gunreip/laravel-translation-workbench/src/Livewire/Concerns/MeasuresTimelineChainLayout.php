<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
use Livewire\Attributes\Locked;

trait MeasuresTimelineChainLayout
{
    #[Locked]
    public array $horizontalLabelLayout = [];

    /** Bound work by layout complexity; one repeatedly failing prop cannot grow its own budget. */
    public static function measuredLayoutPassLimit(array $state): int
    {
        return max(40, count($state['increments'] ?? []) * 4);
    }

    public function extendMeasuredLabelLayout(int $datasetId, int $tick, string $key, float $increment, int $pass, string $reason = 'label'): void
    {
        if ($datasetId !== $this->timelineChainId || $tick !== $this->reloadTick) {
            $this->skipRender();

            return;
        }
        if (! preg_match('/^((merges|rekeys|branches)\.\d+\.(bridge_length|entry_stem_length|extension_bridge_continuations\.\d+|stem_length|start_length|end_length|step\.(beforeLength|afterLength)|stem_continuation\.\d+|extension_stem_lengths\.\d+|extension_stem_continuations\.\d+\.\d+)|trunk\.(start_length|end_length|path_lengths\.[1-9]\d*))$/D', $key)
            || ! in_array($reason, ['label', 'line', 'horizontal'], true)
            || ! is_finite($increment) || $increment <= 0 || $increment > 200) {
            abort(422, 'Invalid measured layout adjustment.');
        }
        $scope = $datasetId.':'.$tick;
        $state = ($this->horizontalLabelLayout['scope'] ?? null) === $scope
            ? $this->horizontalLabelLayout : ['scope' => $scope, 'passes' => 0, 'increments' => []];
        if ($state['passes'] !== $pass || $state['passes'] >= self::measuredLayoutPassLimit($state)) {
            $this->skipRender();

            return;
        }
        $state['increments'][$key] = ($state['increments'][$key] ?? 0) + $increment;
        if ($reason === 'line' && str_starts_with($key, 'trunk.')) {
            $state['lineIncrements'][$key] = ($state['lineIncrements'][$key] ?? 0) + $increment;
        }
        if (str_contains($key, 'bridge')) {
            $state['horizontalRevision'] = ($state['horizontalRevision'] ?? 0) + 1;
        }
        $state['passes']++;
        $this->horizontalLabelLayout = $state;
    }

    /** Re-measure only temporary line clearance after horizontal placement has changed. */
    public function recheckMeasuredTrunkClearance(int $datasetId, int $tick, int $pass): void
    {
        $state = $this->horizontalLabelLayout;
        if ($datasetId !== $this->timelineChainId || $tick !== $this->reloadTick
            || ($state['scope'] ?? null) !== $datasetId.':'.$tick
            || ($state['passes'] ?? 0) !== $pass || $pass >= self::measuredLayoutPassLimit($state)
            || empty($state['lineIncrements'])
            || ($state['horizontalRevision'] ?? 0) <= ($state['recheckedRevision'] ?? 0)) {
            $this->skipRender();

            return;
        }
        foreach ($state['lineIncrements'] as $key => $extra) {
            $state['increments'][$key] = max(0, ($state['increments'][$key] ?? 0) - $extra);
        }
        $state['lineIncrements'] = [];
        $state['recheckedRevision'] = $state['horizontalRevision'];
        $state['passes']++;
        $this->horizontalLabelLayout = $state;
    }

    public function reduceMeasuredMergeBridges(int $datasetId, int $tick, array $reductions, int $pass): void
    {
        $state = $this->horizontalLabelLayout;
        if ($datasetId !== $this->timelineChainId || $tick !== $this->reloadTick
            || ($state['scope'] ?? null) !== $datasetId.':'.$tick
            || ($state['passes'] ?? 0) !== $pass) {
            $this->skipRender();

            return;
        }
        foreach ($reductions as $key => $amount) {
            if (! preg_match('/^merges\.\d+\.bridge_length$/D', $key)
                || ! is_numeric($amount) || ! is_finite((float) $amount) || $amount <= 0
                || $amount > ($state['increments'][$key] ?? 0)) {
                abort(422, 'Invalid measured bridge reduction.');
            }
        }
        foreach ($reductions as $key => $amount) {
            if (isset($state['compactedBridges'][$key])) {
                continue;
            }
            $state['increments'][$key] -= (float) $amount;
            $state['compactedBridges'][$key] = true;
        }
        $state['passes'] = min(self::measuredLayoutPassLimit($state), $state['passes'] + 1);
        $state['compactionRevision'] = ($state['compactionRevision'] ?? 0) + 1;
        $this->horizontalLabelLayout = $state;
    }

    /** Apply a verified two-axis candidate atomically; neither half is rendered on its own. */
    public function compactMeasuredMerge(int $datasetId, int $tick, array $candidate, int $pass): void
    {
        $state = $this->horizontalLabelLayout;
        if ($datasetId !== $this->timelineChainId || $tick !== $this->reloadTick
            || ($state['scope'] ?? null) !== $datasetId.':'.$tick
            || ($state['passes'] ?? 0) !== $pass) {
            $this->skipRender();

            return;
        }
        $bridge = $candidate['bridge'] ?? '';
        if (is_string($bridge) && isset($state['compactedBridges'][$bridge])) {
            $this->skipRender();

            return;
        }
        $stem = $candidate['stem'] ?? '';
        $reduction = $candidate['reduction'] ?? null;
        $addition = $candidate['addition'] ?? null;
        if (! is_string($bridge) || ! preg_match('/^merges\.(\d+)\.bridge_length$/D', $bridge, $match)
            || ! is_string($stem) || ! preg_match('/^merges\.'.$match[1].'\.stem_continuation\.\d+$/D', $stem)
            || ! is_numeric($reduction) || ! is_finite((float) $reduction) || $reduction <= 0
            || ! is_numeric($addition) || ! is_finite((float) $addition) || $addition <= 0
            || $reduction > ($state['increments'][$bridge] ?? 0)
            || $addition > Defaults::dataDrivenRem('merge_compaction_stem_budget', '3rem')
            || $reduction > Defaults::dataDrivenRem('merge_compaction_bridge_budget', '8rem')) {
            abort(422, 'Invalid coupled merge compensation.');
        }
        $extensions = $candidate['extensions'] ?? [];
        if (! is_array($extensions) || count($extensions) > 1) {
            abort(422, 'Invalid coupled extension compensation.');
        }
        foreach ($extensions as $extension) {
            if (! is_array($extension) || ! is_string($extension['stem'] ?? null)
                || ! preg_match('/^merges\.'.$match[1].'\.extension_stem_continuations\.1\.\d+$/D', $extension['stem'])
                || ! is_numeric($extension['addition'] ?? null) || ! is_finite((float) $extension['addition'])
                || $extension['addition'] <= 0 || $extension['addition'] > $addition + 0.0001) {
                abort(422, 'Invalid coupled extension compensation.');
            }
        }
        $state['increments'][$bridge] -= (float) $reduction;
        $state['increments'][$stem] = ($state['increments'][$stem] ?? 0) + (float) $addition;
        $state['compactedBridges'][$bridge] = true;
        $state['coupledMerges'][$bridge] = true;
        foreach ($extensions as $extension) {
            $key = $extension['stem'];
            $state['increments'][$key] = ($state['increments'][$key] ?? 0) + (float) $extension['addition'];
        }
        $state['passes'] = min(self::measuredLayoutPassLimit($state), $state['passes'] + 1);
        $state['compactionRevision'] = ($state['compactionRevision'] ?? 0) + 1;
        $this->horizontalLabelLayout = $state;
    }

    /** One atomic post-layout optimization; it does not restart the collision solver. */
    public function compactMeasuredExtensions(int $datasetId, int $tick, array $candidates, int $pass): void
    {
        $state = $this->horizontalLabelLayout;
        if ($datasetId !== $this->timelineChainId || $tick !== $this->reloadTick
            || ($state['scope'] ?? null) !== $datasetId.':'.$tick
            || ($state['passes'] ?? 0) !== $pass || ! empty($state['extensionCompactionDone'])) {
            $this->skipRender();

            return;
        }
        if ($candidates === [] || count($candidates) > 100) {
            abort(422, 'Invalid extension compensation.');
        }
        $seen = [];
        foreach ($candidates as $candidate) {
            $bridge = $candidate['bridge'] ?? null;
            $stem = $candidate['stem'] ?? null;
            $reduction = $candidate['reduction'] ?? null;
            $addition = $candidate['addition'] ?? null;
            if (! is_string($bridge) || ! preg_match('/^merges\.(\d+)\.extension_bridge_continuations\.(\d+)$/D', $bridge, $match)
                || ! in_array((int) $match[2], Defaults::dataDriven('merge_extension_compaction_indices', [3, 4]), true)
                || ! is_string($stem) || ! preg_match('/^merges\.'.$match[1].'\.extension_stem_continuations\.'.$match[2].'\.\d+$/D', $stem)
                || isset($seen[$bridge]) || isset($state['compactedBridges'][$bridge])
                || ! is_numeric($reduction) || ! is_finite((float) $reduction) || $reduction <= 0
                || $reduction > ($state['increments'][$bridge] ?? 0)
                || ! is_numeric($addition) || ! is_finite((float) $addition) || $addition < 0 || $addition > 200) {
                abort(422, 'Invalid extension compensation.');
            }
            $seen[$bridge] = true;
        }
        foreach ($candidates as $candidate) {
            $state['increments'][$candidate['bridge']] -= (float) $candidate['reduction'];
            if ($candidate['addition'] > 0) {
                $state['increments'][$candidate['stem']] = ($state['increments'][$candidate['stem']] ?? 0) + (float) $candidate['addition'];
            }
            $state['compactedBridges'][$candidate['bridge']] = true;
        }
        $state['extensionCompactionDone'] = true;
        $this->horizontalLabelLayout = $state;
    }

}
