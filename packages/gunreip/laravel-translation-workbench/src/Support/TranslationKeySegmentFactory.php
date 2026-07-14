<?php

namespace Gunreip\TranslationWorkbench\Support;

class TranslationKeySegmentFactory
{
    /**
     * Split a translation key into filterable UI segments.
     *
     * Rule:
     * - segment 0: namespace
     * - segment 1: group
     * - last segment after group: key_segment_name
     * - middle segments after group: domain, section, context, extra
     *
     * Examples:
     * management.people.edit_person.sections.person_core.first_name
     * => domain edit_person, section sections, context person_core, extra null, name first_name
     *
     * management.people.edit_person.first_name
     * => domain edit_person, section null, context null, extra null, name first_name
     *
     * @return array{key_segment_domain: string|null, key_segment_section: string|null, key_segment_context: string|null, key_segment_extra: string|null, key_segment_name: string|null}
     */
    public function fromKey(?string $key): array
    {
        $segments = collect(explode('.', trim((string) $key, '.')))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();

        $keySegments = $segments->slice(2)->values();
        $name = $keySegments->isNotEmpty() ? $keySegments->last() : null;
        $middle = $keySegments->count() > 1
            ? $keySegments->slice(0, -1)->values()
            : collect();

        return [
            'key_segment_domain' => $this->nullableSegment($middle->get(0)),
            'key_segment_section' => $this->nullableSegment($middle->get(1)),
            'key_segment_context' => $this->nullableSegment($middle->get(2)),
            'key_segment_extra' => $middle->count() > 3
                ? $middle->slice(3)->implode('.')
                : null,
            'key_segment_name' => $this->nullableSegment($name),
        ];
    }

    private function nullableSegment(mixed $segment): ?string
    {
        $segment = trim((string) $segment);

        return $segment !== '' ? $segment : null;
    }
}
