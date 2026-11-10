<?php

use Gunreip\TranslationWorkbench\Generation\SourceScanRequired;

it('keeps source coordinates separate from the exception location', function () {
    try {
        throw new SourceScanRequired(522, 'example.blade.php', 58, 24);
    } catch (SourceScanRequired $error) {
        expect($error)->toBeInstanceOf(RuntimeException::class)
            ->and($error->details())->toBe([
                'finding' => 522,
                'path' => 'example.blade.php',
                'line' => 58,
                'column' => 24,
            ])
            ->and($error->getFile())->toBe(__FILE__)
            ->and($error->getMessage())->toContain('A new scan is required');
    }
});
