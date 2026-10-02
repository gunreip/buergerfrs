<?php
function addOne(int $value): int
{
    $result = $value + 1;
    return $result; // 21: resumes doubleAdjusted().
}

function doubleAdjusted(int $value): int
{
    $adjusted = addOne($value);
    $result = $adjusted * 2;
    return $result; // 42: resumes the original caller.
}

function demonstrateNested(): int
{
    $value = 20;
    $result = doubleAdjusted($value);
    echo $result;
    return $result;
}
