<?php
function doublePositive(int $value): int
{
    if ($value <= 0) {
        return 0;
    }
    $result = $value * 2;
    return $result;
}
