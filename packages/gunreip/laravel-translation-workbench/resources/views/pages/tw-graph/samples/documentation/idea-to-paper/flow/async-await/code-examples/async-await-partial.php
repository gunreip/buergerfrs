<?php
// Language note, not an executable equivalent:
// PHP Fibers can suspend and resume execution, but do not themselves
// provide asynchronous I/O or schedule completion of an operation.
// A concrete async runtime/library is needed for this example.
// Its await API must be shown with that dependency explicitly selected.
// See JavaScript or C# for the executable pending-operation example.

// Start independent operations, observe each outcome and wait for all.
// Keep successful values and error details in input order.
// Choose the matching all-settled mechanism in the selected runtime.
