// Language note, not an executable equivalent:
// The graph requires a deadline that requests cancellation of the actual
// operation, followed by acknowledgement and cleanup. A timeout on a future
// alone is insufficient to demonstrate that the underlying work has stopped.
// Choose the operation-specific cancellation API before implementing this path.
// See JavaScript and C# for the complete cooperative timeout examples.
