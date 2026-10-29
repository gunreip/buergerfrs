<?php

// Isolated adapter tests using the real scanner; no project bootstrap or database.
use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Support\CopiedSourceContext;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$directory = sys_get_temp_dir().'/tw-capture-test-'.bin2hex(random_bytes(6));
mkdir($directory, 0700);
$app = new Application($directory);
$app->instance('config', new Repository(['translation-workbench' => []]));
$app->instance('files', new Filesystem);
Facade::setFacadeApplication($app);
$scanner = $app->make(TranslationWorkbenchScanner::class);
$adapter = new CopiedSourceCapture;
$checks = 0;
$check = static function (bool $ok, string $message) use (&$checks): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
    $checks++;
};
$scan = static function (string $source, array $lang = []) use ($directory, $scanner, $adapter): array {
    file_put_contents($directory.'/fixture.blade.php', $source);

    return $scanner->scanFile($directory.'/fixture.blade.php')->map(
        fn ($item) => $adapter->capture($item, $source, hash('sha256', $source), $lang),
    )->all();
};
try {
    $rows = $scan("{{ __('Hello  world') }} {{ __('Hello  world') }}");
    $check(count($rows) === 2 && $rows[0]['source_offset'] !== $rows[1]['source_offset'], 'Same-line findings must stay distinct');
    $check($rows[0]['raw_literal'] === 'Hello  world', 'Do not normalize literal whitespace');
    $check($rows[0]['raw_expression'] === "__('Hello  world')", 'Keep original expression bytes');
    $check($rows[0]['suggested_key'] !== null && $rows[0]['suggested_literal'] === null, 'Separate generated key and suggested literal');
    $rows = $scan("{{ __('ui.button.save') }}", ['ui.button.save' => ['value' => 'Save', 'provenance' => ['locale' => 'en', 'sha256' => 'a']]]);
    $check($rows[0]['raw_source_code_literal'] === 'ui.button.save' && $rows[0]['raw_literal'] === null, 'Key must not become literal');
    $check($rows[0]['suggested_key'] !== null && $rows[0]['suggested_key'] !== 'ui.button.save' && $rows[0]['suggested_literal'] === 'Save', 'Existing key must have an independent source-specific suggestion');
    $rows = $scan("{{ __('ui.button.unknown') }}");
    $check($rows[0]['suggested_literal'] === null, 'Do not derive text from last key segment');
    $rows = $scan(<<<'SOURCE'
{{ __('Hello '.$name) }} {{ __("Hello $name") }} {{ __($label) }}
SOURCE);
    $check(count($rows) === 3 && count(array_filter($rows, fn ($r) => $r['raw_literal'] !== null)) === 0, 'Dynamic expressions must not become literal prefixes');
    $rows = $scan(<<<'SOURCE'
{{ __('a\nb') }}
SOURCE);
    $check($rows[0]['raw_literal'] === 'a\nb', 'Single-quoted escapes use PHP semantics');
    $rows = $scan("<?php \$x = 1; // __('Hidden')\n__('Visible');");
    $check($rows[0]['status'] === 'excluded_comment' && $rows[1]['raw_literal'] === 'Visible', 'Inline PHP comments excluded');
    $rows = $scan("{{-- __('Hidden') --}}\n@lang('Visible')");
    $check($rows[0]['status'] === 'excluded_comment' && $rows[1]['raw_literal'] === 'Visible', 'Blade comments excluded, directive parsed');
    $rows = $scan("{{ __(\n  'Two lines'\n) }}");
    $check($rows[0]['raw_expression'] === "__(\n  'Two lines'\n)", 'Multiline expression preserved');
    $check($rows[0]['status'] === 'pending_review', 'Lexical capture never activates finding');
    $source = "<?php __('Integrity');";
    file_put_contents($directory.'/fixture.blade.php', $source);
    $item = $scanner->scanFile($directory.'/fixture.blade.php')->first();
    $rejected = false;
    try {
        $adapter->capture($item, $source.' ', hash('sha256', $source), []);
    } catch (RuntimeException) {
        $rejected = true;
    }
    $check($rejected, 'Source drift must abort capture');
    $context = new CopiedSourceContext;
    $assertContext = static function (string $source, string $needle, string $expected, string $path = 'fixture.blade.php') use ($context, $check): void {
        $offset = strpos($source, $needle);
        if ($offset === false) {
            throw new RuntimeException('Missing test needle');
        }
        $result = $context->check($path, $source, $offset);
        $check($result['state'] === $expected, $needle.': expected '.$expected.', got '.json_encode($result));
    };
    $assertContext(<<<'SOURCE'
<?php $example = "__('Fake')"; __('Real');
SOURCE, "__('Fake')", 'excluded_string', 'fixture.php');
    $assertContext(<<<'SOURCE'
<?php $example = "__('Fake')"; __('Real');
SOURCE, "__('Real')", 'confirmed_call', 'fixture.php');
    $assertContext(<<<'SOURCE'
<?php $example = <<<'TEXT'
__('Fake')
TEXT;
__('Real');
SOURCE, "__('Fake')", 'excluded_string', 'fixture.php');
    $assertContext("<?php \$object->__('Method');", "__('Method')", 'unresolved', 'fixture.php');
    $assertContext("<?php function broken( { __('Broken');", "__('Broken')", 'unresolved', 'fixture.php');
    $assertContext("@verbatim {{ __('Ignored') }} @endverbatim", "__('Ignored')", 'excluded_verbatim');
    $assertContext("@{{ __('Escaped') }}", "__('Escaped')", 'excluded_escaped_blade');
    $assertContext("{!! __('Raw echo') !!}", "__('Raw echo')", 'confirmed_call');
    $assertContext("@if(__('Condition')) yes @endif", "__('Condition')", 'confirmed_call');
    $assertContext("@unknown(__('Custom'))", "__('Custom')", 'unresolved');
    $assertContext("<flux:heading :text=\"__('Bound')\" />", "__('Bound')", 'confirmed_call');
    $assertContext("<flux:heading text=\"__('Plain')\" />", "__('Plain')", 'unresolved');
    $assertContext(<<<'SOURCE'
<x-card :data="['sample' => &quot;__('Encoded example')&quot;]" />
SOURCE, "__('Encoded example')", 'unresolved');
    $assertContext(<<<'SOURCE'
<x-card :data="['sample' => '__(\'Example\')']" />
SOURCE, "__(\\'Example", 'excluded_string');
    $assertContext(<<<'SOURCE'
@php
$example = "{{ __('String example') }}";
__('PHP block');
@endphp
SOURCE, "__('String example')", 'excluded_string');
    $assertContext("@php __('PHP block'); @endphp", "__('PHP block')", 'confirmed_call');
    $assertContext("<?php __('Native'); ?>", "__('Native')", 'confirmed_call');
    $assertContext("{{ __('Brace }} in text') }}", "__('Brace", 'confirmed_call');
    $rows = $scan(<<<'SOURCE'
<?php $sample = "__('Not a call')";
SOURCE);
    $check($rows[0]['status'] === 'excluded_string' && $rows[0]['raw_literal'] === null, 'Excluded source must not generate raw fields or a suggestion');
    $assertContext("@foreach([__('Loop')] as \$value) @endforeach", "__('Loop')", 'confirmed_call');
    $assertContext("@php(\$value = __('Inline'))", "__('Inline')", 'confirmed_call');
    $assertContext("<!-- {{ __('Hidden') }} -->", "__('Hidden')", 'excluded_comment');
    $assertContext(<<<'SOURCE'
<flux:button @class(['x']) :aria-label="__('Bound after directive')" />
SOURCE, "__('Bound after directive')", 'confirmed_call');
    $assertContext(<<<'SOURCE'
<flux:button class="fake :text='__(Bogus)'" />
SOURCE, '__(Bogus)', 'unresolved');
    $assertContext(<<<'SOURCE'
@php
$sample = '@endphp {{ __("Fake close") }}';
__('Still PHP');
@endphp
SOURCE, '__("Fake close")', 'excluded_string');
    $assertContext(<<<'SOURCE'
@php
$sample = '@endphp {{ __("Fake close") }}';
__('Still PHP');
@endphp
SOURCE, "__('Still PHP')", 'confirmed_call');
    $assertContext(<<<'SOURCE'
<flux:button {{-- old --}} {{ $attributes }} :text="__('After attributes')" />
SOURCE, "__('After attributes')", 'confirmed_call');
    $assertContext(<<<'SOURCE'
<flux:button {{-- __('Old') --}} :text="__('Current')" />
SOURCE, "__('Old')", 'excluded_comment');
    echo $checks." assertions passed\n";
} finally {
    unlink($directory.'/fixture.blade.php');
    rmdir($directory);
}
