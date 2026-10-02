<?php
function doublePositive(int $value, array &$events): int
{
    try {
        if ($value > 0) {
            return $value * 2;
        }
        return 0;
    } finally {
        $events[] = 'cleanup';
        $value = 0; // The integer return value has already been evaluated.
    }
}
