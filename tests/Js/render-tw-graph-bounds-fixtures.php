<?php

use Illuminate\Contracts\Console\Kernel;

$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$base = $root.'/packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper';
$dir = $argv[1] ?? sys_get_temp_dir().'/tw-graph-bounds-audit';
@mkdir($dir);
$out = [];
foreach (['canvas', 'primitives', 'segments', 'parts', 'paths', 'strang', 'flow'] as $area) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base.'/'.$area, FilesystemIterator::SKIP_DOTS)) as $file) {
        $name = $file->getPathname();
        if (! str_ends_with($name, '.blade.php') || ! preg_match('/<x-translation-workbench::ui\.tw-graph(?:\s|>)/', file_get_contents($name))) {
            continue;
        }
        $relative = substr($name, strlen($base) + 1, -10);
        $key = str_replace('/', '.', $relative);
        try {
            $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.'.$key, ['dev' => true, 'coordinates' => true])->render();
            file_put_contents($dir.'/'.$key.'.html', $html);
            $out[] = ['name' => $key, 'file' => $key.'.html'];
            echo $key."\n";
        } catch (Throwable $e) {
            $out[] = ['name' => $key, 'error' => $e->getMessage()];
            echo 'ERROR '.$key.' '.substr($e->getMessage(), 0, 140)."\n";
        }
    }
}
file_put_contents($dir.'/index.json', json_encode($out,JSON_PRETTY_PRINT));
