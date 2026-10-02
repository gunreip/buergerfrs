<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.


// Await the next item from an asynchronous source, process it, then request
// the next. On early exit, exhaustion, failure or cancellation, await source
// cleanup. Use the async iterator/stream APIs of the selected runtime.
