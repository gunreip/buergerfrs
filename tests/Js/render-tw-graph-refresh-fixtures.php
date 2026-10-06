<?php

// php tests/Js/render-tw-graph-refresh-fixtures.php /tmp/tw-graph-refresh
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class TwGraphRefreshFixture extends Livewire\Component
{
    public string $length = '4rem';

    public function render()
    {
        return <<<'BLADE'
            <section>
                <button wire:click="$refresh">Refresh</button>
                <x-translation-workbench::ui.tw-graph graph-id="refresh.fixture" :dev="true" :coordinates="true">
                    <x-translation-workbench::ui.tw-graph.segments.path id="refresh.path" :length="$length" />
                </x-translation-workbench::ui.tw-graph>
            </section>
            BLADE;
    }
}

Livewire\Livewire::component('tw-graph-refresh-fixture', TwGraphRefreshFixture::class);
$directory = $argv[1];
if (! is_dir($directory)) mkdir($directory, 0777, true);
$component = Livewire\Livewire::test(TwGraphRefreshFixture::class);
file_put_contents($directory.'/initial.html', $component->html());
$component->call('$refresh');
file_put_contents($directory.'/same.html', $component->html());
$component->set('length', '8rem');
file_put_contents($directory.'/changed.html', $component->html());
