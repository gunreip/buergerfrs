<?php

use Tests\Support\TranslationWorkbench\PublishedValueBaseline;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$root = sys_get_temp_dir().'/published-values-'.bin2hex(random_bytes(6));
mkdir($root.'/lang/en', 0700, true);
mkdir($root.'/lang/de-at', 0700, true);
$files = [];
$contents = [
    'lang/en/ui.php' => "<?php return ['button'=>['save'=>'Save', 'empty'=>'', 'zero'=>'0', 'line'=>\"A\\nB\"]];",
    'lang/de-at/ui.php' => "<?php return ['button'=>['save'=>'Speichern']];",
];
foreach ($contents as $path => $source) {
    file_put_contents($root.'/'.$path, $source);
    $files[$path] = ['sha256' => hash('sha256', $source)];
}
$files['lang/vendor/example/en/messages.php'] = ['sha256' => str_repeat('a', 64)];
$count = 0;
$check = static function ($ok) use (&$count): void {
    if (! $ok) {
        throw new RuntimeException('Baseline assertion failed: '.($count + 1));
    } $count++;
};
try {
    $reader = new PublishedValueBaseline;
    $e = $reader->read($root, $files);
    $check(count($e['sources']) === 2 && count($e['excluded']) === 1 && count($e['values']) === 5);
    $values = [];
    foreach ($e['values'] as $v) {
        $values[$v['locale'].':'.$v['key']] = $v['value'];
    }
    $check($values['en:ui.button.empty'] === '' && $values['en:ui.button.zero'] === '0');
    $check($values['en:ui.button.line'] === "A\nB");
    $check(! isset($values['de-at:ui.button.empty']) && $values['de-at:ui.button.save'] === 'Speichern');
    $changed = $files;
    $changed['lang/en/ui.php']['sha256'] = str_repeat('0', 64);
    $blocked = false;
    try {
        $reader->read($root, $changed);
    } catch (RuntimeException) {
        $blocked = true;
    } $check($blocked);
    $source = "<?php return ['button'=>['save'=>strtoupper('save')]];";
    file_put_contents($root.'/lang/en/ui.php', $source);
    $files['lang/en/ui.php']['sha256'] = hash('sha256', $source);
    $blocked = false;
    try {
        $reader->read($root, $files);
    } catch (RuntimeException) {
        $blocked = true;
    } $check($blocked);
    echo $count." published-baseline assertions passed\n";
} finally {
    foreach (array_keys($contents) as $path) {
        unlink($root.'/'.$path);
    }
    rmdir($root.'/lang/en');
    rmdir($root.'/lang/de-at');
    rmdir($root.'/lang');
    rmdir($root);
}
