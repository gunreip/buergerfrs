<?php

use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class);

it('renders each public component reference with nested fields and the shared visual sections', function (string $component, string $nestedField) {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.'.$component)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<meta charset="utf-8">'.$html);
    $xpath = new DOMXPath($dom);

    expect($dom->textContent)->toContain($component.' — Deep Reference', 'Public component props', 'Array props', 'Inheritance and calculated geometry', 'Connections', 'Validation and practical limits', $nestedField);
    expect($xpath->query('//*[@data-flux-accordion-item]')->length)->toBeGreaterThan(0);
    expect($xpath->query('//pre/code')->length)->toBeGreaterThan(0);
    expect($dom->textContent)->not->toContain('Individual Parts references will be added');
})->with([
    ['strang.flow-start', 'start-node-labels.left.connectorLength'],
    ['strang.flow-step', 'node-labels.end.right.align'],
    ['strang.flow-while', 'action-label.return'],
    ['strang.flow-switch-case', 'cases[].entries'],
    ['strang.flow-if', 'if-start.lineJumps[].radius'],
    ['strang.flow-if-else', 'if-end.returnLength'],
    ['strang.flow-if-ternary', 'if-end.stemLength'],
    ['strang.flow-if-elseif', 'elseifs[].conditionLabel.text'],
    ['strang.flow-if-elseif-multi', 'elseifs[].actionLabel.stemLineJumps[].over'],
    ['strang.trunk', 'stem-lengths[n].length'],
    ['strang.merge-left', 'extension-node-labels[n][n].left.align'],
    ['strang.merge-right', 'extension-stem-continuations[n][].labels.right.text'],
    ['strang.branch-left', 'branch-extension[n].step.stepLabel.text'],
    ['strang.branch-right', 'branch-extension[n].returnBridge[m].nodeLabels[n].left.text'],
    ['strang.branch-end', 'end-label.offset'],
    ['strang.rekey-source-left', 'compressed-stem-parts.gapLength'],
    ['strang.rekey-source-right', 'arc-radiuss.in'],
    ['strang.rekey-target-left', 'end-label.text'],
    ['strang.rekey-target-right', 'stem-continuation[].labels.left.connectorLength'],
    ['parts.start', 'node-image.source'],
    ['parts.end', 'end-label.badgeColor'],
    ['parts.sideways', 'bridge-label.lineJumps[].side'],
    ['parts.chain', 'parts[].nodeLabelLeft.align'],
    ['parts.fusion', 'inputs[].anchor.x'],
    ['parts.split', 'outputs[].label.width'],
]);

it('documents every declared public prop and identifies array defaults as arrays', function (string $component) {
    $relative = str_replace('.', '/', $component).'.blade.php';
    $views = base_path('packages/gunreip/laravel-translation-workbench/resources/views');
    $source = file_get_contents($views.'/components/ui/tw-graph/'.$relative);
    $reference = file_get_contents($views.'/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/'.$relative);
    preg_match('/<flux:table.rows>(.*?)<\/flux:table.rows>/s', $reference, $table);
    preg_match_all('/<flux:table.row>(.*?)<\/flux:table.row>/s', $table[1], $rows);
    $documented = [];
    foreach ($rows[1] as $row) {
        preg_match_all('/<flux:table.cell\b[^>]*>(.*?)<\/flux:table.cell>/s', $row, $cells);
        $values = array_map(fn ($cell) => trim(html_entity_decode(strip_tags($cell))), $cells[1]);
        $documented[$values[0]] = $values;
    }

    // Tokenize only the @props declaration, keeping nested array keys out of the public API.
    $declaration = substr($source, strpos($source, '@props(') + strlen('@props('));
    $tokens = array_values(array_filter(token_get_all('<?php '.$declaration), fn ($token) => ! is_array($token) || ! in_array($token[0], [T_OPEN_TAG, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])));
    $depth = 0;
    $expectKey = false;
    foreach ($tokens as $index => $token) {
        if ($token === '[') {
            $depth++;
            if ($depth === 1) {
                $expectKey = true;
            }
        } elseif ($token === ']') {
            if (--$depth === 0) {
                break;
            }
        } elseif ($token === ',' && $depth === 1) {
            $expectKey = true;
        } elseif ($depth === 1 && $expectKey && is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
            $prop = Str::kebab(trim($token[1], "'\""));
            expect($documented, $component.' missing public prop '.$prop)->toHaveKey($prop);
            $next = $tokens[$index + 1] ?? null;
            if (is_array($next) && $next[0] === T_DOUBLE_ARROW && ($tokens[$index + 2] ?? null) === '[' && count($documented[$prop]) === 4) {
                expect($documented[$prop][1], $component.'.'.$prop.' must document its array type')->toContain('array');
            }
            $expectKey = false;
        }
    }
})->with(function () {
    $directory = __DIR__.'/../../../packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections';
    foreach (glob($directory.'/*/*.blade.php') as $file) {
        if (basename($file) !== 'index.blade.php') {
            $component = basename(dirname($file)).'.'.basename($file, '.blade.php');
            yield $component => [$component];
        }
    }
});
