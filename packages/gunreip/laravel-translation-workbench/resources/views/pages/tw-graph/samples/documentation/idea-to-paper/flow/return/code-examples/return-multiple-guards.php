<?php
function boundedDouble(int $value): int
{
    if ($value <= 0) {
        return 0;
    }
    if ($value >= 100) {
        return 200;
    }
    $result = $value * 2;
    return $result;
}
