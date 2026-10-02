<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.

// Cancellation must be observed by the selected async operation.
// Release resources, report cancellation and run cleanup. A request alone
// does not forcibly stop work. Keep unrelated errors distinct.
