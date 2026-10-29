<?php

namespace Tests\Support\TranslationWorkbench;

/** An option-owner call proves the source context, not the runtime use of every option. */
final class UnassignedValueContext
{
    public function classify(string $key, array $sources): array
    {
        $matches = [];
        foreach ($sources as $source) {
            $owner = $source['arguments']['owner'];
            $prefix = $owner.'.options.';
            if (! str_starts_with($key, $prefix)) {
                continue;
            }
            $segment = substr($key, strlen($prefix));
            $identity = rawurldecode($segment);
            // Mirror the saved helper encoding, preserving case and never guessing nested identities.
            if ($identity === '' || str_replace('.', '%2E', rawurlencode($identity)) !== $segment) {
                continue;
            }
            $matches[$owner]['identity'] = $identity;
            $matches[$owner]['sources'][] = ['capture_id' => $source['capture_id'], 'path' => $source['source_path'],
                'line' => $source['source_line'], 'sha256' => $source['source_sha256'],
                'identity_expression' => $source['arguments']['identity']['expression'],
                'fallback_expression' => $source['arguments']['fallback']['expression']];
        }
        if (count($matches) === 1) {
            $owner = array_key_first($matches);

            return ['kind' => 'option_source_evidenced', 'owner' => $owner, ...$matches[$owner], 'runtime_identity_proven' => false];
        }

        return ['kind' => count($matches) > 1 ? 'ambiguous_option_source' : 'source_assignment_open',
            'owner' => null, 'identity' => null, 'sources' => [], 'runtime_identity_proven' => false];
    }
}
