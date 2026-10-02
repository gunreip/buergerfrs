<?php
function notifyIfEnabled(bool $enabled, array &$events): void
{
    if (!$enabled) {
        return;
    }
    $events[] = 'notified';
    return; // Optional at the end of a void function.
}
