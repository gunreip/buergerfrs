// Language note, not an executable equivalent:
// C++20 coroutines provide co_await, with behavior supplied by an awaitable.
// A suitable coroutine return type and an async runtime/operation are needed.
// co_await alone does not schedule an I/O operation or create a worker thread.
// This example deliberately does not invent a standard Task<int> type.
// See JavaScript or C# for the executable pending-operation example.


// Keep items queued until one of the limited worker slots is free.
// Each worker starts one operation, awaits it, records its outcome,
// then takes another item. Preserve input order when storing outcomes.
// Do not start every operation before applying the concurrency limit.
// Choose the worker/await APIs of the selected async runtime.
