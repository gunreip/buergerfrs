<?php
function validateAndCleanup(array &$events): void
{
    try {
        $events[] = 'inner';
        throw new InvalidArgumentException('Invalid input');
        $events[] = 'unreachable-inner';
    } catch (InvalidArgumentException $error) {
        $events[] = 'inner-log';
        throw $error; // Rethrow the same exception object.
        $events[] = 'unreachable-after-rethrow';
    } finally {
        $events[] = 'cleanup';
    }
}

function demonstrateFinally(array &$events): void
{
    try {
        $events[] = 'outer-try';
        validateAndCleanup($events);
        $events[] = 'unreachable-after-call';
    } catch (InvalidArgumentException $error) {
        $events[] = 'outer-caught';
    }
    $events[] = 'after';
}
