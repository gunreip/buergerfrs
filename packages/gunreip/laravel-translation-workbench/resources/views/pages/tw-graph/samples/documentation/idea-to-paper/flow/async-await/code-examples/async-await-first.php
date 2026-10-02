<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.


// First completion selects the first result OR error.
// First success ignores individual failures until a value arrives;
// if every operation fails, report the collected errors.
// Choose the matching selection API in the selected async runtime.
// Remaining operations require an explicit cancellation policy.
