/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */


/* Count the initial operation as attempt 1 and enforce a maximum.
 * After a transient failure, await a cancellable delay before starting
 * a fresh operation. Permanent errors and cancellation are not retried.
 * At exhaustion, propagate the final error without another delay.
 * Pass cancellation through to the operation and delay APIs of your runtime.
 * Only retry work that is safe to repeat. */
