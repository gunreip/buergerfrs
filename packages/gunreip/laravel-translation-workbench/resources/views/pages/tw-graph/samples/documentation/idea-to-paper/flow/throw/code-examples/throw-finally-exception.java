// Inside a class; import java.util.List.
static void failAndCleanup(List<String> events) {
    IllegalArgumentException original = null;
    try {
        events.add("original");
        throw new IllegalArgumentException("Original failure");
    } catch (IllegalArgumentException error) {
        original = error;
        throw error;
    } finally {
        events.add("cleanup");
        throw new IllegalStateException("Cleanup failed", original);
    }
}

static IllegalStateException demonstrateReplacement(List<String> events) {
    IllegalStateException caught = null;
    try {
        events.add("outer-try");
        failAndCleanup(events);
        events.add("unreachable-after-call");
    } catch (IllegalStateException error) {
        events.add("outer-caught");
        caught = error; // getCause() is the original IllegalArgumentException.
    }
    events.add("after");
    return caught;
}
