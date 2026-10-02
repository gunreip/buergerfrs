<?php
function adjust(int $value, int $factor): int
{
    $value = $value + 1; // Changes only this parameter, not the caller variable.
    $result = $value * $factor;
    return $result;
}

function demonstrateArguments(): array
{
    $value = 20;
    $factor = 2;
    $result = adjust($value, $factor);
    echo "value=$value; factor=$factor; result=$result";
    return [$value, $factor, $result]; // [20, 2, 42]
}
