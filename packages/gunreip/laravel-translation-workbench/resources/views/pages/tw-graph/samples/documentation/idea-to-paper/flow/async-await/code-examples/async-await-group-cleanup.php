<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.


// Start both operations and observe every failure. On the first failure,
// request cancellation of siblings, await every completion and cleanup,
// then propagate the original failure. Cancellation must be cooperative.
