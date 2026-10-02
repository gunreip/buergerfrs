// Language note, not an executable equivalent:
// C++20 coroutines provide co_await, with behavior supplied by an awaitable.
// A suitable coroutine return type and an async runtime/operation are needed.
// co_await alone does not schedule an I/O operation or create a worker thread.
// This example deliberately does not invent a standard Task<int> type.
// See JavaScript or C# for the executable pending-operation example.


// Create one deadline for the whole retry operation. Pass cancellation
// to every attempt and retry delay. Never reset the deadline for a retry.
// Preserve timeout versus caller cancellation and release timers/listeners.
