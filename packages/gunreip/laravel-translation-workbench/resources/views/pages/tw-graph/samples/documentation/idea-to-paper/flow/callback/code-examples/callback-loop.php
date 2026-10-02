<?php
function addOne(int $value): int
{
    return $value + 1;
}

function mapEach(array $items, callable $callback): array
{
    $results = [];
    foreach ($items as $item) {
        $result = $callback($item);
        $results[] = $result;
    }
    return $results;
}

function demonstrateCallbackLoop(): array
{
    return mapEach([10, 20, 30], 'addOne'); // [11, 21, 31]
}
