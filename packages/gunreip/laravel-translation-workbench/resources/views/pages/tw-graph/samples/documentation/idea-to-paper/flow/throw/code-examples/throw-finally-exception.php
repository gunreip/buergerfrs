<?php
function failAndCleanup(array &$events): void
{
    try {
        $events[] = 'original';
        throw new InvalidArgumentException('Original failure');
    } finally {
        $events[] = 'cleanup';
        throw new RuntimeException('Cleanup failed');
        // PHP automatically retains the pending exception as previous.
    }
}

function demonstrateReplacement(array &$events): ?RuntimeException
{
    $caught = null;
    try {
        $events[] = 'outer-try';
        failAndCleanup($events);
        $events[] = 'unreachable-after-call';
    } catch (RuntimeException $error) {
        $events[] = 'outer-caught';
        $caught = $error; // getPrevious() is the original InvalidArgumentException.
    }
    $events[] = 'after';
    return $caught;
}
