<?php

// Read-only source inventory. No Laravel bootstrap, provider invocation or database connection.
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use Tests\Support\TranslationWorkbench\StaticOptionListEvidence;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$files = [
    'app/Livewire/Management/People/CreatePerson.php',
    'app/Livewire/Management/People/EditPerson.php',
    'app/Livewire/Admin/RoleList.php',
    'app/Livewire/Admin/UserList.php',
    'app/Support/Icons/IconRegistry.php',
    'app/Models/PersonDocumentType.php',
    'app/Models/PersonDocument.php',
    'app/Models/PersonContact.php',
    'config/buergerfrs-icons.php',
];
$report = ['mode' => 'read_only_current_source_inventory', 'generated_at' => gmdate(DATE_ATOM),
    'production_connected' => false, 'automatic_assignment' => false,
    'runtime_exhaustiveness_proven' => false, 'files' => []];
$finder = new NodeFinder;
$parser = (new ParserFactory)->createForHostVersion();
foreach ($files as $path) {
    $source = file_get_contents($root.'/'.$path);
    $ast = $parser->parse($source);
    $describe = static fn (Node $node): array => ['line' => $node->getStartLine(),
        'expression' => substr($source, $node->getStartFilePos(), $node->getEndFilePos() - $node->getStartFilePos() + 1)];
    $evidence = ['sha256' => hash('sha256', $source), 'property_defaults' => [], 'literal_array_entries' => [], 'methods' => [], 'constants' => []];
    foreach ($finder->findInstanceOf($ast, Node\Stmt\Property::class) as $property) {
        foreach ($property->props as $prop) {
            if (str_ends_with($prop->name->toString(), 'Options') && $prop->default !== null) {
                $evidence['property_defaults'][$prop->name->toString()] = $describe($prop->default);
            }
        }
    }
    // These are individual syntactically explicit entries, never a complete effective runtime list.
    foreach ($finder->findInstanceOf($ast, Node\ArrayItem::class) as $item) {
        if (! $item->unpack && $item->key instanceof Node\Scalar\String_ && $item->value instanceof Node\Scalar\String_) {
            $evidence['literal_array_entries'][] = ['key' => $item->key->value, 'value' => $item->value->value, ...$describe($item)];
        }
    }
    foreach ($finder->findInstanceOf($ast, Node\Stmt\ClassMethod::class) as $method) {
        $body = $describe($method);
        if (preg_match('/Options|options|roleGroups|badgeColors|badgeVariants|defaults|categoryFor/', $body['expression'])) {
            $evidence['methods'][] = ['name' => $method->name->toString(), ...$body];
        }
    }
    foreach ($finder->findInstanceOf($ast, Node\Const_::class) as $constant) {
        $evidence['constants'][] = ['name' => $constant->name->toString(), ...$describe($constant->value)];
    }
    $report['files'][$path] = $evidence;
}
$lists = new StaticOptionListEvidence;
$constantPath = 'app/Models/PersonContact.php';
$constantSources = [$constantPath => file_get_contents($root.'/'.$constantPath)];
if (hash('sha256', $constantSources[$constantPath]) !== $report['files'][$constantPath]['sha256']) {
    throw new RuntimeException('Constant source changed');
}
$report['option_lists'] = [];
foreach (['CreatePerson', 'EditPerson'] as $class) {
    $path = 'app/Livewire/Management/People/'.$class.'.php';
    $source = file_get_contents($root.'/'.$path);
    foreach (['salutationOptions', 'genderOptions', 'maritalStatusOptions', 'emergencyContactRelationshipOptions'] as $property) {
        $report['option_lists'][] = ['path' => $path, 'class' => 'App\\Livewire\\Management\\People\\'.$class, 'property' => $property,
            ...$lists->property($source, 'App\\Livewire\\Management\\People\\'.$class, $property, $constantSources)];
    }
}
foreach (['colors', 'variants'] as $kind) {
    $path = 'config/buergerfrs-icons.php';
    $configPath = ['categories', 'role_user_management', 'badge', $kind];
    $report['option_lists'][] = ['path' => $path, 'config_path' => $configPath,
        ...$lists->config(file_get_contents($root.'/'.$path), $configPath)];
}
foreach ($report['option_lists'] as $list) {
    if ($list['source_sha256'] !== $report['files'][$list['path']]['sha256']) {
        throw new RuntimeException('Option list source changed');
    }
}
// Refuse a mixed snapshot if any inspected source changed during collection.
foreach ($report['files'] as $path => $evidence) {
    if (hash_file('sha256', $root.'/'.$path) !== $evidence['sha256']) {
        throw new RuntimeException('Source changed: '.$path);
    }
}
$output = __DIR__.'/option-producer-source-inventory-2026-10-21.json';
file_put_contents($output, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
echo json_encode(['files' => count($report['files']), 'production_connected' => false, 'report' => $output], JSON_THROW_ON_ERROR).PHP_EOL;
