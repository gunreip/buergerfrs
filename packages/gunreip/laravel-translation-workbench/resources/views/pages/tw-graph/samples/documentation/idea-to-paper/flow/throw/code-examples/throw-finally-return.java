// Inside a class; import java.util.List.
static int computeAndCleanup(List<String> events) {
    try {
        events.add("pending-return");
        return 42;
    } finally {
        events.add("cleanup");
        throw new IllegalStateException("Cleanup failed");
    }
}

static Integer demonstrateFinallyThrow(List<String> events) {
    Integer result = null;
    try {
        events.add("outer-try");
        result = computeAndCleanup(events);
        events.add("unreachable-after-call");
    } catch (IllegalStateException error) {
        events.add("outer-caught");
    }
    events.add("after");
    return result; // Still null.
}
