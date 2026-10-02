// Language note, not an executable equivalent:
// C++20 coroutines provide co_await, with behavior supplied by an awaitable.
// A suitable coroutine return type and an async runtime/operation are needed.
// co_await alone does not schedule an I/O operation or create a worker thread.
// This example deliberately does not invent a standard Task<int> type.
// See JavaScript or C# for the executable pending-operation example.


// First completion selects the first result OR error.
// First success ignores individual failures until a value arrives;
// if every operation fails, report the collected errors.
// Choose the matching selection API in the selected async runtime.
// Remaining operations require an explicit cancellation policy.
