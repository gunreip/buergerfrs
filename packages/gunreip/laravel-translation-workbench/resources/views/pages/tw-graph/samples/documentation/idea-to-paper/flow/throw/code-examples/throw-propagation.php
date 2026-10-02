<?php
function validateInput(array &$events): void
{
    $events[] = 'inner';
    throw new InvalidArgumentException('Invalid input');
    $events[] = 'unreachable-inner';
}

function demonstratePropagation(array &$events): void
{
    try {
        $events[] = 'outer-try';
        validateInput($events); // No local handler: propagates to this CATCH.
        $events[] = 'unreachable-after-call';
    } catch (InvalidArgumentException $error) {
        $events[] = 'outer-caught';
    }
    $events[] = 'after';
}
