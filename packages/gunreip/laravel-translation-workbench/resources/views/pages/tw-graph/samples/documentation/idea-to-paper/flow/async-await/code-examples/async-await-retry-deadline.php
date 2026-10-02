<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.


// Create one deadline for the whole retry operation. Pass cancellation
// to every attempt and retry delay. Never reset the deadline for a retry.
// Preserve timeout versus caller cancellation and release timers/listeners.
