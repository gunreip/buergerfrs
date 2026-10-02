<?php
function addOne(int $value): int
{
    $result = $value + 1;
    return $result;
}

function demonstrateCall(): int
{
    $value = 41;
    $result = addOne($value);
    echo $result; // 42; the function has returned before this line executes.
    return $result;
}
