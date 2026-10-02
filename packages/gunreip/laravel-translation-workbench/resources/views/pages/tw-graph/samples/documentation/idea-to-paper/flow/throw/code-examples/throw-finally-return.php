<?php
function computeAndCleanup(array &$events): int
{
    try {
        $events[] = 'pending-return';
        return 42;
    } finally {
        $events[] = 'cleanup';
        throw new RuntimeException('Cleanup failed');
    }
}

function demonstrateFinallyThrow(array &$events): ?int
{
    $result = null;
    try {
        $events[] = 'outer-try';
        $result = computeAndCleanup($events);
        $events[] = 'unreachable-after-call';
    } catch (RuntimeException $error) {
        $events[] = 'outer-caught';
    }
    $events[] = 'after';
    return $result; // Still null: the assignment never completed.
}
