// Inside a class; import java.util.List.
static void validateAndCleanup(List<String> events) {
    try {
        events.add("inner");
        throw new IllegalArgumentException("Invalid input");
        // events.add("unreachable-inner"); // Compile-time error if enabled.
    } catch (IllegalArgumentException error) {
        events.add("inner-log");
        throw error; // Same exception object.
        // events.add("unreachable-after-rethrow");
    } finally {
        events.add("cleanup");
    }
}

static void demonstrateFinally(List<String> events) {
    try {
        events.add("outer-try");
        validateAndCleanup(events);
        events.add("unreachable-after-call");
    } catch (IllegalArgumentException error) {
        events.add("outer-caught");
    }
    events.add("after");
}
