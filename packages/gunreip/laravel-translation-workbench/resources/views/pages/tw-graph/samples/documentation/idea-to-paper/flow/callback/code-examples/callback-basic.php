<?php
function addOne(int $value): int
{
    return $value + 1;
}

function apply(int $value, callable $callback): int
{
    $result = $callback($value);
    return $result;
}

function demonstrateCallback(): int
{
    $result = apply(41, 'addOne'); // Pass the callable, not addOne(41).
    echo $result; // 42
    return $result;
}
