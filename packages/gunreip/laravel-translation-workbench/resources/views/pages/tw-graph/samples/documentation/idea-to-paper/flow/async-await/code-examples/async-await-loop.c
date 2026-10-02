/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */

/* For each item: start its async operation, wait through the runtime,
 * append its result, then request the next item. Empty input starts none. */
