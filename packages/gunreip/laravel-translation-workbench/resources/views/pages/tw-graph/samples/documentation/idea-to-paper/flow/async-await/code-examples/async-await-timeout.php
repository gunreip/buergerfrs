<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.

// Arm a timeout, start cancellation-aware work and await through the runtime.
// Expiry requests cancellation; wait for acknowledgement and clean up timers.
// A wait-only timeout does not necessarily stop the underlying work.
