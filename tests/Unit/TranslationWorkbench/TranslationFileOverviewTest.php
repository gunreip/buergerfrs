<?php

use Gunreip\TranslationWorkbench\Generation\TranslationFileOverview;

it('distinguishes publication receipts from file presence and missing translations without executing files', function () {
    $root = sys_get_temp_dir().'/tw-file-overview-'.bin2hex(random_bytes(6));
    mkdir($root);
    $fixtures = [
        'en' => "<?php return ['table'=>['reason'=>'Reason']];",
        'de' => "<?php return ['table'=>['reason'=>'Grund']];",
        'de-at' => "<?php return ['table'=>['reason'=>'Begründung']];",
        'de-ch' => "<?php return ['table'=>[]];",
    ];
    foreach ($fixtures as $locale => $bytes) {
        mkdir($root.'/'.$locale);
        file_put_contents($root.'/'.$locale.'/ui.php', $bytes);
    }
    try {
        $published = [['locale' => 'en', 'full_name' => 'ui.table.reason', 'revision' => 1, 'value' => 'Reason'],
            ['locale' => 'de', 'full_name' => 'ui.table.reason', 'revision' => 2, 'value' => 'Grund']];
        $saved = [['locale' => 'en', 'id' => 1], ['locale' => 'de', 'id' => 3], ['locale' => 'de-LI', 'id' => 4]];
        $rows = (new TranslationFileOverview)->inspect($root, 'ui.table.reason', ['en', 'de', 'de-AT', 'de-CH', 'de-LI'], $saved, $published);
        expect(array_column($rows, 'state'))->toBe(['published', 'published_previous', 'present', 'unavailable'])
            ->and(array_column($rows, 'locale'))->toBe(['en', 'de', 'de-AT', 'de-LI'])
            ->and($rows[2]['path'])->toBe('de-at/ui.php')->and($rows[3]['actual'])->toBeFalse();
        $missingKey = (new TranslationFileOverview)->inspect($root, 'ui.table.reason', ['de-CH'], [['locale' => 'de-CH', 'id' => 6]], []);
        expect($missingKey[0]['state'])->toBe('key_missing');
        expect((new TranslationFileOverview)->inspect($root, 'ui.table.other', ['en', 'de', 'de-LI'], [], [])[0]['locale'])->toBe('en');
        file_put_contents($root.'/en/ui.php', "<?php return ['table'=>['reason'=>'Altered']];");
        expect((new TranslationFileOverview)->inspect($root, 'ui.table.reason', ['en'], $saved, $published)[0]['state'])->toBe('changed');
        mkdir($root.'/de-AT');
        expect((new TranslationFileOverview)->inspect($root, 'ui.table.reason', ['de-AT'], [['locale' => 'de-AT', 'id' => 5]], [])[0]['detail'])->toContain('Several directories');
        rmdir($root.'/de-AT');
    } finally {
        foreach ($fixtures as $locale => $bytes) {
            unlink($root.'/'.$locale.'/ui.php');
            rmdir($root.'/'.$locale);
        }
        rmdir($root);
    }
});
