<?php
function addOne(int $value): int
{
    return $value + 1;
}

function doubleValue(int $value): int
{
    return $value * 2;
}

function apply(int $value, callable $callback): int
{
    $result = $callback($value);
    return $result;
}

function demonstrateCallbacks(): array
{
    $first = apply(20, 'addOne');
    $second = apply(20, 'doubleValue');
    echo "first=$first; second=$second"; // 21 and 40
    return [$first, $second];
}
