<?php
// Small non-negative integers; factorial(0) = 1.
function factorial(int $n): int
{
    if ($n === 0) {
        return 1;
    }
    $inner = factorial($n - 1);
    return $n * $inner;
}

function demonstrateRecursion(): int
{
    $result = factorial(2);
    echo $result; // 2; frames unwind in order: 0, 1, 2.
    return $result;
}
