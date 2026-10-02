/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */


/* Await the next item from an asynchronous source, process it, then request
 * the next. On early exit, exhaustion, failure or cancellation, await source
 * cleanup. Use the async iterator/stream APIs of the selected runtime. */
