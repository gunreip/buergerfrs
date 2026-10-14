<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

uses(TestCase::class);

it('renders the bulk dialog and delete button with scalar translations', function (string $locale, string $cancel, string $delete) {
    app()->setLocale($locale);
    view()->share('errors', new ViewErrorBag);
    $html = view('translation-workbench::livewire.entries.modal-bulk-equalize-translation-key')->render();
    expect($html)->toContain($cancel)->not->toContain('ui.button.cancel');
    expect(Blade::render('<x-ui.button.delete />'))->toContain($delete)->not->toContain('ui.button.delete');
})->with([
    ['en', 'Cancel', 'Delete'],
    ['de', 'Abbrechen', 'Löschen'],
]);

it('does not use UI translation array parents as literal text in application or package views', function () {
    $trees = [];
    foreach (['en', 'de'] as $locale) {
        $trees[$locale] = require lang_path($locale.'/ui.php');
    }
    $failures = [];
    foreach ([resource_path('views'), base_path('packages/gunreip/laravel-translation-workbench/resources/views')] as $directory) {
        foreach (File::allFiles($directory) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            preg_match_all('/__\(\s*[\'"](ui\.[^\'"]+)[\'"]/', $file->getContents(), $matches);
            foreach (array_unique($matches[1]) as $key) {
                foreach ($trees as $locale => $tree) {
                    if (is_array(Arr::get($tree, substr($key, 3)))) {
                        $failures[] = $file->getPathname().' — '.$locale.': '.$key.' resolves to an array';
                    }
                }
            }
        }
    }
    expect($failures)->toBe([], implode("\n", $failures));
});
