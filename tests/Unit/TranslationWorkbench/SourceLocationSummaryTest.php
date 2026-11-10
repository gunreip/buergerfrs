<?php

use Gunreip\TranslationWorkbench\Generation\SourceLocationSummary;

it('detects stale and missing source locations without treating archived findings as inputs', function () {
    $root = sys_get_temp_dir().'/tw-location-'.bin2hex(random_bytes(8));
    mkdir($root);
    file_put_contents($root.'/example.php', "<?php\n__('Save');\n");
    $row = ['id' => 1, 'path' => 'example.php', 'source_line' => 2, 'source_column' => 1, 'raw_expression' => "__('Save')"];
    $service = new SourceLocationSummary;
    try {
        expect($service->inspect([$row], $root)['required'])->toBeFalse();
        file_put_contents($root.'/example.php', "<?php\n\n__('Save');\n");
        $summary = $service->inspect([$row, array_replace($row, ['id' => 2, 'path' => 'missing.php'])], $root);
        expect($summary['findings'])->toBe(2)->and($summary['files'])->toBe(2)
            ->and($summary['examples'][0]['finding'])->toBe(1);
        expect($service->inspect([array_replace($row, ['source_line' => 3])], $root)['required'])->toBeFalse();
    } finally {
        unlink($root.'/example.php');
        rmdir($root);
    }
});
