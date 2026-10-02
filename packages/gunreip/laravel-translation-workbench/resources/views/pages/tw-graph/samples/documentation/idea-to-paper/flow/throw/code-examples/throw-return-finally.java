// Inside a class; import java.util.List.
// Illustrates suppression, not a recommended cleanup pattern.
static int failAndReturn(List<String> events) {
    try {
        events.add("original");
        throw new IllegalArgumentException("Original failure");
    } finally {
        events.add("cleanup");
        return 7; // Suppresses the pending exception.
    }
}

static Integer demonstrateFinallyReturn(List<String> events) {
    Integer result = null;
    try {
        events.add("outer-try");
        result = failAndReturn(events);
        events.add("received");
    } catch (IllegalArgumentException error) {
        events.add("unreachable-catch");
    }
    events.add("after");
    return result;
}
