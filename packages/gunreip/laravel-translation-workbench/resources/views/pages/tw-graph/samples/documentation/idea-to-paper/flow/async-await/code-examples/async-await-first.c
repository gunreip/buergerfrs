/* Language note, not an executable equivalent:
 * This example needs an asynchronous I/O or event-loop library in C.
 * Register a completion callback, then continue processing when the
 * operation reports its result. There is no C async/await syntax here.
 * A blocking wait would not demonstrate the suspension shown in the graph.
 * See JavaScript or C# for the executable pending-operation example.
 */


/* First completion selects the first result OR error.
 * First success ignores individual failures until a value arrives;
 * if every operation fails, report the collected errors.
 * Choose the matching selection API in the selected async runtime.
 * Remaining operations require an explicit cancellation policy. */
