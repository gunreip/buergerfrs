<?php

use Gunreip\TranslationWorkbench\Generation\SourceReplacementPreview;
use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->root = sys_get_temp_dir().'/tw-source-preview-'.bin2hex(random_bytes(8));
    mkdir($this->root.'/lang/en', 0700, true);
    file_put_contents($this->root.'/lang/en/ui.php', "<?php return ['button' => ['save' => 'Save :name']];");
    $this->context = ['finding' => ['id' => 1, 'key_id' => 8, 'link_id' => 4, 'status' => 'active', 'namespace' => '', 'full_name' => 'ui.button.save', 'path' => 'example.blade.php', 'source_line' => 1, 'source_column' => 4, 'raw_expression' => "__('Save :name', ['name' => \$name])"],
        'revision' => ['id' => 2, 'value' => 'Save :name'], 'review' => ['approved' => true, 'context' => ['locale' => 'en']]];
});

afterEach(function () {
    (new Filesystem)->deleteDirectory($this->root);
});

it('previews exactly one occurrence and preserves arguments without writing files', function () {
    $before = "{{ __('Save :name', ['name' => \$name]) }}\n{{ __('Save :name') }}";
    file_put_contents($this->root.'/example.blade.php', $before);
    $preview = (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang');
    expect($preview['ready'])->toBeTrue()
        ->and($preview['after'])->toBe("__('ui.button.save', ['name' => \$name])")
        ->and($preview['published_value'])->toBe('Save :name')
        ->and(file_get_contents($this->root.'/example.blade.php'))->toBe($before);
});

it('rejects moved occurrences rather than looking for matching text elsewhere', function () {
    file_put_contents($this->root.'/example.blade.php', "\n{{ ".$this->context['finding']['raw_expression'].' }}');
    expect(fn () => (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang'))
        ->toThrow(RuntimeException::class, 'changed');
});

it('preserves the exact wrapper and remaining arguments', function (string $path, string $prefix, string $call, string $after) {
    file_put_contents($this->root.'/'.$path, $prefix.$call.($path === 'example.php' ? ';' : ''));
    $this->context['finding'] = [...$this->context['finding'], 'path' => $path, 'source_column' => strlen($prefix) + 1, 'raw_expression' => $call];
    expect((new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang')['after'])->toBe($after);
})->with([
    ['example.blade.php', '', "@lang('Save :name', ['name' => \$name])", "@lang('ui.button.save', ['name' => \$name])"],
    ['example.php', '<?php ', "trans_choice('Save :name', \$count, ['name' => \$name])", "trans_choice('ui.button.save', \$count, ['name' => \$name])"],
    ['example.php', '<?php ', "Lang::get('Save :name', ['name' => \$name], 'de')", "Lang::get('ui.button.save', ['name' => \$name], 'de')"],
]);

it('marks an already assigned source key without changing its quote style', function () {
    $call = '__("ui.button.save")';
    file_put_contents($this->root.'/example.blade.php', '{{ '.$call.' }}');
    $this->context['finding']['raw_expression'] = $call;
    $result = (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang');
    expect($result['already_set'])->toBeTrue()->and($result['after'])->toBe($call)->and($result['ready'])->toBeFalse();
});

it('blocks source replacement without a current approval or matching published value', function () {
    file_put_contents($this->root.'/example.blade.php', '{{ '.$this->context['finding']['raw_expression'].' }}');
    $this->context['review']['approved'] = false;
    $preview = (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang');
    expect($preview['ready'])->toBeFalse()->and($preview['blocker'])->toContain('approve');
    $this->context['review']['approved'] = true;
    $this->context['revision']['value'] = 'Different';
    $preview = (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang');
    expect($preview['ready'])->toBeFalse()->and($preview['blocker'])->toContain('Publish');
});

it('rejects commented and dynamic calls and traversal paths', function () {
    file_put_contents($this->root.'/example.php', "<?php //__('Save')");
    $this->context['finding'] = [...$this->context['finding'], 'path' => 'example.php', 'source_column' => 9, 'raw_expression' => "__('Save')"];
    expect(fn () => (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang'))->toThrow(RuntimeException::class, 'active translation call');
    file_put_contents($this->root.'/example.php', '<?php __($value);');
    $this->context['finding']['source_column'] = 7;
    $this->context['finding']['raw_expression'] = '__($value)';
    expect(fn () => (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang'))->toThrow(RuntimeException::class, 'static first');
    $this->context['finding']['path'] = '../outside.php';
    expect(fn () => (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang'))->toThrow(RuntimeException::class, 'Unsafe');
});

it('retains intentionally empty and zero published source values', function (string $value) {
    file_put_contents($this->root.'/example.blade.php', '{{ '.$this->context['finding']['raw_expression'].' }}');
    file_put_contents($this->root.'/lang/en/ui.php', '<?php return '.var_export(['button' => ['save' => $value]], true).';');
    $this->context['revision']['value'] = $value;
    $preview = (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang');
    expect($preview['ready'])->toBeTrue()->and($preview['published_value'])->toBe($value);
})->with(['', '0']);

it('rejects symlink sources and archived findings', function () {
    file_put_contents($this->root.'/target.php', '<?php echo "untouched";');
    symlink($this->root.'/target.php', $this->root.'/example.blade.php');
    expect(fn () => (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang'))->toThrow(RuntimeException::class, 'Symlink');
    $this->context['finding']['status'] = 'obsolete';
    expect(fn () => (new SourceReplacementPreview)->inspect($this->context, 'en', $this->root, $this->root.'/lang'))->toThrow(RuntimeException::class, 'active finding');
});
