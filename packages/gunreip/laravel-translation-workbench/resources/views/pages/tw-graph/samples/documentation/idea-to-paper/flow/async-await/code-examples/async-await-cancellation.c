/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */

/* Cancellation must be observed by the selected async operation.
 * Release resources, report cancellation and run cleanup. A request alone
 * does not forcibly stop work. Keep unrelated errors distinct. */
