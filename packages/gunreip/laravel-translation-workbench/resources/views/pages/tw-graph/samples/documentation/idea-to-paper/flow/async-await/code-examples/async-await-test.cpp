// Language note, not an executable equivalent:
// C++20 coroutines provide co_await, with behavior supplied by an awaitable.
// A suitable coroutine return type and an async runtime/operation are needed.
// co_await alone does not schedule an I/O operation or create a worker thread.
// This example deliberately does not invent a standard Task<int> type.
// See JavaScript or C# for the executable pending-operation example.


// Count the initial operation as attempt 1 and enforce a maximum.
// After a transient failure, await a cancellable delay before starting
// a fresh operation. Permanent errors and cancellation are not retried.
// At exhaustion, propagate the final error without another delay.
// Pass cancellation through to the operation and delay APIs of your runtime.
// Only retry work that is safe to repeat.
