<?php
function findFirstPositive(array $values): int
{
    $index = 0;
    while ($index < count($values)) {
        $item = $values[$index];
        if ($item > 0) {
            return $index;
        }
        $index++;
    }
    return -1;
}
