// Inside a class; import java.util.List.
static void validateAndLog(List<String> events) {
    try {
        events.add("inner");
        throw new IllegalArgumentException("Invalid input");
        // events.add("unreachable-inner"); // Compile-time error if enabled.
    } catch (IllegalArgumentException error) {
        events.add("inner-log");
        throw error; // Same exception object.
        // events.add("unreachable-after-rethrow");
    }
}

static void demonstrateRethrow(List<String> events) {
    try {
        events.add("outer-try");
        validateAndLog(events);
        events.add("unreachable-after-call");
    } catch (IllegalArgumentException error) {
        events.add("outer-caught");
    }
    events.add("after");
}
