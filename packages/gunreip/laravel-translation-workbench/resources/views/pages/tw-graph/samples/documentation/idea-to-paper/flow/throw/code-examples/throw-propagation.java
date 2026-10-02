// Inside a class; import java.util.List.
static void validateInput(List<String> events) {
    events.add("inner");
    throw new IllegalArgumentException("Invalid input");
    // events.add("unreachable-inner"); // Java rejects this unreachable statement.
}

static void demonstratePropagation(List<String> events) {
    try {
        events.add("outer-try");
        validateInput(events);
        events.add("unreachable-after-call");
    } catch (IllegalArgumentException error) {
        events.add("outer-caught");
    }
    events.add("after");
}
