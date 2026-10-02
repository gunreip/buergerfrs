<?php
function validateAndLog(array &$events): void
{
    try {
        $events[] = 'inner';
        throw new InvalidArgumentException('Invalid input');
        $events[] = 'unreachable-inner';
    } catch (InvalidArgumentException $error) {
        $events[] = 'inner-log';
        throw $error; // Rethrow the same exception object.
        $events[] = 'unreachable-after-rethrow';
    }
}

function demonstrateRethrow(array &$events): void
{
    try {
        $events[] = 'outer-try';
        validateAndLog($events);
        $events[] = 'unreachable-after-call';
    } catch (InvalidArgumentException $error) {
        $events[] = 'outer-caught';
    }
    $events[] = 'after';
}
