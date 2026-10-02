<?php
function demonstrateThrow(array &$events): void
{
    try {
        $events[] = 'try';
        throw new InvalidArgumentException('Invalid input');
        $events[] = 'unreachable'; // Skipped after THROW.
    } catch (InvalidArgumentException $error) {
        $events[] = 'caught';
    }
    $events[] = 'after';
}
