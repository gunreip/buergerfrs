// Language note, not an executable equivalent:
// C++20 coroutines provide co_await, with behavior supplied by an awaitable.
// A suitable coroutine return type and an async runtime/operation are needed.
// co_await alone does not schedule an I/O operation or create a worker thread.
// This example deliberately does not invent a standard Task<int> type.
// See JavaScript or C# for the executable pending-operation example.

// For each item: start its async operation, await through the runtime,
// append its result, then request the next item. Empty input starts none.
