<?php

use App\Support\Debugbar\OverviewViewsCollectorProvider;
use Fruitcake\LaravelDebugbar\CollectorProviders\ViewsCollectorProvider;
use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('only reduces the view timeline for Overview Livewire requests', function (string $method, bool $header, mixed $snapshot, bool $expected) {
    $request = Request::create('/livewire/update', $method, ['components' => [['snapshot' => $snapshot]]], [], [], $header ? ['HTTP_X_LIVEWIRE' => 'true'] : []);

    expect(OverviewViewsCollectorProvider::isOverviewRequest($request))->toBe($expected);
})->with([
    'overview' => ['POST', true, '{"memo":{"name":"translation-workbench.tw-graph.overview"}}', true],
    'other component' => ['POST', true, '{"memo":{"name":"translation-workbench.documentation"}}', false],
    'normal page' => ['GET', true, '{"memo":{"name":"translation-workbench.tw-graph.overview"}}', false],
    'no Livewire header' => ['POST', false, '{"memo":{"name":"translation-workbench.tw-graph.overview"}}', false],
    'invalid JSON' => ['POST', true, '{broken', false],
    'missing snapshot' => ['POST', true, null, false],
]);

it('keeps view diagnostics and only disconnects the Overview view timeline', function (string $name, bool $timeline) {
    $request = Request::create('/livewire/update', 'POST', ['components' => [['snapshot' => json_encode(['memo' => ['name' => $name]])]]], [], [], ['HTTP_X_LIVEWIRE' => 'true']);
    app()->instance('request', $request);
    $debugbar = app(LaravelDebugbar::class);
    $events = new Dispatcher(app());

    // Use Debugbar's real container invocation, including the application binding.
    app()->call(ViewsCollectorProvider::class, ['events' => $events, 'options' => ['timeline' => true, 'data' => false, 'group' => true]]);
    $collector = $debugbar->getCollector('views');

    expect($collector->hasTimeDataCollector())->toBe($timeline);
    $events->dispatch('composing:example', [view('welcome')]);
    expect($collector->collect()['nb_templates'])->toBe(1);
})->with([
    'Overview' => ['translation-workbench.tw-graph.overview', false],
    'other Livewire requests' => ['translation-workbench.documentation', true],
]);

it('renders and stores Overview diagnostics within 128 MiB', function () {
    $script = <<<'PHP'
require getcwd().'/vendor/autoload.php';
$app = require getcwd().'/bootstrap/app.php';
$request = Illuminate\Http\Request::create('/livewire/update', 'POST', ['components' => [['snapshot' => json_encode(['memo' => ['name' => 'translation-workbench.tw-graph.overview']])]]], [], [], ['HTTP_X_LIVEWIRE' => 'true']);
$app->instance('request', $request);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance("request", $request);
config(['debugbar.storage.enabled' => false, 'debugbar.force_allow_enable' => true]);
$debugbar = app(Fruitcake\LaravelDebugbar\LaravelDebugbar::class);
$debugbar->enable();
$html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.index')->render();
$data = $debugbar->getData();
$directory = sys_get_temp_dir().'/overview-debugbar-'.bin2hex(random_bytes(8));
mkdir($directory);
$storage = new DebugBar\Storage\FileStorage($directory);
$storage->save('test', $data);
$file = $directory.'/test.json';
echo json_encode(['htmlBytes' => strlen($html), 'storedBytes' => filesize($file), 'measures' => count($data['time']['measures'] ?? []), 'views' => $data['views']['nb_templates'] ?? null, 'peak' => memory_get_peak_usage(true)])."\n";
unlink($file);
rmdir($directory);
PHP;
    $process = new Process([PHP_BINARY, '-d', 'memory_limit=128M', '-r', $script], base_path(), [
        'APP_ENV' => 'local', 'APP_DEBUG' => 'true', 'SESSION_DRIVER' => 'array',
        'CACHE_STORE' => 'array', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => '',
    ], timeout: 120);
    $process->run();
    expect($process->isSuccessful())->toBeTrue($process->getErrorOutput().$process->getOutput());
    $result = json_decode(trim($process->getOutput()), true, flags: JSON_THROW_ON_ERROR);
    expect($result['views'])->toBeGreaterThan(1000)
        ->and($result['measures'])->toBeLessThan(100)
        ->and($result['storedBytes'])->toBeLessThan(1024 * 1024)
        ->and($result['peak'])->toBeLessThan(128 * 1024 * 1024);
});
