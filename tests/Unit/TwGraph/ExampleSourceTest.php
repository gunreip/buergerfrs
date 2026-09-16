<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('extracts raw Blade and preserves relative indentation without evaluating or escaping it', function () {
    $source = new ExampleSource(<<<'BLADE'
        Outside the example
            {{-- example.1:start --}}
            <x-example
                :label="['text' => ['$value'], 'width' => 'half']"
            />
            {{-- example.1:end --}}
        Outside the example
    BLADE);

    expect($source->example('example.1'))->toBe(<<<'BLADE'
    <x-example
        :label="['text' => ['$value'], 'width' => 'half']"
    />
    BLADE);
});

it('handles empty examples and normalizes line endings', function () {
    $source = new ExampleSource("  {{-- empty:start --}}\r\n  {{-- empty:end --}}\r\n  {{-- body:start --}}\r\n  first\r\n\r\n    second\r\n  {{-- body:end --}}");
    expect($source->example('empty'))->toBe('')
        ->and($source->example('body'))->toBe("first\n\n  second");
});

it('reports missing duplicate and reversed markers with their view context', function (string $source) {
    expect(fn () => (new ExampleSource($source, 'documentation.fixture'))->example('test'))
        ->toThrow(LogicException::class, 'Example [test] in [documentation.fixture]');
})->with([
    'missing end' => "{{-- test:start --}}\ntext",
    'missing start' => "text\n{{-- test:end --}}",
    'duplicate start' => "{{-- test:start --}}\n{{-- test:start --}}\ntext\n{{-- test:end --}}",
    'duplicate end' => "{{-- test:start --}}\ntext\n{{-- test:end --}}\n{{-- test:end --}}",
    'reversed' => "{{-- test:end --}}\ntext\n{{-- test:start --}}",
]);

it('reads current source through the view finder on every new load', function () {
    $directory = sys_get_temp_dir() . '/example-source-' . bin2hex(random_bytes(8));
    mkdir($directory);
    $file = $directory . '/sample.blade.php';
    View::addNamespace('example-source-test', $directory);
    try {
        file_put_contents($file, "{{-- demo:start --}}\nfirst\n{{-- demo:end --}}");
        expect(ExampleSource::fromView('example-source-test::sample')->example('demo'))->toBe('first');
        file_put_contents($file, "{{-- demo:start --}}\nchanged\n{{-- demo:end --}}");
        expect(ExampleSource::fromView('example-source-test::sample')->example('demo'))->toBe('changed');
    } finally {
        unlink($file);
        rmdir($directory);
    }
});
