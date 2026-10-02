<?php
function logMessage(string $message): void
{
    echo $message, "\n";
}

function demonstrateVoid(): void
{
    echo "before\n";
    $message = 'Processed item';
    logMessage($message); // No result assignment.
    echo "after\n";
}
