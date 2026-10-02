<?php
// PHP 8.5: illustrates suppression, not a recommended cleanup pattern.
function failAndReturn(array &$events): int
{
    try {
        $events[] = 'original';
        throw new InvalidArgumentException('Original failure');
    } finally {
        $events[] = 'cleanup';
        return 7; // Suppresses the pending exception (or replaces a pending return).
    }
}

function demonstrateFinallyReturn(array &$events): ?int
{
    $result = null;
    try {
        $events[] = 'outer-try';
        $result = failAndReturn($events);
        $events[] = 'received';
    } catch (InvalidArgumentException $error) {
        $events[] = 'unreachable-catch';
    }
    $events[] = 'after';
    return $result;
}
