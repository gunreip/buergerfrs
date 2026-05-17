<?php

// app/Livewire/Admin/HtmlViewAudit.php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;

class HtmlViewAudit extends Component
{
    public string $sectionFilter = 'all';

    public string $typeFilter = 'all';

    public string $search = '';

    public function clearFilters(): void
    {
        $this->sectionFilter = 'all';
        $this->typeFilter = 'all';
        $this->search = '';
    }

    public function render(): View
    {
        $audit = $this->audit();
        $problems = $this->filteredProblems($audit);

        return view('components.admin.⚡html-view-audit', [
            'audit' => $audit,
            'nativeReferenceFile' => $this->nativeReferenceFile(),
            'problems' => $problems,
            'filteredProblemCount' => count($problems),
            'hasActiveFilters' => $this->hasActiveFilters(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function audit(): array
    {
        $path = storage_path('audits/html/view-html-check.json');
        $relativePath = 'storage/audits/html/view-html-check.json';

        if (! File::exists($path)) {
            return $this->emptyAudit(
                path: $relativePath,
                note: 'Audit file missing. Run php artisan html:check.',
            );
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return $this->emptyAudit(
                path: $relativePath,
                note: 'Audit file is not valid JSON. Run php artisan html:check again.',
            );
        }

        return [
            'exists' => true,
            'path' => $relativePath,
            'generated_at' => $payload['generated_at'] ?? null,
            'files_scanned' => (int) ($payload['files_scanned'] ?? 0),
            'problem_count' => (int) ($payload['problem_count'] ?? 0),
            'note' => $payload['note'] ?? null,
            'references' => is_array($payload['references'] ?? null) ? $payload['references'] : [],
            'sections' => is_array($payload['sections'] ?? null) ? $payload['sections'] : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyAudit(string $path, string $note): array
    {
        return [
            'exists' => false,
            'path' => $path,
            'generated_at' => null,
            'files_scanned' => 0,
            'problem_count' => 0,
            'note' => $note,
            'references' => [],
            'sections' => [
                'native_html' => [
                    'problem_count' => 0,
                    'problems' => [],
                ],
                'custom_components' => [
                    'problem_count' => 0,
                    'problems' => [],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function nativeReferenceFile(): array
    {
        $path = storage_path('audits/html/native-html-tags.json');
        $relativePath = 'storage/audits/html/native-html-tags.json';

        if (! File::exists($path)) {
            return [
                'exists' => false,
                'path' => $relativePath,
                'file_written_at' => null,
                'generated_at' => null,
                'source_name' => null,
                'source_url' => null,
                'normal_count' => 0,
                'void_count' => 0,
                'total_count' => 0,
                'hint' => 'Run php artisan html:sync-native-tags to refresh the WHATWG native HTML reference.',
            ];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return [
                'exists' => false,
                'path' => $relativePath,
                'file_written_at' => date('c', File::lastModified($path)),
                'generated_at' => null,
                'source_name' => null,
                'source_url' => null,
                'normal_count' => 0,
                'void_count' => 0,
                'total_count' => 0,
                'hint' => 'Native HTML reference file is invalid JSON. Run php artisan html:sync-native-tags again.',
            ];
        }

        return [
            'exists' => true,
            'path' => $relativePath,
            'file_written_at' => date('c', File::lastModified($path)),
            'generated_at' => $payload['generated_at'] ?? null,
            'source_name' => $payload['source']['name'] ?? null,
            'source_url' => $payload['source']['url'] ?? null,
            'normal_count' => (int) ($payload['counts']['normal'] ?? count($payload['tags']['normal'] ?? [])),
            'void_count' => (int) ($payload['counts']['void'] ?? count($payload['tags']['void'] ?? [])),
            'total_count' => (int) ($payload['counts']['total'] ?? 0),
            'hint' => 'Refresh occasionally with php artisan html:sync-native-tags.',
        ];
    }

    /**
     * @param  array<string, mixed>  $audit
     * @return array<int, array<string, mixed>>
     */
    private function problems(array $audit): array
    {
        $sections = is_array($audit['sections'] ?? null) ? $audit['sections'] : [];
        $problems = [];

        foreach ($sections as $sectionKey => $section) {
            if (! is_array($section)) {
                continue;
            }

            foreach (($section['problems'] ?? []) as $problem) {
                if (! is_array($problem)) {
                    continue;
                }

                $problem['section'] = $problem['section'] ?? $sectionKey;
                $problems[] = $problem;
            }
        }

        return $problems;
    }

    /**
     * @param  array<string, mixed>  $audit
     * @return array<int, array<string, mixed>>
     */
    private function filteredProblems(array $audit): array
    {
        $search = Str::lower(trim($this->search));

        return collect($this->problems($audit))
            ->filter(function (array $problem) use ($search): bool {
                if ($this->sectionFilter !== 'all' && ($problem['section'] ?? null) !== $this->sectionFilter) {
                    return false;
                }

                if ($this->typeFilter !== 'all' && ($problem['type'] ?? null) !== $this->typeFilter) {
                    return false;
                }

                if ($search === '') {
                    return true;
                }

                return Str::contains(Str::lower((string) ($problem['file'] ?? '')), $search)
                    || Str::contains(Str::lower((string) ($problem['tag'] ?? '')), $search)
                    || Str::contains(Str::lower((string) ($problem['closing_tag'] ?? '')), $search);
            })
            ->values()
            ->all();
    }

    private function hasActiveFilters(): bool
    {
        return $this->sectionFilter !== 'all'
            || $this->typeFilter !== 'all'
            || trim($this->search) !== '';
    }
}
