// Language note, not an executable equivalent:
// A CompletableFuture cancellation is not a universal stop instruction
// for the operation that produces its result. An operation-specific
// cancellation mechanism must be selected and passed to that operation.
// The shown trace requires acknowledgement and resource cleanup before
// the awaiting continuation records cancellation.
// See JavaScript and C# for complete cancellation-aware examples.
