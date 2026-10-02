<?php
function failWithCleanup(): void
{
    try {
        echo "original\n";
        throw new InvalidArgumentException('Original failure');
    } finally {
        echo "cleanup\n";
    }
}

function demonstrateUnhandled(): void
{
    echo "call\n";
    failWithCleanup(); // No local CATCH: the exception leaves this call.
    echo "unreachable-after-call\n";
}
// Calling demonstrateUnhandled() without an external handler is a fatal error.
