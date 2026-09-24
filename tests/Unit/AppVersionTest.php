<?php

use App\Console\Commands\ClearProject;
use App\Support\AppVersion;
use App\Support\VersionManifest;
use App\Support\WatchVersion;
use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchVersion;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->versionPath = sys_get_temp_dir().'/app-version-'.bin2hex(random_bytes(8));
    mkdir($this->versionPath.'/public', 0777, true);
    file_put_contents($this->versionPath.'/versions.json', json_encode([
        'buergerfrs' => '1.2.3',
        'translation-workbench' => '0.7.0',
        'tw-graph' => '0.4.8',
    ]));
    $this->manifest = new VersionManifest($this->versionPath.'/versions.json');
    $this->watchVersion = new WatchVersion($this->versionPath.'/storage/watch-version.json');
    $this->app->instance(VersionManifest::class, $this->manifest);
    $this->app->instance(WatchVersion::class, $this->watchVersion);
    $this->app->instance(AppVersion::class, new AppVersion($this->versionPath));
    $this->git = function (array $arguments): string {
        $process = new Process([
            'git', '-c', 'user.name=Version Test', '-c', 'user.email=version@example.test',
            '-c', 'commit.gpgsign=false', ...$arguments,
        ], $this->versionPath);
        $process->mustRun();

        return trim($process->getOutput());
    };
});

afterEach(function () {
    (new Filesystem)->deleteDirectory($this->versionPath);
});

it('refreshes the footer after commits and tags without rewriting a version file', function () {
    ($this->git)(['init']);
    file_put_contents($this->versionPath.'/public/version.txt', 'outdated-build');
    ($this->git)(['add', '.']);
    ($this->git)(['commit', '-m', 'First']);
    $version = new AppVersion($this->versionPath);
    $this->app->instance(AppVersion::class, $version);

    $first = ($this->git)(['rev-parse', '--short', 'HEAD']);
    expect(view('partials.footer')->render())->toContain('Git: '.$first);

    ($this->git)(['commit', '--allow-empty', '-m', 'Second']);
    $second = ($this->git)(['rev-parse', '--short', 'HEAD']);
    expect($second)->not->toBe($first);
    expect(view('partials.footer')->render())->toContain('Git: '.$second);

    ($this->git)(['tag', 'v1.0.0']);
    expect($version->label())->toBe('v1.0.0');
    file_put_contents($this->versionPath.'/public/version.txt', 'modified-build');
    expect($version->label())->toBe('v1.0.0-dirty');
});

it('reads linked worktrees where dot git is a file', function () {
    ($this->git)(['init']);
    ($this->git)(['commit', '--allow-empty', '-m', 'First']);
    ($this->git)(['worktree', 'add', '--detach', $this->versionPath.'/linked', 'HEAD']);

    expect(is_file($this->versionPath.'/linked/.git'))->toBeTrue();
    expect((new AppVersion($this->versionPath.'/linked'))->label())
        ->toBe(($this->git)(['rev-parse', '--short', 'HEAD']));
});

it('uses build metadata only in deployments without git', function () {
    $version = new AppVersion($this->versionPath);
    expect($version->label())->toBe('n/a');
    file_put_contents($this->versionPath.'/public/version.txt', "release-build\n");
    expect($version->label())->toBe('release-build');
    file_put_contents($this->versionPath.'/public/version.txt', "\n");
    expect($version->label())->toBe('n/a');
});

it('does not hide a broken checkout behind stale build metadata', function () {
    file_put_contents($this->versionPath.'/public/version.txt', 'outdated-build');
    file_put_contents($this->versionPath.'/.git', 'gitdir: missing');

    expect((new AppVersion($this->versionPath))->label())->toBe('n/a');
});

it('increments only the selected component and resets subordinate versions', function ($bump, $expected) {
    expect($this->manifest->update('tw-graph', $bump, null))->toBe($expected);
    expect($this->manifest->all())->toBe([
        'buergerfrs' => '1.2.3',
        'translation-workbench' => '0.7.0',
        'tw-graph' => $expected,
    ]);
})->with(['patch' => ['patch', '0.4.9'], 'minor' => ['minor', '0.5.0'], 'major' => ['major', '1.0.0']]);

it('rejects invalid edits without changing the manifest', function ($component, $bump, $set) {
    $before = file_get_contents($this->versionPath.'/versions.json');
    expect(fn () => $this->manifest->update($component, $bump, $set))->toThrow(InvalidArgumentException::class);
    expect(file_get_contents($this->versionPath.'/versions.json'))->toBe($before);
})->with([
    ['unknown', 'patch', null],
    ['tw-graph', 'invalid', null],
    ['tw-graph', null, '1.2'],
    ['tw-graph', null, '01.2.3'],
    ['tw-graph', 'patch', '1.2.3'],
    ['tw-graph', null, null],
]);

it('exposes read and edit operations through Artisan and the footer', function () {
    $this->artisan('app:version')->expectsOutputToContain('translation-workbench')->assertSuccessful();
    $this->artisan('app:version', ['component' => 'tw-graph', '--bump' => 'minor'])->assertSuccessful();
    $this->artisan('app:version', ['component' => 'buergerfrs', '--set' => '2.0.0'])->assertSuccessful();
    $this->artisan('app:version', ['--bump' => 'patch'])->assertFailed();
    expect(view('partials.footer')->render())->toContain('buergerfrs 2.0.0', 'TW-Graph 0.5.0', 'Translation Workbench 0.7.0');
});

it('counts only successful changed builds and resets for another commit', function () {
    $watch = $this->watchVersion;
    expect($watch->record('first', true, false))->toBeNull();
    expect($watch->record('first', false, true))->toBeNull();
    expect($watch->record(null, true, true))->toBeNull();
    expect($watch->current('first'))->toBeNull();
    expect($watch->record('first', true, true)['count'])->toBe(1);
    expect($watch->record('first', true, true)['count'])->toBe(2);
    $watch->record('first', false, true);
    expect($watch->current('first')['count'])->toBe(2);
    expect($watch->current('second'))->toBeNull();
    expect($watch->record('second', true, true))->toMatchArray(['count' => 1, 'commit' => 'second']);
    expect($watch->current('first'))->toBeNull();
});

it('does not trigger the source watcher when updating the local counter', function () {
    $originalStorage = $this->app->storagePath();
    $this->app->useStoragePath($this->versionPath.'/storage');
    try {
        $watch = new WatchVersion;
        $method = new ReflectionMethod(ClearProject::class, 'watchedFilesSignature');
        $command = new ClearProject;
        $before = $method->invoke($command);
        $watch->record('commit', true, true);
        expect($method->invoke($command))->toBe($before);
    } finally {
        $this->app->useStoragePath($originalStorage);
    }
});

it('shows the watch count only for the currently checked out commit', function () {
    ($this->git)(['init']);
    ($this->git)(['add', 'versions.json']);
    ($this->git)(['commit', '-m', 'First']);
    $this->watchVersion->record(($this->git)(['rev-parse', 'HEAD']), true, true);
    expect(view('partials.footer')->render())->toContain('Watch 1');
    ($this->git)(['commit', '--allow-empty', '-m', 'Second']);
    expect(view('partials.footer')->render())->not->toContain('Watch 1');
    $this->watchVersion->record(($this->git)(['rev-parse', 'HEAD']), true, true);
    expect(view('partials.footer')->render())->toContain('Watch 1');
});

it('creates a release tag only for committed clean state and never overwrites it', function () {
    ($this->git)(['init']);
    ($this->git)(['config', 'user.name', 'Version Test']);
    ($this->git)(['config', 'user.email', 'version@example.test']);
    ($this->git)(['config', 'tag.gpgsign', 'false']);
    ($this->git)(['add', 'versions.json']);
    ($this->git)(['commit', '-m', 'Versions']);
    $originalBase = base_path();
    $this->app->setBasePath($this->versionPath);
    try {
        $this->artisan('app:release', ['component' => 'tw-graph'])->assertSuccessful();
        expect(($this->git)(['rev-parse', 'tw-graph/v0.4.8^{}']))->toBe(($this->git)(['rev-parse', 'HEAD']));
        expect(($this->git)(['cat-file', '-t', 'tw-graph/v0.4.8']))->toBe('tag');
        $this->artisan('app:release', ['component' => 'tw-graph'])->assertFailed();
        $this->manifest->update('tw-graph', 'patch', null);
        $this->artisan('app:release', ['component' => 'tw-graph'])->assertFailed();
        expect(($this->git)(['tag', '--list']))->toBe('tw-graph/v0.4.8');
    } finally {
        $this->app->setBasePath($originalBase);
    }
});

it('uses the managed Workbench version in the package display too', function () {
    $originalBase = base_path();
    $this->manifest->update('translation-workbench', null, '0.9.0');
    $this->app->setBasePath($this->versionPath);
    try {
        expect((new TranslationWorkbenchVersion)->toArray())->toMatchArray([
            'version' => '0.9.0', 'source' => 'manifest',
        ]);
    } finally {
        $this->app->setBasePath($originalBase);
    }
});
