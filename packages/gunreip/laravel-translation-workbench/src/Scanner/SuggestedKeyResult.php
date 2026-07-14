<?php

namespace Gunreip\TranslationWorkbench\Scanner;

final class SuggestedKeyResult
{
    /**
     * @param  array<int, string>  $pathSegments
     */
    public function __construct(
        public readonly string $key,
        public readonly ?string $namespace,
        public readonly ?string $group,
        public readonly array $pathSegments = [],
        public readonly ?string $keyName = null,
    ) {}
}
