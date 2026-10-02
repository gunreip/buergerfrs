/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */

/* Start independent operations, observe each outcome and wait for all.
 * Keep successful values and error details in input order.
 * Choose the matching all-settled mechanism in the selected runtime. */
