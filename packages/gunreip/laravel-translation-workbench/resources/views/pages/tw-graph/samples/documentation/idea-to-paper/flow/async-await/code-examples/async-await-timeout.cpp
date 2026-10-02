// Language note, not an executable equivalent:
// C++20 coroutines provide co_await, with behavior supplied by an awaitable.
// A suitable coroutine return type and an async runtime/operation are needed.
// co_await alone does not schedule an I/O operation or create a worker thread.
// This example deliberately does not invent a standard Task<int> type.
// See JavaScript or C# for the executable pending-operation example.

// Arm a timeout, start cancellation-aware work and await through the runtime.
// Expiry requests cancellation; wait for acknowledgement and clean up timers.
// A wait-only timeout does not necessarily stop the underlying work.
