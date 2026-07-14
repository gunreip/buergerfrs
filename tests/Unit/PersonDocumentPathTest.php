<?php

use App\Support\Documents\PersonDocumentPath;

test('person document paths are sharded by uuid and do not expose person ids', function (): void {
    $path = PersonDocumentPath::relativePath('123e4567-e89b-12d3-a456-426614174000', 'PDF');

    expect($path)->toBe('person-documents/12/3e/45/123e4567-e89b-12d3-a456-426614174000.pdf')
        ->and($path)->not->toContain('/1/');
});
