/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */


/* Start both operations and observe every failure. On the first failure,
 * request cancellation of siblings, await every completion and cleanup,
 * then propagate the original failure. Cancellation must be cooperative. */
