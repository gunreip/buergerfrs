/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */

/* Arm a timeout, start cancellation-aware work and wait through the runtime.
 * Expiry requests cancellation; wait for acknowledgement and clean up timers.
 * A wait-only timeout does not necessarily stop the underlying work. */
