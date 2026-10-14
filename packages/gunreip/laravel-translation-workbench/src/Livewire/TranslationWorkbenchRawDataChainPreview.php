<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Illuminate\Contracts\View\View;

/** Render the existing chain details independently from the paginated raw table. */
class TranslationWorkbenchRawDataChainPreview extends TranslationWorkbenchRawData
{
    use Concerns\MeasuresTimelineChainLayout;

    public function mount(string $previewId = 'auto'): void
    {
        $this->timelineChainPreviewId = $previewId;
        $this->timelineChainId = ctype_digit($previewId) ? (int) $previewId : null;
    }

    public function placeholder(): View
    {
        return view('translation-workbench::livewire.raw-data.timeline-chains.loading');
    }

    public function render(): View
    {
        $table = 'translation_workbench_timeline_chains';

        $mainRow = $this->timelineChainMainRow($table);
        if ($mainRow !== null) {
            $mainRow['dataset_context'] = true;
        }
        $this->timelineChainId = isset($mainRow['id']) ? (int) $mainRow['id'] : null;

        return view('translation-workbench::livewire.raw-data.timeline-chains.lazy-preview', [
            'mainRow' => $mainRow,
            'rootRows' => collect($this->timelineChainRootRows($table)),
            'originRows' => collect($this->timelineChainOriginRows($table)),
            'sampleRows' => collect($this->timelineChainSampleRows($table)),
        ]);
    }
}
