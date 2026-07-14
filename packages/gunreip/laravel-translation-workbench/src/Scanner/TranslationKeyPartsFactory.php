<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Illuminate\Support\Collection;

class TranslationKeyPartsFactory
{
    /**
     * Split a translation key into the workbench key context.
     *
     * The current rule is:
     * - namespace: first segment
     * - group: second segment
     * - scope: penultimate segment
     * - path_key: segments between group and scope
     *
     * Example:
     * management.people.edit_person.sections.person_core.not_set
     * => namespace management, group people, path_key edit_person.sections,
     *    scope person_core.
     *
     * Do not change this rule silently. Suggested key context must be stable
     * across scanners, reports, DB sync and UI review.
     *
     * @return array{namespace: string|null, group: string|null, path_key: string|null, scope: string|null}
     */
    public function fromKey(?string $key): array
    {
        $segments = collect(explode('.', trim((string) $key, '.')))
            ->filter()
            ->values();

        $namespace = $segments->get(0);
        $group = $segments->get(1);
        $scope = $segments->count() > 2
            ? $segments->get($segments->count() - 2)
            : null;
        $pathSegments = $this->pathSegments($segments);

        return [
            'namespace' => is_string($namespace) && $namespace !== '' ? $namespace : null,
            'group' => is_string($group) && $group !== '' ? $group : null,
            'path_key' => $pathSegments->isNotEmpty() ? $pathSegments->implode('.') : null,
            'scope' => is_string($scope) && $scope !== '' ? $scope : null,
        ];
    }

    /**
     * @param  Collection<int, string>  $segments
     * @return Collection<int, string>
     */
    private function pathSegments(Collection $segments): Collection
    {
        if ($segments->count() <= 4) {
            return collect();
        }

        return $segments
            ->slice(2, $segments->count() - 4)
            ->values();
    }
}
