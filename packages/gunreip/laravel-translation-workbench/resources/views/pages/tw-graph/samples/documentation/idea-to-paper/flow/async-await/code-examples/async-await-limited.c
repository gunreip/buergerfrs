/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */


/* Keep items queued until one of the limited worker slots is free.
 * Each worker starts one operation, awaits it, records its outcome,
 * then takes another item. Preserve input order when storing outcomes.
 * Do not start every operation before applying the concurrency limit.
 * Choose the worker/await APIs of the selected async runtime. */
